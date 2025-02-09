<?php
namespace AmigoPet\Domain\Database;

use AmigoPet\Domain\Entities\Organization;

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
            'address' => $organization->getAddress(),
            'city' => $organization->getCity(),
            'state' => $organization->getState(),
            'zip_code' => $organization->getZipCode()
        ];

        if ($organization->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $organization->getId()]
            );
            return $organization->getId();
        }

        $this->wpdb->insert($this->table, $data);
        return $this->wpdb->insert_id;
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

    public function findAll(): array {
        $rows = $this->wpdb->get_results(
            "SELECT * FROM {$this->table} ORDER BY name",
            ARRAY_A
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    private function hydrate(array $row): Organization {
        $organization = new Organization(
            $row['name'],
            $row['email'],
            $row['phone'],
            $row['address'],
            $row['city'],
            $row['state'],
            $row['zip_code']
        );

        $reflection = new \ReflectionClass($organization);
        
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($organization, (int) $row['id']);

        return $organization;
    }
}
