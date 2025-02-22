<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\PetBreed;

class PetBreedRepository {
    private $wpdb;
    private $table;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'amigopet_pet_breeds';
    }

    public function findById(int $id): ?PetBreed {
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

    public function findBySpecies(int $speciesId, array $args = []): array {
        $where = ['species_id = %d'];
        $params = [$speciesId];

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

        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where) . " ORDER BY {$orderBy} {$order}",
            $params
        );

        $rows = $this->wpdb->get_results($sql, ARRAY_A);

        return array_map([$this, 'createEntity'], $rows);
    }

    public function findAll(array $args = []): array {
        $where = ['1=1'];
        $params = [];

        if (isset($args['species_id'])) {
            $where[] = 'species_id = %d';
            $params[] = $args['species_id'];
        }

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

    public function save(PetBreed $breed): int {
        $data = [
            'species_id' => $breed->getSpeciesId(),
            'name' => $breed->getName(),
            'description' => $breed->getDescription(),
            'characteristics' => json_encode($breed->getCharacteristics()),
            'status' => $breed->getStatus()
        ];

        $format = ['%d', '%s', '%s', '%s', '%s'];

        if ($breed->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $breed->getId()],
                $format,
                ['%d']
            );
            return $breed->getId();
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

    private function createEntity(array $row): PetBreed {
        $breed = new PetBreed(
            (int) $row['species_id'],
            $row['name'],
            $row['description'],
            json_decode($row['characteristics'], true),
            $row['status']
        );

        $breed->setId($row['id']);
        $breed->setCreatedAt(new \DateTime($row['created_at']));
        $breed->setUpdatedAt(new \DateTime($row['updated_at']));

        return $breed;
    }
}
