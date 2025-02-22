<?php
namespace AmigoPetWp\Domain\Entities;

class TermVersion {
    private ?int $id;
    private int $termId;
    private string $content;
    private string $version;
    private string $status;
    private ?string $changeLog;
    private int $createdBy;
    private \DateTimeInterface $effectiveDate;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;

    // Status permitidos
    private const ALLOWED_STATUS = [
        'draft' => 'Rascunho',
        'review' => 'Em Revisão',
        'approved' => 'Aprovado',
        'active' => 'Ativo',
        'inactive' => 'Inativo'
    ];

    public function __construct(
        int $termId,
        string $content,
        string $version,
        string $status,
        \DateTimeInterface $effectiveDate,
        ?string $changeLog = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->termId = $termId;
        $this->content = $content;
        $this->version = $version;
        $this->status = $status;
        $this->effectiveDate = $effectiveDate;
        $this->changeLog = $changeLog;
        $this->createdBy = get_current_user_id();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->validate();
    }

    private function validate(): void {
        if (!array_key_exists($this->status, self::ALLOWED_STATUS)) {
            throw new \InvalidArgumentException("Status inválido");
        }

        if (empty($this->content)) {
            throw new \InvalidArgumentException("Conteúdo é obrigatório");
        }

        if (empty($this->version)) {
            throw new \InvalidArgumentException("Versão é obrigatória");
        }

        if (!preg_match('/^\d+\.\d+\.\d+$/', $this->version)) {
            throw new \InvalidArgumentException("Formato de versão inválido. Use o formato: X.Y.Z");
        }
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getTermId(): int { return $this->termId; }
    public function getContent(): string { return $this->content; }
    public function getVersion(): string { return $this->version; }
    public function getStatus(): string { return $this->status; }
    public function getStatusName(): string { return self::ALLOWED_STATUS[$this->status]; }
    public function getChangeLog(): ?string { return $this->changeLog; }
    public function getCreatedBy(): int { return $this->createdBy; }
    public function getEffectiveDate(): \DateTimeInterface { return $this->effectiveDate; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeInterface { return $this->updatedAt; }

    // Setters
    public function setContent(string $content): void {
        $this->content = $content;
        $this->validate();
        $this->updatedAt = new \DateTime();
    }

    public function setStatus(string $status): void {
        $this->status = $status;
        $this->validate();
        $this->updatedAt = new \DateTime();
    }

    public function setEffectiveDate(\DateTimeInterface $effectiveDate): void {
        $this->effectiveDate = $effectiveDate;
        $this->updatedAt = new \DateTime();
    }

    public function setChangeLog(?string $changeLog): void {
        $this->changeLog = $changeLog;
        $this->updatedAt = new \DateTime();
    }

    // Status helpers
    public function isDraft(): bool { return $this->status === 'draft'; }
    public function isInReview(): bool { return $this->status === 'review'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isActive(): bool { return $this->status === 'active'; }
    public function isInactive(): bool { return $this->status === 'inactive'; }

    // Actions
    public function sendToReview(): void {
        if (!$this->isDraft()) {
            throw new \InvalidArgumentException("Apenas versões em rascunho podem ser enviadas para revisão");
        }
        $this->status = 'review';
        $this->updatedAt = new \DateTime();
    }

    public function approve(): void {
        if (!$this->isInReview()) {
            throw new \InvalidArgumentException("Apenas versões em revisão podem ser aprovadas");
        }
        $this->status = 'approved';
        $this->updatedAt = new \DateTime();
    }

    public function activate(): void {
        if (!$this->isApproved()) {
            throw new \InvalidArgumentException("Apenas versões aprovadas podem ser ativadas");
        }
        $this->status = 'active';
        $this->updatedAt = new \DateTime();
    }

    public function deactivate(): void {
        if (!$this->isActive()) {
            throw new \InvalidArgumentException("Apenas versões ativas podem ser desativadas");
        }
        $this->status = 'inactive';
        $this->updatedAt = new \DateTime();
    }

    /**
     * Verifica se um status é válido
     */
    public static function isValidStatus(string $status): bool {
        return array_key_exists($status, self::ALLOWED_STATUS);
    }

    /**
     * Retorna todos os status permitidos
     */
    public static function getAllowedStatus(): array {
        return self::ALLOWED_STATUS;
    }

    /**
     * Compara duas versões
     * @return int -1 se $version1 < $version2, 0 se iguais, 1 se $version1 > $version2
     */
    public static function compareVersions(string $version1, string $version2): int {
        $v1 = array_map('intval', explode('.', $version1));
        $v2 = array_map('intval', explode('.', $version2));

        for ($i = 0; $i < 3; $i++) {
            if ($v1[$i] < $v2[$i]) return -1;
            if ($v1[$i] > $v2[$i]) return 1;
        }

        return 0;
    }

    /**
     * Gera a próxima versão com base no tipo de mudança
     * @param string $currentVersion Versão atual
     * @param string $changeType Tipo de mudança: major, minor ou patch
     * @return string Nova versão
     */
    public static function generateNextVersion(string $currentVersion, string $changeType): string {
        $version = array_map('intval', explode('.', $currentVersion));

        switch ($changeType) {
            case 'major':
                return ($version[0] + 1) . '.0.0';
            case 'minor':
                return $version[0] . '.' . ($version[1] + 1) . '.0';
            case 'patch':
                return $version[0] . '.' . $version[1] . '.' . ($version[2] + 1);
            default:
                throw new \InvalidArgumentException("Tipo de mudança inválido");
        }
    }
}
