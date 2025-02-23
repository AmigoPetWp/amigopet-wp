<?php
namespace AmigoPetWp\Domain\Database;

class MigrationService {
    private static $instance = null;
    private $wpdb;
    private $migrations = [];
    
    private function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        
        // Carrega todas as migrations
        $this->loadMigrations();
    }
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Carrega todas as migrations disponíveis
     */
    private function loadMigrations(): void {
        // Lista de migrations na ordem correta
        $migrationClasses = [
            'CreateTables' => '1.0.0',
            'CreateSettings' => '1.0.1',
            'SeedTermTypes' => '1.0.2',
            'SeedSpecies' => '1.0.3',
            'SeedBreeds' => '1.0.4',
            'SeedTerms' => '1.0.5',
            'SeedOrganizations' => '1.0.6',
            'SeedVolunteers' => '1.0.7',
            'CreateTemplateTerms' => '2.0.0',
            'SeedTemplateTerms' => '2.0.1'
        ];

        foreach ($migrationClasses as $className => $version) {
            $class = 'AmigoPetWp\\Domain\\Database\\Migrations\\' . $className;
            if (class_exists($class)) {
                $migration = new $class();
                $this->migrations[$version] = $migration;
            }
        }
    }
    
    /**
     * Executa as migrations pendentes
     */
    public function migrate(): array {
        try {
            // Cria tabela de versões se não existir
            $this->createVersionTable();
            
            $results = [];
            $currentVersion = $this->getCurrentVersion();
            
            foreach ($this->migrations as $version => $migration) {
                if (version_compare($version, $currentVersion, '>')) {
                    try {
                        // Tenta fazer backup, mas continua mesmo se falhar
                        try {
                            $affectedTables = $this->getAffectedTables($migration);
                            if (!empty($affectedTables)) {
                                $migration->backupTables($affectedTables);
                            }
                        } catch (\Exception $e) {
                            error_log('AmigoPet WP: Erro ao tentar fazer backup antes da migration ' . $version . ': ' . $e->getMessage());
                            // Continua com a migration mesmo se o backup falhar
                        }
                        
                        // Executa a migration
                        $migration->up();
                        
                        // Atualiza versão
                        $this->updateVersion($version);
                        
                        $results[] = [
                            'version' => $version,
                            'description' => $migration->getDescription(),
                            'status' => 'success'
                        ];
                    } catch (\Exception $e) {
                        $errorMessage = 'Erro na migration ' . $version . ': ' . $e->getMessage();
                        error_log('AmigoPet WP: ' . $errorMessage);
                        
                        $results[] = [
                            'version' => $version,
                            'description' => $migration->getDescription(),
                            'status' => 'error',
                            'message' => $errorMessage
                        ];
                        
                        // Para execução em caso de erro
                        break;
                    }
                }
            }
            
            return $results;
        } catch (\Exception $e) {
            $errorMessage = 'Erro ao executar migrations: ' . $e->getMessage();
            error_log('AmigoPet WP: ' . $errorMessage);
            throw new \Exception($errorMessage);
        }
    }
    
    /**
     * Reverte a última migration
     */
    public function rollback(): array {
        $results = [];
        $currentVersion = $this->getCurrentVersion();
        
        // Reverte na ordem inversa
        $migrations = array_reverse($this->migrations, true);
        
        foreach ($migrations as $version => $migration) {
            if (version_compare($version, $currentVersion, '<=')) {
                try {
                    // Faz backup antes
                    $migration->backupTables($this->getAffectedTables($migration));
                    
                    // Reverte a migration
                    $migration->down();
                    
                    // Atualiza versão
                    $previousVersion = $this->getPreviousVersion($version);
                    $this->updateVersion($previousVersion);
                    
                    $results[] = [
                        'version' => $version,
                        'description' => $migration->getDescription(),
                        'status' => 'success'
                    ];
                    
                    // Reverte apenas a última
                    break;
                } catch (\Exception $e) {
                    $results[] = [
                        'version' => $version,
                        'description' => $migration->getDescription(),
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Retorna a versão atual do banco
     */
    private function createVersionTable(): void {
        $table = $this->wpdb->prefix . 'apwp_migrations';
        
        if ($this->wpdb->get_var("SHOW TABLES LIKE '{$table}'") != $table) {
            $charset_collate = $this->wpdb->get_charset_collate();
            
            $sql = "CREATE TABLE {$table} (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                version VARCHAR(50) NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) {$charset_collate};";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    private function getCurrentVersion(): string {
        $table = $this->wpdb->prefix . 'apwp_migrations';
        
        // Verifica se a tabela existe
        if ($this->wpdb->get_var("SHOW TABLES LIKE '{$table}'") != $table) {
            return '0.0.0';
        }
        
        $version = $this->wpdb->get_var(
            "SELECT version FROM {$table} ORDER BY id DESC LIMIT 1"
        );
        
        return $version ?: '0.0.0';
    }
    
    /**
     * Atualiza a versão do banco
     */
    private function updateVersion(string $version): void {
        $table = $this->wpdb->prefix . 'apwp_migrations';
        
        $this->wpdb->insert(
            $table,
            [
                'version' => $version,
                'description' => 'Migration executada com sucesso'
            ],
            ['%s', '%s']
        );
    }
    
    /**
     * Retorna a versão anterior
     */
    private function getPreviousVersion(string $currentVersion): string {
        $versions = array_keys($this->migrations);
        $index = array_search($currentVersion, $versions);
        
        if ($index > 0) {
            return $versions[$index - 1];
        }
        
        return '0.0.0';
    }
    
    /**
     * Retorna as tabelas afetadas pela migration
     */
    private function getAffectedTables(Migration $migration): array {
        $reflection = new \ReflectionClass($migration);
        $content = file_get_contents($reflection->getFileName());
        
        preg_match_all('/CREATE TABLE.*?`(' . $this->wpdb->prefix . '.*?)`/s', $content, $matches);
        
        return $matches[1] ?? [];
    }
    
    /**
     * Remove todas as tabelas do plugin
     */
    public function dropAllTables(): void {
        try {
            // Reverte todas as migrations na ordem inversa
            $migrations = array_reverse($this->migrations, true);
            
            foreach ($migrations as $migration) {
                try {
                    $migration->down();
                } catch (\Exception $e) {
                    error_log('AmigoPet WP: Erro ao remover tabelas da migration: ' . $e->getMessage());
                }
            }
            
            // Remove a tabela de migrations
            $table = $this->wpdb->prefix . 'apwp_migrations';
            $this->wpdb->query("DROP TABLE IF EXISTS {$table}");
            
        } catch (\Exception $e) {
            error_log('AmigoPet WP: Erro ao remover tabelas do plugin: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lista todas as migrations
     */
    public function listMigrations(): array {
        $currentVersion = $this->getCurrentVersion();
        $migrations = [];
        
        foreach ($this->migrations as $version => $migration) {
            $migrations[] = [
                'version' => $version,
                'description' => $migration->getDescription(),
                'status' => version_compare($version, $currentVersion, '<=') ? 'executed' : 'pending'
            ];
        }
        
        return $migrations;
    }
}
