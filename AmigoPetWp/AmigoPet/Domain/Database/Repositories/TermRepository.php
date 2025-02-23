<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\Term;

/**
 * Repositório para gerenciar termos e condições
 * 
 * @package AmigoPetWp\Domain\Database\Repositories
 */
class TermRepository extends AbstractRepository {
    /**
     * {@inheritDoc}
     */
    protected function getTableName(): string {
        return 'apwp_terms';
    }

    /**
     * {@inheritDoc}
     */
    protected function createEntity(array $data): Term {
        $term = new Term();
        $term->setId((int)$data['id']);
        $term->setOrganizationId((int)$data['organization_id']);
        $term->setTitle($data['title']);
        $term->setContent($data['content']);
        $term->setType($data['type']);
        $term->setRequired((bool)$data['is_required']);
        $term->setActive((bool)$data['is_active']);
        $term->setAcceptedBy(maybe_unserialize($data['accepted_by']));
        $term->setCreatedAt(new \DateTime($data['created_at']));
        $term->setUpdatedAt(new \DateTime($data['updated_at']));
        return $term;
    }

    /**
     * {@inheritDoc}
     */
    protected function toDatabase($entity): array {
        if (!$entity instanceof Term) {
            throw new \InvalidArgumentException('Entity must be an instance of Term');
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
    public function findByType(string $type): array {
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
    public function findByOrganization(int $organizationId): array {
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
    public function findActiveByType(string $type): array {
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
    public function findAcceptedByUser(int $userId): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE accepted_by LIKE %s",
                '%"' . $userId . '"%'
            ),
            ARRAY_A
        );

        return array_map([$this, 'createEntity'], $rows);
    }
}
