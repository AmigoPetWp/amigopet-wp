<?php
namespace Domain\Services;

use Domain\Database\OrganizationRepository;
use Domain\Entities\Organization;

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
        string $city,
        string $state,
        string $zipCode
    ): int {
        $organization = new Organization(
            $name,
            $email,
            $phone,
            $address,
            $city,
            $state,
            $zipCode
        );

        return $this->repository->save($organization);
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
        string $city,
        string $state,
        string $zipCode
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
            $city,
            $state,
            $zipCode
        );

        $this->repository->save($organization);
    }

    /**
     * Busca uma organização por ID
     */
    public function findById(int $id): ?Organization {
        return $this->repository->findById($id);
    }

    /**
     * Lista todas as organizações
     */
    public function findAll(): array {
        return $this->repository->findAll();
    }
}
