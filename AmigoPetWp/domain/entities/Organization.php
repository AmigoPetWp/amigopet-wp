<?php
namespace AmigoPet\Domain\Entities;

class Organization {
    private $id;
    private $name;
    private $email;
    private $phone;
    private $website;
    private $address;
    private $createdAt;

    public function __construct(
        string $name,
        string $email,
        string $phone,
        string $address,
        ?string $website = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->website = $website;
        $this->createdAt = new \DateTimeImmutable();
        $this->validate();
    }

    private function validate(): void {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("E-mail inválido");
        }
    }
}
