<?php
namespace AmigoPet\Domain\Entities;

class QRCode {
    private $id;
    private $petId;
    private $qrcodeUrl;
    private $createdAt;

    public function __construct(int $petId) {
        $this->petId = $petId;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function setQRCodeUrl(string $url): void {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("URL do QR Code inválida");
        }
        $this->qrcodeUrl = $url;
    }

    public function generateTrackingUrl(): string {
        return site_url("/pet-tracking/{$this->petId}");
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getPetId(): int { return $this->petId; }
    public function getQRCodeUrl(): ?string { return $this->qrcodeUrl; }
}
