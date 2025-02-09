<?php
namespace AmigoPetWp\Domain\Entities;

class Terms {
    private $id;
    private $adoptionId;
    private $documentType;
    private $signedBy;
    private $signedAt;
    private $documentUrl;
    private $createdAt;

    // Tipos de documentos permitidos
    private const ALLOWED_DOCUMENT_TYPES = [
        'adoption_term' => 'Termo de Adoção',
        'responsibility_term' => 'Termo de Responsabilidade',
        'health_term' => 'Declaração de Saúde do Animal',
        'spay_neuter_term' => 'Termo de Compromisso de Castração'
    ];

    public function __construct(
        int $adoptionId,
        string $documentType,
        string $signedBy,
        string $documentUrl
    ) {
        $this->adoptionId = $adoptionId;
        $this->documentType = $documentType;
        $this->signedBy = $signedBy;
        $this->documentUrl = $documentUrl;
        $this->signedAt = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
        $this->validate();
    }

    private function validate(): void {
        if (!array_key_exists($this->documentType, self::ALLOWED_DOCUMENT_TYPES)) {
            throw new \InvalidArgumentException("Tipo de documento inválido");
        }

        if (empty($this->signedBy)) {
            throw new \InvalidArgumentException("Nome do assinante é obrigatório");
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
    public function getSignedBy(): string { return $this->signedBy; }
    public function getSignedAt(): \DateTimeInterface { return $this->signedAt; }
    public function getDocumentUrl(): string { return $this->documentUrl; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }

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
}
