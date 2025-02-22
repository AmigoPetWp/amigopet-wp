<?php
namespace AmigoPetWp\Domain\Database;

use AmigoPetWp\Domain\Entities\Adoption;
use AmigoPetWp\Domain\Entities\AdoptionPayment;
use AmigoPetWp\Domain\Entities\Pet;
use AmigoPetWp\Domain\Entities\Adopter;

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

    public function getReport(): array {
        $query = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
        FROM {$this->adoptionsTable}";
        
        $result = $this->wpdb->get_row($query, ARRAY_A);
        return $result ?: [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'completed' => 0
        ];
    }

    public function findAllWithRelations(
        int $perPage = 20,
        int $currentPage = 1,
        string $search = '',
        string $status = '',
        string $orderby = 'ad.created_at',
        string $order = 'DESC'
    ): array {
        $offset = ($currentPage - 1) * $perPage;
        
        $sql = "SELECT 
            ad.*, 
            p.*, 
            s.name as species_name,
            b.name as breed_name,
            a.*,
            v.name as reviewer_name,
            v.role as reviewer_role,
            pay.id as payment_id,
            pay.amount as payment_amount,
            pay.payment_method,
            pay.status as payment_status,
            pay.paid_at
        FROM {$this->adoptionsTable} ad
        LEFT JOIN {$this->wpdb->prefix}amigopet_pets p ON ad.pet_id = p.id
        LEFT JOIN {$this->wpdb->prefix}amigopet_pet_species s ON p.species_id = s.id
        LEFT JOIN {$this->wpdb->prefix}amigopet_pet_breeds b ON p.breed_id = b.id
        LEFT JOIN {$this->wpdb->prefix}amigopet_adopters a ON ad.adopter_id = a.id
        LEFT JOIN {$this->wpdb->prefix}amigopet_volunteers v ON ad.reviewer_id = v.id
        LEFT JOIN {$this->paymentsTable} pay ON ad.id = pay.adoption_id";
        
        $where = [];
        
        if (!empty($search)) {
            $search_term = '%' . $this->wpdb->esc_like($search) . '%';
            $where[] = $this->wpdb->prepare(
                '(p.name LIKE %s OR a.name LIKE %s OR a.email LIKE %s)',
                $search_term, $search_term, $search_term
            );
        }
        
        if (!empty($status)) {
            $where[] = $this->wpdb->prepare('ad.status = %s', $status);
        }
        
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY {$orderby} {$order}";
        $sql .= $this->wpdb->prepare(' LIMIT %d OFFSET %d', $perPage, $offset);
        
        $results = $this->wpdb->get_results($sql, ARRAY_A);
        if (!$results) {
            return [];
        }

        $adoptions = [];
        foreach ($results as $row) {
            $adoptions[] = $this->hydrateAdoptionWithRelations($row);
        }

        return $adoptions;
    }

    public function findAll(int $perPage = 10, int $currentPage = 1): array {
        $offset = ($currentPage - 1) * $perPage;
        $query = $this->wpdb->prepare(
            "SELECT * FROM {$this->adoptionsTable} ORDER BY id DESC LIMIT %d OFFSET %d",
            $perPage,
            $offset
        );
        
        $results = $this->wpdb->get_results($query);
        
        if (!$results) {
            return [];
        }
        
        $adoptions = [];
        foreach ($results as $row) {
            $adoptions[] = $this->hydrateAdoption((array)$row);
        }
        
        return $adoptions;
    }

    public function countWithFilters(string $search = '', string $status = ''): int {
        $sql = "SELECT COUNT(ad.id) FROM {$this->adoptionsTable} ad";
        
        $where = [];
        
        if (!empty($search)) {
            $sql .= " LEFT JOIN {$this->wpdb->prefix}amigopet_pets p ON ad.pet_id = p.id
                     LEFT JOIN {$this->wpdb->prefix}amigopet_adopters a ON ad.adopter_id = a.id";
            
            $search_term = '%' . $this->wpdb->esc_like($search) . '%';
            $where[] = $this->wpdb->prepare(
                '(p.name LIKE %s OR a.name LIKE %s OR a.email LIKE %s)',
                $search_term, $search_term, $search_term
            );
        }
        
        if (!empty($status)) {
            $where[] = $this->wpdb->prepare('ad.status = %s', $status);
        }
        
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        
        return (int) $this->wpdb->get_var($sql);
    }

    public function count(): int {
        return $this->countWithFilters();
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

    private function hydrateAdoptionWithRelations(array $row): Adoption {
        // Cria o objeto Pet
        $pet = new Pet(
            $row['name'],
            $row['species_name'],
            $row['breed_name'],
            (int)$row['age'],
            $row['size'],
            $row['description'],
            (int)$row['organization_id'],
            $row['rga'],
            $row['microchip_number'],
            json_decode($row['health_info'], true) ?? []
        );

        // Cria o objeto Adopter
        $adopter = new Adopter(
            $row['adopter_name'],
            $row['adopter_email'],
            $row['document'],
            $row['phone'],
            $row['address']
        );

        // Cria o objeto Adoption
        $adoption = new Adoption(
            (int)$row['pet_id'],
            (int)$row['adopter_id'],
            (int)$row['organization_id'],
            $row['adoption_reason'],
            $row['pet_experience']
        );

        // Adiciona dados de revisão se houver
        if ($row['reviewer_id']) {
            $adoption->review(
                (int)$row['reviewer_id'],
                $row['review_notes'],
                $row['status'] === 'approved'
            );
        }

        // Adiciona dados de pagamento se houver
        if ($row['payment_id']) {
            $payment = new AdoptionPayment(
                (int)$row['adoption_id'],
                (float)$row['payment_amount'],
                $row['payment_method']
            );
            if ($row['payment_status'] === 'paid') {
                $payment->confirmPayment();
            } elseif ($row['payment_status'] === 'refunded') {
                $payment->refundPayment();
            }
        }

        return $adoption;
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
