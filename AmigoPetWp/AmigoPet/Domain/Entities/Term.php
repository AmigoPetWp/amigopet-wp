<?php
namespace AmigoPetWp\Domain\Entities;

class Term {
    private ?int $id;
    private string $title;
    private string $content;
    private string $type;
    private bool $isRequired;
    private bool $isActive;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;
    private int $organizationId;
    private array $acceptedBy = [];

    public function __construct(
        int $organizationId,
        string $title,
        string $content,
        string $type,
        bool $isRequired = true,
        bool $isActive = true,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->organizationId = $organizationId;
        $this->title = $title;
        $this->content = $content;
        $this->type = $type;
        $this->isRequired = $isRequired;
        $this->isActive = $isActive;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->validate();
    }

    private function validate(): void {
        if (empty($this->title)) {
            throw new \InvalidArgumentException("O título do termo é obrigatório");
        }

        if (empty($this->content)) {
            throw new \InvalidArgumentException("O conteúdo do termo é obrigatório");
        }

        if (empty($this->type)) {
            throw new \InvalidArgumentException("O tipo do termo é obrigatório");
        }

        if (!in_array($this->type, ['adoption', 'volunteer', 'donation'])) {
            throw new \InvalidArgumentException("Tipo de termo inválido");
        }

        if ($this->organizationId <= 0) {
            throw new \InvalidArgumentException("ID da organização inválido");
        }
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
        $this->validate();
        $this->updatedAt = new \DateTime();
    }

    public function getContent(): string {
        return $this->content;
    }

    public function setContent(string $content): void {
        $this->content = $content;
        $this->validate();
        $this->updatedAt = new \DateTime();
    }

    public function getType(): string {
        return $this->type;
    }

    public function setType(string $type): void {
        $this->type = $type;
        $this->validate();
        $this->updatedAt = new \DateTime();
    }

    public function isRequired(): bool {
        return $this->isRequired;
    }

    public function setIsRequired(bool $isRequired): void {
        $this->isRequired = $isRequired;
        $this->updatedAt = new \DateTime();
    }

    public function isActive(): bool {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void {
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTime();
    }

    public function getOrganizationId(): int {
        return $this->organizationId;
    }

    public function getCreatedAt(): \DateTimeInterface {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface {
        return $this->updatedAt;
    }

    public function getAcceptedBy(): array {
        return $this->acceptedBy;
    }

    public function addAcceptedBy(int $userId): void {
        if (!in_array($userId, $this->acceptedBy)) {
            $this->acceptedBy[] = $userId;
            $this->updatedAt = new \DateTime();
        }
    }

    public function removeAcceptedBy(int $userId): void {
        $key = array_search($userId, $this->acceptedBy);
        if ($key !== false) {
            unset($this->acceptedBy[$key]);
            $this->acceptedBy = array_values($this->acceptedBy);
            $this->updatedAt = new \DateTime();
        }
    }

    public function wasAcceptedBy(int $userId): bool {
        return in_array($userId, $this->acceptedBy);
    }
}
