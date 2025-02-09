<?php
namespace AmigoPet\Domain\Entities;

class Volunteer {
    private $id;
    private $name;
    private $email;
    private $role;
    private $createdAt;

    public function __construct(
        string $name,
        string $email,
        string $role
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
        $this->validate();
    }

    private function validate(): void {
        $allowedRoles = ['rescue', 'shelter', 'adoption'];
        if (!in_array($this->role, $allowedRoles)) {
            throw new \InvalidArgumentException("Função inválida");
        }
    }
}
