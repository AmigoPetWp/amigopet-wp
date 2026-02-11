<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\Adoption;

class AdoptionRepository extends AbstractRepository
{
    protected function initTable(): void
    {
        $this->table = $this->getTableName('adoptions');
    }

    protected function createEntity(array $row): object
    {
        $adoption = new Adoption();

        if (isset($row['id'])) {
            $adoption->setId((int) $row['id']);
        }

        $adoption->setPetId((int) $row['pet_id']);
        $adoption->setAdopterId((int) $row['adopter_id']);
        $adoption->setOrganizationId((int) $row['organization_id']);
        $adoption->setStatus($row['status']);

        if (isset($row['adoption_reason'])) {
            $adoption->setAdoptionReason($row['adoption_reason']);
        }

        if (isset($row['pet_experience'])) {
            $adoption->setPetExperience($row['pet_experience']);
        }

        if (isset($row['reviewer_id'])) {
            $adoption->setReviewerId((int) $row['reviewer_id']);
        }

        if (isset($row['review_notes'])) {
            $adoption->setReviewNotes($row['review_notes']);
        }

        if (isset($row['review_date'])) {
            $adoption->setReviewDate(new \DateTime($row['review_date']));
        }

        if (isset($row['completed_date'])) {
            $adoption->setCompletedDate(new \DateTime($row['completed_date']));
        }

        if (isset($row['created_at'])) {
            $adoption->setCreatedAt(new \DateTime($row['created_at']));
        }

        if (isset($row['updated_at'])) {
            $adoption->setUpdatedAt(new \DateTime($row['updated_at']));
        }

        return $adoption;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof Adoption) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of Adoption', 'amigopet'));
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
        $wpdb = $this->wpdb;
        $adoptionsTable = $this->sanitizeIdentifier($this->table);
        $petsTable = $this->sanitizeIdentifier($this->getTableName('pets'));
        $adoptersTable = $this->sanitizeIdentifier($this->getTableName('adopters'));
        if ($adoptionsTable === '' || $petsTable === '' || $adoptersTable === '') {
            return [];
        }
        $search = '%' . $wpdb->esc_like($term) . '%';

        return array_map(
            [$this, 'createEntity'],
            $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT a.* FROM %i a LEFT JOIN %i p ON a.pet_id = p.id LEFT JOIN %i ad ON a.adopter_id = ad.id WHERE p.name LIKE %s OR ad.name LIKE %s OR ad.email LIKE %s OR a.adoption_reason LIKE %s OR a.review_notes LIKE %s',
                    $adoptionsTable,
                    $petsTable,
                    $adoptersTable,
                    $search,
                    $search,
                    $search,
                    $search,
                    $search
                ),
                ARRAY_A
            )
        );
    }

    public function getReport(?string $startDate = null, ?string $endDate = null): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $start = ($startDate ?: '1970-01-01') . ' 00:00:00';
        $end = ($endDate ?: '9999-12-31') . ' 23:59:59';

        return $wpdb->get_results(
            $wpdb->prepare(
                'SELECT COUNT(*) as total, status, DATE(created_at) as date, organization_id FROM %i WHERE created_at BETWEEN %s AND %s GROUP BY status, DATE(created_at), organization_id ORDER BY date DESC',
                $table,
                $start,
                $end
            ),
            ARRAY_A
        );
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
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $start = ($startDate ?: '1970-01-01') . ' 00:00:00';
        $end = ($endDate ?: '9999-12-31') . ' 23:59:59';

        return $wpdb->get_results(
            $wpdb->prepare(
                'SELECT COUNT(*) as total, status, DATE(created_at) as date, AVG(TIMESTAMPDIFF(DAY, created_at, review_date)) as avg_review_time, AVG(TIMESTAMPDIFF(DAY, created_at, completed_date)) as avg_completion_time FROM %i WHERE organization_id = %d AND created_at BETWEEN %s AND %s GROUP BY status, DATE(created_at) ORDER BY date DESC',
                $table,
                $organizationId,
                $start,
                $end
            ),
            ARRAY_A
        );
    }
}