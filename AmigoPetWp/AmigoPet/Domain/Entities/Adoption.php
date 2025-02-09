<?php
namespace AmigoPetWp\Domain\Entities;

class Adoption {
    private $id;
    private $petId;
    private $adopterId;
    private $organizationId;
    private $status;
    private $adoptionReason;
    private $petExperience;
    private $reviewerId;
    private $reviewNotes;
    private $reviewDate;
    private $completedDate;
    private $createdAt;

    // Constantes para status da adoção
    private const ADOPTION_STATUS = [
        'pending' => 'Pendente',
        'approved' => 'Aprovado',
        'rejected' => 'Rejeitado',
        'awaiting_payment' => 'Aguardando Pagamento',
        'paid' => 'Pago',
        'completed' => 'Concluído'
    ];

    public function __construct(
        int $petId,
        int $adopterId,
        int $organizationId,
        string $adoptionReason,
        string $petExperience
    ) {
        $this->petId = $petId;
        $this->adopterId = $adopterId;
        $this->organizationId = $organizationId;
        $this->adoptionReason = $adoptionReason;
        $this->petExperience = $petExperience;
        $this->status = 'pending';
        $this->createdAt = new \DateTimeImmutable();
        $this->validate();
    }

    private function validate(): void {
        if (empty($this->adoptionReason)) {
            throw new \InvalidArgumentException("Motivo da adoção é obrigatório");
        }

        if (empty($this->petExperience)) {
            throw new \InvalidArgumentException("Experiência com pets é obrigatória");
        }
    }

    public function review(int $reviewerId, string $reviewNotes, bool $approved): void {
        if ($this->status !== 'pending') {
            throw new \InvalidArgumentException("Adoção não está pendente de revisão");
        }

        $this->reviewerId = $reviewerId;
        $this->reviewNotes = $reviewNotes;
        $this->reviewDate = new \DateTimeImmutable();
        $this->status = $approved ? 'approved' : 'rejected';
    }

    public function markAsAwaitingPayment(): void {
        if ($this->status !== 'approved') {
            throw new \InvalidArgumentException("Adoção precisa estar aprovada");
        }
        $this->status = 'awaiting_payment';
    }

    public function markAsPaid(): void {
        if ($this->status !== 'awaiting_payment') {
            throw new \InvalidArgumentException("Adoção precisa estar aguardando pagamento");
        }
        $this->status = 'paid';
    }

    public function complete(): void {
        if ($this->status !== 'paid') {
            throw new \InvalidArgumentException("Adoção precisa estar paga");
        }
        $this->status = 'completed';
        $this->completedDate = new \DateTimeImmutable();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getPetId(): int { return $this->petId; }
    public function getAdopterId(): int { return $this->adopterId; }
    public function getOrganizationId(): int { return $this->organizationId; }
    public function getStatus(): string { return $this->status; }
    public function getStatusName(): string { return self::ADOPTION_STATUS[$this->status]; }
    public function getAdoptionReason(): string { return $this->adoptionReason; }
    public function getPetExperience(): string { return $this->petExperience; }
    public function getReviewerId(): ?int { return $this->reviewerId; }
    public function getReviewNotes(): ?string { return $this->reviewNotes; }
    public function getReviewDate(): ?\DateTimeInterface { return $this->reviewDate; }
    public function getCompletedDate(): ?\DateTimeInterface { return $this->completedDate; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }

    /**
     * Verifica se um status é válido
     */
    public static function isValidStatus(string $status): bool {
        return array_key_exists($status, self::ADOPTION_STATUS);
    }

    /**
     * Retorna todos os status possíveis
     */
    public static function getAllStatus(): array {
        return self::ADOPTION_STATUS;
    }
}
