<?php
namespace AmigoPetWp\Domain\Entities;

class Event {
    private $id;
    private $organizationId;
    private $title;
    private $description;
    private $date;
    private $location;
    private $createdAt;

    public function __construct(
        int $organizationId,
        string $title,
        string $description,
        \DateTimeInterface $date,
        string $location
    ) {
        $this->organizationId = $organizationId;
        $this->title = $title;
        $this->description = $description;
        $this->date = $date;
        $this->location = $location;
        $this->createdAt = new \DateTimeImmutable();
        $this->validate();
    }

    private function validate(): void {
        if (empty($this->title)) {
            throw new \InvalidArgumentException("Título do evento é obrigatório");
        }

        if ($this->date < new \DateTime()) {
            throw new \InvalidArgumentException("Data do evento deve ser futura");
        }
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getDate(): \DateTimeInterface { return $this->date; }
    public function getOrganizationId(): int { return $this->organizationId; }
}
