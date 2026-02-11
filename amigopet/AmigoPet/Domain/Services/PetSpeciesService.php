<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Services;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Repositories\PetSpeciesRepository;
use AmigoPetWp\Domain\Entities\PetSpecies;

class PetSpeciesService {
    private $repository;

    public function __construct(PetSpeciesRepository $repository) {
        $this->repository = $repository;
    }

    public function create(array $data): PetSpecies {
        $species = new PetSpecies(
            $data['name'],
            $data['description'] ?? null,
            $data['status'] ?? 'active'
        );

        $id = $this->repository->save($species);
        $species->setId($id);

        return $species;
    }

    public function update(int $id, array $data): ?PetSpecies {
        $species = $this->repository->findById($id);
        
        if (!$species) {
            return null;
        }

        if (isset($data['name'])) {
            $species->setName($data['name']);
        }

        if (isset($data['description'])) {
            $species->setDescription($data['description']);
        }

        if (isset($data['status'])) {
            $species->setStatus($data['status']);
        }

        $this->repository->save($species);

        return $species;
    }

    public function delete(int $id): bool {
        return $this->repository->delete($id);
    }

    public function findById(int $id): ?PetSpecies {
        return $this->repository->findById($id);
    }

    public function findAll(array $args = []): array {
        return $this->repository->findAll($args);
    }

    public function activate(int $id): ?PetSpecies {
        return $this->update($id, ['status' => 'active']);
    }

    public function deactivate(int $id): ?PetSpecies {
        return $this->update($id, ['status' => 'inactive']);
    }

    public function validate(array $data): array {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = __('Nome é obrigatório', 'amigopet');
        }

        if (isset($data['status']) && !in_array($data['status'], ['active', 'inactive'])) {
            $errors['status'] = __('Status inválido', 'amigopet');
        }

        return $errors;
    }
}