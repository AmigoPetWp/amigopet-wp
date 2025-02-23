<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\SignedTerm;

class SignedTermRepository extends AbstractRepository {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
    }

    protected function getTableName(): string {
        return 'apwp_signed_terms';
    }

    protected function createEntity(array $data): SignedTerm {
        $signedTerm = new SignedTerm(
            (int)$data['term_id'],
            (int)$data['user_id']
        );

        if (isset($data['id'])) {
            $signedTerm->setId((int)$data['id']);
        }

        if (isset($data['adoption_id'])) {
            $signedTerm->setAdoptionId((int)$data['adoption_id']);
        }

        if (isset($data['status'])) {
            $signedTerm->setStatus($data['status']);
        }

        if (isset($data['signed_at'])) {
            $signedTerm->setSignedAt(new \DateTime($data['signed_at']));
        }

        if (isset($data['created_at'])) {
            $signedTerm->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at'])) {
            $signedTerm->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $signedTerm;
    }

    protected function toDatabase($entity): array {
        if (!$entity instanceof SignedTerm) {
            throw new \InvalidArgumentException('Entity must be an instance of SignedTerm');
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

    public function findByTermAndUser(int $termId, int $userId): ?SignedTerm {
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

    public function findByAdoption(int $adoptionId): array {
        return $this->findAll([
            'adoption_id' => $adoptionId,
            'orderby' => 'signed_at',
            'order' => 'DESC'
        ]);
    }

    public function findByDateRange(string $startDate, string $endDate): array {
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

    public function getReport(?string $startDate = null, ?string $endDate = null): array {
        $where = ['1=1'];
        $params = [];

        if ($startDate && $endDate) {
            $where[] = 'signed_at BETWEEN %s AND %s';
            $params[] = $startDate . ' 00:00:00';
            $params[] = $endDate . ' 23:59:59';
        }

        $whereClause = implode(' AND ', $where);

        $query = $this->wpdb->prepare(
            "SELECT 
                COUNT(*) as total_signed_terms,
                COUNT(DISTINCT term_id) as unique_terms,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT adoption_id) as total_adoptions,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_terms,
                COUNT(CASE WHEN status = 'revoked' THEN 1 END) as revoked_terms,
                (SELECT term_id 
                 FROM {$this->table} 
                 WHERE {$whereClause}
                 GROUP BY term_id 
                 ORDER BY COUNT(*) DESC 
                 LIMIT 1) as most_signed_term_id
            FROM {$this->table}
            WHERE {$whereClause}",
            $params
        );

        $result = $this->wpdb->get_row($query, ARRAY_A);

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
