<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Migration;

class SeedTermTypes extends Migration {
    public function __construct() {
        parent::__construct();
        $this->prefix = $this->wpdb->prefix . 'apwp_';
    }

    public function getDescription(): string {
        return 'Insere os tipos de termos padrão';
    }

    public function getVersion(): string {
        return '1.0.1';
    }

    public function up(): void {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'term_types');
        if (!is_string($table) || $table === '') {
            return;
        }
        
        // Tipos de termos padrão
        $defaultTypes = [
            [
                'name' => 'Termo de Adoção',
                'description' => 'Termos e condições para adoção de animais',
                'status' => 'active',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ],
            [
                'name' => 'Termo de Voluntariado',
                'description' => 'Termos e condições para voluntários',
                'status' => 'active',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ]
        ];

        foreach ($defaultTypes as $type) {
            $this->wpdb->replace($table, $type);
        }
    }

    public function down(): void {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'term_types');
        if (!is_string($table) || $table === '') {
            return;
        }
        $this->wpdb->query('DELETE FROM `' . esc_sql($table) . '`');
    }
}