<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\Breed;

class BreedRepository extends AbstractRepository {
    protected function getTableName(): string {
        return 'apwp_breeds';
    }

    protected function createEntity(array $data): Breed {
        $breed = new Breed(
            $data['name'],
            (int)$data['species_id']
        );

        if (isset($data['id'])) {
            $breed->setId((int)$data['id']);
        }

        if (isset($data['description'])) {
            $breed->setDescription($data['description']);
        }

        if (isset($data['characteristics'])) {
            $breed->setCharacteristics($data['characteristics']);
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

    protected function toDatabase($entity): array {
        if (!$entity instanceof Breed) {
            throw new \InvalidArgumentException('Entity must be an instance of Breed');
        }

        return [
            'name' => $entity->getName(),
            'species_id' => $entity->getSpeciesId(),
            'description' => $entity->getDescription(),
            'characteristics' => $entity->getCharacteristics(),
            'status' => $entity->getStatus(),
            'created_at' => $entity->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()?->format('Y-m-d H:i:s')
        ];
    }

    public function findBySpecies(int $speciesId): array {
        return $this->findAll(['species_id' => $speciesId]);
    }

    public function findByStatus(string $status): array {
        return $this->findAll(['status' => $status]);
    }

    public function findActive(): array {
        return $this->findAll(['status' => 'active']);
    }

    public function search(string $term): array {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->table} 
            WHERE name LIKE %s 
            OR description LIKE %s 
            OR characteristics LIKE %s",
            "%{$term}%",
            "%{$term}%",
            "%{$term}%"
        );
        
        return array_map(
            [$this, 'createEntity'],
            $this->wpdb->get_results($sql, ARRAY_A)
        );
    }

    public function getReport(?string $startDate = null, ?string $endDate = null): array {
        $where = [];
        $params = [];
        
        if ($startDate) {
            $where[] = "created_at >= %s";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $where[] = "created_at <= %s";
            $params[] = $endDate;
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        $sql = $this->wpdb->prepare(
            "SELECT 
                COUNT(*) as total,
                species_id,
                status,
                DATE(created_at) as date
            FROM {$this->table}
            {$whereClause}
            GROUP BY species_id, status, DATE(created_at)
            ORDER BY date DESC",
            $params
        );
        
        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    public function findByFilters(array $filters): array {
        $criteria = [];
        
        if (isset($filters['species_id'])) {
            $criteria['species_id'] = (int)$filters['species_id'];
        }
        
        if (isset($filters['status'])) {
            $criteria['status'] = $filters['status'];
        }
        
        if (isset($filters['has_characteristics'])) {
            $criteria['has_characteristics'] = (bool)$filters['has_characteristics'];
        }
        
        return $this->findAll($criteria);
    }
}
