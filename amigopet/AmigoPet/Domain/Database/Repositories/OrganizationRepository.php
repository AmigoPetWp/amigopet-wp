<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\Organization;

class OrganizationRepository extends AbstractRepository
{
    protected function initTable(): void
    {
        $this->table = $this->getTableName('organizations');
    }

    protected function createEntity(array $row): object
    {
        $organization = new Organization(
            $row['name'],
            $row['email'],
            $row['phone'],
            $row['address']
        );

        if (isset($row['id'])) {
            $organization->setId((int) $row['id']);
        }

        if (isset($row['website'])) {
            $organization->setWebsite($row['website']);
        }

        if (isset($row['status'])) {
            $organization->setStatus($row['status']);
        }

        if (isset($row['created_at'])) {
            $organization->setCreatedAt(new \DateTime($row['created_at']));
        }

        if (isset($row['updated_at'])) {
            $organization->setUpdatedAt(new \DateTime($row['updated_at']));
        }

        return $organization;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof Organization) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of Organization', 'amigopet'));
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

    public function findByEmail(string $email): ?Organization
    {
        $results = $this->findAll(['email' => $email]);
        return !empty($results) ? $results[0] : null;
    }

    public function findByStatus(string $status): array
    {
        return $this->findAll(['status' => $status]);
    }

    public function findActive(): array
    {
        return $this->findAll(['status' => 'active']);
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
                    'SELECT * FROM %i WHERE name LIKE %s OR email LIKE %s OR phone LIKE %s OR address LIKE %s',
                    $table,
                    $search,
                    $search,
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

    public function findByFilters(array $filters): array
    {
        $criteria = [];

        if (isset($filters['status'])) {
            $criteria['status'] = $filters['status'];
        }

        if (isset($filters['has_website'])) {
            $criteria['has_website'] = (bool) $filters['has_website'];
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