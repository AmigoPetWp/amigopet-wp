<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\AdoptionPayment;

class AdoptionPaymentRepository {
    private $wpdb;
    private $table;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'amigopet_adoption_payments';
    }

    public function findById(int $id): ?AdoptionPayment {
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
    }

    public function findByAdoption(int $adoptionId): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE adoption_id = %d ORDER BY created_at DESC",
                $adoptionId
            ),
            ARRAY_A
        );

        return array_map([$this, 'createEntity'], $rows);
    }

    public function findAll(array $args = []): array {
        $where = ['1=1'];
        $params = [];

        if (isset($args['adoption_id'])) {
            $where[] = 'adoption_id = %d';
            $params[] = $args['adoption_id'];
        }

        if (isset($args['status'])) {
            $where[] = 'status = %s';
            $params[] = $args['status'];
        }

        if (isset($args['payment_method'])) {
            $where[] = 'payment_method = %s';
            $params[] = $args['payment_method'];
        }

        if (isset($args['start_date'])) {
            $where[] = 'created_at >= %s';
            $params[] = $args['start_date'];
        }

        if (isset($args['end_date'])) {
            $where[] = 'created_at <= %s';
            $params[] = $args['end_date'];
        }

        if (isset($args['min_amount'])) {
            $where[] = 'amount >= %f';
            $params[] = $args['min_amount'];
        }

        if (isset($args['max_amount'])) {
            $where[] = 'amount <= %f';
            $params[] = $args['max_amount'];
        }

        $orderBy = $args['orderby'] ?? 'created_at';
        $order = $args['order'] ?? 'DESC';

        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where) . " ORDER BY {$orderBy} {$order}";
        
        if (!empty($params)) {
            $sql = $this->wpdb->prepare($sql, $params);
        }

        $rows = $this->wpdb->get_results($sql, ARRAY_A);

        return array_map([$this, 'createEntity'], $rows);
    }

    public function save(AdoptionPayment $payment): int {
        $data = [
            'adoption_id' => $payment->getAdoptionId(),
            'amount' => $payment->getAmount(),
            'payment_method' => $payment->getPaymentMethod(),
            'transaction_id' => $payment->getTransactionId(),
            'status' => $payment->getStatus(),
            'notes' => $payment->getNotes()
        ];

        $format = ['%d', '%f', '%s', '%s', '%s', '%s'];

        if ($payment->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $payment->getId()],
                $format,
                ['%d']
            );
            return $payment->getId();
        }

        $this->wpdb->insert($this->table, $data, $format);
        return $this->wpdb->insert_id;
    }

    public function delete(int $id): bool {
        return (bool) $this->wpdb->delete(
            $this->table,
            ['id' => $id],
            ['%d']
        );
    }

    public function sumByAdoption(int $adoptionId): float {
        return (float) $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT SUM(amount) FROM {$this->table} WHERE adoption_id = %d AND status = 'completed'",
                $adoptionId
            )
        );
    }

    private function createEntity(array $row): AdoptionPayment {
        $payment = new AdoptionPayment(
            (int) $row['adoption_id'],
            (float) $row['amount'],
            $row['payment_method'],
            $row['transaction_id'],
            $row['status'],
            $row['notes']
        );

        $payment->setId($row['id']);
        $payment->setCreatedAt(new \DateTime($row['created_at']));
        $payment->setUpdatedAt(new \DateTime($row['updated_at']));

        return $payment;
    }
}
