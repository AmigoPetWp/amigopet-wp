<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Entities;

if (!defined('ABSPATH')) {
    exit;
}

class PetBreed {
    private $id;
    private $speciesId;
    private $name;
    private $description;
    private $characteristics;
    private $status;
    private $createdAt;
    private $updatedAt;

    public function __construct(
        int $speciesId,
        string $name,
        ?string $description = null,
        ?array $characteristics = null,
        ?string $status = 'active'
    ) {
        $this->speciesId = $speciesId;
        $this->name = $name;
        $this->description = $description;
        $this->characteristics = $characteristics ? json_encode($characteristics) : null;
        $this->status = $status;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getSpeciesId(): int {
        return $this->speciesId;
    }

    public function setSpeciesId(int $speciesId): void {
        $this->speciesId = $speciesId;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function getCharacteristics(): ?array {
        return $this->characteristics ? json_decode($this->characteristics, true) : null;
    }

    public function setCharacteristics(?array $characteristics): void {
        $this->characteristics = $characteristics ? json_encode($characteristics) : null;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function setStatus(string $status): void {
        if (!in_array($status, ['active', 'inactive'])) {
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
            'species_id' => $this->speciesId,
            'name' => $this->name,
            'description' => $this->description,
            'characteristics' => $this->getCharacteristics(),
            'status' => $this->status,
            'created_at' => $this->createdAt ? $this->createdAt->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null
        ];
    }
}