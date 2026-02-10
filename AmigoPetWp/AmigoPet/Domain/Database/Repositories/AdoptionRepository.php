<?php
declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\Adoption;

class AdoptionRepository extends AbstractRepository
{
    protected function getTableName(): string
    {
        return 'apwp_adoptions';
    }

    protected function createEntity(array $data): Adoption
    {
        $adoption = new Adoption();

        if (isset($data['id'])) {
            $adoption->setId((int) $data['id']);
        }

        $adoption->setPetId((int) $data['pet_id']);
        $adoption->setAdopterId((int) $data['adopter_id']);
        $adoption->setOrganizationId((int) $data['organization_id']);
        $adoption->setStatus($data['status']);

        if (isset($data['adoption_reason'])) {
            $adoption->setAdoptionReason($data['adoption_reason']);
        }

        if (isset($data['pet_experience'])) {
            $adoption->setPetExperience($data['pet_experience']);
        }

        if (isset($data['reviewer_id'])) {
            $adoption->setReviewerId((int) $data['reviewer_id']);
        }

        if (isset($data['review_notes'])) {
            $adoption->setReviewNotes($data['review_notes']);
        }

        if (isset($data['review_date'])) {
            $adoption->setReviewDate(new \DateTime($data['review_date']));
        }

        if (isset($data['completed_date'])) {
            $adoption->setCompletedDate(new \DateTime($data['completed_date']));
        }

        if (isset($data['created_at'])) {
            $adoption->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at'])) {
            $adoption->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $adoption;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof Adoption) {
            throw new \InvalidArgumentException('Entity must be an instance of Adoption');
        }

        return [
            'pet_id' => $entity->getPetId(),
            'adopter_id' => $entity->getAdopterId(),
            'organization_id' => $entity->getOrganizationId(),
            'status' => $entity->getStatus(),
            'adoption_reason' => $entity->getAdoptionReason(),
            'pet_experience' => $entity->getPetExperience(),
            'reviewer_id' => $entity->getReviewerId(),
            'review_notes' => $entity->getReviewNotes(),
            'review_date' => $entity->getReviewDate()?->format('Y-m-d H:i:s'),
            'completed_date' => $entity->getCompletedDate()?->format('Y-m-d H:i:s'),
            'created_at' => $entity->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()?->format('Y-m-d H:i:s')
        ];
    }

    public function findByPet(int $petId): array
    {
        return $this->findAll(['pet_id' => $petId]);
    }

    public function findByAdopter(int $adopterId): array
    {
        return $this->findAll(['adopter_id' => $adopterId]);
    }

    public function findByOrganization(int $organizationId): array
    {
        return $this->findAll(['organization_id' => $organizationId]);
    }

    public function findByReviewer(int $reviewerId): array
    {
        return $this->findAll(['reviewer_id' => $reviewerId]);
    }

    public function findByStatus(string $status): array
    {
        return $this->findAll(['status' => $status]);
    }

    public function findPending(): array
    {
        return $this->findAll(['status' => 'pending']);
    }

    public function findApproved(): array
    {
        return $this->findAll(['status' => 'approved']);
    }

    public function findRejected(): array
    {
        return $this->findAll(['status' => 'rejected']);
    }

    public function findCompleted(): array
    {
        return $this->findAll(['status' => 'completed']);
    }

    public function findCancelled(): array
    {
        return $this->findAll(['status' => 'cancelled']);
    }

    public function search(string $term): array
    {
        $sql = $this->wpdb->prepare(
            "SELECT a.* 
            FROM {$this->table} a
            LEFT JOIN {$this->wpdb->prefix}apwp_pets p ON a.pet_id = p.id
            LEFT JOIN {$this->wpdb->prefix}apwp_adopters ad ON a.adopter_id = ad.id
            WHERE p.name LIKE %s 
            OR ad.name LIKE %s 
            OR ad.email LIKE %s 
            OR a.adoption_reason LIKE %s 
            OR a.review_notes LIKE %s",
            "%{$term}%",
            "%{$term}%",
            "%{$term}%",
            "%{$term}%",
            "%{$term}%"
        );

        return array_map(
            [$this, 'createEntity'],
            $this->wpdb->get_results($sql, ARRAY_A)
        );
    }

    public function getReport(?string $startDate = null, ?string $endDate = null): array
    {
        $where = [];
        $params = [];

        if ($startDate) {
            $where[] = "created_at >= %s";
            $params[] = $startDate;
        }

        if ($endDate) {
            $where[] = "created_at <= %s";
            $params[] = $endDate;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $sql = $this->wpdb->prepare(
            "SELECT 
                COUNT(*) as total,
                status,
                DATE(created_at) as date,
                organization_id
            FROM {$this->table}
            {$whereClause}
            GROUP BY status, DATE(created_at), organization_id
            ORDER BY date DESC",
            $params
        );

        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    public function findByFilters(array $filters): array
    {
        $criteria = [];

        if (isset($filters['pet_id'])) {
            $criteria['pet_id'] = (int) $filters['pet_id'];
        }

        if (isset($filters['adopter_id'])) {
            $criteria['adopter_id'] = (int) $filters['adopter_id'];
        }

        if (isset($filters['organization_id'])) {
            $criteria['organization_id'] = (int) $filters['organization_id'];
        }

        if (isset($filters['reviewer_id'])) {
            $criteria['reviewer_id'] = (int) $filters['reviewer_id'];
        }

        if (isset($filters['status'])) {
            $criteria['status'] = $filters['status'];
        }

        if (isset($filters['date_start'])) {
            $criteria['date_start'] = $filters['date_start'];
        }

        if (isset($filters['date_end'])) {
            $criteria['date_end'] = $filters['date_end'];
        }

        if (isset($filters['has_review'])) {
            $criteria['has_review'] = (bool) $filters['has_review'];
        }

        return $this->findAll($criteria);
    }

    public function getOrganizationReport(int $organizationId, ?string $startDate = null, ?string $endDate = null): array
    {
        $where = ['organization_id = %d'];
        $params = [$organizationId];

        if ($startDate) {
            $where[] = "created_at >= %s";
            $params[] = $startDate;
        }

        if ($endDate) {
            $where[] = "created_at <= %s";
            $params[] = $endDate;
        }

        $whereClause = implode(" AND ", $where);

        $sql = $this->wpdb->prepare(
            "SELECT 
                COUNT(*) as total,
                status,
                DATE(created_at) as date,
                AVG(TIMESTAMPDIFF(DAY, created_at, review_date)) as avg_review_time,
                AVG(TIMESTAMPDIFF(DAY, created_at, completed_date)) as avg_completion_time
            FROM {$this->table}
            WHERE {$whereClause}
            GROUP BY status, DATE(created_at)
            ORDER BY date DESC",
            $params
        );

        return $this->wpdb->get_results($sql, ARRAY_A);
    }
}
