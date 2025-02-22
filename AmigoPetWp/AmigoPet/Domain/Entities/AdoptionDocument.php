<?php
namespace AmigoPetWp\Domain\Entities;

class AdoptionDocument {
    private ?int $id;
    private int $adoptionId;
    private string $documentType;
    private string $content;
    private int $signedBy;
    private \DateTimeInterface $signedAt;
    private string $documentUrl;
    private string $status;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;

    // Tipos de documentos permitidos
    private const ALLOWED_DOCUMENT_TYPES = [
        'adoption_term' => 'Termo de Adoção',
        'responsibility_term' => 'Termo de Responsabilidade',
        'health_term' => 'Declaração de Saúde do Animal',
        'spay_neuter_term' => 'Termo de Compromisso de Castração'
    ];

    // Status permitidos
    private const ALLOWED_STATUS = [
        'draft' => 'Rascunho',
        'pending' => 'Pendente de Assinatura',
        'signed' => 'Assinado',
        'expired' => 'Expirado',
        'cancelled' => 'Cancelado'
    ];

    public function __construct(
        int $adoptionId,
        string $documentType,
        string $content,
        int $signedBy,
        string $documentUrl,
        string $status = 'pending',
        ?int $id = null
    ) {
        $this->id = $id;
        $this->adoptionId = $adoptionId;
        $this->documentType = $documentType;
        $this->content = $content;
        $this->signedBy = $signedBy;
        $this->documentUrl = $documentUrl;
        $this->status = $status;
        $this->signedAt = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->validate();
    }

    private function validate(): void {
        if (!array_key_exists($this->documentType, self::ALLOWED_DOCUMENT_TYPES)) {
            throw new \InvalidArgumentException("Tipo de documento inválido");
        }

        if (!array_key_exists($this->status, self::ALLOWED_STATUS)) {
            throw new \InvalidArgumentException("Status inválido");
        }

        if (empty($this->content)) {
            throw new \InvalidArgumentException("Conteúdo do documento é obrigatório");
        }

        if ($this->signedBy <= 0) {
            throw new \InvalidArgumentException("ID do assinante inválido");
        }

        if (!filter_var($this->documentUrl, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("URL do documento inválida");
        }
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getAdoptionId(): int { return $this->adoptionId; }
    public function getDocumentType(): string { return $this->documentType; }
    public function getDocumentTypeName(): string { return self::ALLOWED_DOCUMENT_TYPES[$this->documentType]; }
    public function getContent(): string { return $this->content; }
    public function getSignedBy(): int { return $this->signedBy; }
    public function getSignedAt(): \DateTimeInterface { return $this->signedAt; }
    public function getDocumentUrl(): string { return $this->documentUrl; }
    public function getStatus(): string { return $this->status; }
    public function getStatusName(): string { return self::ALLOWED_STATUS[$this->status]; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeInterface { return $this->updatedAt; }

    // Setters
    public function setContent(string $content): void {
        $this->content = $content;
        $this->validate();
        $this->updatedAt = new \DateTime();
    }

    public function setStatus(string $status): void {
        $this->status = $status;
        $this->validate();
        $this->updatedAt = new \DateTime();
    }

    public function setDocumentUrl(string $documentUrl): void {
        $this->documentUrl = $documentUrl;
        $this->validate();
        $this->updatedAt = new \DateTime();
    }

    // Status helpers
    public function isDraft(): bool { return $this->status === 'draft'; }
    public function isPending(): bool { return $this->status === 'pending'; }
    public function isSigned(): bool { return $this->status === 'signed'; }
    public function isExpired(): bool { return $this->status === 'expired'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }

    // Actions
    public function sign(): void {
        if (!$this->isPending()) {
            throw new \InvalidArgumentException("Documento não está pendente de assinatura");
        }
        $this->status = 'signed';
        $this->signedAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function cancel(): void {
        if ($this->isSigned()) {
            throw new \InvalidArgumentException("Documento já foi assinado");
        }
        $this->status = 'cancelled';
        $this->updatedAt = new \DateTime();
    }

    public function expire(): void {
        if ($this->isSigned() || $this->isCancelled()) {
            throw new \InvalidArgumentException("Não é possível expirar um documento assinado ou cancelado");
        }
        $this->status = 'expired';
        $this->updatedAt = new \DateTime();
    }

    /**
     * Verifica se um tipo de documento é válido
     */
    public static function isValidDocumentType(string $type): bool {
        return array_key_exists($type, self::ALLOWED_DOCUMENT_TYPES);
    }

    /**
     * Retorna todos os tipos de documentos permitidos
     */
    public static function getAllowedDocumentTypes(): array {
        return self::ALLOWED_DOCUMENT_TYPES;
    }

    /**
     * Retorna todos os status permitidos
     */
    public static function getAllowedStatus(): array {
        return self::ALLOWED_STATUS;
    }
}
