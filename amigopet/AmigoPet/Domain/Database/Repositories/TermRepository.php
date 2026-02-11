<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\Term;

/**
 * Repositório para gerenciar termos e condições
 * 
 * @package AmigoPetWp\Domain\Database\Repositories
 */
class TermRepository extends AbstractRepository
{
    /**
     * {@inheritDoc}
     */
    protected function initTable(): void
    {
        $this->table = $this->getTableName('terms');
    }

    /**
     * {@inheritDoc}
     */
    protected function createEntity(array $row): object
    {
        $term = new Term();
        $term->setId((int) $row['id']);
        $term->setOrganizationId((int) $row['organization_id']);
        $term->setTitle($row['title']);
        $term->setContent($row['content']);
        $term->setType($row['type']);
        $term->setRequired((bool) $row['is_required']);
        $term->setActive((bool) $row['is_active']);
        $term->setAcceptedBy(maybe_unserialize($row['accepted_by']));
        $term->setCreatedAt(new \DateTime($row['created_at']));
        $term->setUpdatedAt(new \DateTime($row['updated_at']));
        return $term;
    }

    /**
     * {@inheritDoc}
     */
    protected function toDatabase($entity): array
    {
        if (!$entity instanceof Term) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of Term', 'amigopet'));
        }

        return [
            'organization_id' => $entity->getOrganizationId(),
            'title' => $entity->getTitle(),
            'content' => $entity->getContent(),
            'type' => $entity->getType(),
            'is_required' => $entity->isRequired(),
            'is_active' => $entity->isActive(),
            'accepted_by' => maybe_serialize($entity->getAcceptedBy()),
            'created_at' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Encontra termos por tipo
     *
     * @param string $type Tipo do termo
     * @return array Lista de termos do tipo especificado
     */
    public function findByType(string $type): array
    {
        return $this->findAll([
            'type' => $type,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    /**
     * Encontra termos por organização
     *
     * @param int $organizationId ID da organização
     * @return array Lista de termos da organização
     */
    public function findByOrganization(int $organizationId): array
    {
        return $this->findAll([
            'organization_id' => $organizationId,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    /**
     * Encontra termos ativos por tipo
     *
     * @param string $type Tipo do termo
     * @return array Lista de termos ativos do tipo especificado
     */
    public function findActiveByType(string $type): array
    {
        return $this->findAll([
            'type' => $type,
            'is_active' => 1,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    /**
     * Encontra termos aceitos por um usuário específico
     *
     * @param int $userId ID do usuário
     * @return array Lista de termos aceitos pelo usuário
     */
    public function findAcceptedByUser(int $userId): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE accepted_by LIKE %s',
                $table,
                '%"' . $userId . '"%'
            ),
            ARRAY_A
        );

        return array_map([$this, 'createEntity'], $rows);
    }
}