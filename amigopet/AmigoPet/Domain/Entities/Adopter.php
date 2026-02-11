<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Entities;

if (!defined('ABSPATH')) {
    exit;
}

class Adopter
{
    private $id;
    private $name;
    private $email;
    private $phone;
    private $document;
    private $address;
    private $adoptionHistory;
    private $status;
    private $createdAt;

    // Constantes para status do adotante
    private const ADOPTER_STATUS = [
        'active' => 'Ativo',
        'pending_review' => 'Pendente de Revisão',
        'approved' => 'Aprovado',
        'rejected' => 'Rejeitado',
        'blocked' => 'Bloqueado'
    ];

    public function __construct(
        string $name,
        string $email,
        string $document,
        string $phone,
        string $address
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->document = $document;
        $this->phone = $phone;
        $this->address = $address;
        $this->status = 'active';
        $this->adoptionHistory = [];
        $this->createdAt = new \DateTimeImmutable();
        $this->validate();
    }

    private function validate(): void
    {
        if (empty($this->name) || strlen($this->name) < 3) {
            throw new \InvalidArgumentException(esc_html__("Nome deve ter pelo menos 3 caracteres", 'amigopet'));
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(esc_html__("E-mail inválido", 'amigopet'));
        }

        if (!preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $this->document)) {
            throw new \InvalidArgumentException(esc_html__("CPF inválido", 'amigopet'));
        }

        if (empty($this->phone)) {
            throw new \InvalidArgumentException(esc_html__("Telefone é obrigatório", 'amigopet'));
        }

        if (empty($this->address)) {
            throw new \InvalidArgumentException(esc_html__("Endereço é obrigatório", 'amigopet'));
        }
    }

    public function approve(): void
    {
        if ($this->status !== 'pending_review') {
            throw new \InvalidArgumentException(esc_html__("Adotante não está pendente de revisão", 'amigopet'));
        }
        $this->status = 'approved';
    }

    public function reject(): void
    {
        if ($this->status !== 'pending_review') {
            throw new \InvalidArgumentException(esc_html__("Adotante não está pendente de revisão", 'amigopet'));
        }
        $this->status = 'rejected';
    }

    public function block(): void
    {
        $this->status = 'blocked';
    }

    public function addToAdoptionHistory(int $adoptionId): void
    {
        $this->adoptionHistory[] = $adoptionId;
    }

    // Setters
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
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
    public function getDocument(): string
    {
        return $this->document;
    }
    public function getAddress(): string
    {
        return $this->address;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getStatusName(): string
    {
        return self::ADOPTER_STATUS[$this->status];
    }
    public function getAdoptionHistory(): array
    {
        return $this->adoptionHistory;
    }
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}