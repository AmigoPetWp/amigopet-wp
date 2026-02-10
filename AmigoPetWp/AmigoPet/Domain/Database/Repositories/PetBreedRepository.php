<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\PetBreed;

class PetBreedRepository extends AbstractRepository
{
    public function __construct($wpdb)
    {
        parent::__construct($wpdb);
    }

    protected function getTableName(): string
    {
        return 'apwp_pet_breeds';
    }

    protected function createEntity(array $data): PetBreed
    {
        $breed = new PetBreed(
            (int) $data['species_id'],
            $data['name'],
            $data['description'] ?? ''
        );

        if (isset($data['id'])) {
            $breed->setId((int) $data['id']);
        }

        if (isset($data['characteristics'])) {
            $breed->setCharacteristics(json_decode($data['characteristics'], true) ?: []);
        }

        if (isset($data['status'])) {
            $breed->setStatus($data['status']);
        }

        if (isset($data['created_at'])) {
            $breed->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at'])) {
            $breed->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $breed;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof PetBreed) {
            throw new \InvalidArgumentException('Entity must be an instance of PetBreed');
        }

        return [
            'species_id' => $entity->getSpeciesId(),
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
            'characteristics' => json_encode($entity->getCharacteristics()),
            'status' => $entity->getStatus(),
            'created_at' => $entity->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()?->format('Y-m-d H:i:s')
        ];
    }

    public function findBySpecies(int $speciesId, array $args = []): array
    {
        $args['species_id'] = $speciesId;
        $args['orderby'] = $args['orderby'] ?? 'name';
        $args['order'] = $args['order'] ?? 'ASC';

        return $this->findAll($args);
    }

    public function findByNameAndSpecies(string $name, int $speciesId): ?PetBreed
    {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE name = %s AND species_id = %d LIMIT 1",
            $name,
            $speciesId
        );

        $result = $this->wpdb->get_row($sql, ARRAY_A);
        if (!$result) {
            return null;
        }

        return $this->createEntity($result);
    }

    public function findByCharacteristic(string $characteristic): array
    {
        return $this->findAll([
            'characteristic' => $characteristic,
            'orderby' => 'name',
            'order' => 'ASC'
        ]);
    }

    public function findByStatus(string $status): array
    {
        return $this->findAll([
            'status' => $status,
            'orderby' => 'name',
            'order' => 'ASC'
        ]);
    }

    public function findActive(): array
    {
        return $this->findByStatus('active');
    }

    public function search(string $term): array
    {
        return $this->findAll([
            'search' => $term,
            'orderby' => 'name',
            'order' => 'ASC'
        ]);
    }

    public function getReport(?string $startDate = null, ?string $endDate = null): array
    {
        $where = ['1=1'];
        $params = [];

        if ($startDate && $endDate) {
            $where[] = 'created_at BETWEEN %s AND %s';
            $params[] = $startDate . ' 00:00:00';
            $params[] = $endDate . ' 23:59:59';
        }

        $whereClause = implode(' AND ', $where);

        $query = $this->wpdb->prepare(
            "SELECT 
                COUNT(*) as total_breeds,
                COUNT(DISTINCT species_id) as total_species,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_breeds,
                SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_breeds,
                (SELECT species_id 
                 FROM {$this->table} 
                 WHERE {$whereClause}
                 GROUP BY species_id 
                 ORDER BY COUNT(*) DESC 
                 LIMIT 1) as most_diverse_species_id,
                (SELECT COUNT(*) 
                 FROM {$this->wpdb->prefix}apwp_pets 
                 WHERE breed_id IN (SELECT id FROM {$this->table})) as total_pets,
                (SELECT breed_id 
                 FROM {$this->wpdb->prefix}apwp_pets 
                 GROUP BY breed_id 
                 ORDER BY COUNT(*) DESC 
                 LIMIT 1) as most_common_breed_id
            FROM {$this->table}
            WHERE {$whereClause}",
            $params
        );

        $result = $this->wpdb->get_row($query, ARRAY_A);

        // Adiciona informações da raça mais comum
        if ($result['most_common_breed_id']) {
            $mostCommonBreed = $this->findById((int) $result['most_common_breed_id']);
            if ($mostCommonBreed) {
                $result['most_common_breed'] = [
                    'id' => $mostCommonBreed->getId(),
                    'name' => $mostCommonBreed->getName(),
                    'species_id' => $mostCommonBreed->getSpeciesId()
                ];
            }
        }

        // Adiciona informações da espécie com mais raças
        if ($result['most_diverse_species_id']) {
            $speciesRepo = new PetSpeciesRepository($this->wpdb);
            $mostDiverseSpecies = $speciesRepo->findById((int) $result['most_diverse_species_id']);
            if ($mostDiverseSpecies) {
                $result['most_diverse_species'] = [
                    'id' => $mostDiverseSpecies->getId(),
                    'name' => $mostDiverseSpecies->getName()
                ];
            }
        }

        return $result ?: [
            'total_breeds' => 0,
            'total_species' => 0,
            'active_breeds' => 0,
            'inactive_breeds' => 0,
            'most_diverse_species_id' => null,
            'most_diverse_species' => null,
            'total_pets' => 0,
            'most_common_breed_id' => null,
            'most_common_breed' => null
        ];
    }

    public function save($entity): int
    {
        if (!$entity instanceof PetBreed) {
            throw new \InvalidArgumentException('Entity must be an instance of PetBreed');
        }

        $data = [
            'species_id' => $entity->getSpeciesId(),
            'name' => $entity->getName(),
            'status' => $entity->getStatus(),
            'characteristics' => json_encode($entity->getCharacteristics())
        ];

        $format = ['%d', '%s', '%s', '%s'];

        if ($entity->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $entity->getId()],
                $format,
                ['%d']
            );
            return $breed->getId();
        }

        $this->wpdb->insert($this->table, $data, $format);
        return $this->wpdb->insert_id;
    }

    public function delete(int $id): bool
    {
        return (bool) $this->wpdb->delete(
            $this->table,
            ['id' => $id],
            ['%d']
        );
    }
}
