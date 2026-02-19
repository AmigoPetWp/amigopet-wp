<?php declare(strict_types=1);
namespace AmigoPetWp\Controllers\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Database;
use AmigoPetWp\Domain\Services\SignedTermService;
use AmigoPetWp\Domain\Services\TermService;
use WP_Error;

class AdminSignedTermController extends BaseAdminController
{
    private $service;
    private $termService;

    public function __construct()
    {
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

    public function registerHooks(): void
    {
        add_action('admin_menu', [$this, 'addMenuItems']);
        add_action('wp_ajax_amigopet_list_signed_terms', [$this, 'listSignedTerms']);
        add_action('wp_ajax_amigopet_get_signed_term', [$this, 'getSignedTerm']);
        add_action('wp_ajax_amigopet_revoke_signed_term', [$this, 'revokeSignedTerm']);
    }

    public function addMenuItems(): void
    {
        add_submenu_page(
            'amigopetwp',
            esc_html__('Termos Assinados', 'amigopet'),
            esc_html__('Termos Assinados', 'amigopet'),
            'manage_options',
            'amigopet-signed-terms',
            [$this, 'renderPage']
        );
    }

    public function renderPage(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Você não tem permissão para acessar esta página.', 'amigopet'));
        }

        $this->loadView('admin/signed-terms/index', [
            'signedTerms' => $this->service->findAll(),
            'terms' => $this->termService->findAll(['status' => 'active'])
        ]);
    }

    public function listSignedTerms(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
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
        $data = array_map(function ($signedTerm) {
            $data = $signedTerm->toArray();
            $data['user'] = get_userdata($signedTerm->getUserId());
            $data['term'] = $this->termService->findById($signedTerm->getTermId());
            return $data;
        }, $signedTerms);

        wp_send_json_success(['data' => $data]);
    }

    public function getSignedTerm(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => esc_html__('ID inválido', 'amigopet')]);
            return;
        }

        $signedTerm = $this->service->findById($id);
        if (!$signedTerm) {
            wp_send_json_error(['message' => esc_html__('Termo assinado não encontrado', 'amigopet')]);
            return;
        }

        $data = $signedTerm->toArray();
        $data['user'] = get_userdata($signedTerm->getUserId());
        $data['term'] = $this->termService->findById($signedTerm->getTermId());

        wp_send_json_success(['data' => $data]);
    }

    public function revokeSignedTerm(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'] ?? '')), 'amigopet_revoke_signed_term')) {
            wp_send_json_error(['message' => esc_html__('Nonce inválido', 'amigopet')]);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => esc_html__('ID inválido', 'amigopet')]);
            return;
        }

        try {
            $signedTerm = $this->service->revoke($id);
            if (!$signedTerm) {
                wp_send_json_error(['message' => esc_html__('Termo assinado não encontrado', 'amigopet')]);
                return;
            }

            wp_send_json_success([
                'message' => esc_html__('Termo revogado com sucesso', 'amigopet'),
                'data' => $signedTerm->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
}