<?php
namespace AmigoPetWp\Domain\Database;

use AmigoPetWp\Domain\Entities\Organization;

class OrganizationRepository {
    private $wpdb;
    private $table;
    
    public function __construct(\wpdb $wpdb) {
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'amigopet_organizations';
    }
    
    public function save(Organization $organization): int {
        $data = [
            'name' => $organization->getName(),
            'email' => $organization->getEmail(),
            'phone' => $organization->getPhone(),
            'website' => $organization->getWebsite(),
            'address' => $organization->getAddress(),
            'status' => $organization->getStatus(),
            'created_at' => $organization->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $organization->getUpdatedAt()->format('Y-m-d H:i:s')
        ];

        if ($organization->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $organization->getId()],
                ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'],
                ['%d']
            );
            return $organization->getId();
        }

        $this->wpdb->insert(
            $this->table,
            $data,
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );
        $id = $this->wpdb->insert_id;
        $organization->setId($id);
        return $id;
    }

    public function findById(int $id): ?Organization {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findByStatus(string $status): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE status = %s ORDER BY name",
                $status
            ),
            ARRAY_A
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findActive(): array {
        return $this->findByStatus(Organization::STATUS_ACTIVE);
    }

    public function findInactive(): array {
        return $this->findByStatus(Organization::STATUS_INACTIVE);
    }

    public function findPending(): array {
        return $this->findByStatus(Organization::STATUS_PENDING);
    }

    public function findAll(): array {
        $rows = $this->wpdb->get_results(
            "SELECT * FROM {$this->table} ORDER BY name",
            ARRAY_A
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function getReport(): array {
        $query = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = %s THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status = %s THEN 1 ELSE 0 END) as inactive,
            SUM(CASE WHEN status = %s THEN 1 ELSE 0 END) as pending
            FROM {$this->table}";

        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                $query,
                [
                    Organization::STATUS_ACTIVE,
                    Organization::STATUS_INACTIVE,
                    Organization::STATUS_PENDING
                ]
            ),
            ARRAY_A
        );
    }

    private function hydrate(array $row): Organization {
        $organization = new Organization(
            $row['name'],
            $row['email'],
            $row['phone'],
            $row['address'],
            $row['website']
        );

        $reflection = new \ReflectionClass($organization);
        
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($organization, (int) $row['id']);

        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($organization, $row['status']);

        $createdAtProperty = $reflection->getProperty('createdAt');
        $createdAtProperty->setAccessible(true);
        $createdAtProperty->setValue($organization, new \DateTimeImmutable($row['created_at']));

        $updatedAtProperty = $reflection->getProperty('updatedAt');
        $updatedAtProperty->setAccessible(true);
        $updatedAtProperty->setValue($organization, new \DateTimeImmutable($row['updated_at']));

        return $organization;
    }
}
