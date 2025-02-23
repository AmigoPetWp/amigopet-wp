<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\PetRepository;
use AmigoPetWp\Domain\Entities\Pet;

class PetService {
    private $repository;

    public function __construct(PetRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Cria um novo pet
     */
    public function createPet(
        string $name,
        int $speciesId,
        int $breedId,
        int $organizationId,
        ?string $size = null,
        ?string $gender = null,
        ?int $age = null,
        ?float $weight = null,
        ?string $color = null,
        ?string $description = null
    ): int {
        $pet = new Pet(
            $name,
            $speciesId,
            $breedId,
            $organizationId
        );

        if ($size) {
            $pet->setSize($size);
        }

        if ($gender) {
            $pet->setGender($gender);
        }

        if ($age) {
            $pet->setAge($age);
        }

        if ($weight) {
            $pet->setWeight($weight);
        }

        if ($color) {
            $pet->setColor($color);
        }

        if ($description) {
            $pet->setDescription($description);
        }

        return $this->repository->save($pet);
    }

    /**
     * Atualiza um pet existente
     */
    public function updatePet(
        int $id,
        string $name,
        int $speciesId,
        int $breedId,
        int $organizationId,
        ?string $size = null,
        ?string $gender = null,
        ?int $age = null,
        ?float $weight = null,
        ?string $color = null,
        ?string $description = null
    ): bool {
        $pet = $this->repository->findById($id);
        if (!$pet) {
            return false;
        }

        $pet = new Pet(
            $name,
            $speciesId,
            $breedId,
            $organizationId
        );
        $pet->setId($id);

        if ($size) {
            $pet->setSize($size);
        }

        if ($gender) {
            $pet->setGender($gender);
        }

        if ($age) {
            $pet->setAge($age);
        }

        if ($weight) {
            $pet->setWeight($weight);
        }

        if ($color) {
            $pet->setColor($color);
        }

        if ($description) {
            $pet->setDescription($description);
        }

        return $this->repository->save($pet) > 0;
    }

    /**
     * Busca um pet por ID
     */
    public function findById(int $id): ?Pet {
        return $this->repository->findById($id);
    }

    /**
     * Busca pets por organização
     */
    public function findByOrganization(int $organizationId): array {
        return $this->repository->findByOrganization($organizationId);
    }

    /**
     * Busca pets por espécie
     */
    public function findBySpecies(int $speciesId): array {
        return $this->repository->findBySpecies($speciesId);
    }

    /**
     * Busca pets por raça
     */
    public function findByBreed(int $breedId): array {
        return $this->repository->findByBreed($breedId);
    }

    /**
     * Busca pets por status
     */
    public function findByStatus(string $status): array {
        return $this->repository->findByStatus($status);
    }

    /**
     * Busca pets disponíveis para adoção
     */
    public function findAvailableForAdoption(): array {
        return $this->repository->findAvailableForAdoption();
    }

    /**
     * Busca pets adotados
     */
    public function findAdopted(): array {
        return $this->repository->findAdopted();
    }

    /**
     * Busca pets por termo
     */
    public function search(string $term): array {
        return $this->repository->search($term);
    }

    /**
     * Gera relatório de pets
     */
    public function getReport(?string $startDate = null, ?string $endDate = null): array {
        return $this->repository->getReport($startDate, $endDate);
    }

    /**
     * Busca pets por filtros
     */
    public function findByFilters(array $filters): array {
        return $this->repository->findByFilters($filters);
    }

    /**
     * Marca um pet como adotado
     */
    public function markAsAdopted(int $id): bool {
        $pet = $this->repository->findById($id);
        if (!$pet) {
            return false;
        }

        $pet->setStatus('adopted');
        return $this->repository->save($pet) > 0;
    }

    /**
     * Marca um pet como disponível
     */
    public function markAsAvailable(int $id): bool {
        $pet = $this->repository->findById($id);
        if (!$pet) {
            return false;
        }

        $pet->setStatus('available');
        return $this->repository->save($pet) > 0;
    }

    /**
     * Marca um pet como indisponível
     */
    public function markAsUnavailable(int $id): bool {
        $pet = $this->repository->findById($id);
        if (!$pet) {
            return false;
        }

        $pet->setStatus('unavailable');
        return $this->repository->save($pet) > 0;
    }
}
