<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\Donation;

class DonationRepository extends AbstractRepository
{


    protected function initTable(): void
    {
        $this->table = $this->getTableName('donations');
    }

    protected function createEntity(array $row): object
    {
        $donation = new Donation(
            (int) $row['organization_id'],
            $row['donor_name'],
            $row['donor_email'],
            $row['amount'],
            $row['payment_method']
        );

        if (isset($row['id'])) {
            $donation->setId((int) $row['id']);
        }

        if (isset($row['donor_phone'])) {
            $donation->setDonorPhone($row['donor_phone']);
        }

        if (isset($row['payment_status'])) {
            $donation->setPaymentStatus($row['payment_status']);
        }

        if (isset($row['transaction_id'])) {
            $donation->setTransactionId($row['transaction_id']);
        }

        if (isset($row['description'])) {
            $donation->setDescription($row['description']);
        }

        if (isset($row['date'])) {
            $donation->setDate(new \DateTime($row['date']));
        }

        if (isset($row['created_at'])) {
            $donation->setCreatedAt(new \DateTime($row['created_at']));
        }

        if (isset($row['updated_at'])) {
            $donation->setUpdatedAt(new \DateTime($row['updated_at']));
        }

        return $donation;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof Donation) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of Donation', 'amigopet'));
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

    public function findByEmail(string $email): array
    {
        return $this->findAll([
            'donor_email' => $email,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);
    }

    public function findByStatus(string $status, ?string $startDate = null, ?string $endDate = null): array
    {
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

    public function findByOrganization(int $organizationId, ?string $status = null, ?string $startDate = null, ?string $endDate = null): array
    {
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

    public function getReport(?string $startDate = null, ?string $endDate = null): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [
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
        $start = ($startDate ?: '1970-01-01') . ' 00:00:00';
        $end = ($endDate ?: '9999-12-31') . ' 23:59:59';
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT 
                COUNT(*) as total_donations,
                COUNT(DISTINCT organization_id) as total_organizations,
                COUNT(DISTINCT donor_email) as total_donors,
                SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as total_amount,
                AVG(CASE WHEN payment_status = 'completed' THEN amount ELSE NULL END) as avg_donation,
                MAX(CASE WHEN payment_status = 'completed' THEN amount ELSE NULL END) as largest_donation,
                (SELECT donor_email 
                 FROM %i
                 WHERE date BETWEEN %s AND %s
                 GROUP BY donor_email 
                 ORDER BY COUNT(*) DESC 
                 LIMIT 1) as most_frequent_donor,
                (SELECT payment_method 
                 FROM %i
                 WHERE date BETWEEN %s AND %s
                 GROUP BY payment_method 
                 ORDER BY COUNT(*) DESC 
                 LIMIT 1) as preferred_payment_method,
                COUNT(CASE WHEN payment_status = 'completed' THEN 1 END) as successful_donations,
                COUNT(CASE WHEN payment_status = 'failed' THEN 1 END) as failed_donations,
                COUNT(CASE WHEN payment_status = 'refunded' THEN 1 END) as refunded_donations
            FROM %i
            WHERE date BETWEEN %s AND %s",
                $table,
                $table,
                $table,
                $start,
                $end,
                $start,
                $end,
                $start,
                $end
            ),
            ARRAY_A
        );

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