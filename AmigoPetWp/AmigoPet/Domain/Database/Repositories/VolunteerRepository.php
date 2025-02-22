<?php
namespace AmigoPetWp\Domain\Database;

use AmigoPetWp\DomainEntities\Volunteer;

class VolunteerRepository {
    private $wpdb;
    private $table;
    
    public function __construct(\wpdb $wpdb) {
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'amigopet_volunteers';
    }
    
    public function save(Volunteer $volunteer): int {
        $data = [
            'organization_id' => $volunteer->getOrganizationId(),
            'name' => $volunteer->getName(),
            'email' => $volunteer->getEmail(),
            'phone' => $volunteer->getPhone(),
            'availability' => $volunteer->getAvailability(),
            'skills' => $volunteer->getSkills(),
            'status' => $volunteer->getStatus(),
            'start_date' => $volunteer->getStartDate()?->format('Y-m-d'),
            'end_date' => $volunteer->getEndDate()?->format('Y-m-d')
        ];

        if ($volunteer->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $volunteer->getId()]
            );
            return $volunteer->getId();
        }

        $this->wpdb->insert($this->table, $data);
        return $this->wpdb->insert_id;
    }

    public function findById(int $id): ?Volunteer {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findByOrganization(int $organizationId): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE organization_id = %d ORDER BY name",
                $organizationId
            ),
            ARRAY_A
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findActive(): array {
        $rows = $this->wpdb->get_results(
            "SELECT * FROM {$this->table} 
            WHERE status = 'active' 
            AND (end_date IS NULL OR end_date >= CURDATE())
            ORDER BY name",
            ARRAY_A
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findBySkill(string $skill): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} 
                WHERE status = 'active' 
                AND skills LIKE %s
                ORDER BY name",
                '%' . $this->wpdb->esc_like($skill) . '%'
            ),
            ARRAY_A
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    private function hydrate(array $row): Volunteer {
        $volunteer = new Volunteer(
            (int) $row['organization_id'],
            $row['name'],
            $row['email'],
            $row['phone'],
            $row['availability'],
            $row['skills']
        );

        $reflection = new \ReflectionClass($volunteer);
        
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($volunteer, (int) $row['id']);

        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($volunteer, $row['status']);

        if ($row['start_date']) {
            $startDateProperty = $reflection->getProperty('startDate');
            $startDateProperty->setAccessible(true);
            $startDateProperty->setValue($volunteer, new \DateTimeImmutable($row['start_date']));
        }

        if ($row['end_date']) {
            $endDateProperty = $reflection->getProperty('endDate');
            $endDateProperty->setAccessible(true);
            $endDateProperty->setValue($volunteer, new \DateTimeImmutable($row['end_date']));
        }

        return $volunteer;
    }
}
