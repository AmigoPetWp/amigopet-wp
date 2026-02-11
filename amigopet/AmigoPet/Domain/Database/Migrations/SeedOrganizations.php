<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

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
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'organizations');
        if (!is_string($table) || $table === '') {
            return;
        }
        
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

        $this->wpdb->replace($table, $defaultOrg);
    }

    public function down(): void {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'organizations');
        if (!is_string($table) || $table === '') {
            return;
        }
        $this->wpdb->query('DELETE FROM `' . esc_sql($table) . '`');
    }
}