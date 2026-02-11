<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Entities;

if (!defined('ABSPATH')) {
    exit;
}

class Pet
{
    private ?int $id = null;
    private string $name;
    private int $speciesId;
    private int $breedId;
    private ?int $age = null;
    private string $size;
    private ?string $description = null;
    private string $status;
    private int $organizationId;
    private ?int $qrcodeId = null;
    private ?string $rga = null;
    private ?string $microchipNumber = null;
    private array $healthInfo = [];
    private \DateTimeInterface $createdAt;

    // Constantes para status do pet
    private const PET_STATUS = [
        'available' => 'Disponível',
        'adopted' => 'Adotado',
        'rescued' => 'Resgatado'
    ];

    // Constantes para informações de saúde
    private const HEALTH_STATUS = [
        'vaccination' => 'Vacinação',
        'deworming' => 'Vermifugação',
        'castration' => 'Castração',
        'checkup' => 'Check-up'
    ];

    public function __construct(
        string $name,
        int $speciesId,
        int $breedId,
        int $organizationId
    ) {
        $this->name = $name;
        $this->speciesId = $speciesId;
        $this->breedId = $breedId;
        $this->organizationId = $organizationId;
        $this->status = 'available';
        $this->size = 'medium';
        $this->createdAt = new \DateTimeImmutable();
    }

    private function validate(): void
    {
        if (empty($this->name)) {
            throw new \InvalidArgumentException(esc_html__("Nome é obrigatório", 'amigopet'));
        }
    }

    public function updateHealthInfo(string $type, \DateTimeInterface $date): void
    {
        if (!isset(self::HEALTH_STATUS[$type])) {
            throw new \InvalidArgumentException(esc_html__("Tipo de informação de saúde inválido", 'amigopet'));
        }

        $this->healthInfo["last_{$type}_date"] = $date;
    }

    public function markAsAdopted(): void
    {
        if ($this->status !== 'available') {
            throw new \InvalidArgumentException(esc_html__("Pet não está disponível para adoção", 'amigopet'));
        }
        $this->status = 'adopted';
    }

    public function markAsAvailable(): void
    {
        $this->status = 'available';
    }

    public function markAsRescued(): void
    {
        $this->status = 'rescued';
    }

    // Hydration Setters
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function setSpeciesId(int $id): self
    {
        $this->speciesId = $id;
        return $this;
    }
    public function setBreedId(int $id): self
    {
        $this->breedId = $id;
        return $this;
    }
    public function setAge(?int $age): self
    {
        $this->age = $age;
        return $this;
    }
    public function setSize(string $size): self
    {
        $this->size = $size;
        return $this;
    }
    public function setDescription(?string $desc): self
    {
        $this->description = $desc;
        return $this;
    }
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
    public function setOrganizationId(int $id): self
    {
        $this->organizationId = $id;
        return $this;
    }
    public function setQRCodeId(?int $id): self
    {
        $this->qrcodeId = $id;
        return $this;
    }
    public function setRGA(?string $rga): self
    {
        $this->rga = $rga;
        return $this;
    }
    public function setMicrochipNumber(?string $num): self
    {
        $this->microchipNumber = $num;
        return $this;
    }
    public function setHealthInfo(array $info): self
    {
        $this->healthInfo = $info;
        return $this;
    }
    public function setCreatedAt(\DateTimeInterface $date): self
    {
        $this->createdAt = $date;
        return $this;
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
    public function getSpeciesId(): int
    {
        return $this->speciesId;
    }
    public function getBreedId(): int
    {
        return $this->breedId;
    }
    public function getAge(): ?int
    {
        return $this->age;
    }
    public function getSize(): string
    {
        return $this->size;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getStatusName(): string
    {
        return self::PET_STATUS[$this->status] ?? 'Desconhecido';
    }
    public function getOrganizationId(): int
    {
        return $this->organizationId;
    }
    public function getQRCodeId(): ?int
    {
        return $this->qrcodeId;
    }
    public function getRGA(): ?string
    {
        return $this->rga;
    }
    public function getMicrochipNumber(): ?string
    {
        return $this->microchipNumber;
    }
    public function getHealthInfo(): array
    {
        return $this->healthInfo;
    }
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}