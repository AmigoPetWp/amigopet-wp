<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\PetBreed;

class PetBreedRepository extends AbstractRepository
{


    protected function initTable(): void
    {
        $this->table = $this->getTableName('pet_breeds');
    }

    protected function createEntity(array $row): object
    {
        $breed = new PetBreed(
            (int) $row['species_id'],
            $row['name'],
            $row['description'] ?? ''
        );

        if (isset($row['id'])) {
            $breed->setId((int) $row['id']);
        }

        if (isset($row['characteristics'])) {
            $breed->setCharacteristics(json_decode($row['characteristics'], true) ?: []);
        }

        if (isset($row['status'])) {
            $breed->setStatus($row['status']);
        }

        if (isset($row['created_at'])) {
            $breed->setCreatedAt(new \DateTime($row['created_at']));
        }

        if (isset($row['updated_at'])) {
            $breed->setUpdatedAt(new \DateTime($row['updated_at']));
        }

        return $breed;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof PetBreed) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of PetBreed', 'amigopet'));
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
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return null;
        }
        $result = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE name = %s AND species_id = %d LIMIT 1',
                $table,
                $name,
                $speciesId
            ),
            ARRAY_A
        );
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
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        $petsTable = $this->sanitizeIdentifier($this->wpdb->prefix . 'apwp_pets');
        if ($table === '' || $petsTable === '') {
            return [
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
        $start = ($startDate ?: '1970-01-01') . ' 00:00:00';
        $end = ($endDate ?: '9999-12-31') . ' 23:59:59';
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT 
                COUNT(*) as total_breeds,
                COUNT(DISTINCT species_id) as total_species,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_breeds,
                SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_breeds,
                (SELECT species_id 
                 FROM %i
                 WHERE created_at BETWEEN %s AND %s
                 GROUP BY species_id 
                 ORDER BY COUNT(*) DESC 
                 LIMIT 1) as most_diverse_species_id,
                (SELECT COUNT(*) 
                 FROM %i
                 WHERE breed_id IN (SELECT id FROM %i)) as total_pets,
                (SELECT breed_id 
                 FROM %i
                 GROUP BY breed_id 
                 ORDER BY COUNT(*) DESC 
                 LIMIT 1) as most_common_breed_id
            FROM %i
            WHERE created_at BETWEEN %s AND %s",
                $table,
                $petsTable,
                $table,
                $petsTable,
                $table,
                $start,
                $end,
                $start,
                $end
            ),
            ARRAY_A
        );

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
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of PetBreed', 'amigopet'));
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
            return $entity->getId();
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