<?php
declare(strict_types=1);
namespace AmigoPetWp\Domain\Entities;

class Adoption
{
    private ?int $id = null;
    private int $petId;
    private int $adopterId;
    private int $organizationId;
    private string $status;
    private string $adoptionReason;
    private string $petExperience;
    private ?int $reviewerId = null;
    private ?string $reviewNotes = null;
    private ?\DateTimeInterface $reviewDate = null;
    private ?\DateTimeInterface $completedDate = null;
    private \DateTimeInterface $createdAt;

    // Constantes para status da adoção
    private const ADOPTION_STATUS = [
        'pending' => 'Pendente',
        'approved' => 'Aprovado',
        'rejected' => 'Rejeitado',
        'awaiting_payment' => 'Aguardando Pagamento',
        'paid' => 'Pago',
        'completed' => 'Concluído',
        'cancelled' => 'Cancelado'
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

    private function validate(): void
    {
        if (empty($this->adoptionReason)) {
            throw new \InvalidArgumentException("Motivo da adoção é obrigatório");
        }

        if (empty($this->petExperience)) {
            throw new \InvalidArgumentException("Experiência com pets é obrigatória");
        }
    }

    public function review(int $reviewerId, string $reviewNotes, bool $approved): void
    {
        if ($this->status !== 'pending' && $this->status !== 'rejected') {
            throw new \InvalidArgumentException("Adoção não está em estado para revisão");
        }

        $this->reviewerId = $reviewerId;
        $this->reviewNotes = $reviewNotes;
        $this->reviewDate = new \DateTimeImmutable();
        $this->status = $approved ? 'approved' : 'rejected';
    }

    // Hydration Setters
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
    public function setPetId(int $petId): self
    {
        $this->petId = $petId;
        return $this;
    }
    public function setAdopterId(int $adopterId): self
    {
        $this->adopterId = $adopterId;
        return $this;
    }
    public function setOrganizationId(int $organizationId): self
    {
        $this->organizationId = $organizationId;
        return $this;
    }
    public function setStatus(string $status): self
    {
        if (!self::isValidStatus($status))
            throw new \InvalidArgumentException("Status inválido: $status");
        $this->status = $status;
        return $this;
    }
    public function setAdoptionReason(string $reason): self
    {
        $this->adoptionReason = $reason;
        return $this;
    }
    public function setPetExperience(string $experience): self
    {
        $this->petExperience = $experience;
        return $this;
    }
    public function setReviewerId(?int $id): self
    {
        $this->reviewerId = $id;
        return $this;
    }
    public function setReviewNotes(?string $notes): self
    {
        $this->reviewNotes = $notes;
        return $this;
    }
    public function setReviewDate(?\DateTimeInterface $date): self
    {
        $this->reviewDate = $date;
        return $this;
    }
    public function setCompletedDate(?\DateTimeInterface $date): self
    {
        $this->completedDate = $date;
        return $this;
    }
    public function setCreatedAt(\DateTimeInterface $date): self
    {
        $this->createdAt = $date;
        return $this;
    }

    public function markAsAwaitingPayment(): void
    {
        if ($this->status !== 'approved') {
            throw new \InvalidArgumentException("Adoção precisa estar aprovada");
        }
        $this->status = 'awaiting_payment';
    }

    public function markAsPaid(): void
    {
        if ($this->status !== 'awaiting_payment') {
            throw new \InvalidArgumentException("Adoção precisa estar aguardando pagamento");
        }
        $this->status = 'paid';
    }

    public function complete(): void
    {
        if ($this->status !== 'paid') {
            throw new \InvalidArgumentException("Adoção precisa estar paga");
        }
        $this->status = 'completed';
        $this->completedDate = new \DateTimeImmutable();
    }

    public function cancel(): void
    {
        if (!in_array($this->status, ['pending', 'approved', 'awaiting_payment'])) {
            throw new \InvalidArgumentException("Adoção não pode ser cancelada no status atual");
        }
        $this->status = 'cancelled';
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getPetId(): int
    {
        return $this->petId;
    }
    public function getAdopterId(): int
    {
        return $this->adopterId;
    }
    public function getOrganizationId(): int
    {
        return $this->organizationId;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getStatusName(): string
    {
        return self::ADOPTION_STATUS[$this->status];
    }
    public function getAdoptionReason(): string
    {
        return $this->adoptionReason;
    }
    public function getPetExperience(): string
    {
        return $this->petExperience;
    }
    public function getReviewerId(): ?int
    {
        return $this->reviewerId;
    }
    public function getReviewNotes(): ?string
    {
        return $this->reviewNotes;
    }
    public function getReviewDate(): ?\DateTimeInterface
    {
        return $this->reviewDate;
    }
    public function getCompletedDate(): ?\DateTimeInterface
    {
        return $this->completedDate;
    }
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Verifica se um status é válido
     */
    public static function isValidStatus(string $status): bool
    {
        return array_key_exists($status, self::ADOPTION_STATUS);
    }

    /**
     * Retorna todos os status possíveis
     */
    public static function getAllStatus(): array
    {
        return self::ADOPTION_STATUS;
    }
}
