<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\DomainDatabase\DonationRepository;
use AmigoPetWp\DomainEntities\Donation;

class DonationService {
    private $repository;

    public function __construct(DonationRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Cria uma nova doação
     */
    public function createDonation(
        int $organizationId,
        string $donorName,
        string $donorEmail,
        string $donorPhone,
        string $type,
        string $description,
        float $amount
    ): int {
        $donation = new Donation(
            $organizationId,
            $donorName,
            $donorEmail,
            $donorPhone,
            $type,
            $description,
            $amount
        );

        return $this->repository->save($donation);
    }

    /**
     * Marca uma doação como recebida
     */
    public function markAsReceived(int $donationId): void {
        $donation = $this->repository->findById($donationId);
        if (!$donation) {
            throw new \InvalidArgumentException("Doação não encontrada");
        }

        $donation->markAsReceived(new \DateTimeImmutable());
        $this->repository->save($donation);
    }

    /**
     * Busca uma doação por ID
     */
    public function findById(int $id): ?Donation {
        return $this->repository->findById($id);
    }

    /**
     * Lista doações de uma organização
     */
    public function findByOrganization(int $organizationId): array {
        return $this->repository->findByOrganization($organizationId);
    }

    /**
     * Lista doações pendentes
     */
    public function findPending(): array {
        return $this->repository->findPending();
    }
}
