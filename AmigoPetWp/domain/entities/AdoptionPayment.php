<?php
namespace AmigoPet\Domain\Entities;

class AdoptionPayment {
    private $id;
    private $adoptionId;
    private $amount;
    private $paymentMethod;
    private $status;
    private $transactionId;
    private $paymentUrl;
    private $paidAt;
    private $createdAt;

    // Constantes para métodos de pagamento
    private const PAYMENT_METHODS = [
        'pix' => 'PIX',
        'credit_card' => 'Cartão de Crédito',
        'bank_slip' => 'Boleto Bancário'
    ];

    // Constantes para status de pagamento
    private const PAYMENT_STATUS = [
        'pending' => 'Pendente',
        'processing' => 'Processando',
        'completed' => 'Concluído',
        'failed' => 'Falhou',
        'refunded' => 'Reembolsado'
    ];

    public function __construct(
        int $adoptionId,
        float $amount,
        string $paymentMethod
    ) {
        $this->adoptionId = $adoptionId;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
        $this->status = 'pending';
        $this->createdAt = new \DateTimeImmutable();
        $this->validate();
    }

    private function validate(): void {
        if ($this->amount <= 0) {
            throw new \InvalidArgumentException("O valor do pagamento deve ser maior que zero");
        }

        if (!array_key_exists($this->paymentMethod, self::PAYMENT_METHODS)) {
            throw new \InvalidArgumentException("Método de pagamento inválido");
        }
    }

    public function processPayment(string $transactionId, string $paymentUrl): void {
        $this->transactionId = $transactionId;
        $this->paymentUrl = $paymentUrl;
        $this->status = 'processing';
    }

    public function confirmPayment(): void {
        if ($this->status !== 'processing') {
            throw new \InvalidArgumentException("Pagamento não está em processamento");
        }
        $this->status = 'completed';
        $this->paidAt = new \DateTimeImmutable();
    }

    public function failPayment(): void {
        if ($this->status !== 'processing') {
            throw new \InvalidArgumentException("Pagamento não está em processamento");
        }
        $this->status = 'failed';
    }

    public function refundPayment(): void {
        if ($this->status !== 'completed') {
            throw new \InvalidArgumentException("Apenas pagamentos concluídos podem ser reembolsados");
        }
        $this->status = 'refunded';
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getAdoptionId(): int { return $this->adoptionId; }
    public function getAmount(): float { return $this->amount; }
    public function getPaymentMethod(): string { return $this->paymentMethod; }
    public function getPaymentMethodName(): string { return self::PAYMENT_METHODS[$this->paymentMethod]; }
    public function getStatus(): string { return $this->status; }
    public function getStatusName(): string { return self::PAYMENT_STATUS[$this->status]; }
    public function getTransactionId(): ?string { return $this->transactionId; }
    public function getPaymentUrl(): ?string { return $this->paymentUrl; }
    public function getPaidAt(): ?\DateTimeInterface { return $this->paidAt; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }

    /**
     * Retorna todos os métodos de pagamento disponíveis
     */
    public static function getAvailablePaymentMethods(): array {
        return self::PAYMENT_METHODS;
    }

    /**
     * Verifica se um método de pagamento é válido
     */
    public static function isValidPaymentMethod(string $method): bool {
        return array_key_exists($method, self::PAYMENT_METHODS);
    }
}
