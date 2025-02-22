<?php
namespace AmigoPetWp\Domain\Database;

use AmigoPetWp\Domain\Entities\AdoptionDocument;

class AdoptionDocumentRepository {
    private $wpdb;
    private $table;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'apwp_adoption_documents';
    }

    public function save(AdoptionDocument $document): int {
        $data = [
            'adoption_id' => $document->getAdoptionId(),
            'document_type' => $document->getDocumentType(),
            'content' => $document->getContent(),
            'signed_by' => $document->getSignedBy(),
            'signed_at' => $document->getSignedAt()->format('Y-m-d H:i:s'),
            'document_url' => $document->getDocumentUrl(),
            'status' => $document->getStatus(),
            'created_at' => $document->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $document->getUpdatedAt()->format('Y-m-d H:i:s')
        ];

        $format = [
            '%d', // adoption_id
            '%s', // document_type
            '%s', // content
            '%d', // signed_by
            '%s', // signed_at
            '%s', // document_url
            '%s', // status
            '%s', // created_at
            '%s'  // updated_at
        ];

        if ($document->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $document->getId()],
                $format,
                ['%d']
            );
            return $document->getId();
        }

        $this->wpdb->insert($this->table, $data, $format);
        return $this->wpdb->insert_id;
    }

    public function findById(int $id): ?AdoptionDocument {
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

        return $this->createFromRow($row);
    }

    public function findByAdoption(int $adoptionId): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE adoption_id = %d ORDER BY created_at DESC",
                $adoptionId
            ),
            ARRAY_A
        );

        return array_map([$this, 'createFromRow'], $rows);
    }

    public function findByType(string $documentType): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE document_type = %s ORDER BY created_at DESC",
                $documentType
            ),
            ARRAY_A
        );

        return array_map([$this, 'createFromRow'], $rows);
    }

    public function findBySignedBy(int $signedBy): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE signed_by = %d ORDER BY signed_at DESC",
                $signedBy
            ),
            ARRAY_A
        );

        return array_map([$this, 'createFromRow'], $rows);
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
