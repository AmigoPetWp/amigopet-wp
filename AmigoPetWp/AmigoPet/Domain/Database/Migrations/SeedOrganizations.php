<?php
namespace AmigoPetWp\Domain\Database\Migrations;

use AmigoPetWp\Domain\Database\Migration;

class SeedOrganizations extends Migration {
    public function __construct() {
        parent::__construct();
        $this->prefix = $this->wpdb->prefix . 'apwp_';
    }

    public function getDescription(): string {
        return 'Insere a organização padrão';
    }

    public function getVersion(): string {
        return '1.0.5';
    }

    public function up(): void {
        $table = $this->prefix . 'organizations';
        
        // Organização padrão
        $defaultOrg = [
            'name' => 'Minha Organização',
            'email' => 'contato@minhaorganizacao.com',
            'phone' => '(00) 0000-0000',
            'document' => '00000000000000',
            'address' => 'Rua Principal, 123',
            'website' => 'https://minhaorganizacao.com',
            'status' => 'active',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        // Verifica se já existe
        $exists = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE email = %s OR document = %s",
                $defaultOrg['email'],
                $defaultOrg['document']
            )
        );

        if (!$exists) {
            $this->wpdb->insert($table, $defaultOrg);
        }
    }

    public function down(): void {
        $table = $this->prefix . 'organizations';
        $this->wpdb->query("TRUNCATE TABLE {$table}");
    }
}
