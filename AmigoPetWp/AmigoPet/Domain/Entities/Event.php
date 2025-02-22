<?php
namespace AmigoPetWp\Domain\Entities;

class Event {
    private $id;
    private $organizationId;
    private $title;
    private $description;
    private $date;
    private $location;
    private $maxParticipants;
    private $currentParticipants;
    private $status;
    private $createdAt;
    private $updatedAt;

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public function __construct(
        int $organizationId,
        string $title,
        string $description,
        \DateTimeInterface $date,
        string $location,
        ?int $maxParticipants = null
    ) {
        $this->organizationId = $organizationId;
        $this->title = $title;
        $this->description = $description;
        $this->date = $date;
        $this->location = $location;
        $this->maxParticipants = $maxParticipants;
        $this->currentParticipants = 0;
        $this->status = self::STATUS_SCHEDULED;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->validate();
    }

    private function validate(): void {
        if (empty($this->title)) {
            throw new \InvalidArgumentException("Título do evento é obrigatório");
        }

        if (empty($this->description)) {
            throw new \InvalidArgumentException("Descrição do evento é obrigatória");
        }

        if (empty($this->location)) {
            throw new \InvalidArgumentException("Local do evento é obrigatório");
        }

        if ($this->date < new \DateTime()) {
            throw new \InvalidArgumentException("Data do evento deve ser futura");
        }

        if ($this->maxParticipants !== null && $this->maxParticipants <= 0) {
            throw new \InvalidArgumentException("Número máximo de participantes deve ser maior que zero");
        }
    }

    public function addParticipant(): void {
        if ($this->status !== self::STATUS_SCHEDULED) {
            throw new \DomainException("Não é possível adicionar participantes a um evento {$this->status}");
        }

        if ($this->maxParticipants !== null && $this->currentParticipants >= $this->maxParticipants) {
            throw new \DomainException("Evento já atingiu o número máximo de participantes");
        }

        $this->currentParticipants++;
        $this->updateTimestamp();
    }

    public function removeParticipant(): void {
        if ($this->status !== self::STATUS_SCHEDULED) {
            throw new \DomainException("Não é possível remover participantes de um evento {$this->status}");
        }

        if ($this->currentParticipants <= 0) {
            throw new \DomainException("Não há participantes para remover");
        }

        $this->currentParticipants--;
        $this->updateTimestamp();
    }

    public function start(): void {
        if ($this->status !== self::STATUS_SCHEDULED) {
            throw new \DomainException("Não é possível iniciar um evento {$this->status}");
        }

        $this->status = self::STATUS_ONGOING;
        $this->updateTimestamp();
    }

    public function complete(): void {
        if ($this->status !== self::STATUS_ONGOING) {
            throw new \DomainException("Não é possível completar um evento {$this->status}");
        }

        $this->status = self::STATUS_COMPLETED;
        $this->updateTimestamp();
    }

    public function cancel(): void {
        if ($this->status === self::STATUS_COMPLETED || $this->status === self::STATUS_CANCELLED) {
            throw new \DomainException("Não é possível cancelar um evento {$this->status}");
        }

        $this->status = self::STATUS_CANCELLED;
        $this->updateTimestamp();
    }

    private function updateTimestamp(): void {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getOrganizationId(): int { return $this->organizationId; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getDate(): \DateTimeInterface { return $this->date; }
    public function getLocation(): string { return $this->location; }
    public function getMaxParticipants(): ?int { return $this->maxParticipants; }
    public function getCurrentParticipants(): int { return $this->currentParticipants; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeInterface { return $this->updatedAt; }

    public function hasAvailableSpots(): bool {
        if ($this->maxParticipants === null) {
            return true;
        }
        return $this->currentParticipants < $this->maxParticipants;
    }

    public function isScheduled(): bool { return $this->status === self::STATUS_SCHEDULED; }
    public function isOngoing(): bool { return $this->status === self::STATUS_ONGOING; }
    public function isCompleted(): bool { return $this->status === self::STATUS_COMPLETED; }
    public function isCancelled(): bool { return $this->status === self::STATUS_CANCELLED; }
}
