<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Entities;

if (!defined('ABSPATH')) {
    exit;
}

class Organization
{
    private $id;
    private $name;
    private $email;
    private $phone;
    private $website;
    private $address;
    private $status;
    private $createdAt;
    private $updatedAt;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_PENDING = 'pending';

    public function __construct(
        string $name,
        string $email,
        string $phone,
        string $address,
        ?string $website = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->website = $website;
        $this->status = self::STATUS_PENDING;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->validate();
    }

    private function validate(): void
    {
        if (empty($this->name)) {
            throw new \InvalidArgumentException(esc_html__("Nome da organização é obrigatório", 'amigopet'));
        }

        if (strlen($this->name) > 100) {
            throw new \InvalidArgumentException(esc_html__("Nome da organização não pode ter mais que 100 caracteres", 'amigopet'));
        }

        if (empty($this->email)) {
            throw new \InvalidArgumentException(esc_html__("E-mail é obrigatório", 'amigopet'));
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(esc_html__("E-mail inválido", 'amigopet'));
        }

        if (empty($this->phone)) {
            throw new \InvalidArgumentException(esc_html__("Telefone é obrigatório", 'amigopet'));
        }

        if (!preg_match('/^\+?[1-9][0-9]{7,14}$/', preg_replace('/[^0-9+]/', '', $this->phone))) {
            throw new \InvalidArgumentException(esc_html__("Telefone inválido", 'amigopet'));
        }

        if (empty($this->address)) {
            throw new \InvalidArgumentException(esc_html__("Endereço é obrigatório", 'amigopet'));
        }

        if ($this->website && !filter_var($this->website, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(esc_html__("Website inválido", 'amigopet'));
        }
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setStatus(string $status): void
    {
        if (!in_array($status, [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_PENDING])) {
            throw new \InvalidArgumentException(esc_html__("Status inválido", 'amigopet'));
        }
        $this->status = $status;
        $this->updateTimestamp();
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    private function updateTimestamp(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function activate(): void
    {
        if ($this->status === self::STATUS_ACTIVE) {
            throw new \DomainException(esc_html__("Organização já está ativa", 'amigopet'));
        }
        $this->status = self::STATUS_ACTIVE;
        $this->updateTimestamp();
    }

    public function deactivate(): void
    {
        if ($this->status === self::STATUS_INACTIVE) {
            throw new \DomainException(esc_html__("Organização já está inativa", 'amigopet'));
        }
        $this->status = self::STATUS_INACTIVE;
        $this->updateTimestamp();
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getPhone(): string
    {
        return $this->phone;
    }
    public function getWebsite(): ?string
    {
        return $this->website;
    }
    public function getAddress(): string
    {
        return $this->address;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    public function isInactive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}