<?php
namespace AmigoPetWp\Domain\Database;

use AmigoPetWp\DomainEntities\Pet;

class PetRepository {
    private $wpdb;
    private $table;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'amigopet_pets';
    }

    public function save(Pet $pet): int {
        $data = [
            'name' => $pet->getName(),
            'species' => $pet->getSpecies(),
            'size' => $pet->getSize(),
            'status' => 'available'
        ];

        $this->wpdb->insert($this->table, $data);
        return $this->wpdb->insert_id;
    }

    public function findById(int $id): ?Pet {
        $query = $this->wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE id = %d",
            $id
        );

        $result = $this->wpdb->get_row($query);
        if (!$result) return null;

        return $this->hydrate($result);
    }

    public function findAll(int $perPage = 10, int $page = 1): array {
        $offset = ($page - 1) * $perPage;
        
        $query = $this->wpdb->prepare(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $perPage,
            $offset
        );

        $results = $this->wpdb->get_results($query);
        return array_map([$this, 'hydrate'], $results);
    }

    private function hydrate($data): Pet {
        return new Pet(
            $data->name,
            $data->species,
            $data->size
        );
    }
}
