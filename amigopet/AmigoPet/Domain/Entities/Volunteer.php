<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Entities;

if (!defined('ABSPATH')) {
    exit;
}

class Volunteer
{
    private $id;
    private $organizationId;
    private $name;
    private $email;
    private $phone;
    private $availability;
    private $skills;
    private $status;
    private $startDate;
    private $endDate;

    public function __construct(
        int $organizationId,
        string $name,
        string $email,
        string $phone,
        string $availability,
        array $skills = [],
        string $status = 'active'
    ) {
        $this->organizationId = $organizationId;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->availability = $availability;
        $this->skills = $skills;
        $this->status = $status;
        $this->validate();
    }

    private function validate(): void
    {
        if (empty($this->name)) {
            throw new \InvalidArgumentException(esc_html__("Nome é obrigatório", 'amigopet'));
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(esc_html__("E-mail inválido", 'amigopet'));
        }
        $allowedStatus = ['active', 'inactive', 'pending'];
        if (!in_array($this->status, $allowedStatus)) {
            throw new \InvalidArgumentException(esc_html__("Status inválido", 'amigopet'));
        }
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getOrganizationId(): int
    {
        return $this->organizationId;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getPhone(): string
    {
        return $this->phone;
    }
    public function getAvailability(): string
    {
        return $this->availability;
    }
    public function getSkills(): array
    {
        return $this->skills;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    // Setters
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setOrganizationId(int $id): void
    {
        $this->organizationId = $id;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }
    public function setAvailability(string $availability): void
    {
        $this->availability = $availability;
    }
    public function setSkills(array $skills): void
    {
        $this->skills = $skills;
    }
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
    public function setStartDate(?\DateTime $date): void
    {
        $this->startDate = $date;
    }
    public function setEndDate(?\DateTime $date): void
    {
        $this->endDate = $date;
    }
}