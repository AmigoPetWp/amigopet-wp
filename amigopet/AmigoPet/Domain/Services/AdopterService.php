<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Services;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Repositories\AdopterRepository;
use AmigoPetWp\Domain\Entities\Adopter;

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
    ): bool {
        $adopter = $this->repository->findById($id);
        if (!$adopter) {
            return false;
        }

        $adopter->setName($name);
        $adopter->setEmail($email);
        $adopter->setPhone($phone);
        $adopter->setDocument($document);
        $adopter->setAddress($address);
        $adopter->setCity($city);
        $adopter->setState($state);
        $adopter->setZipCode($zipCode);

        return $this->repository->save($adopter) > 0;
    }

    /**
     * Busca um adotante pelo ID
     */
    public function findById(int $id): ?Adopter {
        return $this->repository->findById($id);
    }

    /**
     * Busca adotantes por critérios
     */
    public function findAdopters(array $criteria = []): array {
        return $this->repository->findAll($criteria);
    }

    /**
     * Busca adotantes por nome ou email
     */
    public function searchAdopters(string $term): array {
        return $this->repository->search($term);
    }

    /**
     * Gera relatório de adotantes
     */
    public function getReport(?string $startDate = null, ?string $endDate = null): array {
        return $this->repository->getReport($startDate, $endDate);
    }

    /**
     * Verifica se um documento já está em uso
     */
    public function isDocumentInUse(string $document, ?int $excludeId = null): bool {
        $adopters = $this->repository->findByDocument($document);
        
        if (empty($adopters)) {
            return false;
        }

        if ($excludeId !== null) {
            foreach ($adopters as $adopter) {
                if ($adopter->getId() !== $excludeId) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }

    /**
     * Verifica se um email já está em uso
     */
    public function isEmailInUse(string $email, ?int $excludeId = null): bool {
        $adopters = $this->repository->findByEmail($email);
        
        if (empty($adopters)) {
            return false;
        }

        if ($excludeId !== null) {
            foreach ($adopters as $adopter) {
                if ($adopter->getId() !== $excludeId) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }
}