<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\PetBreedRepository;
use AmigoPetWp\Domain\Database\Repositories\PetSpeciesRepository;
use AmigoPetWp\Domain\Entities\PetBreed;

class PetBreedService
{
    private $repository;
    private $speciesRepository;

    public function __construct(
        PetBreedRepository $repository,
        PetSpeciesRepository $speciesRepository
    ) {
        $this->repository = $repository;
        $this->speciesRepository = $speciesRepository;
    }

    public function create(array $data): PetBreed
    {
        $breed = new PetBreed(
            (int) $data['species_id'],
            $data['name'],
            $data['description'] ?? null,
            $data['characteristics'] ?? null,
            $data['status'] ?? 'active'
        );

        $id = $this->repository->save($breed);
        $breed->setId($id);

        return $breed;
    }

    public function getOrCreate(string $name, int $speciesId): int
    {
        $existing = $this->repository->findByNameAndSpecies($name, $speciesId);
        if ($existing) {
            return $existing->getId();
        }

        $breed = $this->create([
            'name' => $name,
            'species_id' => $speciesId,
            'status' => 'active'
        ]);

        return $breed->getId();
    }

    public function update(int $id, array $data): ?PetBreed
    {
        $breed = $this->repository->findById($id);

        if (!$breed) {
            return null;
        }

        if (isset($data['species_id'])) {
            $breed->setSpeciesId($data['species_id']);
        }

        if (isset($data['name'])) {
            $breed->setName($data['name']);
        }

        if (isset($data['description'])) {
            $breed->setDescription($data['description']);
        }

        if (isset($data['characteristics'])) {
            $breed->setCharacteristics($data['characteristics']);
        }

        if (isset($data['status'])) {
            $breed->setStatus($data['status']);
        }

        $this->repository->save($breed);

        return $breed;
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function findById(int $id): ?PetBreed
    {
        return $this->repository->findById($id);
    }

    public function findBySpecies(int $speciesId, array $args = []): array
    {
        return $this->repository->findBySpecies($speciesId, $args);
    }

    public function findAll(array $args = []): array
    {
        return $this->repository->findAll($args);
    }

    public function activate(int $id): ?PetBreed
    {
        return $this->update($id, ['status' => 'active']);
    }

    public function deactivate(int $id): ?PetBreed
    {
        return $this->update($id, ['status' => 'inactive']);
    }

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['species_id'])) {
            $errors['species_id'] = __('Espécie é obrigatória', 'amigopet-wp');
        } elseif (!$this->speciesRepository->findById($data['species_id'])) {
            $errors['species_id'] = __('Espécie não encontrada', 'amigopet-wp');
        }

        if (empty($data['name'])) {
            $errors['name'] = __('Nome é obrigatório', 'amigopet-wp');
        }

        if (isset($data['status']) && !in_array($data['status'], ['active', 'inactive'])) {
            $errors['status'] = __('Status inválido', 'amigopet-wp');
        }

        return $errors;
    }
}
