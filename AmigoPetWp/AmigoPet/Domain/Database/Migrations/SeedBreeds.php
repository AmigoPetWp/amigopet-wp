<?php
namespace AmigoPetWp\Domain\Database\Migrations;

class SeedBreeds extends Migration {
    public function __construct() {
        parent::__construct();
        $this->prefix = $this->wpdb->prefix . 'apwp_';
    }

    public function getDescription(): string {
        return 'Insere as raças padrão para cães e gatos';
    }

    public function getVersion(): string {
        return '1.0.3';
    }

    public function up(): void {
        $table = $this->prefix . 'pet_breeds';
        $speciesTable = $this->prefix . 'pet_species';
        
        // Primeiro obtém os IDs das espécies
        $dogId = $this->wpdb->get_var("SELECT id FROM {$speciesTable} WHERE name = 'Cachorro'");
        $catId = $this->wpdb->get_var("SELECT id FROM {$speciesTable} WHERE name = 'Gato'");

        if (!$dogId || !$catId) {
            return; // Se não encontrar as espécies, não insere as raças
        }

        // Raças padrão para cães
        $dogBreeds = [
            'Vira-lata', 'Pastor Alemão', 'Labrador', 'Golden Retriever', 
            'Rottweiler', 'Poodle', 'Bulldog', 'Pitbull'
        ];

        // Raças padrão para gatos
        $catBreeds = [
            'Sem Raça Definida', 'Siamês', 'Persa', 'Maine Coon', 
            'Angorá', 'Ragdoll', 'Bengal', 'British Shorthair'
        ];

        // Insere raças de cães
        foreach ($dogBreeds as $breed) {
            $this->insertIfNotExists($table, [
                'species_id' => $dogId,
                'name' => $breed,
                'description' => "Raça {$breed}",
                'status' => 'active',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ], ['name' => $breed, 'species_id' => $dogId]);
        }

        // Insere raças de gatos
        foreach ($catBreeds as $breed) {
            $this->insertIfNotExists($table, [
                'species_id' => $catId,
                'name' => $breed,
                'description' => "Raça {$breed}",
                'status' => 'active',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ], ['name' => $breed, 'species_id' => $catId]);
        }
    }

    public function down(): void {
        $table = $this->prefix . 'pet_breeds';
        $this->wpdb->query("TRUNCATE TABLE {$table}");
    }
}
