<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\SignedTerm;

class SignedTermRepository extends AbstractRepository
{


    protected function initTable(): void
    {
        $this->table = $this->getTableName('signed_terms');
    }

    protected function createEntity(array $row): object
    {
        $signedTerm = new SignedTerm(
            (int) $row['term_id'],
            (int) $row['user_id']
        );

        if (isset($row['id'])) {
            $signedTerm->setId((int) $row['id']);
        }

        if (isset($row['adoption_id'])) {
            $signedTerm->setAdoptionId((int) $row['adoption_id']);
        }

        if (isset($row['status'])) {
            $signedTerm->setStatus($row['status']);
        }

        if (isset($row['signed_at'])) {
            $signedTerm->setSignedAt(new \DateTime($row['signed_at']));
        }

        if (isset($row['created_at'])) {
            $signedTerm->setCreatedAt(new \DateTime($row['created_at']));
        }

        if (isset($row['updated_at'])) {
            $signedTerm->setUpdatedAt(new \DateTime($row['updated_at']));
        }

        return $signedTerm;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof SignedTerm) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of SignedTerm', 'amigopet'));
        }

        $data = [
            'term_id' => $entity->getTermId(),
            'user_id' => $entity->getUserId(),
            'status' => $entity->getStatus(),
            'signed_at' => $entity->getSignedAt()->format('Y-m-d H:i:s'),
            'created_at' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()->format('Y-m-d H:i:s')
        ];

        if ($entity->getAdoptionId()) {
            $data['adoption_id'] = $entity->getAdoptionId();
        }

        return $data;
    }

    public function findByTermAndUser(int $termId, int $userId): ?SignedTerm
    {
        $args = [
            'term_id' => $termId,
            'user_id' => $userId,
            'orderby' => 'signed_at',
            'order' => 'DESC',
            'limit' => 1
        ];

        $results = $this->findAll($args);
        return !empty($results) ? $results[0] : null;
    }

    public function findByAdoption(int $adoptionId): array
    {
        return $this->findAll([
            'adoption_id' => $adoptionId,
            'orderby' => 'signed_at',
            'order' => 'DESC'
        ]);
    }

    public function findByDateRange(string $startDate, string $endDate): array
    {
        return $this->findAll([
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
                'column' => 'signed_at'
            ],
            'orderby' => 'signed_at',
            'order' => 'DESC'
        ]);
    }

    public function getReport(?string $startDate = null, ?string $endDate = null): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [
                'total_signed_terms' => 0,
                'unique_terms' => 0,
                'unique_users' => 0,
                'total_adoptions' => 0,
                'active_terms' => 0,
                'revoked_terms' => 0,
                'most_signed_term_id' => null
            ];
        }
        $start = ($startDate ?: '1970-01-01') . ' 00:00:00';
        $end = ($endDate ?: '9999-12-31') . ' 23:59:59';
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT 
                COUNT(*) as total_signed_terms,
                COUNT(DISTINCT term_id) as unique_terms,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT adoption_id) as total_adoptions,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_terms,
                COUNT(CASE WHEN status = 'revoked' THEN 1 END) as revoked_terms,
                (SELECT term_id 
                 FROM %i
                 WHERE signed_at BETWEEN %s AND %s
                 GROUP BY term_id 
                 ORDER BY COUNT(*) DESC 
                 LIMIT 1) as most_signed_term_id
            FROM %i
            WHERE signed_at BETWEEN %s AND %s",
                $table,
                $table,
                $start,
                $end,
                $start,
                $end
            ),
            ARRAY_A
        );

        return $result ?: [
            'total_signed_terms' => 0,
            'unique_terms' => 0,
            'unique_users' => 0,
            'total_adoptions' => 0,
            'active_terms' => 0,
            'revoked_terms' => 0,
            'most_signed_term_id' => null
        ];
    }
}