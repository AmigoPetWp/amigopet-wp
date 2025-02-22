<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\AdoptionRepository;
use AmigoPetWp\Domain\Entities\Adoption;
use AmigoPetWp\Domain\Entities\AdoptionPayment;

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
    public function markPaymentAsPaid(int $adoptionId): void {
        $adoption = $this->repository->findById($adoptionId);
        if (!$adoption) {
            throw new \InvalidArgumentException("Adoção não encontrada");
        }

        $payment = $this->repository->findPaymentByAdoptionId($adoptionId);
        if (!$payment) {
            throw new \InvalidArgumentException("Pagamento não encontrado");
        }

        $payment->confirmPayment();
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

    /**
     * Lista todas as adoções com dados relacionados
     */
    public function listAdoptionsWithRelations(
        int $perPage = 20,
        int $currentPage = 1,
        string $search = '',
        string $status = '',
        string $orderby = 'ad.created_at',
        string $order = 'DESC'
    ): array {
        return $this->repository->findAllWithRelations(
            $perPage,
            $currentPage,
            $search,
            $status,
            $orderby,
            $order
        );
    }

    /**
     * Cancela uma adoção
     */
    public function cancelAdoption(int $adoptionId): void {
        $adoption = $this->repository->findById($adoptionId);
        if (!$adoption) {
            throw new \InvalidArgumentException("Adoção não encontrada");
        }

        $adoption->cancel();
        $this->repository->save($adoption);
    }

    /**
     * Reembolsa o pagamento de uma adoção
     */
    public function refundPayment(int $adoptionId): void {
        $adoption = $this->repository->findById($adoptionId);
        if (!$adoption) {
            throw new \InvalidArgumentException("Adoção não encontrada");
        }

        $payment = $this->repository->findPaymentByAdoptionId($adoptionId);
        if (!$payment) {
            throw new \InvalidArgumentException("Pagamento não encontrado");
        }

        $payment->refundPayment();
        $this->repository->savePayment($payment);
    }

    /**
     * Conta o total de adoções com filtros
     */
    public function countAdoptionsWithFilters(string $search = '', string $status = ''): int {
        return $this->repository->countWithFilters($search, $status);
    }
}
