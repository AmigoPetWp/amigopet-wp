<?php

namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\TemplateTerm;

class TemplateTermsRepository extends AbstractRepository
{
    protected function getTableName(): string
    {
        return 'apwp_template_terms';
    }

    protected function createEntity(array $data): TemplateTerm
    {
        $template = new TemplateTerm(
            $data['type'],
            $data['content'],
            $data['title']
        );

        if (isset($data['id'])) {
            $template->setId((int)$data['id']);
        }

        if (isset($data['description'])) {
            $template->setDescription($data['description']);
        }

        if (isset($data['is_active'])) {
            $template->setActive((bool)$data['is_active']);
        }

        if (isset($data['created_at'])) {
            $template->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at'])) {
            $template->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $template;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof TemplateTerm) {
            throw new \InvalidArgumentException('Entity must be an instance of TemplateTerm');
        }

        $data = [
            'type' => $entity->getType(),
            'content' => $entity->getContent(),
            'title' => $entity->getTitle(),
            'is_active' => $entity->isActive()
        ];

        if ($entity->getDescription()) {
            $data['description'] = $entity->getDescription();
        }

        if ($entity->getCreatedAt()) {
            $data['created_at'] = $entity->getCreatedAt()->format('Y-m-d H:i:s');
        }

        if ($entity->getUpdatedAt()) {
            $data['updated_at'] = $entity->getUpdatedAt()->format('Y-m-d H:i:s');
        }

        return $data;
    }

    /**
     * Encontra templates por tipo
     *
     * @param string $type Tipo do template
     * @return array Lista de templates do tipo especificado
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
     * Encontra templates ativos por tipo
     *
     * @param string $type Tipo do template
     * @return array Lista de templates ativos do tipo especificado
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
}
