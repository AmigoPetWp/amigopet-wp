<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Entities;

if (!defined('ABSPATH')) {
    exit;
}

class Donation
{
    private $id;
    private $organizationId;
    private $donorName;
    private $donorEmail;
    private $donorPhone;
    private $amount;
    private $paymentMethod;
    private $paymentStatus;
    private $transactionId;
    private $description;
    private $date;
    private $createdAt;
    private $updatedAt;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    public const PAYMENT_METHOD_CREDIT = 'credit';
    public const PAYMENT_METHOD_DEBIT = 'debit';
    public const PAYMENT_METHOD_PIX = 'pix';
    public const PAYMENT_METHOD_BOLETO = 'boleto';

    public function __construct(
        int $organizationId,
        string $donorName,
        string $donorEmail,
        float $amount,
        string $paymentMethod,
        ?string $donorPhone = null,
        ?string $description = null
    ) {
        $this->organizationId = $organizationId;
        $this->donorName = $donorName;
        $this->donorEmail = $donorEmail;
        $this->donorPhone = $donorPhone;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
        $this->description = $description;
        $this->paymentStatus = self::STATUS_PENDING;
        $this->date = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->validate();
    }

    private function validate(): void
    {
        if (empty($this->donorName)) {
            throw new \InvalidArgumentException(esc_html__("Nome do doador é obrigatório", 'amigopet'));
        }

        if (strlen($this->donorName) > 100) {
            throw new \InvalidArgumentException(esc_html__("Nome do doador não pode ter mais que 100 caracteres", 'amigopet'));
        }

        if (empty($this->donorEmail)) {
            throw new \InvalidArgumentException(esc_html__("E-mail do doador é obrigatório", 'amigopet'));
        }

        if (!filter_var($this->donorEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(esc_html__("E-mail do doador inválido", 'amigopet'));
        }

        if ($this->donorPhone && !preg_match('/^\+?[1-9][0-9]{7,14}$/', preg_replace('/[^0-9+]/', '', $this->donorPhone))) {
            throw new \InvalidArgumentException(esc_html__("Telefone do doador inválido", 'amigopet'));
        }

        if ($this->amount <= 0) {
            throw new \InvalidArgumentException(esc_html__("Valor da doação deve ser maior que zero", 'amigopet'));
        }

        if (
            !in_array($this->paymentMethod, [
                self::PAYMENT_METHOD_CREDIT,
                self::PAYMENT_METHOD_DEBIT,
                self::PAYMENT_METHOD_PIX,
                self::PAYMENT_METHOD_BOLETO
            ])
        ) {
            throw new \InvalidArgumentException(esc_html__("Método de pagamento inválido", 'amigopet'));
        }

        if ($this->description && strlen($this->description) > 500) {
            throw new \InvalidArgumentException(esc_html__("Descrição não pode ter mais que 500 caracteres", 'amigopet'));
        }
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setTransactionId(string $transactionId): void
    {
        $this->transactionId = $transactionId;
        $this->updateTimestamp();
    }

    public function setPaymentStatus(string $status): void
    {
        if (
            !in_array($status, [
                self::STATUS_PENDING,
                self::STATUS_COMPLETED,
                self::STATUS_FAILED,
                self::STATUS_REFUNDED
            ])
        ) {
            throw new \InvalidArgumentException(esc_html__("Status de pagamento inválido", 'amigopet'));
        }
        $this->paymentStatus = $status;
        $this->updateTimestamp();
    }

    public function setDonorPhone(?string $phone): void
    {
        $this->donorPhone = $phone;
        $this->updateTimestamp();
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
        $this->updateTimestamp();
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
        $this->updateTimestamp();
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    private function updateTimestamp(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
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
    public function getDonorName(): string
    {
        return $this->donorName;
    }
    public function getDonorEmail(): string
    {
        return $this->donorEmail;
    }
    public function getDonorPhone(): ?string
    {
        return $this->donorPhone;
    }
    public function getAmount(): float
    {
        return $this->amount;
    }
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }
    public function getPaymentStatus(): string
    {
        return $this->paymentStatus;
    }
    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}