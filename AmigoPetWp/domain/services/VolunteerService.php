<?php
namespace AmigoPet\Domain\Services;

use AmigoPet\Domain\Database\VolunteerRepository;
use AmigoPet\Domain\Entities\Volunteer;

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
    ): void {
        $volunteer = $this->repository->findById($id);
        if (!$volunteer) {
            throw new \InvalidArgumentException("Voluntário não encontrado");
        }

        $volunteer = new Volunteer(
            $volunteer->getOrganizationId(),
            $name,
            $email,
            $phone,
            $availability,
            $skills
        );

        $this->repository->save($volunteer);
    }

    /**
     * Ativa um voluntário
     */
    public function activate(int $volunteerId): void {
        $volunteer = $this->repository->findById($volunteerId);
        if (!$volunteer) {
            throw new \InvalidArgumentException("Voluntário não encontrado");
        }

        $volunteer->activate(new \DateTimeImmutable());
        $this->repository->save($volunteer);
    }

    /**
     * Desativa um voluntário
     */
    public function deactivate(int $volunteerId): void {
        $volunteer = $this->repository->findById($volunteerId);
        if (!$volunteer) {
            throw new \InvalidArgumentException("Voluntário não encontrado");
        }

        $volunteer->deactivate(new \DateTimeImmutable());
        $this->repository->save($volunteer);
    }

    /**
     * Busca um voluntário por ID
     */
    public function findById(int $id): ?Volunteer {
        return $this->repository->findById($id);
    }

    /**
     * Lista voluntários de uma organização
     */
    public function findByOrganization(int $organizationId): array {
        return $this->repository->findByOrganization($organizationId);
    }

    /**
     * Lista voluntários ativos
     */
    public function findActive(): array {
        return $this->repository->findActive();
    }

    /**
     * Busca voluntários por habilidade
     */
    public function findBySkill(string $skill): array {
        return $this->repository->findBySkill($skill);
    }
}
