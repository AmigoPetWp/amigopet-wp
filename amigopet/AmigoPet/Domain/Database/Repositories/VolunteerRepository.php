<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\Volunteer;

class VolunteerRepository extends AbstractRepository
{
    protected function initTable(): void
    {
        $this->table = $this->getTableName('volunteers');
    }

    protected function createEntity(array $row): object
    {
        $volunteer = new Volunteer(
            (int) $row['organization_id'],
            $row['name'],
            $row['email'],
            $row['phone'],
            $row['availability'],
            is_string($row['skills']) ? (json_decode($row['skills'], true) ?: []) : (array) $row['skills'],
            $row['status']
        );

        $volunteer->setId((int) $row['id']);

        if (!empty($row['start_date'])) {
            $volunteer->setStartDate(new \DateTime($row['start_date']));
        }

        if (!empty($row['end_date'])) {
            $volunteer->setEndDate(new \DateTime($row['end_date']));
        }

        return $volunteer;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof Volunteer) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of Volunteer', 'amigopet'));
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

    public function findByOrganization(int $organizationId): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE organization_id = %d ORDER BY name',
                $table,
                $organizationId
            ),
            ARRAY_A
        );

        return array_map([$this, 'createEntity'], $rows);
    }

    public function findActive(): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE status = %s AND (end_date IS NULL OR end_date >= CURDATE()) ORDER BY name',
                $table,
                'active'
            ),
            ARRAY_A
        );

        return array_map([$this, 'createEntity'], $rows);
    }

    public function findBySkill(string $skill): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE status = %s AND skills LIKE %s ORDER BY name',
                $table,
                'active',
                '%' . $wpdb->esc_like($skill) . '%'
            ),
            ARRAY_A
        );

        return array_map([$this, 'createEntity'], $rows);
    }

    public function search(string $term): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $search = '%' . $wpdb->esc_like($term) . '%';

        return array_map(
            [$this, 'createEntity'],
            $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT * FROM %i WHERE name LIKE %s OR email LIKE %s',
                    $table,
                    $search,
                    $search
                ),
                ARRAY_A
            )
        );
    }

    public function getReport(?string $startDate = null, ?string $endDate = null): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $start = ($startDate ?: '1970-01-01') . ' 00:00:00';
        $end = ($endDate ?: '9999-12-31') . ' 23:59:59';

        return $wpdb->get_results(
            $wpdb->prepare(
                'SELECT COUNT(*) as total, status, DATE(created_at) as date FROM %i WHERE created_at BETWEEN %s AND %s GROUP BY status, DATE(created_at) ORDER BY date DESC',
                $table,
                $start,
                $end
            ),
            ARRAY_A
        );
    }
}