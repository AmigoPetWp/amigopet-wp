<?php
namespace AmigoPet\Domain\Services;

use AmigoPet\Domain\Database\PetRepository;
use AmigoPet\Domain\Entities\Pet;

class PetService {
    private $repository;

    public function __construct(PetRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Cria um novo pet
     */
    public function createPet(
        int $organizationId,
        string $name,
        string $species,
        ?string $breed,
        ?int $age,
        string $size,
        string $description,
        array $healthInfo = []
    ): int {
        $pet = new Pet(
            $organizationId,
            $name,
            $species,
            $breed,
            $age,
            $size,
            $description,
            $healthInfo
        );

        return $this->repository->save($pet);
    }

    /**
     * Atualiza um pet existente
     */
    public function updatePet(
        int $id,
        string $name,
        string $species,
        ?string $breed,
        ?int $age,
        string $size,
        string $description,
        array $healthInfo = []
    ): void {
        $pet = $this->repository->findById($id);
        if (!$pet) {
            throw new \InvalidArgumentException("Pet não encontrado");
        }

        $pet = new Pet(
            $pet->getOrganizationId(),
            $name,
            $species,
            $breed,
            $age,
            $size,
            $description,
            $healthInfo
        );

        $this->repository->save($pet);
    }

    /**
     * Marca um pet como adotado
     */
    public function markAsAdopted(int $petId): void {
        $pet = $this->repository->findById($petId);
        if (!$pet) {
            throw new \InvalidArgumentException("Pet não encontrado");
        }

        $pet->markAsAdopted();
        $this->repository->save($pet);
    }

    /**
     * Marca um pet como resgatado
     */
    public function markAsRescued(int $petId): void {
        $pet = $this->repository->findById($petId);
        if (!$pet) {
            throw new \InvalidArgumentException("Pet não encontrado");
        }

        $pet->markAsRescued();
        $this->repository->save($pet);
    }

    /**
     * Busca um pet por ID
     */
    public function findById(int $id): ?Pet {
        return $this->repository->findById($id);
    }

    /**
     * Lista pets de uma organização
     */
    public function findByOrganization(int $organizationId): array {
        return $this->repository->findByOrganization($organizationId);
    }

    /**
     * Lista pets disponíveis para adoção
     */
    public function findAvailable(): array {
        return $this->repository->findAvailable();
    }

    /**
     * Busca pets por espécie
     */
    public function findBySpecies(string $species): array {
        return $this->repository->findBySpecies($species);
    }

    /**
     * Busca pets por porte
     */
    public function findBySize(string $size): array {
        return $this->repository->findBySize($size);
    }
}
