<?php
namespace AmigoPetWp\Domain\Database;

use AmigoPetWp\Domain\Entities\Term;

class TermRepository {
    private $wpdb;
    private $table;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'apwp_terms';
    }

    public function save(Term $term): int {
        $data = [
            'organization_id' => $term->getOrganizationId(),
            'title' => $term->getTitle(),
            'content' => $term->getContent(),
            'type' => $term->getType(),
            'is_required' => $term->isRequired(),
            'is_active' => $term->isActive(),
            'accepted_by' => maybe_serialize($term->getAcceptedBy()),
            'created_at' => $term->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $term->getUpdatedAt()->format('Y-m-d H:i:s')
        ];

        $format = [
            '%d', // organization_id
            '%s', // title
            '%s', // content
            '%s', // type
            '%d', // is_required
            '%d', // is_active
            '%s', // accepted_by
            '%s', // created_at
            '%s'  // updated_at
        ];

        if ($term->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $term->getId()],
                $format,
                ['%d']
            );
            return $term->getId();
        }

        $this->wpdb->insert($this->table, $data, $format);
        return $this->wpdb->insert_id;
    }

    public function findById(int $id): ?Term {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        if (!$row) {
            return null;
        }

        return $this->createTermFromRow($row);
    }

    public function findByType(string $type): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE type = %s ORDER BY created_at DESC",
                $type
            ),
            ARRAY_A
        );

        return array_map([$this, 'createTermFromRow'], $rows);
    }

    public function findByOrganization(int $organizationId): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE organization_id = %d ORDER BY created_at DESC",
                $organizationId
            ),
            ARRAY_A
        );

        return array_map([$this, 'createTermFromRow'], $rows);
    }

    public function findActiveByType(string $type): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE type = %s AND is_active = 1 ORDER BY created_at DESC",
                $type
            ),
            ARRAY_A
        );

        return array_map([$this, 'createTermFromRow'], $rows);
    }

    public function delete(int $id): bool {
        return $this->wpdb->delete(
            $this->table,
            ['id' => $id],
            ['%d']
        ) !== false;
    }

    public function findAcceptedByUser(int $userId): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE accepted_by LIKE %s",
                '%"' . $userId . '"%'
            ),
            ARRAY_A
        );

        return array_map([$this, 'createTermFromRow'], $rows);
    }

    private function createTermFromRow(array $row): Term {
        $term = new Term(
            (int)$row['organization_id'],
            $row['title'],
            $row['content'],
            $row['type'],
            (bool)$row['is_required'],
            (bool)$row['is_active'],
            (int)$row['id']
        );

        $acceptedBy = maybe_unserialize($row['accepted_by']);
        if (is_array($acceptedBy)) {
            foreach ($acceptedBy as $userId) {
                $term->addAcceptedBy($userId);
            }
        }

        return $term;
    }
}
