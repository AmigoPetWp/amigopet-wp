<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Entities;

if (!defined('ABSPATH')) {
    exit;
}

class QRCode
{
    private $id;
    private $petId;
    private $qrcodeUrl;
    private $createdAt;

    public function __construct(int $petId)
    {
        $this->petId = $petId;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function setQRCodeUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(esc_html__("URL do QR Code inválida", 'amigopet'));
        }
        $this->qrcodeUrl = $url;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function generateTrackingUrl(): string
    {
        return site_url("/pet-tracking/{$this->petId}");
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }
    public function getPetId(): int
    {
        return $this->petId;
    }
    public function getQRCodeUrl(): ?string
    {
        return $this->qrcodeUrl;
    }
}