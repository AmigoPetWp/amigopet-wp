<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Entities;

if (!defined('ABSPATH')) {
    exit;
}

class PetMedicalRecord {
    private $id;
    private $petId;
    private $date;
    private $type;
    private $description;
    private $veterinarian;
    private $attachments;
    private $createdAt;
    private $updatedAt;
    
    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->petId = $data['pet_id'] ?? null;
        $this->date = $data['date'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->veterinarian = $data['veterinarian'] ?? null;
        $this->attachments = $data['attachments'] ?? [];
        $this->createdAt = $data['created_at'] ?? current_time('mysql');
        $this->updatedAt = $data['updated_at'] ?? current_time('mysql');
    }
    
    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    
    public function getPetId(): ?int {
        return $this->petId;
    }
    
    public function getDate(): ?string {
        return $this->date;
    }
    
    public function getType(): ?string {
        return $this->type;
    }
    
    public function getDescription(): ?string {
        return $this->description;
    }
    
    public function getVeterinarian(): ?string {
        return $this->veterinarian;
    }
    
    public function getAttachments(): array {
        return $this->attachments;
    }
    
    public function getCreatedAt(): string {
        return $this->createdAt;
    }
    
    public function getUpdatedAt(): string {
        return $this->updatedAt;
    }
    
    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }
    
    public function setPetId(int $petId): void {
        $this->petId = $petId;
    }
    
    public function setDate(string $date): void {
        $this->date = $date;
    }
    
    public function setType(string $type): void {
        $this->type = $type;
    }
    
    public function setDescription(string $description): void {
        $this->description = $description;
    }
    
    public function setVeterinarian(string $veterinarian): void {
        $this->veterinarian = $veterinarian;
    }
    
    public function setAttachments(array $attachments): void {
        $this->attachments = $attachments;
    }
    
    public function addAttachment(string $attachment): void {
        $this->attachments[] = $attachment;
    }
    
    public function removeAttachment(string $attachment): void {
        $key = array_search($attachment, $this->attachments);
        if ($key !== false) {
            unset($this->attachments[$key]);
        }
    }
    
    public function toArray(): array {
        return [
            'id' => $this->id,
            'pet_id' => $this->petId,
            'date' => $this->date,
            'type' => $this->type,
            'description' => $this->description,
            'veterinarian' => $this->veterinarian,
            'attachments' => $this->attachments,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}