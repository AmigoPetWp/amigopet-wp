<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\TermType;

class TermTypeRepository extends AbstractRepository {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
    }

    protected function getTableName(): string {
        return 'apwp_term_types';
    }

    protected function createEntity(array $data): TermType {
        $type = new TermType(
            $data['name'],
            $data['slug']
        );

        if (isset($data['id'])) {
            $type->setId((int)$data['id']);
        }

        if (isset($data['description'])) {
            $type->setDescription($data['description']);
        }

        if (isset($data['required'])) {
            $type->setRequired((bool)$data['required']);
        }

        if (isset($data['status'])) {
            $type->setStatus($data['status']);
        }

        if (isset($data['created_at'])) {
            $type->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at'])) {
            $type->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $type;
    }

    protected function toDatabase($entity): array {
        if (!$entity instanceof TermType) {
            throw new \InvalidArgumentException('Entity must be an instance of TermType');
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

    public function findBySlug(string $slug): ?TermType {
        $args = [
            'slug' => $slug,
            'limit' => 1
        ];

        $results = $this->findAll($args);
        return !empty($results) ? $results[0] : null;
    }

    public function findRequired(): array {
        return $this->findAll([
            'required' => true,
            'status' => 'active',
            'orderby' => 'name',
            'order' => 'ASC'
        ]);
    }

    public function findByStatus(string $status): array {
        return $this->findAll([
            'status' => $status,
            'orderby' => 'name',
            'order' => 'ASC'
        ]);
    }

    public function search(string $term): array {
        return $this->findAll([
            'search' => $term,
            'search_columns' => ['name', 'slug', 'description'],
            'orderby' => 'name',
            'order' => 'ASC'
        ]);
    }

    public function getReport(?string $startDate = null, ?string $endDate = null): array {
        $where = ['1=1'];
        $params = [];

        if ($startDate && $endDate) {
            $where[] = 'created_at BETWEEN %s AND %s';
            $params[] = $startDate . ' 00:00:00';
            $params[] = $endDate . ' 23:59:59';
        }

        $whereClause = implode(' AND ', $where);

        $query = $this->wpdb->prepare(
            "SELECT 
                COUNT(*) as total_types,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_types,
                COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_types,
                COUNT(CASE WHEN required = 1 THEN 1 END) as required_types,
                (SELECT COUNT(*) 
                 FROM {$this->wpdb->prefix}apwp_signed_terms st 
                 WHERE st.term_id IN (SELECT id FROM {$this->table})) as total_signatures,
                (SELECT term_id 
                 FROM {$this->wpdb->prefix}apwp_signed_terms 
                 GROUP BY term_id 
                 ORDER BY COUNT(*) DESC 
                 LIMIT 1) as most_signed_type_id
            FROM {$this->table}
            WHERE {$whereClause}",
            $params
        );

        $result = $this->wpdb->get_row($query, ARRAY_A);

        if ($result && $result['most_signed_type_id']) {
            $mostSignedType = $this->findById((int)$result['most_signed_type_id']);
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
