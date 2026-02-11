<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Entities;

if (!defined('ABSPATH')) {
    exit;
}

class Term
{
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

    private function validate(): void
    {
        if (empty($this->title)) {
            throw new \InvalidArgumentException(esc_html__("O título do termo é obrigatório", 'amigopet'));
        }

        if (empty($this->content)) {
            throw new \InvalidArgumentException(esc_html__("O conteúdo do termo é obrigatório", 'amigopet'));
        }

        if (empty($this->type)) {
            throw new \InvalidArgumentException(esc_html__("O tipo do termo é obrigatório", 'amigopet'));
        }

        if (!in_array($this->type, ['adoption', 'volunteer', 'donation'])) {
            throw new \InvalidArgumentException(esc_html__("Tipo de termo inválido", 'amigopet'));
        }

        if ($this->organizationId <= 0) {
            throw new \InvalidArgumentException(esc_html__("ID da organização inválido", 'amigopet'));
        }
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

    public function setTitle(string $title): void
    {
        $this->title = $title;
        $this->validate();
        $this->updatedAt = new \DateTime();
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
        $this->validate();
        $this->updatedAt = new \DateTime();
    }

    public function setType(string $type): void
    {
        $this->type = $type;
        $this->validate();
        $this->updatedAt = new \DateTime();
    }

    public function setRequired(bool $isRequired): void
    {
        $this->isRequired = $isRequired;
        $this->updatedAt = new \DateTime();
    }

    public function setActive(bool $isActive): void
    {
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTime();
    }

    public function setAcceptedBy(array $acceptedBy): void
    {
        $this->acceptedBy = $acceptedBy;
        $this->updatedAt = new \DateTime();
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function isRequired(): bool
    {
        return $this->isRequired;
    }
    public function isActive(): bool
    {
        return $this->isActive;
    }
    public function getOrganizationId(): int
    {
        return $this->organizationId;
    }
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }
    public function getAcceptedBy(): array
    {
        return $this->acceptedBy;
    }

    public function addAcceptedBy(int $userId): void
    {
        if (!in_array($userId, $this->acceptedBy)) {
            $this->acceptedBy[] = $userId;
            $this->updatedAt = new \DateTime();
        }
    }

    public function removeAcceptedBy(int $userId): void
    {
        $key = array_search($userId, $this->acceptedBy);
        if ($key !== false) {
            unset($this->acceptedBy[$key]);
            $this->acceptedBy = array_values($this->acceptedBy);
            $this->updatedAt = new \DateTime();
        }
    }

    public function wasAcceptedBy(int $userId): bool
    {
        return in_array($userId, $this->acceptedBy);
    }
}