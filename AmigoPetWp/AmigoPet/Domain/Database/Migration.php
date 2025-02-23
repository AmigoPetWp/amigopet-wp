<?php
namespace AmigoPetWp\Domain\Database;

abstract class Migration {
    protected $wpdb;
    protected $charset_collate;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
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
     * Retorna a versão da migration
     */
    abstract public function getVersion(): string;
    
    /**
     * Retorna a descrição da migration
     */
    abstract public function getDescription(): string;
    
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
     * Executa queries SQL
     */
    protected function executeQueries(array $queries): void {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        foreach ($queries as $query) {
            dbDelta($query);
        }
    }
    
    /**
     * Faz backup das tabelas
     */
    public function backupTables(array $tables): void {
        try {
            $backupDir = WP_CONTENT_DIR . '/backups/database/';
            
            // Tenta criar o diretório de backup
            if (!file_exists($backupDir) && !wp_mkdir_p($backupDir)) {
                error_log('AmigoPet WP: Não foi possível criar o diretório de backup. Continuando sem backup.');
                return;
            }
            
            // Verifica permissões de escrita
            if (!is_writable($backupDir)) {
                error_log('AmigoPet WP: Diretório de backup sem permissão de escrita. Continuando sem backup.');
                return;
            }
            
            $filename = sprintf(
                'backup_%s_%s.sql',
                date('Y-m-d_H-i-s'),
                $this->getVersion()
            );
            
            $filepath = $backupDir . $filename;
            
            // Cabeçalho do arquivo
            $content = "-- AmigoPet WP Database Backup\n";
            $content .= "-- Version: " . $this->getVersion() . "\n";
            $content .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
            
            foreach ($tables as $table) {
                // Estrutura da tabela
                $createTable = $this->wpdb->get_row("SHOW CREATE TABLE {$table}", ARRAY_N);
                if ($createTable) {
                    $content .= "\n\n" . $createTable[1] . ";\n\n";
                    
                    // Dados da tabela
                    $rows = $this->wpdb->get_results("SELECT * FROM {$table}", ARRAY_A);
                    foreach ($rows as $row) {
                        $values = array_map(function($value) {
                            return $this->wpdb->prepare('%s', $value);
                        }, $row);
                        
                        $content .= "INSERT INTO {$table} VALUES (" . implode(',', $values) . ");\n";
                    }
                }
            }
            
            if (@file_put_contents($filepath, $content) === false) {
                error_log('AmigoPet WP: Não foi possível salvar o arquivo de backup. Continuando sem backup.');
                return;
            }
        } catch (\Exception $e) {
            error_log('AmigoPet WP: Erro ao tentar fazer backup: ' . $e->getMessage() . '. Continuando sem backup.');
            return;
        }
    }
}
