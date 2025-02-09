<?php
namespace Domain\Entities;

class Donation {
    private $id;
    private $organizationId;
    private $donorName;
    private $donorEmail;
    private $amount;
    private $paymentMethod;
    private $date;
    private $createdAt;

    public function __construct(
        int $organizationId,
        string $donorName,
        string $donorEmail,
        float $amount,
        string $paymentMethod
    ) {
        $this->organizationId = $organizationId;
        $this->donorName = $donorName;
        $this->donorEmail = $donorEmail;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
        $this->date = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
        $this->validate();
    }

    private function validate(): void {
        if ($this->amount <= 0) {
            throw new \InvalidArgumentException("Valor da doação deve ser maior que zero");
        }

        if (!filter_var($this->donorEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("E-mail do doador inválido");
        }
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getAmount(): float { return $this->amount; }
    public function getDonorName(): string { return $this->donorName; }
    public function getOrganizationId(): int { return $this->organizationId; }
}
