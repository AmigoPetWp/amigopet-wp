<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\OrganizationRepository;
use AmigoPetWp\Domain\Entities\Organization;

class OrganizationService {
    private $repository;

    public function __construct(OrganizationRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Cria uma nova organização
     */
    public function createOrganization(
        string $name,
        string $email,
        string $phone,
        string $address,
        ?string $website = null
    ): Organization {
        $organization = new Organization(
            $name,
            $email,
            $phone,
            $address,
            $website
        );

        $this->repository->save($organization);
        return $organization;
    }

    /**
     * Atualiza uma organização existente
     */
    public function updateOrganization(
        int $id,
        string $name,
        string $email,
        string $phone,
        string $address,
        ?string $website = null
    ): void {
        $organization = $this->repository->findById($id);
        if (!$organization) {
            throw new \InvalidArgumentException("Organização não encontrada");
        }

        $organization = new Organization(
            $name,
            $email,
            $phone,
            $address,
            $website
        );
        $organization->setId($id);

        $this->repository->save($organization);
    }

    /**
     * Ativa uma organização
     */
    public function activateOrganization(int $id): void {
        $organization = $this->repository->findById($id);
        if (!$organization) {
            throw new \InvalidArgumentException("Organização não encontrada");
        }

        $organization->activate();
        $this->repository->save($organization);
    }

    /**
     * Desativa uma organização
     */
    public function deactivateOrganization(int $id): void {
        $organization = $this->repository->findById($id);
        if (!$organization) {
            throw new \InvalidArgumentException("Organização não encontrada");
        }

        $organization->deactivate();
        $this->repository->save($organization);
    }

    /**
     * Busca uma organização por ID
     */
    public function findById(int $id): ?Organization {
        return $this->repository->findById($id);
    }

    /**
     * Lista organizações por status
     */
    public function findByStatus(string $status): array {
        return $this->repository->findByStatus($status);
    }

    /**
     * Lista organizações ativas
     */
    public function findActive(): array {
        return $this->repository->findActive();
    }

    /**
     * Lista organizações inativas
     */
    public function findInactive(): array {
        return $this->repository->findInactive();
    }

    /**
     * Lista organizações pendentes
     */
    public function findPending(): array {
        return $this->repository->findPending();
    }

    /**
     * Lista todas as organizações
     */
    public function findAll(): array {
        return $this->repository->findAll();
    }

    /**
     * Obtém relatório de organizações
     */
    public function getReport(): array {
        return $this->repository->getReport();
    }
}
