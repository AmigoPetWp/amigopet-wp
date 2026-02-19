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
        $volunteers_table = $this->wpdb->prefix . 'apwp_volunteers';
        $organizations_table = $this->wpdb->prefix . 'apwp_organizations';
        // phpcs:disable WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name from prefix only.
        $orgId = $this->wpdb->get_var($this->wpdb->prepare(
            'SELECT id FROM `' . $organizations_table . '` LIMIT %d',
            1
        ));
        // phpcs:enable WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter

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

            $this->wpdb->insert($volunteers_table, $defaultVolunteer);
        }
    }

    public function down(): void {
        $volunteers_table = $this->wpdb->prefix . 'apwp_volunteers';
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name from prefix only.
        $this->wpdb->query('DELETE FROM ' . $volunteers_table);
    }
}