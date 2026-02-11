<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\TermType;

class TermTypeRepository extends AbstractRepository
{


    protected function initTable(): void
    {
        $this->table = $this->getTableName('term_types');
    }

    protected function createEntity(array $row): object
    {
        $type = new TermType(
            $row['name'],
            $row['slug']
        );

        if (isset($row['id'])) {
            $type->setId((int) $row['id']);
        }

        if (isset($row['description'])) {
            $type->setDescription($row['description']);
        }

        if (isset($row['required'])) {
            $type->setRequired((bool) $row['required']);
        }

        if (isset($row['status'])) {
            $type->setStatus($row['status']);
        }

        if (isset($row['created_at'])) {
            $type->setCreatedAt(new \DateTime($row['created_at']));
        }

        if (isset($row['updated_at'])) {
            $type->setUpdatedAt(new \DateTime($row['updated_at']));
        }

        return $type;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof TermType) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of TermType', 'amigopet'));
        }

        return [
            'name' => $entity->getName(),
            'slug' => $entity->getSlug(),
            'description' => $entity->getDescription(),
            'required' => $entity->isRequired(),
            'status' => $entity->getStatus(),
            'created_at' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()->format('Y-m-d H:i:s')
        ];
    }

    public function findBySlug(string $slug): ?TermType
    {
        $args = [
            'slug' => $slug,
            'limit' => 1
        ];

        $results = $this->findAll($args);
        return !empty($results) ? $results[0] : null;
    }

    public function findRequired(): array
    {
        return $this->findAll([
            'required' => true,
            'status' => 'active',
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

    public function search(string $term): array
    {
        return $this->findAll([
            'search' => $term,
            'search_columns' => ['name', 'slug', 'description'],
            'orderby' => 'name',
            'order' => 'ASC'
        ]);
    }

    public function getReport(?string $startDate = null, ?string $endDate = null): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        $signedTermsTable = $this->sanitizeIdentifier($this->wpdb->prefix . 'apwp_signed_terms');
        if ($table === '' || $signedTermsTable === '') {
            return [
                'total_types' => 0,
                'active_types' => 0,
                'inactive_types' => 0,
                'required_types' => 0,
                'total_signatures' => 0,
                'most_signed_type_id' => null,
                'most_signed_type' => null
            ];
        }
        $start = ($startDate ?: '1970-01-01') . ' 00:00:00';
        $end = ($endDate ?: '9999-12-31') . ' 23:59:59';
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT 
                COUNT(*) as total_types,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_types,
                COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_types,
                COUNT(CASE WHEN required = 1 THEN 1 END) as required_types,
                (SELECT COUNT(*) 
                 FROM %i st 
                 WHERE st.term_id IN (SELECT id FROM %i)) as total_signatures,
                (SELECT term_id 
                 FROM %i
                 GROUP BY term_id 
                 ORDER BY COUNT(*) DESC 
                 LIMIT 1) as most_signed_type_id
            FROM %i
            WHERE created_at BETWEEN %s AND %s",
                $signedTermsTable,
                $table,
                $signedTermsTable,
                $table,
                $start,
                $end
            ),
            ARRAY_A
        );

        if ($result && $result['most_signed_type_id']) {
            $mostSignedType = $this->findById((int) $result['most_signed_type_id']);
            if ($mostSignedType) {
                $result['most_signed_type'] = [
                    'id' => $mostSignedType->getId(),
                    'name' => $mostSignedType->getName(),
                    'slug' => $mostSignedType->getSlug()
                ];
            }
        }

        return $result ?: [
            'total_types' => 0,
            'active_types' => 0,
            'inactive_types' => 0,
            'required_types' => 0,
            'total_signatures' => 0,
            'most_signed_type_id' => null,
            'most_signed_type' => null
        ];
    }
}