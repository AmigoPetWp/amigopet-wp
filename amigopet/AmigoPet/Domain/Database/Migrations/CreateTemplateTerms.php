<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Migration;

class CreateTemplateTerms extends Migration
{
    public function __construct()
    {
        parent::__construct();
        $this->prefix = $this->wpdb->prefix . 'apwp_';
    }

    public function getVersion(): string
    {
        return '2.0.0';
    }

    public function getDescription(): string
    {
        return 'Cria a tabela de templates de termos';
    }

    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}template_terms (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            type varchar(50) NOT NULL,
            title varchar(255) NOT NULL,
            content longtext NOT NULL,
            description text,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY type (type),
            KEY is_active (is_active)
        ) {$this->charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function down(): void
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'template_terms');
        if (!is_string($table) || $table === '') {
            return;
        }
        $this->wpdb->query("DROP TABLE IF EXISTS `{$table}`");
    }
}