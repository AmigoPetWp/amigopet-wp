<?php
namespace AmigoPetWp\Domain\Database\Migrations;

class SeedSpecies {
    private $wpdb;
    private $charset_collate;
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->charset_collate = $wpdb->get_charset_collate();
        $this->table_name = $wpdb->prefix . 'amigopet_pet_species';
    }

    public function up(): void {
        $default_species = [
            ['name' => 'Cachorro', 'slug' => 'cachorro', 'description' => 'Cães de todas as raças e portes'],
            ['name' => 'Gato', 'slug' => 'gato', 'description' => 'Gatos de todas as raças'],
            ['name' => 'Coelho', 'slug' => 'coelho', 'description' => 'Coelhos domésticos'],
            ['name' => 'Ave', 'slug' => 'ave', 'description' => 'Aves domésticas'],
            ['name' => 'Roedor', 'slug' => 'roedor', 'description' => 'Hamsters, porquinhos da índia, etc'],
            ['name' => 'Outro', 'slug' => 'outro', 'description' => 'Outras espécies']
        ];

        foreach ($default_species as $species) {
            $exists = $this->wpdb->get_var(
                $this->wpdb->prepare(
                    "SELECT COUNT(*) FROM {$this->table_name} WHERE slug = %s",
                    $species['slug']
                )
            );

            if (!$exists) {
                $this->wpdb->insert(
                    $this->table_name,
                    [
                        'name' => $species['name'],
                        'slug' => $species['slug'],
                        'description' => $species['description'],
                        'status' => 'active'
                    ],
                    ['%s', '%s', '%s', '%s']
                );
            }
        }
    }
}
