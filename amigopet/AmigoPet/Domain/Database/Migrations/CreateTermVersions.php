<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Migration;

class CreateTermVersions extends Migration
{
    public function getVersion(): string
    {
        return '2.1.0';
    }

    public function getDescription(): string
    {
        return 'Cria a tabela de histórico de versões de termos';
    }

    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}term_versions (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            term_id BIGINT(20) UNSIGNED NOT NULL,
            content LONGTEXT NOT NULL,
            version VARCHAR(20) NOT NULL,
            status ENUM('active', 'inactive', 'review', 'draft') DEFAULT 'draft',
            effective_date DATETIME NOT NULL,
            change_log TEXT,
            created_by BIGINT(20) UNSIGNED,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY term_id (term_id),
            KEY status (status),
            KEY effective_date (effective_date),
            FOREIGN KEY (term_id) REFERENCES {$this->prefix}terms(id) ON DELETE CASCADE
        ) {$this->charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function down(): void
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'term_versions');
        if (!is_string($table) || $table === '') {
            return;
        }
        $this->wpdb->query("DROP TABLE IF EXISTS `{$table}`");
    }
}
