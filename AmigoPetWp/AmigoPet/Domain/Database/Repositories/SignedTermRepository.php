<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\SignedTerm;

class SignedTermRepository {
    private $wpdb;
    private $table;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'amigopet_signed_terms';
    }

    public function findById(int $id): ?SignedTerm {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        if (!$row) {
            return null;
        }

        return $this->createEntity($row);
    }

    public function findByTermAndUser(int $termId, int $userId): ?SignedTerm {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE term_id = %d AND user_id = %d ORDER BY signed_at DESC LIMIT 1",
                $termId,
                $userId
            ),
            ARRAY_A
        );

        if (!$row) {
            return null;
        }

        return $this->createEntity($row);
    }

    public function findByAdoption(int $adoptionId): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE adoption_id = %d ORDER BY signed_at DESC",
                $adoptionId
            ),
            ARRAY_A
        );

        return array_map([$this, 'createEntity'], $rows);
    }

    public function findAll(array $args = []): array {
        $where = ['1=1'];
        $params = [];

        if (isset($args['term_id'])) {
            $where[] = 'term_id = %d';
            $params[] = $args['term_id'];
        }

        if (isset($args['user_id'])) {
            $where[] = 'user_id = %d';
            $params[] = $args['user_id'];
        }

        if (isset($args['adoption_id'])) {
            $where[] = 'adoption_id = %d';
            $params[] = $args['adoption_id'];
        }

        if (isset($args['status'])) {
            $where[] = 'status = %s';
            $params[] = $args['status'];
        }

        if (isset($args['start_date'])) {
            $where[] = 'signed_at >= %s';
            $params[] = $args['start_date'];
        }

        if (isset($args['end_date'])) {
            $where[] = 'signed_at <= %s';
            $params[] = $args['end_date'];
        }

        $orderBy = $args['orderby'] ?? 'signed_at';
        $order = $args['order'] ?? 'DESC';

        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where) . " ORDER BY {$orderBy} {$order}";
        
        if (!empty($params)) {
            $sql = $this->wpdb->prepare($sql, $params);
        }

        $rows = $this->wpdb->get_results($sql, ARRAY_A);

        return array_map([$this, 'createEntity'], $rows);
    }

    public function save(SignedTerm $signedTerm): int {
        $data = [
            'term_id' => $signedTerm->getTermId(),
            'user_id' => $signedTerm->getUserId(),
            'adoption_id' => $signedTerm->getAdoptionId(),
            'signed_at' => $signedTerm->getSignedAt()->format('Y-m-d H:i:s'),
            'ip_address' => $signedTerm->getIpAddress(),
            'user_agent' => $signedTerm->getUserAgent(),
            'document_url' => $signedTerm->getDocumentUrl(),
            'status' => $signedTerm->getStatus()
        ];

        $format = ['%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s'];

        if ($signedTerm->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $signedTerm->getId()],
                $format,
                ['%d']
            );
            return $signedTerm->getId();
        }

        $this->wpdb->insert($this->table, $data, $format);
        return $this->wpdb->insert_id;
    }

    public function delete(int $id): bool {
        return (bool) $this->wpdb->delete(
            $this->table,
            ['id' => $id],
            ['%d']
        );
    }

    private function createEntity(array $row): SignedTerm {
        $signedTerm = new SignedTerm(
            (int) $row['term_id'],
            (int) $row['user_id'],
            $row['adoption_id'] ? (int) $row['adoption_id'] : null,
            $row['ip_address'],
            $row['user_agent'],
            $row['document_url'],
            $row['status']
        );

        $signedTerm->setId($row['id']);
        $signedTerm->setSignedAt(new \DateTime($row['signed_at']));
        $signedTerm->setCreatedAt(new \DateTime($row['created_at']));
        $signedTerm->setUpdatedAt(new \DateTime($row['updated_at']));

        return $signedTerm;
    }
}
