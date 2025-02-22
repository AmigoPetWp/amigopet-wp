<?php
namespace AmigoPetWp\Domain\Database;

use AmigoPetWp\Domain\Entities\Donation;

class DonationRepository {
    private $wpdb;
    private $table;
    
    public function __construct(\wpdb $wpdb) {
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'amigopet_donations';
    }
    
    public function save(Donation $donation): int {
        $data = [
            'organization_id' => $donation->getOrganizationId(),
            'donor_name' => $donation->getDonorName(),
            'donor_email' => $donation->getDonorEmail(),
            'donor_phone' => $donation->getDonorPhone(),
            'amount' => $donation->getAmount(),
            'payment_method' => $donation->getPaymentMethod(),
            'payment_status' => $donation->getPaymentStatus(),
            'transaction_id' => $donation->getTransactionId(),
            'description' => $donation->getDescription(),
            'date' => $donation->getDate()->format('Y-m-d H:i:s'),
            'created_at' => $donation->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $donation->getUpdatedAt()->format('Y-m-d H:i:s')
        ];

        if ($donation->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $donation->getId()]
            );
            return $donation->getId();
        }

        $this->wpdb->insert($this->table, $data);
        $id = $this->wpdb->insert_id;
        $donation->setId($id);
        return $id;
    }

    public function count(): int {
        $query = "SELECT COUNT(*) FROM {$this->table}";
        return (int) $this->wpdb->get_var($query);
    }

    public function getReport(): array {
        $query = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN payment_status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN payment_status = 'refunded' THEN 1 ELSE 0 END) as refunded,
            SUM(amount) as total_amount,
            SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as received_amount,
            COUNT(DISTINCT donor_email) as unique_donors
        FROM {$this->table}";
        
        $result = $this->wpdb->get_row($query, ARRAY_A);
        return $result ?: [
            'total' => 0,
            'pending' => 0,
            'completed' => 0,
            'failed' => 0,
            'refunded' => 0,
            'total_amount' => 0,
            'received_amount' => 0,
            'unique_donors' => 0
        ];
    }

    public function findById(int $id): ?Donation {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findByStatus(string $status): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE payment_status = %s ORDER BY date DESC",
                $status
            ),
            ARRAY_A
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findByOrganization(int $organizationId, ?string $status = null): array {
        $query = "SELECT * FROM {$this->table} WHERE organization_id = %d";
        $params = [$organizationId];

        if ($status) {
            $query .= " AND payment_status = %s";
            $params[] = $status;
        }

        $query .= " ORDER BY date DESC";

        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare($query, ...$params),
            ARRAY_A
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findByOrganization(int $organizationId): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE organization_id = %d ORDER BY created_at DESC",
                $organizationId
            ),
            ARRAY_A
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findPending(): array {
        $rows = $this->wpdb->get_results(
            "SELECT * FROM {$this->table} WHERE status = 'pending' ORDER BY created_at DESC",
            ARRAY_A
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    private function hydrate(array $row): Donation {
        $donation = new Donation(
            (int) $row['organization_id'],
            $row['donor_name'],
            $row['donor_email'],
            (float) $row['amount'],
            $row['payment_method'],
            $row['donor_phone'] ?? null,
            $row['description'] ?? null
        );

        if (isset($row['id'])) {
            $donation->setId((int) $row['id']);
        }

        if (isset($row['transaction_id'])) {
            $donation->setTransactionId($row['transaction_id']);
        }

        if (isset($row['payment_status'])) {
            $donation->setPaymentStatus($row['payment_status']);
        }

        return $donation;
    }
}
