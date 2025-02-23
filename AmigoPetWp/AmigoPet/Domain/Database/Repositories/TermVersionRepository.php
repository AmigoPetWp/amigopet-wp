<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\TermVersion;

class TermVersionRepository extends AbstractRepository {
    protected function getTableName(): string {
        return 'apwp_term_versions';
    }

    protected function createEntity(array $data): TermVersion {
        $version = new TermVersion(
            (int)$data['term_id'],
            $data['content'],
            $data['version'],
            $data['change_log'],
            (int)$data['created_by']
        );

        if (isset($data['id'])) {
            $version->setId((int)$data['id']);
        }

        if (isset($data['status'])) {
            $version->setStatus($data['status']);
        }

        if (isset($data['effective_date'])) {
            $version->setEffectiveDate(new \DateTime($data['effective_date']));
        }

        if (isset($data['created_at'])) {
            $version->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at'])) {
            $version->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $version;
    }

    protected function toDatabase($entity): array {
        if (!$entity instanceof TermVersion) {
            throw new \InvalidArgumentException('Entity must be an instance of TermVersion');
        }

        return [
            'term_id' => $entity->getTermId(),
            'content' => $entity->getContent(),
            'version' => $entity->getVersion(),
            'status' => $entity->getStatus(),
            'change_log' => $entity->getChangeLog(),
            'created_by' => $entity->getCreatedBy(),
            'effective_date' => $entity->getEffectiveDate()->format('Y-m-d H:i:s'),
            'created_at' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()->format('Y-m-d H:i:s')
        ];
    }

    public function save(TermVersion $version): int {
        $data = [
            'term_id' => $version->getTermId(),
            'content' => $version->getContent(),
            'version' => $version->getVersion(),
            'status' => $version->getStatus(),
            'change_log' => $version->getChangeLog(),
            'created_by' => $version->getCreatedBy(),
            'effective_date' => $version->getEffectiveDate()->format('Y-m-d H:i:s'),
            'created_at' => $version->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $version->getUpdatedAt()->format('Y-m-d H:i:s')
        ];

        $format = [
            '%d', // term_id
            '%s', // content
            '%s', // version
            '%s', // status
            '%s', // change_log
            '%d', // created_by
            '%s', // effective_date
            '%s', // created_at
            '%s'  // updated_at
        ];

        if ($version->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $version->getId()],
                $format,
                ['%d']
            );
            return $version->getId();
        }

        $this->wpdb->insert($this->table, $data, $format);
        return $this->wpdb->insert_id;
    }

    public function findById(int $id): ?TermVersion {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        return $row ? $this->createFromRow($row) : null;
    }

    public function findByTerm(int $termId): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE term_id = %d ORDER BY version DESC",
                $termId
            ),
            ARRAY_A
        );

        return array_map([$this, 'createFromRow'], $rows);
    }

    public function findActiveVersion(int $termId): ?TermVersion {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE term_id = %d AND status = 'active' ORDER BY effective_date DESC LIMIT 1",
                $termId
            ),
            ARRAY_A
        );

        return $row ? $this->createFromRow($row) : null;
    }

    public function findByVersion(int $termId, string $version): ?TermVersion {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE term_id = %d AND version = %s",
                $termId,
                $version
            ),
            ARRAY_A
        );

        return $row ? $this->createFromRow($row) : null;
    }

    public function findByStatus(string $status): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE status = %s ORDER BY updated_at DESC",
                $status
            ),
            ARRAY_A
        );

        return array_map([$this, 'createFromRow'], $rows);
    }

    public function findPendingReview(): array {
        return $this->findByStatus('review');
    }

    public function findEffectiveAt(\DateTimeInterface $date): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE status = 'active' AND effective_date <= %s ORDER BY effective_date DESC",
                $date->format('Y-m-d H:i:s')
            ),
            ARRAY_A
        );

        return array_map([$this, 'createFromRow'], $rows);
    }

    public function getLatestVersion(int $termId): ?string {
        return $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT version FROM {$this->table} WHERE term_id = %d ORDER BY version DESC LIMIT 1",
                $termId
            )
        );
    }

    public function deactivateAllVersions(int $termId): bool {
        return $this->wpdb->update(
            $this->table,
            ['status' => 'inactive'],
            ['term_id' => $termId, 'status' => 'active'],
            ['%s'],
            ['%d', '%s']
        ) !== false;
    }

    public function delete(int $id): bool {
        $version = $this->findById($id);
        if (!$version) {
            return false;
        }

        if (!$version->isDraft()) {
            throw new \InvalidArgumentException("Apenas versões em rascunho podem ser excluídas");
        }

        return $this->wpdb->delete(
            $this->table,
            ['id' => $id],
            ['%d']
        ) !== false;
    }

    private function createFromRow(array $row): TermVersion {
        return new TermVersion(
            (int)$row['term_id'],
            $row['content'],
            $row['version'],
            $row['status'],
            new \DateTime($row['effective_date']),
            $row['change_log'],
            (int)$row['id']
        );
    }
}
