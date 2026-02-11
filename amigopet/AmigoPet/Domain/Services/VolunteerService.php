<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Services;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Repositories\VolunteerRepository;
use AmigoPetWp\Domain\Entities\Volunteer;

class VolunteerService {
    private $repository;

    public function __construct(VolunteerRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Cria um novo voluntário
     */
    public function createVolunteer(
        int $organizationId,
        string $name,
        string $email,
        string $phone,
        string $availability,
        string $skills
    ): int {
        $volunteer = new Volunteer(
            $organizationId,
            $name,
            $email,
            $phone,
            $availability,
            $skills
        );

        return $this->repository->save($volunteer);
    }

    /**
     * Atualiza um voluntário existente
     */
    public function updateVolunteer(
        int $id,
        string $name,
        string $email,
        string $phone,
        string $availability,
        string $skills
    ): bool {
        $volunteer = $this->repository->findById($id);
        if (!$volunteer) {
            return false;
        }

        $volunteer->setName($name);
        $volunteer->setEmail($email);
        $volunteer->setPhone($phone);
        $volunteer->setAvailability($availability);
        $volunteer->setSkills($skills);

        return $this->repository->save($volunteer) > 0;
    }

    /**
     * Busca um voluntário por ID
     */
    public function findById(int $id): ?Volunteer {
        return $this->repository->findById($id);
    }

    /**
     * Lista voluntários por critérios
     */
    public function findVolunteers(array $criteria = []): array {
        return $this->repository->findAll($criteria);
    }

    /**
     * Busca voluntários por termo
     */
    public function searchVolunteers(string $term): array {
        return $this->repository->search($term);
    }

    /**
     * Marca um voluntário como ativo
     */
    public function activate(int $id): bool {
        $volunteer = $this->repository->findById($id);
        if (!$volunteer) {
            return false;
        }

        $volunteer->setStatus('active');
        return $this->repository->save($volunteer) > 0;
    }

    /**
     * Marca um voluntário como inativo
     */
    public function deactivate(int $id): bool {
        $volunteer = $this->repository->findById($id);
        if (!$volunteer) {
            return false;
        }

        $volunteer->setStatus('inactive');
        return $this->repository->save($volunteer) > 0;
    }

    /**
     * Gera relatório de voluntários
     */
    public function getReport(?string $startDate = null, ?string $endDate = null): array {
        return $this->repository->getReport($startDate, $endDate);
    }
}