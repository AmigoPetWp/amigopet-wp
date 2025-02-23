<?php
namespace AmigoPetWp\Domain\Entities;

class AdoptionPayment {
    private ?int $id = null;
    private int $adoptionId;
    private float $amount;
    private string $paymentMethod;
    private string $status;
    private ?string $transactionId = null;
    private ?string $payerName = null;
    private ?string $payerEmail = null;
    private ?string $payerDocument = null;
    private ?\DateTime $paymentDate = null;
    private ?\DateTime $refundDate = null;
    private ?string $refundReason = null;
    private ?array $gatewayResponse = null;
    private ?string $notes = null;
    private ?\DateTime $createdAt = null;
    private ?\DateTime $updatedAt = null;

    // Constantes para métodos de pagamento
    public const PAYMENT_METHOD_CREDIT_CARD = 'credit_card';
    public const PAYMENT_METHOD_PIX = 'pix';
    public const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';
    public const PAYMENT_METHOD_CASH = 'cash';
    public const PAYMENT_METHOD_OTHER = 'other';

    private const PAYMENT_METHODS = [
        self::PAYMENT_METHOD_CREDIT_CARD => 'Cartão de Crédito',
        self::PAYMENT_METHOD_PIX => 'PIX',
        self::PAYMENT_METHOD_BANK_TRANSFER => 'Transferência Bancária',
        self::PAYMENT_METHOD_CASH => 'Dinheiro',
        self::PAYMENT_METHOD_OTHER => 'Outro'
    ];

    // Constantes para status de pagamento
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_CANCELLED = 'cancelled';

    private const PAYMENT_STATUS = [
        self::STATUS_PENDING => 'Pendente',
        self::STATUS_PROCESSING => 'Processando',
        self::STATUS_COMPLETED => 'Concluído',
        self::STATUS_FAILED => 'Falhou',
        self::STATUS_REFUNDED => 'Reembolsado',
        self::STATUS_CANCELLED => 'Cancelado'
    ];

    public function __construct(
        int $adoptionId,
        float $amount,
        string $paymentMethod,
        ?string $payerName = null,
        ?string $payerEmail = null,
        ?string $payerDocument = null
    ) {
        $this->adoptionId = $adoptionId;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
        $this->status = self::STATUS_PENDING;
        $this->payerName = $payerName;
        $this->payerEmail = $payerEmail;
        $this->payerDocument = $payerDocument;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->validate();
    }

    private function validate(): void {
        if ($this->amount <= 0) {
            throw new \InvalidArgumentException("O valor do pagamento deve ser maior que zero");
        }

        if (!array_key_exists($this->paymentMethod, self::PAYMENT_METHODS)) {
            throw new \InvalidArgumentException("Método de pagamento inválido");
        }

        if ($this->payerEmail && !filter_var($this->payerEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email do pagador inválido");
        }
    }

    public function startProcessing(?string $transactionId = null): void {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \InvalidArgumentException("Apenas pagamentos pendentes podem ser processados");
        }
        $this->status = self::STATUS_PROCESSING;
        if ($transactionId) {
            $this->transactionId = $transactionId;
        }
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function confirmPayment(?string $transactionId = null): void {
        if ($this->status !== self::STATUS_PENDING && $this->status !== self::STATUS_PROCESSING) {
            throw new \InvalidArgumentException("Pagamento não pode ser confirmado no status atual");
        }
        $this->status = self::STATUS_COMPLETED;
        $this->paymentDate = new \DateTimeImmutable();
        if ($transactionId) {
            $this->transactionId = $transactionId;
        }
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function markAsFailed(?string $reason = null): void {
        if ($this->status === self::STATUS_COMPLETED || $this->status === self::STATUS_REFUNDED) {
            throw new \InvalidArgumentException("Pagamento não pode ser marcado como falho no status atual");
        }
        $this->status = self::STATUS_FAILED;
        if ($reason) {
            $this->notes = $reason;
        }
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function refundPayment(?string $reason = null): void {
        if ($this->status !== self::STATUS_COMPLETED) {
            throw new \InvalidArgumentException("Apenas pagamentos concluídos podem ser reembolsados");
        }
        $this->status = self::STATUS_REFUNDED;
        $this->refundDate = new \DateTimeImmutable();
        if ($reason) {
            $this->refundReason = $reason;
        }
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function cancel(?string $reason = null): void {
        if ($this->status === self::STATUS_COMPLETED || $this->status === self::STATUS_REFUNDED) {
            throw new \InvalidArgumentException("Pagamento não pode ser cancelado no status atual");
        }
        $this->status = self::STATUS_CANCELLED;
        if ($reason) {
            $this->notes = $reason;
        }
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateGatewayResponse(array $response): void {
        $this->gatewayResponse = $response;
        $this->updatedAt = new \DateTimeImmutable();
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
    public function getPayerName(): ?string { return $this->payerName; }
    public function getPayerEmail(): ?string { return $this->payerEmail; }
    public function getPayerDocument(): ?string { return $this->payerDocument; }
    public function getPaymentDate(): ?\DateTimeInterface { return $this->paymentDate; }
    public function getRefundDate(): ?\DateTimeInterface { return $this->refundDate; }
    public function getRefundReason(): ?string { return $this->refundReason; }
    public function getGatewayResponse(): ?array { return $this->gatewayResponse; }
    public function getNotes(): ?string { return $this->notes; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setPayerName(?string $payerName): void { $this->payerName = $payerName; }
    public function setPayerEmail(?string $payerEmail): void {
        if ($payerEmail && !filter_var($payerEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email do pagador inválido");
        }
        $this->payerEmail = $payerEmail;
    }
    public function setPayerDocument(?string $payerDocument): void { $this->payerDocument = $payerDocument; }
    public function setNotes(?string $notes): void { $this->notes = $notes; }

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

    /**
     * Retorna todos os status de pagamento disponíveis
     */
    public static function getAvailablePaymentStatus(): array {
        return self::PAYMENT_STATUS;
    }
}
