<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\TermRepository;
use AmigoPetWp\Domain\Entities\Term;

class TermService {
    private $repository;

    public function __construct(TermRepository $repository) {
        $this->repository = $repository;
    }

    public function createTerm(array $data): int {
        $term = new Term(
            $data['organization_id'],
            $data['title'],
            $data['content'],
            $data['type'],
            $data['is_required'] ?? true,
            $data['is_active'] ?? true
        );

        return $this->repository->save($term);
    }

    public function updateTerm(int $id, array $data): void {
        $term = $this->repository->findById($id);
        if (!$term) {
            throw new \InvalidArgumentException("Termo não encontrado");
        }

        if (isset($data['title'])) {
            $term->setTitle($data['title']);
        }

        if (isset($data['content'])) {
            $term->setContent($data['content']);
        }

        if (isset($data['type'])) {
            $term->setType($data['type']);
        }

        if (isset($data['is_required'])) {
            $term->setIsRequired($data['is_required']);
        }

        if (isset($data['is_active'])) {
            $term->setIsActive($data['is_active']);
        }

        $this->repository->save($term);
    }

    public function deleteTerm(int $id): void {
        $term = $this->repository->findById($id);
        if (!$term) {
            throw new \InvalidArgumentException("Termo não encontrado");
        }

        if (!$this->repository->delete($id)) {
            throw new \RuntimeException("Erro ao excluir o termo");
        }
    }

    public function findById(int $id): ?Term {
        return $this->repository->findById($id);
    }

    public function findByType(string $type): array {
        return $this->repository->findByType($type);
    }

    public function findActiveByType(string $type): array {
        return $this->repository->findActiveByType($type);
    }

    public function findByOrganization(int $organizationId): array {
        return $this->repository->findByOrganization($organizationId);
    }

    public function acceptTerm(int $termId, int $userId): void {
        $term = $this->repository->findById($termId);
        if (!$term) {
            throw new \InvalidArgumentException("Termo não encontrado");
        }

        if ($term->wasAcceptedBy($userId)) {
            throw new \InvalidArgumentException("Termo já foi aceito por este usuário");
        }

        $term->addAcceptedBy($userId);
        $this->repository->save($term);
    }

    public function revokeAcceptance(int $termId, int $userId): void {
        $term = $this->repository->findById($termId);
        if (!$term) {
            throw new \InvalidArgumentException("Termo não encontrado");
        }

        if (!$term->wasAcceptedBy($userId)) {
            throw new \InvalidArgumentException("Termo não foi aceito por este usuário");
        }

        $term->removeAcceptedBy($userId);
        $this->repository->save($term);
    }

    public function getAcceptedTerms(int $userId): array {
        return $this->repository->findAcceptedByUser($userId);
    }

    public function activateTerm(int $id): void {
        $term = $this->repository->findById($id);
        if (!$term) {
            throw new \InvalidArgumentException("Termo não encontrado");
        }

        $term->setIsActive(true);
        $this->repository->save($term);
    }

    public function deactivateTerm(int $id): void {
        $term = $this->repository->findById($id);
        if (!$term) {
            throw new \InvalidArgumentException("Termo não encontrado");
        }

        $term->setIsActive(false);
        $this->repository->save($term);
    }

    public function getTypes(): array {
        return [
            'adoption' => __('Adoção', 'amigopet-wp'),
            'volunteer' => __('Voluntário', 'amigopet-wp'),
            'donation' => __('Doação', 'amigopet-wp')
        ];
    }
}
