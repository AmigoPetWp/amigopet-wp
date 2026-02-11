<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

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
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'pet_species');
        if (!is_string($table) || $table === '') {
            return;
        }
        
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
            $this->wpdb->replace($table, $species);
        }
    }

    public function down(): void {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'pet_species');
        if (!is_string($table) || $table === '') {
            return;
        }
        $this->wpdb->query('DELETE FROM `' . esc_sql($table) . '`');
    }
}