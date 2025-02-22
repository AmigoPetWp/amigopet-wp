<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\TermType;

class TermTypeRepository {
    private $wpdb;
    private $table;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'amigopet_term_types';
    }

    public function findById(int $id): ?TermType {
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

        return $this->createEntity($row);
    }

    public function findBySlug(string $slug): ?TermType {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE slug = %s",
                $slug
            ),
            ARRAY_A
        );

        if (!$row) {
            return null;
        }

        return $this->createEntity($row);
    }

    public function findAll(array $args = []): array {
        $where = ['1=1'];
        $params = [];

        if (isset($args['status'])) {
            $where[] = 'status = %s';
            $params[] = $args['status'];
        }

        if (isset($args['required'])) {
            $where[] = 'required = %d';
            $params[] = (int) $args['required'];
        }

        if (isset($args['search'])) {
            $where[] = '(name LIKE %s OR slug LIKE %s)';
            $params[] = '%' . $args['search'] . '%';
            $params[] = '%' . $args['search'] . '%';
        }

        $orderBy = $args['orderby'] ?? 'name';
        $order = $args['order'] ?? 'ASC';

        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where) . " ORDER BY {$orderBy} {$order}";
        
        if (!empty($params)) {
            $sql = $this->wpdb->prepare($sql, $params);
        }

        $rows = $this->wpdb->get_results($sql, ARRAY_A);

        return array_map([$this, 'createEntity'], $rows);
    }

    public function save(TermType $type): int {
        $data = [
            'name' => $type->getName(),
            'slug' => $type->getSlug(),
            'description' => $type->getDescription(),
            'required' => $type->isRequired(),
            'status' => $type->getStatus()
        ];

        $format = ['%s', '%s', '%s', '%d', '%s'];

        if ($type->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $type->getId()],
                $format,
                ['%d']
            );
            return $type->getId();
        }

        $this->wpdb->insert($this->table, $data, $format);
        return $this->wpdb->insert_id;
    }

    public function delete(int $id): bool {
        return (bool) $this->wpdb->delete(
            $this->table,
            ['id' => $id],
            ['%d']
        );
    }

    private function createEntity(array $row): TermType {
        $type = new TermType(
            $row['name'],
            $row['slug'],
            $row['description'],
            (bool) $row['required'],
            $row['status']
        );

        $type->setId($row['id']);
        $type->setCreatedAt(new \DateTime($row['created_at']));
        $type->setUpdatedAt(new \DateTime($row['updated_at']));

        return $type;
    }
}
