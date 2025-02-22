<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Database\Database;
use AmigoPetWp\Domain\Services\SignedTermService;
use AmigoPetWp\Domain\Services\TermService;
use WP_Error;

class AdminSignedTermController extends BaseAdminController {
    private $service;
    private $termService;

    public function __construct() {
        parent::__construct();
        $database = Database::getInstance();
        $this->service = new SignedTermService(
            $database->getSignedTermRepository(),
            $database->getTermRepository()
        );
        $this->termService = new TermService(
            $database->getTermRepository()
        );
    }

    public function registerHooks(): void {
        add_action('admin_menu', [$this, 'addMenuItems']);
        add_action('wp_ajax_amigopet_list_signed_terms', [$this, 'listSignedTerms']);
        add_action('wp_ajax_amigopet_get_signed_term', [$this, 'getSignedTerm']);
        add_action('wp_ajax_amigopet_revoke_signed_term', [$this, 'revokeSignedTerm']);
    }

    public function addMenuItems(): void {
        add_submenu_page(
            'amigopet',
            __('Termos Assinados', 'amigopet-wp'),
            __('Termos Assinados', 'amigopet-wp'),
            'manage_options',
            'amigopet-signed-terms',
            [$this, 'renderPage']
        );
    }

    public function renderPage(): void {
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
        }

        $this->loadView('admin/signed-terms/index', [
            'signedTerms' => $this->service->findAll(),
            'terms' => $this->termService->findAll(['status' => 'active'])
        ]);
    }

    public function listSignedTerms(): void {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissão negada', 'amigopet-wp')]);
            return;
        }

        $args = [
            'term_id' => (int) ($_GET['term_id'] ?? 0),
            'user_id' => (int) ($_GET['user_id'] ?? 0),
            'adoption_id' => (int) ($_GET['adoption_id'] ?? 0),
            'status' => sanitize_text_field($_GET['status'] ?? ''),
            'start_date' => sanitize_text_field($_GET['start_date'] ?? ''),
            'end_date' => sanitize_text_field($_GET['end_date'] ?? '')
        ];

        $signedTerms = $this->service->findAll($args);
        $data = array_map(function($signedTerm) {
            $data = $signedTerm->toArray();
            $data['user'] = get_userdata($signedTerm->getUserId());
            $data['term'] = $this->termService->findById($signedTerm->getTermId());
            return $data;
        }, $signedTerms);

        wp_send_json_success(['data' => $data]);
    }

    public function getSignedTerm(): void {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissão negada', 'amigopet-wp')]);
            return;
        }

        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => __('ID inválido', 'amigopet-wp')]);
            return;
        }

        $signedTerm = $this->service->findById($id);
        if (!$signedTerm) {
            wp_send_json_error(['message' => __('Termo assinado não encontrado', 'amigopet-wp')]);
            return;
        }

        $data = $signedTerm->toArray();
        $data['user'] = get_userdata($signedTerm->getUserId());
        $data['term'] = $this->termService->findById($signedTerm->getTermId());

        wp_send_json_success(['data' => $data]);
    }

    public function revokeSignedTerm(): void {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissão negada', 'amigopet-wp')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_revoke_signed_term')) {
            wp_send_json_error(['message' => __('Nonce inválido', 'amigopet-wp')]);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => __('ID inválido', 'amigopet-wp')]);
            return;
        }

        try {
            $signedTerm = $this->service->revoke($id);
            if (!$signedTerm) {
                wp_send_json_error(['message' => __('Termo assinado não encontrado', 'amigopet-wp')]);
                return;
            }

            wp_send_json_success([
                'message' => __('Termo revogado com sucesso', 'amigopet-wp'),
                'data' => $signedTerm->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
}
