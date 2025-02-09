<?php
namespace Domain\Database;

use Domain\Entities\Donation;

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
            'type' => $donation->getType(),
            'description' => $donation->getDescription(),
            'amount' => $donation->getAmount(),
            'status' => $donation->getStatus(),
            'received_at' => $donation->getReceivedAt()?->format('Y-m-d H:i:s')
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
        return $this->wpdb->insert_id;
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
            $row['donor_phone'],
            $row['type'],
            $row['description'],
            (float) $row['amount']
        );

        $reflection = new \ReflectionClass($donation);
        
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($donation, (int) $row['id']);

        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($donation, $row['status']);

        if ($row['received_at']) {
            $receivedAtProperty = $reflection->getProperty('receivedAt');
            $receivedAtProperty->setAccessible(true);
            $receivedAtProperty->setValue($donation, new \DateTimeImmutable($row['received_at']));
        }

        return $donation;
    }
}
