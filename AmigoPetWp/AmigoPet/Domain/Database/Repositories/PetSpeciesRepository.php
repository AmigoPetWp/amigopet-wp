<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\PetSpecies;

class PetSpeciesRepository {
    private $wpdb;
    private $table;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'amigopet_pet_species';
    }

    public function findById(int $id): ?PetSpecies {
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

    public function findAll(array $args = []): array {
        $where = ['1=1'];
        $params = [];

        if (isset($args['status'])) {
            $where[] = 'status = %s';
            $params[] = $args['status'];
        }

        if (isset($args['search'])) {
            $where[] = 'name LIKE %s';
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

    public function save(PetSpecies $species): int {
        $data = [
            'name' => $species->getName(),
            'description' => $species->getDescription(),
            'status' => $species->getStatus()
        ];

        $format = ['%s', '%s', '%s'];

        if ($species->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $species->getId()],
                $format,
                ['%d']
            );
            return $species->getId();
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

    private function createEntity(array $row): PetSpecies {
        $species = new PetSpecies(
            $row['name'],
            $row['description'],
            $row['status']
        );

        $species->setId($row['id']);
        $species->setCreatedAt(new \DateTime($row['created_at']));
        $species->setUpdatedAt(new \DateTime($row['updated_at']));

        return $species;
    }
}
