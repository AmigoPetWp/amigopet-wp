<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\TermVersion;

class TermVersionRepository extends AbstractRepository
{
    protected function initTable(): void
    {
        $this->table = $this->getTableName('term_versions');
    }

    protected function createEntity(array $row): object
    {
        $version = new TermVersion(
            (int) $row['term_id'],
            $row['content'],
            $row['version'],
            $row['status'],
            new \DateTime($row['effective_date']),
            $row['change_log'] ?? null,
            (int) $row['id']
        );

        if (isset($row['created_by'])) {
            $version->setCreatedBy((int) $row['created_by']);
        }

        if (isset($row['created_at'])) {
            $version->setCreatedAt(new \DateTime($row['created_at']));
        }

        if (isset($row['updated_at'])) {
            $version->setUpdatedAt(new \DateTime($row['updated_at']));
        }

        return $version;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof TermVersion) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of TermVersion', 'amigopet'));
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

    public function findByTerm(int $termId): array
    {
        return $this->findAll([
            'term_id' => $termId,
            'orderby' => 'version',
            'order' => 'DESC'
        ]);
    }

    public function findActiveVersion(int $termId): ?TermVersion
    {
        $results = $this->findAll([
            'term_id' => $termId,
            'status' => 'active',
            'orderby' => 'effective_date',
            'order' => 'DESC',
            'limit' => 1
        ]);

        return !empty($results) ? $results[0] : null;
    }

    public function findByVersion(int $termId, string $version): ?TermVersion
    {
        $results = $this->findAll([
            'term_id' => $termId,
            'version' => $version,
            'limit' => 1
        ]);

        return !empty($results) ? $results[0] : null;
    }

    public function findByStatus(string $status): array
    {
        return $this->findAll([
            'status' => $status,
            'orderby' => 'updated_at',
            'order' => 'DESC'
        ]);
    }

    public function findPendingReview(): array
    {
        return $this->findByStatus('review');
    }

    public function findEffectiveAt(\DateTimeInterface $date): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE status = %s AND effective_date <= %s ORDER BY effective_date DESC',
                $table,
                'active',
                $date->format('Y-m-d H:i:s')
            ),
            ARRAY_A
        );

        return array_map([$this, 'createEntity'], $rows);
    }

    public function getLatestVersion(int $termId): ?string
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return null;
        }
        return $wpdb->get_var(
            $wpdb->prepare(
                'SELECT version FROM %i WHERE term_id = %d ORDER BY version DESC LIMIT 1',
                $table,
                $termId
            )
        );
    }

    public function deactivateAllVersions(int $termId): bool
    {
        return $this->wpdb->update(
            $this->table,
            ['status' => 'inactive'],
            ['term_id' => $termId, 'status' => 'active'],
            ['%s'],
            ['%d', '%s']
        ) !== false;
    }

    public function delete(int $id): bool
    {
        $version = $this->findById($id);
        if (!$version) {
            return false;
        }

        if (!$version->isDraft()) {
            throw new \InvalidArgumentException(esc_html__("Apenas versões em rascunho podem ser excluídas", 'amigopet'));
        }

        return parent::delete($id);
    }
}