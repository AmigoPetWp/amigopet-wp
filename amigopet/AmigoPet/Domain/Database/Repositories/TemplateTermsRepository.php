<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\TemplateTerm;

class TemplateTermsRepository extends AbstractRepository
{
    protected function initTable(): void
    {
        $this->table = $this->getTableName('template_terms');
    }

    protected function createEntity(array $row): object
    {
        $template = new TemplateTerm(
            $row['type'],
            $row['content'],
            $row['title']
        );

        if (isset($row['id'])) {
            $template->setId((int) $row['id']);
        }

        if (isset($row['description'])) {
            $template->setDescription($row['description']);
        }

        if (isset($row['is_active'])) {
            $template->setActive((bool) $row['is_active']);
        }

        if (isset($row['created_at'])) {
            $template->setCreatedAt(new \DateTime($row['created_at']));
        }

        if (isset($row['updated_at'])) {
            $template->setUpdatedAt(new \DateTime($row['updated_at']));
        }

        return $template;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof TemplateTerm) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of TemplateTerm', 'amigopet'));
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