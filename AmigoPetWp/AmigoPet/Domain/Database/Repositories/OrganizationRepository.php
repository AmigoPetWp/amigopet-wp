<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\Organization;

class OrganizationRepository extends AbstractRepository {
    protected function getTableName(): string {
        return 'apwp_organizations';
    }
    
    protected function createEntity(array $data): Organization {
        $organization = new Organization(
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['address']
        );

        if (isset($data['id'])) {
            $organization->setId((int)$data['id']);
        }

        if (isset($data['website'])) {
            $organization->setWebsite($data['website']);
        }

        if (isset($data['status'])) {
            $organization->setStatus($data['status']);
        }

        if (isset($data['created_at'])) {
            $organization->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at'])) {
            $organization->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $organization;
    }

    protected function toDatabase($entity): array {
        if (!$entity instanceof Organization) {
            throw new \InvalidArgumentException('Entity must be an instance of Organization');
        }

        return [
            'name' => $entity->getName(),
            'email' => $entity->getEmail(),
            'phone' => $entity->getPhone(),
            'address' => $entity->getAddress(),
            'website' => $entity->getWebsite(),
            'status' => $entity->getStatus(),
            'created_at' => $entity->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()?->format('Y-m-d H:i:s')
        ];
    }

    public function findByEmail(string $email): ?Organization {
        $results = $this->findAll(['email' => $email]);
        return !empty($results) ? $results[0] : null;
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
            OR email LIKE %s 
            OR phone LIKE %s 
            OR address LIKE %s",
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
        
        if (isset($filters['has_website'])) {
            $criteria['has_website'] = (bool)$filters['has_website'];
        }
        
        if (isset($filters['city'])) {
            $criteria['city'] = $filters['city'];
        }
        
        if (isset($filters['state'])) {
            $criteria['state'] = $filters['state'];
        }
        
        return $this->findAll($criteria);
    }
}
