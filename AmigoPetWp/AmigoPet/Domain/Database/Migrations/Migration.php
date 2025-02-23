<?php
namespace AmigoPetWp\Domain\Database\Migrations;

/**
 * Interface base para migrations
 */
abstract class Migration {
    protected $wpdb;
    protected $prefix;
    protected $charset_collate;

    /**
     * Insere um registro apenas se ele não existir
     */
    protected function insertIfNotExists(string $table, array $data, array $where): bool {
        // Verifica se o registro já existe
        $exists = false;
        $whereClause = [];
        $whereValues = [];

        foreach ($where as $field => $value) {
            $whereClause[] = "`$field` = %s";
            $whereValues[] = $value;
        }

        $query = $this->wpdb->prepare(
            "SELECT COUNT(*) FROM `$table` WHERE " . implode(' AND ', $whereClause),
            $whereValues
        );

        $count = (int) $this->wpdb->get_var($query);

        if ($count === 0) {
            return $this->wpdb->insert($table, $data);
        }

        return false;
    }

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->prefix = $wpdb->prefix;
        $this->charset_collate = $wpdb->get_charset_collate();
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
     * Executa a migration
     * @return bool Retorna true se a execução foi bem sucedida, false caso contrário
     */
    public function run(): bool {
        try {
            $this->up();
            return true;
        } catch (\Exception $e) {
            error_log('Erro ao executar migration ' . $this->getVersion() . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Retorna a versão da migration
     */
    abstract public function getVersion(): string;

    /**
     * Retorna a descrição da migration
     */
    abstract public function getDescription(): string;

    /**
     * Faz backup das tabelas antes da migração
     */
    public function backupTables(array $tables): void {
        foreach ($tables as $table) {
            $backupTable = $table . '_backup_' . date('YmdHis');
            $this->wpdb->query("CREATE TABLE IF NOT EXISTS {$backupTable} LIKE {$table}");
            $this->wpdb->query("INSERT INTO {$backupTable} SELECT * FROM {$table}");
        }
    }

    /**
     * Restaura backup das tabelas
     */
    protected function restoreBackup(array $tables): void {
        foreach ($tables as $table) {
            $backups = $this->wpdb->get_results(
                "SHOW TABLES LIKE '{$table}_backup_%'",
                ARRAY_N
            );

            if (!empty($backups)) {
                // Pega o backup mais recente
                $latestBackup = end($backups)[0];
                $this->wpdb->query("DROP TABLE IF EXISTS {$table}");
                $this->wpdb->query("CREATE TABLE {$table} LIKE {$latestBackup}");
                $this->wpdb->query("INSERT INTO {$table} SELECT * FROM {$latestBackup}");
                $this->wpdb->query("DROP TABLE IF EXISTS {$latestBackup}");
            }
        }
    }
}
