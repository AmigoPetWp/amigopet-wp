<?php
namespace AmigoPetWp\Domain\Database\Migrations;

class SeedVolunteers {
    private $wpdb;
    private $table_name;
    private $org_table;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'amigopet_volunteers';
        $this->org_table = $wpdb->prefix . 'amigopet_organizations';
    }

    public function up(): void {
        // Pega o ID da primeira organização
        $org_id = $this->wpdb->get_var("SELECT id FROM {$this->org_table} LIMIT 1");

        if (!$org_id) {
            return; // Não pode continuar sem uma organização
        }

        // Pega o usuário admin do WordPress
        $admin = get_user_by('login', 'admin');
        if (!$admin) {
            $admins = get_users(['role' => 'administrator', 'number' => 1]);
            $admin = !empty($admins) ? $admins[0] : null;
        }

        if (!$admin) {
            return; // Não pode continuar sem um admin
        }

        // Verifica se já existe algum voluntário
        $exists = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");

        if (!$exists) {
            // Cria um voluntário baseado no admin
            $this->wpdb->insert(
                $this->table_name,
                [
                    'organization_id' => $org_id,
                    'name' => $admin->display_name,
                    'email' => $admin->user_email,
                    'phone' => get_user_meta($admin->ID, 'phone', true) ?: '(00) 00000-0000',
                    'role' => 'adoption'
                ],
                ['%d', '%s', '%s', '%s', '%s']
            );
        }
    }
}
