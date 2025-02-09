<?php
namespace AmigoPetWp\Domain\Entities;

class Adopter {
    private $id;
    private $name;
    private $email;
    private $phone;
    private $document;
    private $address;
    private $adoptionHistory;
    private $createdAt;
    
    public function __construct(
        string $name,
        string $email,
        string $document,
        string $phone,
        string $address
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->document = $document;
        $this->phone = $phone;
        $this->address = $address;
        $this->createdAt = new \DateTimeImmutable();
        $this->validate();
    }
    
    private function validate(): void {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("E-mail inválido");
        }

        if (!preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $this->document)) {
            throw new \InvalidArgumentException("CPF inválido");
        }

        if (empty($this->phone)) {
            throw new \InvalidArgumentException("Telefone é obrigatório");
        }
            throw new \InvalidArgumentException("Nome deve ter pelo menos 3 caracteres");
        }
    }
    
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getStatus(): string { return $this->adoptionStatus; }
}
