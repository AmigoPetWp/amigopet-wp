<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Services;

if (!defined('ABSPATH')) {
    exit;
}

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
    ): int {
        $organization = new Organization(
            $name,
            $email,
            $phone,
            $address
        );

        if ($website) {
            $organization->setWebsite($website);
        }

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
        ?string $website = null
    ): bool {
        $organization = $this->repository->findById($id);
        if (!$organization) {
            return false;
        }

        $organization = new Organization(
            $name,
            $email,
            $phone,
            $address
        );
        $organization->setId($id);

        if ($website) {
            $organization->setWebsite($website);
        }

        return $this->repository->save($organization) > 0;
    }

    /**
     * Ativa uma organização
     */
    public function activateOrganization(int $id): bool {
        $organization = $this->repository->findById($id);
        if (!$organization) {
            return false;
        }

        $organization->setStatus('active');
        return $this->repository->save($organization) > 0;
    }

    /**
     * Desativa uma organização
     */
    public function deactivateOrganization(int $id): bool {
        $organization = $this->repository->findById($id);
        if (!$organization) {
            return false;
        }

        $organization->setStatus('inactive');
        return $this->repository->save($organization) > 0;
    }

    /**
     * Busca uma organização por ID
     */
    public function findById(int $id): ?Organization {
        return $this->repository->findById($id);
    }

    /**
     * Busca uma organização por email
     */
    public function findByEmail(string $email): ?Organization {
        return $this->repository->findByEmail($email);
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
     * Busca organizações por termo
     */
    public function search(string $term): array {
        return $this->repository->search($term);
    }

    /**
     * Gera relatório de organizações
     */
    public function getReport(?string $startDate = null, ?string $endDate = null): array {
        return $this->repository->getReport($startDate, $endDate);
    }

    /**
     * Busca organizações por filtros
     */
    public function findByFilters(array $filters): array {
        return $this->repository->findByFilters($filters);
    }

    /**
     * Verifica se uma organização existe por email
     */
    public function existsByEmail(string $email): bool {
        return $this->findByEmail($email) !== null;
    }

    /**
     * Deleta uma organização
     */
    public function deleteOrganization(int $id): bool {
        $organization = $this->repository->findById($id);
        if (!$organization) {
            return false;
        }

        return $this->repository->delete($id);
    }
}