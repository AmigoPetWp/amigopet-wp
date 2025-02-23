<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\Volunteer;

class VolunteerRepository extends AbstractRepository {
    protected function getTableName(): string {
        return 'apwp_volunteers';
    }

    protected function createEntity(array $data): Volunteer {
        $volunteer = new Volunteer(
            (int)$data['organization_id'],
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['availability'],
            json_decode($data['skills'], true) ?: [],
            $data['status']
        );

        if (isset($data['id'])) {
            $volunteer->setId((int)$data['id']);
        }

        if (isset($data['start_date'])) {
            $volunteer->setStartDate(new \DateTime($data['start_date']));
        }

        if (isset($data['end_date'])) {
            $volunteer->setEndDate(new \DateTime($data['end_date']));
        }

        return $volunteer;
    }

    protected function toDatabase($entity): array {
        if (!$entity instanceof Volunteer) {
            throw new \InvalidArgumentException('Entity must be an instance of Volunteer');
        }

        return [
            'organization_id' => $entity->getOrganizationId(),
            'name' => $entity->getName(),
            'email' => $entity->getEmail(),
            'phone' => $entity->getPhone(),
            'availability' => $entity->getAvailability(),
            'skills' => json_encode($entity->getSkills()),
            'status' => $entity->getStatus(),
            'start_date' => $entity->getStartDate()?->format('Y-m-d'),
            'end_date' => $entity->getEndDate()?->format('Y-m-d')
        ];
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
    
    public function search(string $term): array {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->table} 
            WHERE name LIKE %s 
            OR email LIKE %s",
            "%{$term}%",
            "%{$term}%"
        );
        
        return array_map(
            [$this, 'hydrate'],
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
}
