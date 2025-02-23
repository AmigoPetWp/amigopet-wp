<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\Species;

class SpeciesRepository extends AbstractRepository {
    protected function getTableName(): string {
        return 'apwp_species';
    }

    protected function createEntity(array $data): Species {
        $species = new Species(
            $data['name'],
            $data['scientific_name']
        );

        if (isset($data['id'])) {
            $species->setId((int)$data['id']);
        }

        if (isset($data['description'])) {
            $species->setDescription($data['description']);
        }

        if (isset($data['characteristics'])) {
            $species->setCharacteristics($data['characteristics']);
        }

        if (isset($data['status'])) {
            $species->setStatus($data['status']);
        }

        if (isset($data['created_at'])) {
            $species->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at'])) {
            $species->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $species;
    }

    protected function toDatabase($entity): array {
        if (!$entity instanceof Species) {
            throw new \InvalidArgumentException('Entity must be an instance of Species');
        }

        return [
            'name' => $entity->getName(),
            'scientific_name' => $entity->getScientificName(),
            'description' => $entity->getDescription(),
            'characteristics' => $entity->getCharacteristics(),
            'status' => $entity->getStatus(),
            'created_at' => $entity->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()?->format('Y-m-d H:i:s')
        ];
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
            OR scientific_name LIKE %s 
            OR description LIKE %s 
            OR characteristics LIKE %s",
            "%{$term}%",
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
                status,
                DATE(created_at) as date
            FROM {$this->table}
            {$whereClause}
            GROUP BY status, DATE(created_at)
            ORDER BY date DESC",
            $params
        );
        
        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    public function findByFilters(array $filters): array {
        $criteria = [];
        
        if (isset($filters['status'])) {
            $criteria['status'] = $filters['status'];
        }
        
        if (isset($filters['has_characteristics'])) {
            $criteria['has_characteristics'] = (bool)$filters['has_characteristics'];
        }
        
        return $this->findAll($criteria);
    }
}
