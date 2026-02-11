<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Entities;

if (!defined('ABSPATH')) {
    exit;
}

class SignedTerm {
    private $id;
    private $termId;
    private $userId;
    private $adoptionId;
    private $signedAt;
    private $ipAddress;
    private $userAgent;
    private $documentUrl;
    private $status;
    private $createdAt;
    private $updatedAt;

    public function __construct(
        int $termId,
        int $userId,
        ?int $adoptionId = null,
        string $ipAddress,
        string $userAgent,
        ?string $documentUrl = null,
        ?string $status = 'signed'
    ) {
        $this->termId = $termId;
        $this->userId = $userId;
        $this->adoptionId = $adoptionId;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->documentUrl = $documentUrl;
        $this->status = $status;
        $this->signedAt = new \DateTime();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getTermId(): int {
        return $this->termId;
    }

    public function setTermId(int $termId): void {
        $this->termId = $termId;
    }

    public function getUserId(): int {
        return $this->userId;
    }

    public function setUserId(int $userId): void {
        $this->userId = $userId;
    }

    public function getAdoptionId(): ?int {
        return $this->adoptionId;
    }

    public function setAdoptionId(?int $adoptionId): void {
        $this->adoptionId = $adoptionId;
    }

    public function getSignedAt(): \DateTime {
        return $this->signedAt;
    }

    public function setSignedAt(\DateTime $signedAt): void {
        $this->signedAt = $signedAt;
    }

    public function getIpAddress(): string {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): void {
        $this->ipAddress = $ipAddress;
    }

    public function getUserAgent(): string {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): void {
        $this->userAgent = $userAgent;
    }

    public function getDocumentUrl(): ?string {
        return $this->documentUrl;
    }

    public function setDocumentUrl(?string $documentUrl): void {
        $this->documentUrl = $documentUrl;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function setStatus(string $status): void {
        if (!in_array($status, ['signed', 'revoked', 'expired'])) {
            throw new \InvalidArgumentException(esc_html__('Status inválido', 'amigopet'));
        }
        $this->status = $status;
    }

    public function getCreatedAt(): ?\DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTime {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'term_id' => $this->termId,
            'user_id' => $this->userId,
            'adoption_id' => $this->adoptionId,
            'signed_at' => $this->signedAt->format('Y-m-d H:i:s'),
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'document_url' => $this->documentUrl,
            'status' => $this->status,
            'created_at' => $this->createdAt ? $this->createdAt->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null
        ];
    }
}