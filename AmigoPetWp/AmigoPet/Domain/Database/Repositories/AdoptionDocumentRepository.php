<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\AdoptionDocument;

class AdoptionDocumentRepository extends AbstractRepository {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
    }

    protected function getTableName(): string {
        return 'apwp_adoption_documents';
    }

    protected function toDatabase($entity): array {
        if (!$entity instanceof AdoptionDocument) {
            throw new \InvalidArgumentException('Entity must be an instance of AdoptionDocument');
        }

        return [
            'adoption_id' => $entity->getAdoptionId(),
            'document_type' => $entity->getDocumentType(),
            'content' => $entity->getContent(),
            'signed_by' => $entity->getSignedBy(),
            'signed_at' => $entity->getSignedAt()?->format('Y-m-d H:i:s'),
            'document_url' => $entity->getDocumentUrl(),
            'status' => $entity->getStatus(),
            'created_at' => $entity->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()?->format('Y-m-d H:i:s')
        ];
    }

    protected function createEntity(array $data): AdoptionDocument {
        $document = new AdoptionDocument(
            (int)$data['adoption_id'],
            $data['document_type'],
            $data['content'],
            (int)$data['signed_by'],
            $data['document_url']
        );

        if (isset($data['id'])) {
            $document->setId((int)$data['id']);
        }

        if (isset($data['status'])) {
            $document->setStatus($data['status']);
        }

        if (isset($data['signed_at'])) {
            $document->setSignedAt(new \DateTime($data['signed_at']));
        }

        if (isset($data['created_at'])) {
            $document->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at'])) {
            $document->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $document;
    }

    public function findByAdoption(int $adoptionId): array {
        return $this->findAll([
            'adoption_id' => $adoptionId,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    public function findByType(string $documentType): array {
        return $this->findAll([
            'document_type' => $documentType,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    public function findBySignedBy(int $signedBy): array {
        return $this->findAll([
            'signed_by' => $signedBy,
            'orderby' => 'signed_at',
            'order' => 'DESC'
        ]);
    }

    public function getDocumentReport(?string $startDate = null, ?string $endDate = null): array {
        $where = ['1=1'];
        $params = [];

        if ($startDate && $endDate) {
            $where[] = 'created_at BETWEEN %s AND %s';
            $params[] = $startDate . ' 00:00:00';
            $params[] = $endDate . ' 23:59:59';
        }

        $whereClause = implode(' AND ', $where);

        $query = $this->wpdb->prepare(
            "SELECT 
                COUNT(*) as total_documents,
                COUNT(DISTINCT document_type) as document_types,
                COUNT(DISTINCT adoption_id) as adoptions_with_documents,
                COUNT(DISTINCT signed_by) as unique_signers,
                COUNT(CASE WHEN status = 'signed' THEN 1 END) as signed_documents,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_documents,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_documents,
                AVG(CASE WHEN signed_at IS NOT NULL 
                    THEN TIMESTAMPDIFF(HOUR, created_at, signed_at)
                    ELSE NULL END) as avg_signing_hours,
                (SELECT document_type
                 FROM {$this->table} 
                 WHERE {$whereClause}
                 GROUP BY document_type
                 ORDER BY COUNT(*) DESC
                 LIMIT 1) as most_common_type
            FROM {$this->table}
            WHERE {$whereClause}",
            array_merge($params, $params)
        );

        return $this->wpdb->get_row($query, ARRAY_A);
    }

    public function findByStatus(string $status): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE status = %s ORDER BY created_at DESC",
                $status
            ),
            ARRAY_A
        );

        return array_map([$this, 'createFromRow'], $rows);
    }

    public function delete(int $id): bool {
        return $this->wpdb->delete(
            $this->table,
            ['id' => $id],
            ['%d']
        ) !== false;
    }

    private function createFromRow(array $row): AdoptionDocument {
        return new AdoptionDocument(
            (int)$row['adoption_id'],
            $row['document_type'],
            $row['content'],
            (int)$row['signed_by'],
            $row['document_url'],
            $row['status'],
            (int)$row['id']
        );
    }
}
