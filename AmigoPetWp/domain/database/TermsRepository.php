<?php
namespace Domain\Database;

use Domain\Entities\Terms;

class TermsRepository {
    private $wpdb;
    private $table;
    
    public function __construct(\wpdb $wpdb) {
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'amigopet_terms';
    }
    
    public function save(Terms $terms): int {
        $data = [
            'document_type' => $terms->getDocumentType(),
            'document_url' => $terms->getDocumentUrl(),
            'signed_by' => $terms->getSignedBy(),
            'signed_at' => $terms->getSignedAt()->format('Y-m-d H:i:s'),
            'status' => $terms->getStatus()
        ];

        if ($terms->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $terms->getId()]
            );
            return $terms->getId();
        }

        $this->wpdb->insert($this->table, $data);
        return $this->wpdb->insert_id;
    }

    public function findById(int $id): ?Terms {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findByType(string $documentType): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE document_type = %s ORDER BY signed_at DESC",
                $documentType
            ),
            ARRAY_A
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findBySignedBy(string $signedBy): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE signed_by = %s ORDER BY signed_at DESC",
                $signedBy
            ),
            ARRAY_A
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    private function hydrate(array $row): Terms {
        $terms = new Terms(
            $row['document_type'],
            $row['document_url'],
            $row['signed_by'],
            new \DateTimeImmutable($row['signed_at'])
        );

        $reflection = new \ReflectionClass($terms);
        
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($terms, (int) $row['id']);

        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($terms, $row['status']);

        return $terms;
    }
}
