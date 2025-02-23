<?php

namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\TemplateTermsRepository;
use AmigoPetWp\Domain\Entities\TemplateTerm;

class TemplateTermsService
{
    private TemplateTermsRepository $repository;

    public function __construct(TemplateTermsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function save(TemplateTerm $template): void
    {
        $this->repository->save($template);
    }

    /**
     * Obtém um termo com os placeholders substituídos pelos dados reais
     *
     * @param string $type Tipo do termo
     * @param array $data Dados para substituir os placeholders
     * @return string|null
     */
    public function getProcessedTerm(string $type, array $data): ?string
    {
        $templates = $this->repository->findActiveByType($type);
        if (empty($templates)) {
            return null;
        }

        // Pega o template mais recente (primeiro da lista, já que está ordenado por data)
        $template = $templates[0];
        return $this->replacePlaceholders($template->getContent(), $data);
    }

    /**
     * Obtém todos os templates de termos
     *
     * @return array Array associativo [tipo => array de templates]
     */
    public function getAllTemplates(): array
    {
        $templates = $this->repository->findAll(['orderby' => 'type,created_at', 'order' => 'ASC']);
        $grouped = [];
        
        foreach ($templates as $template) {
            $type = $template->getType();
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $template;
        }

        return $grouped;
    }

    /**
     * Salva um novo template
     *
     * @param string $type Tipo do termo
     * @param string $title Título do termo
     * @param string $content Conteúdo do template
     * @param string|null $description Descrição opcional
     * @return int ID do template salvo
     */
    public function saveTemplate(string $type, string $title, string $content, ?string $description = null): int
    {
        $template = new TemplateTerm($type, $content, $title);
        if ($description) {
            $template->setDescription($description);
        }
        $template->setCreatedAt(new \DateTime());
        $template->setUpdatedAt(new \DateTime());

        return $this->repository->save($template);
    }

    /**
     * Atualiza um template existente
     *
     * @param int $id ID do template
     * @param array $data Dados para atualizar
     * @return bool
     */
    public function updateTemplate(int $id, array $data): bool
    {
        $template = $this->repository->findById($id);
        if (!$template) {
            return false;
        }

        if (isset($data['title'])) {
            $template->setTitle($data['title']);
        }

        if (isset($data['content'])) {
            $template->setContent($data['content']);
        }

        if (isset($data['description'])) {
            $template->setDescription($data['description']);
        }

        if (isset($data['is_active'])) {
            $template->setActive($data['is_active']);
        }

        $template->setUpdatedAt(new \DateTime());

        return $this->repository->save($template) > 0;
    }

    /**
     * Substitui os placeholders pelos valores reais
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    private function replacePlaceholders(string $template, array $data): string
    {
        $placeholders = array_map(function ($key) {
            return '{' . $key . '}';
        }, array_keys($data));

        $values = array_values($data);

        return str_replace($placeholders, $values, $template);
    }

    /**
     * Obtém a lista de placeholders disponíveis para cada tipo de termo
     *
     * @return array
     */
    public function getAvailablePlaceholders(): array
    {
        return [
            'adoption' => [
                'organization_name' => 'Nome da organização',
                'organization_email' => 'Email da organização',
                'organization_phone' => 'Telefone da organização',
                'organization_address' => 'Endereço da organização',
                'adopter_name' => 'Nome do adotante',
                'adopter_email' => 'Email do adotante',
                'adopter_phone' => 'Telefone do adotante',
                'adopter_address' => 'Endereço do adotante',
                'adopter_document' => 'Documento do adotante',
                'pet_name' => 'Nome do pet',
                'pet_type' => 'Tipo do pet (cão/gato)',
                'pet_breed' => 'Raça do pet',
                'current_date' => 'Data atual'
            ],
            'volunteer' => [
                'organization_name' => 'Nome da organização',
                'organization_email' => 'Email da organização',
                'organization_phone' => 'Telefone da organização',
                'organization_address' => 'Endereço da organização',
                'volunteer_name' => 'Nome do voluntário',
                'volunteer_email' => 'Email do voluntário',
                'volunteer_phone' => 'Telefone do voluntário',
                'volunteer_address' => 'Endereço do voluntário',
                'volunteer_document' => 'Documento do voluntário',
                'current_date' => 'Data atual'
            ],
            'donation' => [
                'organization_name' => 'Nome da organização',
                'organization_email' => 'Email da organização',
                'organization_phone' => 'Telefone da organização',
                'organization_address' => 'Endereço da organização',
                'donor_name' => 'Nome do doador',
                'donor_email' => 'Email do doador',
                'donor_phone' => 'Telefone do doador',
                'donor_address' => 'Endereço do doador',
                'donor_document' => 'Documento do doador',
                'donation_amount' => 'Valor da doação',
                'donation_type' => 'Tipo de doação',
                'current_date' => 'Data atual'
            ]
        ];
    }
}
