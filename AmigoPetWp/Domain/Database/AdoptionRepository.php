<?php
namespace Domain\Database;

use Domain\Entities\Adoption;
use Domain\Entities\AdoptionPayment;

class AdoptionRepository {
    private $wpdb;
    private $adoptionsTable;
    private $paymentsTable;
    
    public function __construct(\wpdb $wpdb) {
        $this->wpdb = $wpdb;
        $this->adoptionsTable = $wpdb->prefix . 'amigopet_adoptions';
        $this->paymentsTable = $wpdb->prefix . 'amigopet_adoption_payments';
    }
    
    public function save(Adoption $adoption): int {
        $data = [
            'pet_id' => $adoption->getPetId(),
            'adopter_id' => $adoption->getAdopterId(),
            'organization_id' => $adoption->getOrganizationId(),
            'status' => $adoption->getStatus(),
            'adoption_reason' => $adoption->getAdoptionReason(),
            'pet_experience' => $adoption->getPetExperience(),
            'reviewer_id' => $adoption->getReviewerId(),
            'review_notes' => $adoption->getReviewNotes(),
            'review_date' => $adoption->getReviewDate()?->format('Y-m-d H:i:s'),
            'completed_date' => $adoption->getCompletedDate()?->format('Y-m-d H:i:s')
        ];

        if ($adoption->getId()) {
            $this->wpdb->update(
                $this->adoptionsTable,
                $data,
                ['id' => $adoption->getId()]
            );
            return $adoption->getId();
        }

        $this->wpdb->insert($this->adoptionsTable, $data);
        return $this->wpdb->insert_id;
    }

    public function savePayment(AdoptionPayment $payment): int {
        $data = [
            'adoption_id' => $payment->getAdoptionId(),
            'amount' => $payment->getAmount(),
            'payment_method' => $payment->getPaymentMethod(),
            'status' => $payment->getStatus(),
            'paid_at' => $payment->getPaidAt()?->format('Y-m-d H:i:s')
        ];

        if ($payment->getId()) {
            $this->wpdb->update(
                $this->paymentsTable,
                $data,
                ['id' => $payment->getId()]
            );
            return $payment->getId();
        }

        $this->wpdb->insert($this->paymentsTable, $data);
        return $this->wpdb->insert_id;
    }

    public function findById(int $id): ?Adoption {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->adoptionsTable} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        return $row ? $this->hydrateAdoption($row) : null;
    }

    public function findPaymentByAdoptionId(int $adoptionId): ?AdoptionPayment {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->paymentsTable} WHERE adoption_id = %d ORDER BY created_at DESC LIMIT 1",
                $adoptionId
            ),
            ARRAY_A
        );

        return $row ? $this->hydratePayment($row) : null;
    }

    public function findPendingAdoptions(): array {
        $rows = $this->wpdb->get_results(
            "SELECT * FROM {$this->adoptionsTable} WHERE status = 'pending' ORDER BY created_at DESC",
            ARRAY_A
        );

        return array_map([$this, 'hydrateAdoption'], $rows);
    }

    public function findAwaitingPayment(): array {
        $rows = $this->wpdb->get_results(
            "SELECT * FROM {$this->adoptionsTable} WHERE status = 'awaiting_payment' ORDER BY created_at DESC",
            ARRAY_A
        );

        return array_map([$this, 'hydrateAdoption'], $rows);
    }

    private function hydrateAdoption(array $row): Adoption {
        $adoption = new Adoption(
            (int) $row['pet_id'],
            (int) $row['adopter_id'],
            (int) $row['organization_id'],
            $row['adoption_reason'],
            $row['pet_experience']
        );

        // Reflection para setar propriedades privadas
        $reflection = new \ReflectionClass($adoption);

        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($adoption, (int) $row['id']);

        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($adoption, $row['status']);

        if ($row['reviewer_id']) {
            $reviewerIdProperty = $reflection->getProperty('reviewerId');
            $reviewerIdProperty->setAccessible(true);
            $reviewerIdProperty->setValue($adoption, (int) $row['reviewer_id']);

            $reviewNotesProperty = $reflection->getProperty('reviewNotes');
            $reviewNotesProperty->setAccessible(true);
            $reviewNotesProperty->setValue($adoption, $row['review_notes']);

            $reviewDateProperty = $reflection->getProperty('reviewDate');
            $reviewDateProperty->setAccessible(true);
            $reviewDateProperty->setValue($adoption, new \DateTimeImmutable($row['review_date']));
        }

        if ($row['completed_date']) {
            $completedDateProperty = $reflection->getProperty('completedDate');
            $completedDateProperty->setAccessible(true);
            $completedDateProperty->setValue($adoption, new \DateTimeImmutable($row['completed_date']));
        }

        return $adoption;
    }

    private function hydratePayment(array $row): AdoptionPayment {
        $payment = new AdoptionPayment(
            (int) $row['adoption_id'],
            (float) $row['amount'],
            $row['payment_method']
        );

        // Reflection para setar propriedades privadas
        $reflection = new \ReflectionClass($payment);

        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($payment, (int) $row['id']);

        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($payment, $row['status']);

        if ($row['paid_at']) {
            $paidAtProperty = $reflection->getProperty('paidAt');
            $paidAtProperty->setAccessible(true);
            $paidAtProperty->setValue($payment, new \DateTimeImmutable($row['paid_at']));
        }

        return $payment;
    }
}
