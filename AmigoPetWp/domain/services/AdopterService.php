<?php
namespace AmigoPet\Domain\Services;

use AmigoPet\Domain\Database\AdopterRepository;
use AmigoPet\Domain\Entities\Adopter;

class AdopterService {
    private $repository;

    public function __construct(AdopterRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Cria um novo adotante
     */
    public function createAdopter(
        string $name,
        string $email,
        string $phone,
        string $document,
        string $address,
        string $city,
        string $state,
        string $zipCode
    ): int {
        $adopter = new Adopter(
            $name,
            $email,
            $phone,
            $document,
            $address,
            $city,
            $state,
            $zipCode
        );

        return $this->repository->save($adopter);
    }

    /**
     * Atualiza um adotante existente
     */
    public function updateAdopter(
        int $id,
        string $name,
        string $email,
        string $phone,
        string $document,
        string $address,
        string $city,
        string $state,
        string $zipCode
    ): void {
        $adopter = $this->repository->findById($id);
        if (!$adopter) {
            throw new \InvalidArgumentException("Adotante não encontrado");
        }

        $adopter = new Adopter(
            $name,
            $email,
            $phone,
            $document,
            $address,
            $city,
            $state,
            $zipCode
        );

        $this->repository->save($adopter);
    }

    /**
     * Busca um adotante por ID
     */
    public function findById(int $id): ?Adopter {
        return $this->repository->findById($id);
    }

    /**
     * Busca um adotante por email
     */
    public function findByEmail(string $email): ?Adopter {
        return $this->repository->findByEmail($email);
    }

    /**
     * Busca um adotante por documento
     */
    public function findByDocument(string $document): ?Adopter {
        return $this->repository->findByDocument($document);
    }

    /**
     * Lista todos os adotantes
     */
    public function findAll(): array {
        return $this->repository->findAll();
    }
}
