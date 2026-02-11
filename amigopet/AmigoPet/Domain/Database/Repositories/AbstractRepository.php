<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

abstract class AbstractRepository implements BaseRepository
{
    protected $wpdb;
    protected $table;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->initTable();
    }

    abstract protected function initTable(): void;
    abstract protected function createEntity(array $row): object;
    abstract protected function toDatabase($entity): array;

    protected function getTableName(string $tableName): string
    {
        $safeName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
        if (!is_string($safeName) || $safeName === '') {
            $safeName = 'default';
        }
        return $this->wpdb->prefix . 'apwp_' . $safeName;
    }

    protected function sanitizeIdentifier(string $identifier): string
    {
        $safeIdentifier = preg_replace('/[^a-zA-Z0-9_]/', '', $identifier);
        return is_string($safeIdentifier) ? $safeIdentifier : '';
    }

    protected function quoteIdentifier(string $identifier): string
    {
        $safeIdentifier = $this->sanitizeIdentifier($identifier);
        if ($safeIdentifier === '') {
            return '';
        }

        return '`' . $safeIdentifier . '`';
    }

    public function findById(int $id): ?object
    {
        try {
            $wpdb = $this->wpdb;
            $table = $this->sanitizeIdentifier($this->table);
            if ($table === '') {
                return null;
            }

            $row = $wpdb->get_row(
                $wpdb->prepare('SELECT * FROM %i WHERE id = %d', $table, $id),
                ARRAY_A
            );
            if (!$row) {
                return null;
            }

            return $this->createEntity($row);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findAll(array $args = []): array
    {
        try {
            $wpdb = $this->wpdb;
            $table = $this->sanitizeIdentifier($this->table);
            if ($table === '') {
                return [];
            }

            $allowedOrderby = ['created_at', 'status', 'id', 'name', 'payment_date', 'amount'];
            $orderBy = in_array($args['orderby'] ?? '', $allowedOrderby, true) ? (string) $args['orderby'] : 'created_at';
            $safeOrderBy = $this->sanitizeIdentifier($orderBy);
            if ($safeOrderBy === '') {
                $safeOrderBy = 'created_at';
            }

            $orderIsAsc = strtoupper((string) ($args['order'] ?? '')) === 'ASC';
            if ($orderIsAsc) {
                $rows = $wpdb->get_results(
                    $wpdb->prepare(
                        'SELECT * FROM %i ORDER BY %i ASC',
                        $table,
                        $safeOrderBy
                    ),
                    ARRAY_A
                );
            } else {
                $rows = $wpdb->get_results(
                    $wpdb->prepare(
                        'SELECT * FROM %i ORDER BY %i DESC',
                        $table,
                        $safeOrderBy
                    ),
                    ARRAY_A
                );
            }

            if (!is_array($rows)) {
                return [];
            }

            $rows = array_values(array_filter($rows, function (array $row) use ($args, $wpdb): bool {
                if (isset($args['status']) && (string) ($row['status'] ?? '') !== (string) $args['status']) {
                    return false;
                }
                if (isset($args['type']) && (string) ($row['type'] ?? '') !== (string) $args['type']) {
                    return false;
                }
                if (isset($args['pet_id']) && (int) ($row['pet_id'] ?? 0) !== (int) $args['pet_id']) {
                    return false;
                }
                if (isset($args['adopter_id']) && (int) ($row['adopter_id'] ?? 0) !== (int) $args['adopter_id']) {
                    return false;
                }
                if (isset($args['reviewer_id']) && (int) ($row['reviewer_id'] ?? 0) !== (int) $args['reviewer_id']) {
                    return false;
                }
                if (isset($args['start_date']) && isset($row['created_at'])) {
                    $rowTime = strtotime((string) $row['created_at']);
                    $startTime = strtotime((string) $args['start_date']);
                    if ($rowTime !== false && $startTime !== false && $rowTime < $startTime) {
                        return false;
                    }
                }
                if (isset($args['end_date']) && isset($row['created_at'])) {
                    $rowTime = strtotime((string) $row['created_at']);
                    $endTime = strtotime((string) $args['end_date']);
                    if ($rowTime !== false && $endTime !== false && $rowTime > $endTime) {
                        return false;
                    }
                }
                if (isset($args['min_amount']) && (float) ($row['amount'] ?? 0) < (float) $args['min_amount']) {
                    return false;
                }
                if (isset($args['max_amount']) && (float) ($row['amount'] ?? 0) > (float) $args['max_amount']) {
                    return false;
                }
                if (isset($args['search']) && isset($args['search_columns']) && is_array($args['search_columns'])) {
                    $searchNeedle = mb_strtolower((string) $args['search']);
                    $searchMatched = false;
                    foreach ($args['search_columns'] as $column) {
                        $safeColumn = $this->sanitizeIdentifier((string) $column);
                        if ($safeColumn === '' || !array_key_exists($safeColumn, $row)) {
                            continue;
                        }
                        $cell = mb_strtolower((string) $row[$safeColumn]);
                        if (strpos($cell, $searchNeedle) !== false) {
                            $searchMatched = true;
                            break;
                        }
                    }
                    if (!$searchMatched) {
                        return false;
                    }
                }

                return true;
            }));

            if (isset($args['limit'])) {
                $limit = max(0, (int) $args['limit']);
                $offset = isset($args['offset']) ? max(0, (int) $args['offset']) : 0;
                $rows = array_slice($rows, $offset, $limit);
            }

            return array_map([$this, 'createEntity'], $rows);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function save($entity): int
    {
        try {
            $data = $this->toDatabase($entity);
            $id = $entity->getId();

            if ($id > 0) {
                $result = $this->wpdb->update($this->table, $data, ['id' => $id]);
                return $result !== false ? $id : 0;
            }

            $result = $this->wpdb->insert($this->table, $data);
            if ($result) {
                $newId = (int) $this->wpdb->insert_id;
                $entity->setId($newId);
                return $newId;
            }

            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $result = $this->wpdb->delete($this->table, ['id' => $id]);
            return $result !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}