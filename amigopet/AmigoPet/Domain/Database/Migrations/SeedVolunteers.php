<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

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
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'volunteers');
        $orgTable = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'organizations');
        if (!is_string($table) || $table === '' || !is_string($orgTable) || $orgTable === '') {
            return;
        }
        
        // Primeiro obtém o ID da organização padrão
        $orgId = $this->wpdb->get_var('SELECT id FROM `' . esc_sql($orgTable) . '` LIMIT 1');

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
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'volunteers');
        if (!is_string($table) || $table === '') {
            return;
        }
        $this->wpdb->query('DELETE FROM `' . esc_sql($table) . '`');
    }
}