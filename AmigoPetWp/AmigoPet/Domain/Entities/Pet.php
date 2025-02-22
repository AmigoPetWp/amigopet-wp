<?php
namespace AmigoPetWp\Domain\Entities;

class Pet {
    private $id;
    private $name;
    private $species;
    private $breed;
    private $age;
    private $size;
    private $description;
    private $status;
    private $organizationId;
    private $qrcodeId;
    private $rga;
    private $microchipNumber;
    private $healthInfo;
    private $createdAt;

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
        string $species,
        ?string $breed,
        ?int $age,
        string $size,
        ?string $description,
        int $organizationId,
        string $rga,
        string $microchipNumber,
        array $healthInfo = []
    ) {
        $this->name = $name;
        $this->species = $species;
        $this->breed = $breed;
        $this->age = $age;
        $this->size = $size;
        $this->description = $description;
        $this->organizationId = $organizationId;
        $this->rga = $rga;
        $this->microchipNumber = $microchipNumber;
        $this->healthInfo = $healthInfo;
        $this->status = 'available';
        $this->createdAt = new \DateTimeImmutable();
        $this->validate();
    }

    private function validate(): void {
        if (empty($this->name)) {
            throw new \InvalidArgumentException("Nome é obrigatório");
        }

        if (empty($this->species)) {
            throw new \InvalidArgumentException("Espécie é obrigatória");
        }

        $allowedSizes = ['small', 'medium', 'large'];
        if (!in_array($this->size, $allowedSizes)) {
            throw new \InvalidArgumentException("Porte inválido");
        }

        if (empty($this->rga)) {
            throw new \InvalidArgumentException("RGA é obrigatório");
        }

        if (empty($this->microchipNumber)) {
            throw new \InvalidArgumentException("Número do microchip é obrigatório");
        }

        if ($this->age !== null && $this->age < 0) {
            throw new \InvalidArgumentException("Idade não pode ser negativa");
        }
    }

    public function updateHealthInfo(string $type, \DateTimeInterface $date): void {
        if (!isset(self::HEALTH_STATUS[$type])) {
            throw new \InvalidArgumentException("Tipo de informação de saúde inválido");
        }

        $this->healthInfo["last_{$type}_date"] = $date;
    }

    public function getHealthInfo(): array {
        return $this->healthInfo;
    }

    public function getRGA(): string {
        return $this->rga;
    }

    public function getMicrochipNumber(): string {
        return $this->microchipNumber;
    }

    public function markAsAdopted(): void {
        if ($this->status !== 'available') {
            throw new \InvalidArgumentException("Pet não está disponível para adoção");
        }
        $this->status = 'adopted';
    }

    public function markAsAvailable(): void {
        $this->status = 'available';
    }

    public function markAsRescued(): void {
        $this->status = 'rescued';
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getSpecies(): string { return $this->species; }
    public function getBreed(): ?string { return $this->breed; }
    public function getAge(): ?int { return $this->age; }
    public function getSize(): string { return $this->size; }
    public function getDescription(): ?string { return $this->description; }
    public function getStatus(): string { return $this->status; }
    public function getStatusName(): string { return self::PET_STATUS[$this->status]; }
    public function getOrganizationId(): int { return $this->organizationId; }
    public function getQRCodeId(): ?int { return $this->qrcodeId; }
    public function getRGA(): string { return $this->rga; }
    public function getMicrochipNumber(): string { return $this->microchipNumber; }
    public function getHealthInfo(): array { return $this->healthInfo; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
}
