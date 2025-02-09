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
    private $rga; // Registro Geral do Animal
    private $microchipNumber;
    private $healthInfo;
    private $createdAt;

    // Constantes para status de saúde
    private const HEALTH_STATUS = [
        'castrated' => 'Castrado',
        'vaccinated' => 'Vacinado',
        'dewormed' => 'Vermifugado',
        'flea_controlled' => 'Controle de Ectoparasitas'
    ];

    public function __construct(
        string $name, 
        string $species,
        string $size,
        int $organizationId,
        string $rga,
        string $microchipNumber
    ) {
        $this->name = $name;
        $this->species = $species;
        $this->size = $size;
        $this->organizationId = $organizationId;
        $this->rga = $rga;
        $this->microchipNumber = $microchipNumber;
        $this->status = 'available';
        $this->healthInfo = [
            'castrated' => true,
            'vaccinated' => true,
            'dewormed' => true,
            'flea_controlled' => true,
            'last_vaccination_date' => null,
            'last_deworming_date' => null,
            'last_flea_control_date' => null
        ];
        $this->createdAt = new \DateTimeImmutable();
        $this->validate();
    }

    private function validate(): void {
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

    // Getters
    public function getId(): int { return $this->id; }
    public function getStatus(): string { return $this->status; }
}
