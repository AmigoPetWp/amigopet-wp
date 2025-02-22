<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Services\TermVersionService;
use AmigoPetWp\Domain\Services\DocumentTemplateService;

class AdminTermVersionController {
    private $versionService;
    private $templateService;

    public function __construct(
        TermVersionService $versionService,
        DocumentTemplateService $templateService
    ) {
        $this->versionService = $versionService;
        $this->templateService = $templateService;

        // Registra os menus
        add_action('admin_menu', [$this, 'registerMenus']);

        // Registra os endpoints AJAX
        add_action('wp_ajax_apwp_save_term_version', [$this, 'handleSaveVersion']);
        add_action('wp_ajax_apwp_send_to_review', [$this, 'handleSendToReview']);
        add_action('wp_ajax_apwp_approve_version', [$this, 'handleApproveVersion']);
        add_action('wp_ajax_apwp_activate_version', [$this, 'handleActivateVersion']);
        add_action('wp_ajax_apwp_delete_version', [$this, 'handleDeleteVersion']);
        add_action('wp_ajax_apwp_get_version_preview', [$this, 'handleGetVersionPreview']);
    }

    /**
     * Registra os menus do admin
     */
    public function registerMenus(): void {
        add_submenu_page(
            'amigopet',
            'Versões de Termos',
            'Versões de Termos',
            'manage_terms',
            'amigopet-term-versions',
            [$this, 'renderVersionsPage']
        );
    }

    /**
     * Renderiza a página principal de versões
     */
    public function renderVersionsPage(): void {
        // Carrega os dados necessários
        $termId = $_GET['term_id'] ?? null;
        $versions = $termId ? $this->versionService->getVersions($termId) : [];
        $pendingReview = $this->versionService->getPendingReview();
        $activeVersion = $termId ? $this->versionService->getActiveVersion($termId) : null;
        $templates = $this->templateService->getAllDefaultTemplates();

        // Inclui os assets necessários
        wp_enqueue_style('apwp-admin');
        wp_enqueue_script('apwp-term-versions');
        wp_localize_script('apwp-term-versions', 'apwpTermVersions', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('apwp_term_versions'),
            'canApprove' => current_user_can('approve_terms'),
            'canActivate' => current_user_can('activate_terms'),
            'canDelete' => current_user_can('delete_terms')
        ]);

        // Renderiza o template
        include AMIGOPET_WP_PLUGIN_DIR . '/views/admin/term-versions/index.php';
    }

    /**
     * Manipula o salvamento de uma versão
     */
    public function handleSaveVersion(): void {
        check_ajax_referer('apwp_term_versions');

        try {
            $data = [
                'term_id' => (int)$_POST['term_id'],
                'content' => wp_kses_post($_POST['content']),
                'version' => sanitize_text_field($_POST['version']),
                'effective_date' => sanitize_text_field($_POST['effective_date']),
                'change_log' => sanitize_textarea_field($_POST['change_log'] ?? '')
            ];

            if (isset($_POST['id'])) {
                $this->versionService->updateVersion((int)$_POST['id'], $data);
                $message = 'Versão atualizada com sucesso!';
            } else {
                $id = $this->versionService->createVersion($data);
                $message = 'Versão criada com sucesso!';
            }

            wp_send_json_success([
                'message' => $message,
                'id' => $id ?? (int)$_POST['id']
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Manipula o envio para revisão
     */
    public function handleSendToReview(): void {
        check_ajax_referer('apwp_term_versions');

        try {
            $id = (int)$_POST['id'];
            $this->versionService->sendToReview($id);
            wp_send_json_success(['message' => 'Versão enviada para revisão!']);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Manipula a aprovação de uma versão
     */
    public function handleApproveVersion(): void {
        check_ajax_referer('apwp_term_versions');

        try {
            if (!current_user_can('approve_terms')) {
                throw new \Exception('Você não tem permissão para aprovar versões.');
            }

            $id = (int)$_POST['id'];
            $this->versionService->approveVersion($id);
            wp_send_json_success(['message' => 'Versão aprovada com sucesso!']);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Manipula a ativação de uma versão
     */
    public function handleActivateVersion(): void {
        check_ajax_referer('apwp_term_versions');

        try {
            if (!current_user_can('activate_terms')) {
                throw new \Exception('Você não tem permissão para ativar versões.');
            }

            $id = (int)$_POST['id'];
            $this->versionService->activateVersion($id);
            wp_send_json_success(['message' => 'Versão ativada com sucesso!']);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Manipula a exclusão de uma versão
     */
    public function handleDeleteVersion(): void {
        check_ajax_referer('apwp_term_versions');

        try {
            if (!current_user_can('delete_terms')) {
                throw new \Exception('Você não tem permissão para excluir versões.');
            }

            $id = (int)$_POST['id'];
            $this->versionService->deleteVersion($id);
            wp_send_json_success(['message' => 'Versão excluída com sucesso!']);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Retorna uma prévia da versão
     */
    public function handleGetVersionPreview(): void {
        check_ajax_referer('apwp_term_versions');

        try {
            $id = (int)$_POST['id'];
            $version = $this->versionService->findById($id);
            if (!$version) {
                throw new \Exception('Versão não encontrada.');
            }

            $preview = $this->templateService->getTemplatePreview($version->getContent());
            wp_send_json_success(['preview' => $preview]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
}
