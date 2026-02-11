<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Migration;

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
        $wpdb = $this->wpdb;
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'pet_breeds');
        $speciesTable = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'pet_species');
        if (!is_string($table) || $table === '' || !is_string($speciesTable) || $speciesTable === '') {
            return;
        }
        
        $dogId = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT id FROM %i WHERE name = %s',
                $speciesTable,
                'Cachorro'
            )
        );
        $catId = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT id FROM %i WHERE name = %s',
                $speciesTable,
                'Gato'
            )
        );

        if (!$dogId || !$catId) {
            return;
        }

        $dogBreeds = [
            'Vira-lata', 'Pastor Alemão', 'Labrador', 'Golden Retriever', 
            'Rottweiler', 'Poodle', 'Bulldog', 'Pitbull'
        ];

        $catBreeds = [
            'Sem Raça Definida', 'Siamês', 'Persa', 'Maine Coon', 
            'Angorá', 'Ragdoll', 'Bengal', 'British Shorthair'
        ];

        foreach ($dogBreeds as $breed) {
            $this->wpdb->replace($table, [
                'species_id' => (int) $dogId,
                'name' => $breed,
                'description' => 'Raça ' . $breed,
                'status' => 'active',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ]);
        }

        foreach ($catBreeds as $breed) {
            $this->wpdb->replace($table, [
                'species_id' => (int) $catId,
                'name' => $breed,
                'description' => 'Raça ' . $breed,
                'status' => 'active',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ]);
        }
    }

    public function down(): void {
        $wpdb = $this->wpdb;
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'pet_breeds');
        if (!is_string($table) || $table === '') {
            return;
        }
        $wpdb->query($wpdb->prepare('DELETE FROM %i', $table));
    }
}