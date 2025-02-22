<?php
namespace AmigoPetWp\Domain\Database\Migrations;

class SeedBreeds {
    private $wpdb;
    private $table_name;
    private $species_table;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'amigopet_pet_breeds';
        $this->species_table = $wpdb->prefix . 'amigopet_pet_species';
    }

    public function up(): void {
        // Pega os IDs das espécies
        $dog_id = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT id FROM {$this->species_table} WHERE slug = %s",
                'cachorro'
            )
        );

        $cat_id = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT id FROM {$this->species_table} WHERE slug = %s",
                'gato'
            )
        );

        if (!$dog_id || !$cat_id) {
            return; // Não pode continuar sem as espécies
        }

        $default_breeds = [
            // Raças de cachorro
            ['name' => 'SRD (Vira-lata)', 'slug' => 'srd-dog', 'species_id' => $dog_id],
            ['name' => 'Labrador', 'slug' => 'labrador', 'species_id' => $dog_id],
            ['name' => 'Pastor Alemão', 'slug' => 'pastor-alemao', 'species_id' => $dog_id],
            ['name' => 'Golden Retriever', 'slug' => 'golden-retriever', 'species_id' => $dog_id],
            ['name' => 'Poodle', 'slug' => 'poodle', 'species_id' => $dog_id],
            ['name' => 'Rottweiler', 'slug' => 'rottweiler', 'species_id' => $dog_id],
            ['name' => 'Bulldog', 'slug' => 'bulldog', 'species_id' => $dog_id],
            ['name' => 'Pinscher', 'slug' => 'pinscher', 'species_id' => $dog_id],
            ['name' => 'Pitbull', 'slug' => 'pitbull', 'species_id' => $dog_id],
            ['name' => 'Husky Siberiano', 'slug' => 'husky', 'species_id' => $dog_id],

            // Raças de gato
            ['name' => 'SRD (Vira-lata)', 'slug' => 'srd-cat', 'species_id' => $cat_id],
            ['name' => 'Siamês', 'slug' => 'siames', 'species_id' => $cat_id],
            ['name' => 'Persa', 'slug' => 'persa', 'species_id' => $cat_id],
            ['name' => 'Maine Coon', 'slug' => 'maine-coon', 'species_id' => $cat_id],
            ['name' => 'Angorá', 'slug' => 'angora', 'species_id' => $cat_id],
            ['name' => 'Bengal', 'slug' => 'bengal', 'species_id' => $cat_id],
            ['name' => 'Ragdoll', 'slug' => 'ragdoll', 'species_id' => $cat_id],
            ['name' => 'Sphynx', 'slug' => 'sphynx', 'species_id' => $cat_id],
            ['name' => 'British Shorthair', 'slug' => 'british-shorthair', 'species_id' => $cat_id],
            ['name' => 'Munchkin', 'slug' => 'munchkin', 'species_id' => $cat_id]
        ];

        foreach ($default_breeds as $breed) {
            $exists = $this->wpdb->get_var(
                $this->wpdb->prepare(
                    "SELECT COUNT(*) FROM {$this->table_name} WHERE slug = %s",
                    $breed['slug']
                )
            );

            if (!$exists) {
                $this->wpdb->insert(
                    $this->table_name,
                    [
                        'name' => $breed['name'],
                        'slug' => $breed['slug'],
                        'species_id' => $breed['species_id'],
                        'status' => 'active'
                    ],
                    ['%s', '%s', '%d', '%s']
                );
            }
        }
    }
}
