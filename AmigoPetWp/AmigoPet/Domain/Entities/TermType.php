<?php
namespace AmigoPetWp\Domain\Entities;

class TermType {
    private $id;
    private $name;
    private $slug;
    private $description;
    private $required;
    private $status;
    private $createdAt;
    private $updatedAt;

    public function __construct(
        string $name,
        string $slug,
        ?string $description = null,
        bool $required = false,
        ?string $status = 'active'
    ) {
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->required = $required;
        $this->status = $status;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getSlug(): string {
        return $this->slug;
    }

    public function setSlug(string $slug): void {
        $this->slug = $slug;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function isRequired(): bool {
        return $this->required;
    }

    public function setRequired(bool $required): void {
        $this->required = $required;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function setStatus(string $status): void {
        if (!in_array($status, ['active', 'inactive'])) {
            throw new \InvalidArgumentException('Status inválido');
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'required' => $this->required,
            'status' => $this->status,
            'created_at' => $this->createdAt ? $this->createdAt->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null
        ];
    }
}
