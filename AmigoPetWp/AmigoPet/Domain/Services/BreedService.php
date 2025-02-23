<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\BreedRepository;
use AmigoPetWp\Domain\Entities\Breed;

class BreedService {
    private $repository;

    public function __construct(BreedRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Cria uma nova raça
     */
    public function createBreed(
        string $name,
        int $speciesId,
        ?string $description = null,
        ?string $characteristics = null
    ): int {
        $breed = new Breed(
            $name,
            $speciesId
        );

        if ($description) {
            $breed->setDescription($description);
        }

        if ($characteristics) {
            $breed->setCharacteristics($characteristics);
        }

        return $this->repository->save($breed);
    }

    /**
     * Atualiza uma raça existente
     */
    public function updateBreed(
        int $id,
        string $name,
        int $speciesId,
        ?string $description = null,
        ?string $characteristics = null
    ): bool {
        $breed = $this->repository->findById($id);
        if (!$breed) {
            return false;
        }

        $breed = new Breed(
            $name,
            $speciesId
        );
        $breed->setId($id);

        if ($description) {
            $breed->setDescription($description);
        }

        if ($characteristics) {
            $breed->setCharacteristics($characteristics);
        }

        return $this->repository->save($breed) > 0;
    }

    /**
     * Busca uma raça por ID
     */
    public function findById(int $id): ?Breed {
        return $this->repository->findById($id);
    }

    /**
     * Busca raças por espécie
     */
    public function findBySpecies(int $speciesId): array {
        return $this->repository->findBySpecies($speciesId);
    }

    /**
     * Lista raças por status
     */
    public function findByStatus(string $status): array {
        return $this->repository->findByStatus($status);
    }

    /**
     * Lista raças ativas
     */
    public function findActive(): array {
        return $this->repository->findActive();
    }

    /**
     * Busca raças por termo
     */
    public function search(string $term): array {
        return $this->repository->search($term);
    }

    /**
     * Gera relatório de raças
     */
    public function getReport(?string $startDate = null, ?string $endDate = null): array {
        return $this->repository->getReport($startDate, $endDate);
    }

    /**
     * Busca raças por filtros
     */
    public function findByFilters(array $filters): array {
        return $this->repository->findByFilters($filters);
    }

    /**
     * Ativa uma raça
     */
    public function activateBreed(int $id): bool {
        $breed = $this->repository->findById($id);
        if (!$breed) {
            return false;
        }

        $breed->setStatus('active');
        return $this->repository->save($breed) > 0;
    }

    /**
     * Desativa uma raça
     */
    public function deactivateBreed(int $id): bool {
        $breed = $this->repository->findById($id);
        if (!$breed) {
            return false;
        }

        $breed->setStatus('inactive');
        return $this->repository->save($breed) > 0;
    }

    /**
     * Deleta uma raça
     */
    public function deleteBreed(int $id): bool {
        $breed = $this->repository->findById($id);
        if (!$breed) {
            return false;
        }

        return $this->repository->delete($id);
    }
}
