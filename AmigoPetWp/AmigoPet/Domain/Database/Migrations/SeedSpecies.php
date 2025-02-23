<?php
namespace AmigoPetWp\Domain\Database\Migrations;

use AmigoPetWp\Domain\Database\Migration;

class SeedSpecies extends Migration {
    public function __construct() {
        parent::__construct();
        $this->prefix = $this->wpdb->prefix . 'apwp_';
    }

    public function getDescription(): string {
        return 'Insere as espécies padrão';
    }

    public function getVersion(): string {
        return '1.0.2';
    }

    public function up(): void {
        $table = $this->prefix . 'pet_species';
        
        // Espécies padrão
        $defaultSpecies = [
            [
                'name' => 'Cachorro',
                'description' => 'Cães e cachorros',
                'status' => 'active',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ],
            [
                'name' => 'Gato',
                'description' => 'Gatos e felinos',
                'status' => 'active',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ]
        ];

        foreach ($defaultSpecies as $species) {
            // Verifica se já existe
            $exists = $this->wpdb->get_var(
                $this->wpdb->prepare(
                    "SELECT COUNT(*) FROM {$table} WHERE name = %s",
                    $species['name']
                )
            );

            if (!$exists) {
                $this->wpdb->insert($table, $species);
            }
        }
    }

    public function down(): void {
        $table = $this->prefix . 'pet_species';
        $this->wpdb->query("TRUNCATE TABLE {$table}");
    }
}
