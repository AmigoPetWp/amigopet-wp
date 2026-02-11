<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database;

if (!defined('ABSPATH')) {
    exit;
}

abstract class Migration
{
    protected $wpdb;
    protected $charset_collate;
    protected $prefix;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->charset_collate = $wpdb->get_charset_collate();
        $this->prefix = $wpdb->prefix . 'apwp_';
    }

    /**
     * Executa a migration
     */
    abstract public function up(): void;

    /**
     * Reverte a migration
     */
    abstract public function down(): void;

    /**
     * Retorna a versão da migration
     */
    abstract public function getVersion(): string;

    /**
     * Retorna a descrição da migration
     */
    abstract public function getDescription(): string;

    /**
     * Retorna as tabelas afetadas pela migration
     * @return array Lista de nomes de tabelas (sem prefixo)
     */
    public function getAffectedTables(): array
    {
        return [];
    }

    /**
     * Executa a migration
     * @return bool Retorna true se a execução foi bem sucedida, false caso contrário
     */
    public function run(): bool
    {
        try {
            $this->up();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Executa queries SQL
     */
    protected function executeQueries(array $queries): void
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        foreach ($queries as $query) {
            dbDelta($query);
        }
    }

    /**
     * Faz backup das tabelas
     */
    public function backupTables(array $tables): void
    {
        return;
    }

    /**
     * Verifica se uma coluna existe em uma tabela
     */
    protected function columnExists(string $table, string $column): bool
    {
        $wpdb = $this->wpdb;
        $safeTable = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $safeColumn = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
        if (!is_string($safeTable) || $safeTable === '' || !is_string($safeColumn) || $safeColumn === '') {
            return false;
        }

        $result = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = %s AND COLUMN_NAME = %s',
                $safeTable,
                $safeColumn
            )
        );

        return is_string($result) && $result !== '';
    }
}