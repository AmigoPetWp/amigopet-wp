<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\SpeciesRepository;
use AmigoPetWp\Domain\Entities\Species;

class SpeciesService {
    private $repository;

    public function __construct(SpeciesRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Cria uma nova espécie
     */
    public function createSpecies(
        string $name,
        string $scientificName,
        ?string $description = null,
        ?string $characteristics = null
    ): int {
        $species = new Species(
            $name,
            $scientificName
        );

        if ($description) {
            $species->setDescription($description);
        }

        if ($characteristics) {
            $species->setCharacteristics($characteristics);
        }

        return $this->repository->save($species);
    }

    /**
     * Atualiza uma espécie existente
     */
    public function updateSpecies(
        int $id,
        string $name,
        string $scientificName,
        ?string $description = null,
        ?string $characteristics = null
    ): bool {
        $species = $this->repository->findById($id);
        if (!$species) {
            return false;
        }

        $species = new Species(
            $name,
            $scientificName
        );
        $species->setId($id);

        if ($description) {
            $species->setDescription($description);
        }

        if ($characteristics) {
            $species->setCharacteristics($characteristics);
        }

        return $this->repository->save($species) > 0;
    }

    /**
     * Busca uma espécie por ID
     */
    public function findById(int $id): ?Species {
        return $this->repository->findById($id);
    }

    /**
     * Lista espécies por status
     */
    public function findByStatus(string $status): array {
        return $this->repository->findByStatus($status);
    }

    /**
     * Lista espécies ativas
     */
    public function findActive(): array {
        return $this->repository->findActive();
    }

    /**
     * Busca espécies por termo
     */
    public function search(string $term): array {
        return $this->repository->search($term);
    }

    /**
     * Gera relatório de espécies
     */
    public function getReport(?string $startDate = null, ?string $endDate = null): array {
        return $this->repository->getReport($startDate, $endDate);
    }

    /**
     * Busca espécies por filtros
     */
    public function findByFilters(array $filters): array {
        return $this->repository->findByFilters($filters);
    }

    /**
     * Ativa uma espécie
     */
    public function activateSpecies(int $id): bool {
        $species = $this->repository->findById($id);
        if (!$species) {
            return false;
        }

        $species->setStatus('active');
        return $this->repository->save($species) > 0;
    }

    /**
     * Desativa uma espécie
     */
    public function deactivateSpecies(int $id): bool {
        $species = $this->repository->findById($id);
        if (!$species) {
            return false;
        }

        $species->setStatus('inactive');
        return $this->repository->save($species) > 0;
    }

    /**
     * Deleta uma espécie
     */
    public function deleteSpecies(int $id): bool {
        $species = $this->repository->findById($id);
        if (!$species) {
            return false;
        }

        return $this->repository->delete($id);
    }
}
