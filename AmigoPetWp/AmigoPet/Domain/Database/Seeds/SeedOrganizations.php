<?php
namespace AmigoPetWp\Domain\Database\Migrations;

class SeedOrganizations {
    private $wpdb;
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'amigopet_organizations';
    }

    public function up(): void {
        // Pega o nome da organização das configurações do WordPress
        $org_name = get_option('apwp_organization_name', get_bloginfo('name'));
        $org_email = get_option('apwp_organization_email', get_bloginfo('admin_email'));
        $org_phone = get_option('apwp_organization_phone', '');
        $org_website = get_bloginfo('url');
        $org_address = get_option('apwp_organization_address', '');

        // Se não houver nenhuma organização, cria uma padrão
        $exists = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");

        if (!$exists) {
            $this->wpdb->insert(
                $this->table_name,
                [
                    'name' => $org_name ?: 'Organização AmigoPet',
                    'email' => $org_email ?: 'contato@' . str_replace('www.', '', $_SERVER['HTTP_HOST']),
                    'phone' => $org_phone ?: '(00) 00000-0000',
                    'website' => $org_website,
                    'address' => $org_address ?: 'Endereço não cadastrado'
                ],
                ['%s', '%s', '%s', '%s', '%s']
            );
        }
    }
}
