<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\AdoptionDocument;

class AdoptionDocumentRepository extends AbstractRepository
{


    protected function initTable(): void
    {
        $this->table = $this->getTableName('adoption_documents');
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof AdoptionDocument) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of AdoptionDocument', 'amigopet'));
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

    protected function createEntity(array $row): object
    {
        $document = new AdoptionDocument(
            (int) $row['adoption_id'],
            $row['document_type'],
            $row['content'],
            (int) $row['signed_by'],
            $row['document_url']
        );

        if (isset($row['id'])) {
            $document->setId((int) $row['id']);
        }

        if (isset($row['status'])) {
            $document->setStatus($row['status']);
        }

        if (isset($row['signed_at'])) {
            $document->setSignedAt(new \DateTime($row['signed_at']));
        }

        if (isset($row['created_at'])) {
            $document->setCreatedAt(new \DateTime($row['created_at']));
        }

        if (isset($row['updated_at'])) {
            $document->setUpdatedAt(new \DateTime($row['updated_at']));
        }

        return $document;
    }

    public function findByAdoption(int $adoptionId): array
    {
        return $this->findAll([
            'adoption_id' => $adoptionId,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    public function findByType(string $documentType): array
    {
        return $this->findAll([
            'document_type' => $documentType,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    public function findBySignedBy(int $signedBy): array
    {
        return $this->findAll([
            'signed_by' => $signedBy,
            'orderby' => 'signed_at',
            'order' => 'DESC'
        ]);
    }

    public function getDocumentReport(?string $startDate = null, ?string $endDate = null): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $start = ($startDate ?: '1970-01-01') . ' 00:00:00';
        $end = ($endDate ?: '9999-12-31') . ' 23:59:59';
        return $wpdb->get_row(
            $wpdb->prepare(
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
                 FROM %i
                 WHERE created_at BETWEEN %s AND %s
                 GROUP BY document_type
                 ORDER BY COUNT(*) DESC
                 LIMIT 1) as most_common_type
            FROM %i
            WHERE created_at BETWEEN %s AND %s",
                $table,
                $table,
                $start,
                $end,
                $start,
                $end
            ),
            ARRAY_A
        );
    }

    public function findByStatus(string $status): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE status = %s ORDER BY created_at DESC',
                $table,
                $status
            ),
            ARRAY_A
        );

        return array_map([$this, 'createFromRow'], $rows);
    }

    public function delete(int $id): bool
    {
        return $this->wpdb->delete(
            $this->table,
            ['id' => $id],
            ['%d']
        ) !== false;
    }

    private function createFromRow(array $row): AdoptionDocument
    {
        return new AdoptionDocument(
            (int) $row['adoption_id'],
            $row['document_type'],
            $row['content'],
            (int) $row['signed_by'],
            $row['document_url'],
            $row['status'],
            (int) $row['id']
        );
    }
}