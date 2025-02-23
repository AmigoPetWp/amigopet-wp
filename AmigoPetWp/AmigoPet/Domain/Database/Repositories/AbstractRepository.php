<?php
namespace AmigoPetWp\Domain\Database\Repositories;

/**
 * Classe base abstrata para repositórios do AmigoPet
 * 
 * @package AmigoPetWp\Domain\Database\Repositories
 */
abstract class AbstractRepository implements BaseRepository {
    /** @var \wpdb WordPress database instance */
    protected $wpdb;

    /** @var string Nome da tabela com prefixo */
    protected $table;

    /**
     * Construtor do repositório
     *
     * @param \wpdb $wpdb Instância do banco de dados WordPress
     */
    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . $this->getTableName();
    }

    /**
     * Retorna o nome da tabela sem o prefixo
     *
     * @return string Nome da tabela
     */
    abstract protected function getTableName(): string;

    /**
     * Cria uma entidade a partir de um array de dados
     *
     * @param array $data Dados da entidade
     * @return mixed Entidade criada
     */
    abstract protected function createEntity(array $data);

    /**
     * Converte uma entidade em array para salvar no banco
     *
     * @param mixed $entity Entidade a ser convertida
     * @return array Dados para salvar no banco
     */
    abstract protected function toDatabase($entity): array;

    /**
     * {@inheritDoc}
     */
    public function findById(int $id) {
        try {
            $row = $this->wpdb->get_row(
                $this->wpdb->prepare(
                    "SELECT * FROM {$this->table} WHERE id = %d",
                    $id
                ),
                ARRAY_A
            );

            if (!$row) {
                return null;
            }

            return $this->createEntity($row);
        } catch (\Exception $e) {
            error_log("Erro ao buscar entidade por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(array $args = []): array {
        try {
            $where = ['1=1'];
            $params = [];

            // Mapeamento de campos diretos
            $directFields = [
                'status', 'payment_status', 'payment_method', 'transaction_id',
                'payer_name', 'payer_email', 'payer_document', 'adoption_id'
            ];

            foreach ($directFields as $field) {
                if (isset($args[$field])) {
                    $where[] = "{$field} = %s";
                    $params[] = $args[$field];
                }
            }

            // Campos de data
            $dateFields = [
                'start_date' => ['field' => 'created_at', 'operator' => '>='],
                'end_date' => ['field' => 'created_at', 'operator' => '<='],
                'payment_start_date' => ['field' => 'payment_date', 'operator' => '>='],
                'payment_end_date' => ['field' => 'payment_date', 'operator' => '<='],
                'refund_start_date' => ['field' => 'refund_date', 'operator' => '>='],
                'refund_end_date' => ['field' => 'refund_date', 'operator' => '<=']
            ];

            foreach ($dateFields as $arg => $config) {
                if (isset($args[$arg])) {
                    $where[] = "{$config['field']} {$config['operator']} %s";
                    $params[] = $args[$arg];
                }
            }

            // Campos de valor
            if (isset($args['min_amount'])) {
                $where[] = "amount >= %f";
                $params[] = $args['min_amount'];
            }
            if (isset($args['max_amount'])) {
                $where[] = "amount <= %f";
                $params[] = $args['max_amount'];
            }

            // Busca em múltiplos campos
            if (isset($args['search']) && isset($args['search_columns'])) {
                $searchConditions = [];
                foreach ($args['search_columns'] as $column) {
                    $searchConditions[] = "{$column} LIKE %s";
                    $params[] = '%' . $args['search'] . '%';
                }
                $where[] = '(' . implode(' OR ', $searchConditions) . ')';
            }

            // Ordenação e paginação
            $orderBy = $args['orderby'] ?? 'created_at';
            $order = $args['order'] ?? 'DESC';
            $limit = isset($args['limit']) ? ' LIMIT ' . (int)$args['limit'] : '';
            $offset = isset($args['offset']) ? ' OFFSET ' . (int)$args['offset'] : '';

            $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where) . 
                   " ORDER BY {$orderBy} {$order}{$limit}{$offset}";

            if (!empty($params)) {
                $sql = $this->wpdb->prepare($sql, $params);
            }

            $rows = $this->wpdb->get_results($sql, ARRAY_A);
            
            return array_map([$this, 'createEntity'], $rows);
        } catch (\Exception $e) {
            error_log("Erro ao buscar entidades: " . $e->getMessage());
            return [];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function save($entity): int {
        try {
            $data = $this->toDatabase($entity);
            $format = array_fill(0, count($data), '%s'); // Assume string format for all fields

            if (method_exists($entity, 'getId') && $entity->getId()) {
                $result = $this->wpdb->update(
                    $this->table,
                    $data,
                    ['id' => $entity->getId()],
                    $format,
                    ['%d']
                );

                if ($result === false) {
                    throw new \Exception("Erro ao atualizar entidade: " . $this->wpdb->last_error);
                }

                return $entity->getId();
            }

            $result = $this->wpdb->insert(
                $this->table,
                $data,
                $format
            );

            if ($result === false) {
                throw new \Exception("Erro ao inserir entidade: " . $this->wpdb->last_error);
            }

            return $this->wpdb->insert_id;
        } catch (\Exception $e) {
            error_log("Erro ao salvar entidade: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id): bool {
        try {
            $result = $this->wpdb->delete(
                $this->table,
                ['id' => $id],
                ['%d']
            );

            return $result !== false;
        } catch (\Exception $e) {
            error_log("Erro ao excluir entidade: " . $e->getMessage());
            return false;
        }
    }
}
