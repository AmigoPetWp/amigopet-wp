<?php
namespace AmigoPetWp\Domain\Database;

use AmigoPetWp\DomainEntities\Adopter;

class AdopterRepository {
    private $wpdb;
    private $table;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'amigopet_adopters';
    }

    public function save(Adopter $adopter): int {
        $data = [
            'name' => $adopter->getName(),
            'email' => $adopter->getEmail(),
            'document' => $adopter->getDocument()
        ];

        $this->wpdb->insert($this->table, $data);
        return $this->wpdb->insert_id;
    }

    public function findByEmail(string $email): ?Adopter {
        $query = $this->wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE email = %s",
            $email
        );

        $result = $this->wpdb->get_row($query);
        if (!$result) return null;

        return $this->hydrate($result);
    }

    private function hydrate($data): Adopter {
        return new Adopter(
            $data->name,
            $data->email,
            $data->document
        );
    }
}
