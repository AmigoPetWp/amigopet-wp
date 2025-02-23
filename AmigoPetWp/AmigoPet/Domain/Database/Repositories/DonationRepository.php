<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\Donation;

class DonationRepository extends AbstractRepository {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
    }

    protected function getTableName(): string {
        return 'apwp_donations';
    }
    
    protected function createEntity(array $data): Donation {
        $donation = new Donation(
            (int)$data['organization_id'],
            $data['donor_name'],
            $data['donor_email'],
            $data['amount'],
            $data['payment_method']
        );

        if (isset($data['id'])) {
            $donation->setId((int)$data['id']);
        }

        if (isset($data['donor_phone'])) {
            $donation->setDonorPhone($data['donor_phone']);
        }

        if (isset($data['payment_status'])) {
            $donation->setPaymentStatus($data['payment_status']);
        }

        if (isset($data['transaction_id'])) {
            $donation->setTransactionId($data['transaction_id']);
        }

        if (isset($data['description'])) {
            $donation->setDescription($data['description']);
        }

        if (isset($data['date'])) {
            $donation->setDate(new \DateTime($data['date']));
        }

        if (isset($data['created_at'])) {
            $donation->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at'])) {
            $donation->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $donation;
    }

    protected function toDatabase($entity): array {
        if (!$entity instanceof Donation) {
            throw new \InvalidArgumentException('Entity must be an instance of Donation');
        }

        return [
            'organization_id' => $entity->getOrganizationId(),
            'donor_name' => $entity->getDonorName(),
            'donor_email' => $entity->getDonorEmail(),
            'donor_phone' => $entity->getDonorPhone(),
            'amount' => $entity->getAmount(),
            'payment_method' => $entity->getPaymentMethod(),
            'payment_status' => $entity->getPaymentStatus(),
            'transaction_id' => $entity->getTransactionId(),
            'description' => $entity->getDescription(),
            'date' => $entity->getDate()->format('Y-m-d H:i:s'),
            'created_at' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()->format('Y-m-d H:i:s')
        ];
    }

    public function findByEmail(string $email): array {
        return $this->findAll([
            'donor_email' => $email,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);
    }

    public function findByStatus(string $status, ?string $startDate = null, ?string $endDate = null): array {
        $args = [
            'payment_status' => $status,
            'orderby' => 'date',
            'order' => 'DESC'
        ];

        if ($startDate && $endDate) {
            $args['date_range'] = [
                'start' => $startDate,
                'end' => $endDate
            ];
        }

        return $this->findAll($args);
    }

    public function findByOrganization(int $organizationId, ?string $status = null, ?string $startDate = null, ?string $endDate = null): array {
        $args = [
            'organization_id' => $organizationId,
            'orderby' => 'date',
            'order' => 'DESC'
        ];

        if ($status) {
            $args['payment_status'] = $status;
        }

        if ($startDate && $endDate) {
            $args['date_range'] = [
                'start' => $startDate,
                'end' => $endDate
            ];
        }

        return $this->findAll($args);
    }

    public function getReport(?string $startDate = null, ?string $endDate = null): array {
        $where = ['1=1'];
        $params = [];

        if ($startDate && $endDate) {
            $where[] = 'date BETWEEN %s AND %s';
            $params[] = $startDate . ' 00:00:00';
            $params[] = $endDate . ' 23:59:59';
        }

        $whereClause = implode(' AND ', $where);

        $query = $this->wpdb->prepare(
            "SELECT 
                COUNT(*) as total_donations,
                COUNT(DISTINCT organization_id) as total_organizations,
                COUNT(DISTINCT donor_email) as total_donors,
                SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as total_amount,
                AVG(CASE WHEN payment_status = 'completed' THEN amount ELSE NULL END) as avg_donation,
                MAX(CASE WHEN payment_status = 'completed' THEN amount ELSE NULL END) as largest_donation,
                (SELECT donor_email 
                 FROM {$this->table} 
                 WHERE {$whereClause}
                 GROUP BY donor_email 
                 ORDER BY COUNT(*) DESC 
                 LIMIT 1) as most_frequent_donor,
                (SELECT payment_method 
                 FROM {$this->table} 
                 WHERE {$whereClause}
                 GROUP BY payment_method 
                 ORDER BY COUNT(*) DESC 
                 LIMIT 1) as preferred_payment_method,
                COUNT(CASE WHEN payment_status = 'completed' THEN 1 END) as successful_donations,
                COUNT(CASE WHEN payment_status = 'failed' THEN 1 END) as failed_donations,
                COUNT(CASE WHEN payment_status = 'refunded' THEN 1 END) as refunded_donations
            FROM {$this->table}
            WHERE {$whereClause}",
            $params
        );

        $result = $this->wpdb->get_row($query, ARRAY_A);

        return $result ?: [
            'total_donations' => 0,
            'total_organizations' => 0,
            'total_donors' => 0,
            'total_amount' => 0,
            'avg_donation' => 0,
            'largest_donation' => 0,
            'most_frequent_donor' => null,
            'preferred_payment_method' => null,
            'successful_donations' => 0,
            'failed_donations' => 0,
            'refunded_donations' => 0
        ];
    }
}
