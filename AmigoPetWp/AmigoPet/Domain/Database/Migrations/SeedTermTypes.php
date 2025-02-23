<?php
namespace AmigoPetWp\Domain\Database\Migrations;

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
        $table = $this->prefix . 'term_types';
        
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
            // Verifica se já existe
            $exists = $this->wpdb->get_var(
                $this->wpdb->prepare(
                    "SELECT COUNT(*) FROM {$table} WHERE name = %s",
                    $type['name']
                )
            );

            if (!$exists) {
                $this->wpdb->insert($table, $type);
            }
        }
    }

    public function down(): void {
        $table = $this->prefix . 'term_types';
        $this->wpdb->query("TRUNCATE TABLE {$table}");
    }
}
