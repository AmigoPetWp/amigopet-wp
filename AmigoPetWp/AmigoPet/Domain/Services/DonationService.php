<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\DonationRepository;
use AmigoPetWp\Domain\Entities\Donation;

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
        float $amount,
        string $paymentMethod,
        ?string $donorPhone = null,
        ?string $description = null
    ): Donation {
        $donation = new Donation(
            $organizationId,
            $donorName,
            $donorEmail,
            $amount,
            $paymentMethod,
            $donorPhone,
            $description
        );

        $this->repository->save($donation);
        return $donation;
    }

    /**
     * Processa o pagamento de uma doação
     */
    public function processPayment(int $donationId, string $transactionId): void {
        $donation = $this->repository->findById($donationId);
        if (!$donation) {
            throw new \InvalidArgumentException("Doação não encontrada");
        }

        $donation->setTransactionId($transactionId);
        $donation->setPaymentStatus(Donation::STATUS_COMPLETED);
        $this->repository->save($donation);
    }

    /**
     * Reembolsa uma doação
     */
    public function refundPayment(int $donationId): void {
        $donation = $this->repository->findById($donationId);
        if (!$donation) {
            throw new \InvalidArgumentException("Doação não encontrada");
        }

        if ($donation->getPaymentStatus() !== Donation::STATUS_COMPLETED) {
            throw new \InvalidArgumentException("Apenas doações pagas podem ser reembolsadas");
        }

        $donation->setPaymentStatus(Donation::STATUS_REFUNDED);
        $this->repository->save($donation);
    }

    /**
     * Marca uma doação como falha
     */
    public function markAsFailed(int $donationId, string $reason = null): void {
        $donation = $this->repository->findById($donationId);
        if (!$donation) {
            throw new \InvalidArgumentException("Doação não encontrada");
        }

        $donation->setPaymentStatus(Donation::STATUS_FAILED);
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
    public function findByOrganization(int $organizationId, ?string $status = null): array {
        return $this->repository->findByOrganization($organizationId, $status);
    }

    /**
     * Lista doações por status
     */
    public function findByStatus(string $status): array {
        return $this->repository->findByStatus($status);
    }

    /**
     * Obtém relatório de doações
     */
    public function getReport(): array {
        return $this->repository->getReport();
    }
}
