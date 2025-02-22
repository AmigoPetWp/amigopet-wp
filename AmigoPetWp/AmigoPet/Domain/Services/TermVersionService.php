<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\TermVersionRepository;
use AmigoPetWp\Domain\Entities\TermVersion;

class TermVersionService {
    private $repository;

    public function __construct(TermVersionRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Cria uma nova versão de um termo
     */
    public function createVersion(array $data): int {
        // Verifica se já existe uma versão com o mesmo número
        if ($this->repository->findByVersion($data['term_id'], $data['version'])) {
            throw new \InvalidArgumentException("Já existe uma versão {$data['version']} para este termo");
        }

        $version = new TermVersion(
            $data['term_id'],
            $data['content'],
            $data['version'],
            $data['status'] ?? 'draft',
            new \DateTime($data['effective_date']),
            $data['change_log'] ?? null
        );

        return $this->repository->save($version);
    }

    /**
     * Atualiza uma versão existente
     */
    public function updateVersion(int $id, array $data): void {
        $version = $this->repository->findById($id);
        if (!$version) {
            throw new \InvalidArgumentException("Versão não encontrada");
        }

        if (!$version->isDraft()) {
            throw new \InvalidArgumentException("Apenas versões em rascunho podem ser atualizadas");
        }

        if (isset($data['content'])) {
            $version->setContent($data['content']);
        }

        if (isset($data['effective_date'])) {
            $version->setEffectiveDate(new \DateTime($data['effective_date']));
        }

        if (isset($data['change_log'])) {
            $version->setChangeLog($data['change_log']);
        }

        $this->repository->save($version);
    }

    /**
     * Envia uma versão para revisão
     */
    public function sendToReview(int $id): void {
        $version = $this->repository->findById($id);
        if (!$version) {
            throw new \InvalidArgumentException("Versão não encontrada");
        }

        $version->sendToReview();
        $this->repository->save($version);

        // Notifica os revisores
        do_action('apwp_term_version_review_requested', $version);
    }

    /**
     * Aprova uma versão
     */
    public function approveVersion(int $id): void {
        $version = $this->repository->findById($id);
        if (!$version) {
            throw new \InvalidArgumentException("Versão não encontrada");
        }

        $version->approve();
        $this->repository->save($version);

        // Notifica o autor
        do_action('apwp_term_version_approved', $version);
    }

    /**
     * Ativa uma versão
     */
    public function activateVersion(int $id): void {
        $version = $this->repository->findById($id);
        if (!$version) {
            throw new \InvalidArgumentException("Versão não encontrada");
        }

        // Desativa todas as versões ativas do termo
        $this->repository->deactivateAllVersions($version->getTermId());

        // Ativa a nova versão
        $version->activate();
        $this->repository->save($version);

        // Notifica os administradores
        do_action('apwp_term_version_activated', $version);
    }

    /**
     * Desativa uma versão
     */
    public function deactivateVersion(int $id): void {
        $version = $this->repository->findById($id);
        if (!$version) {
            throw new \InvalidArgumentException("Versão não encontrada");
        }

        $version->deactivate();
        $this->repository->save($version);

        // Notifica os administradores
        do_action('apwp_term_version_deactivated', $version);
    }

    /**
     * Exclui uma versão
     */
    public function deleteVersion(int $id): void {
        if (!$this->repository->delete($id)) {
            throw new \RuntimeException("Erro ao excluir a versão");
        }

        // Notifica os administradores
        do_action('apwp_term_version_deleted', $id);
    }

    /**
     * Retorna a versão ativa de um termo
     */
    public function getActiveVersion(int $termId): ?TermVersion {
        return $this->repository->findActiveVersion($termId);
    }

    /**
     * Retorna todas as versões de um termo
     */
    public function getVersions(int $termId): array {
        return $this->repository->findByTerm($termId);
    }

    /**
     * Retorna as versões pendentes de revisão
     */
    public function getPendingReview(): array {
        return $this->repository->findPendingReview();
    }

    /**
     * Retorna as versões efetivas em uma data específica
     */
    public function getEffectiveVersions(\DateTimeInterface $date): array {
        return $this->repository->findEffectiveAt($date);
    }

    /**
     * Gera o próximo número de versão para um termo
     */
    public function generateNextVersion(int $termId, string $changeType = 'minor'): string {
        $currentVersion = $this->repository->getLatestVersion($termId) ?? '0.0.0';
        return TermVersion::generateNextVersion($currentVersion, $changeType);
    }

    /**
     * Compara duas versões
     */
    public function compareVersions(string $version1, string $version2): int {
        return TermVersion::compareVersions($version1, $version2);
    }

    /**
     * Retorna o histórico de mudanças de um termo
     */
    public function getChangeHistory(int $termId): array {
        $versions = $this->repository->findByTerm($termId);
        $history = [];

        foreach ($versions as $version) {
            $history[] = [
                'version' => $version->getVersion(),
                'status' => $version->getStatusName(),
                'change_log' => $version->getChangeLog(),
                'author' => get_userdata($version->getCreatedBy())->display_name,
                'effective_date' => $version->getEffectiveDate()->format('d/m/Y H:i'),
                'created_at' => $version->getCreatedAt()->format('d/m/Y H:i')
            ];
        }

        return $history;
    }

    /**
     * Verifica se uma versão pode ser atualizada
     */
    public function canUpdate(int $id): bool {
        $version = $this->repository->findById($id);
        return $version && $version->isDraft();
    }

    /**
     * Verifica se uma versão pode ser enviada para revisão
     */
    public function canSendToReview(int $id): bool {
        $version = $this->repository->findById($id);
        return $version && $version->isDraft();
    }

    /**
     * Verifica se uma versão pode ser aprovada
     */
    public function canApprove(int $id): bool {
        $version = $this->repository->findById($id);
        return $version && $version->isInReview() && current_user_can('approve_terms');
    }

    /**
     * Verifica se uma versão pode ser ativada
     */
    public function canActivate(int $id): bool {
        $version = $this->repository->findById($id);
        return $version && $version->isApproved() && current_user_can('activate_terms');
    }

    /**
     * Verifica se uma versão pode ser excluída
     */
    public function canDelete(int $id): bool {
        $version = $this->repository->findById($id);
        return $version && $version->isDraft() && current_user_can('delete_terms');
    }
}
