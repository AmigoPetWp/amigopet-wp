<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\DomainDatabase\AdoptionRepository;
use AmigoPetWp\DomainEntities\Adoption;
use AmigoPetWp\DomainEntities\AdoptionPayment;

class AdoptionService {
    private $repository;

    public function __construct(AdoptionRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Cria uma nova solicitação de adoção
     */
    public function createAdoption(
        int $petId,
        int $adopterId,
        int $organizationId,
        string $adoptionReason,
        string $petExperience
    ): int {
        $adoption = new Adoption(
            $petId,
            $adopterId,
            $organizationId,
            $adoptionReason,
            $petExperience
        );

        return $this->repository->save($adoption);
    }

    /**
     * Revisa uma solicitação de adoção
     */
    public function reviewAdoption(
        int $adoptionId,
        int $reviewerId,
        string $reviewNotes,
        bool $approved
    ): void {
        $adoption = $this->repository->findById($adoptionId);
        if (!$adoption) {
            throw new \InvalidArgumentException("Adoção não encontrada");
        }

        $adoption->review($reviewerId, $reviewNotes, $approved);
        $this->repository->save($adoption);
    }

    /**
     * Cria um novo pagamento para uma adoção
     */
    public function createPayment(
        int $adoptionId,
        float $amount,
        string $paymentMethod
    ): int {
        $adoption = $this->repository->findById($adoptionId);
        if (!$adoption) {
            throw new \InvalidArgumentException("Adoção não encontrada");
        }

        $adoption->markAsAwaitingPayment();
        $this->repository->save($adoption);

        $payment = new AdoptionPayment($adoptionId, $amount, $paymentMethod);
        return $this->repository->savePayment($payment);
    }

    /**
     * Marca o pagamento de uma adoção como pago
     */
    public function markPaymentAsPaid(
        int $adoptionId,
        \DateTimeInterface $paidAt
    ): void {
        $adoption = $this->repository->findById($adoptionId);
        if (!$adoption) {
            throw new \InvalidArgumentException("Adoção não encontrada");
        }

        $payment = $this->repository->findPaymentByAdoptionId($adoptionId);
        if (!$payment) {
            throw new \InvalidArgumentException("Pagamento não encontrado");
        }

        $payment->markAsPaid($paidAt);
        $this->repository->savePayment($payment);

        $adoption->markAsPaid();
        $this->repository->save($adoption);
    }

    /**
     * Finaliza uma adoção
     */
    public function completeAdoption(int $adoptionId): void {
        $adoption = $this->repository->findById($adoptionId);
        if (!$adoption) {
            throw new \InvalidArgumentException("Adoção não encontrada");
        }

        $adoption->complete();
        $this->repository->save($adoption);
    }

    /**
     * Busca adoções pendentes
     */
    public function findPendingAdoptions(): array {
        return $this->repository->findPendingAdoptions();
    }

    /**
     * Busca adoções aguardando pagamento
     */
    public function findAwaitingPayment(): array {
        return $this->repository->findAwaitingPayment();
    }
}
