<?php
namespace AmigoPetWp\Domain\Database\Migrations;

use AmigoPetWp\Domain\Database\Migration;

class SeedVolunteers extends Migration {
    public function __construct() {
        parent::__construct();
        $this->prefix = $this->wpdb->prefix . 'apwp_';
    }

    public function getDescription(): string {
        return 'Insere o voluntário administrador padrão';
    }

    public function getVersion(): string {
        return '1.0.6';
    }

    public function up(): void {
        $table = $this->prefix . 'volunteers';
        $orgTable = $this->prefix . 'organizations';
        
        // Primeiro obtém o ID da organização padrão
        $orgId = $this->wpdb->get_var("SELECT id FROM {$orgTable} LIMIT 1");

        if (!$orgId) {
            return; // Se não encontrar a organização, não insere os voluntários
        }

        // Voluntário administrador padrão
        $adminUser = wp_get_current_user();
        if ($adminUser && $adminUser->ID) {
            $defaultVolunteer = [
                'organization_id' => $orgId,
                'user_id' => $adminUser->ID,
                'name' => $adminUser->display_name,
                'email' => $adminUser->user_email,
                'phone' => '',
                'address' => '',
                'city' => '',
                'state' => '',
                'zip_code' => '',
                'is_active' => 1,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ];

            $this->wpdb->insert($table, $defaultVolunteer);
        }
    }

    public function down(): void {
        $table = $this->prefix . 'volunteers';
        $this->wpdb->query("TRUNCATE TABLE {$table}");
    }
}
