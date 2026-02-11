<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Entities;

if (!defined('ABSPATH')) {
    exit;
}

class Event
{
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

    private function validate(): void
    {
        if (empty($this->title)) {
            throw new \InvalidArgumentException(esc_html__("Título do evento é obrigatório", 'amigopet'));
        }

        if (empty($this->description)) {
            throw new \InvalidArgumentException(esc_html__("Descrição do evento é obrigatória", 'amigopet'));
        }

        if (empty($this->location)) {
            throw new \InvalidArgumentException(esc_html__("Local do evento é obrigatório", 'amigopet'));
        }

        if ($this->date < new \DateTime()) {
            throw new \InvalidArgumentException(esc_html__("Data do evento deve ser futura", 'amigopet'));
        }

        if ($this->maxParticipants !== null && $this->maxParticipants <= 0) {
            throw new \InvalidArgumentException(esc_html__("Número máximo de participantes deve ser maior que zero", 'amigopet'));
        }
    }

    public function addParticipant(): void
    {
        if ($this->status !== self::STATUS_SCHEDULED) {
            // translators: %s: placeholder
            throw new \DomainException(esc_html(sprintf(esc_html__('Não é possível adicionar participantes a um evento %s', 'amigopet'), esc_html(this->status))));
        }

        if ($this->maxParticipants !== null && $this->currentParticipants >= $this->maxParticipants) {
            throw new \DomainException(esc_html__("Evento já atingiu o número máximo de participantes", 'amigopet'));
        }

        $this->currentParticipants++;
        $this->updateTimestamp();
    }

    public function removeParticipant(): void
    {
        if ($this->status !== self::STATUS_SCHEDULED) {
            // translators: %s: placeholder
            throw new \DomainException(esc_html(sprintf(esc_html__('Não é possível remover participantes de um evento %s', 'amigopet'), esc_html(this->status))));
        }

        if ($this->currentParticipants <= 0) {
            throw new \DomainException(esc_html__("Não há participantes para remover", 'amigopet'));
        }

        $this->currentParticipants--;
        $this->updateTimestamp();
    }

    public function start(): void
    {
        if ($this->status !== self::STATUS_SCHEDULED) {
            // translators: %s: placeholder
            throw new \DomainException(esc_html(sprintf(esc_html__('Não é possível iniciar um evento %s', 'amigopet'), esc_html(this->status))));
        }

        $this->status = self::STATUS_ONGOING;
        $this->updateTimestamp();
    }

    public function complete(): void
    {
        if ($this->status !== self::STATUS_ONGOING) {
            // translators: %s: placeholder
            throw new \DomainException(esc_html(sprintf(esc_html__('Não é possível completar um evento %s', 'amigopet'), esc_html(this->status))));
        }

        $this->status = self::STATUS_COMPLETED;
        $this->updateTimestamp();
    }

    public function cancel(): void
    {
        if ($this->status === self::STATUS_COMPLETED || $this->status === self::STATUS_CANCELLED) {
            // translators: %s: placeholder
            throw new \DomainException(esc_html(sprintf(esc_html__('Não é possível cancelar um evento %s', 'amigopet'), esc_html(this->status))));
        }

        $this->status = self::STATUS_CANCELLED;
        $this->updateTimestamp();
    }

    private function updateTimestamp(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setCurrentParticipants(int $count): void
    {
        $this->currentParticipants = $count;
    }

    public function setStatus(string $status): void
    {
        if (!in_array($status, [self::STATUS_SCHEDULED, self::STATUS_ONGOING, self::STATUS_COMPLETED, self::STATUS_CANCELLED])) {
            throw new \InvalidArgumentException(esc_html__("Status inválido", 'amigopet'));
        }
        $this->status = $status;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getOrganizationId(): int
    {
        return $this->organizationId;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
    public function getLocation(): string
    {
        return $this->location;
    }
    public function getMaxParticipants(): ?int
    {
        return $this->maxParticipants;
    }
    public function getCurrentParticipants(): int
    {
        return $this->currentParticipants;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function hasAvailableSpots(): bool
    {
        if ($this->maxParticipants === null) {
            return true;
        }
        return $this->currentParticipants < $this->maxParticipants;
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }
    public function isOngoing(): bool
    {
        return $this->status === self::STATUS_ONGOING;
    }
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }
}