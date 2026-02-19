<?php declare(strict_types=1);
namespace AmigoPetWp\Controllers\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Database;
use AmigoPetWp\Domain\Services\TermTypeService;
use WP_Error;

class AdminTermTypeController extends BaseAdminController
{
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new TermTypeService(
            Database::getInstance()->getTermTypeRepository()
        );
    }

    public function registerHooks(): void
    {
        add_action('admin_menu', [$this, 'addMenuItems']);
        add_action('wp_ajax_amigopet_list_term_types', [$this, 'listTermTypes']);
        add_action('wp_ajax_amigopet_create_term_type', [$this, 'createTermType']);
        add_action('wp_ajax_amigopet_update_term_type', [$this, 'updateTermType']);
        add_action('wp_ajax_amigopet_delete_term_type', [$this, 'deleteTermType']);
    }

    public function addMenuItems(): void
    {
        add_submenu_page(
            'amigopetwp',
            esc_html__('Tipos de Termos', 'amigopet'),
            esc_html__('Tipos de Termos', 'amigopet'),
            'manage_options',
            'amigopet-term-types',
            [$this, 'renderPage']
        );
    }

    public function renderPage(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Você não tem permissão para acessar esta página.', 'amigopet'));
        }

        $this->loadView('admin/term-types/index', [
            'termTypes' => $this->service->findAll(['status' => 'active'])
        ]);
    }

    public function listTermTypes(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        $args = [
            'search' => sanitize_text_field($_GET['search'] ?? ''),
            'status' => sanitize_text_field($_GET['status'] ?? 'active'),
            'required' => isset($_GET['required']) ? (bool) $_GET['required'] : null
        ];

        $termTypes = $this->service->findAll($args);
        $data = array_map(function ($termType) {
            return $termType->toArray();
        }, $termTypes);

        wp_send_json_success(['data' => $data]);
    }

    public function createTermType(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'] ?? '')), 'amigopet_create_term_type')) {
            wp_send_json_error(['message' => esc_html__('Nonce inválido', 'amigopet')]);
            return;
        }

        $data = [
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'slug' => sanitize_title($_POST['slug'] ?? ''),
            'description' => sanitize_textarea_field($_POST['description'] ?? ''),
            'required' => (bool) ($_POST['required'] ?? false),
            'status' => sanitize_text_field($_POST['status'] ?? 'active')
        ];

        $errors = $this->service->validate($data);
        if (!empty($errors)) {
            wp_send_json_error(['errors' => $errors]);
            return;
        }

        try {
            $termType = $this->service->create($data);
            wp_send_json_success([
                'message' => esc_html__('Tipo de termo criado com sucesso', 'amigopet'),
                'data' => $termType->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function updateTermType(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'] ?? '')), 'amigopet_update_term_type')) {
            wp_send_json_error(['message' => esc_html__('Nonce inválido', 'amigopet')]);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => esc_html__('ID inválido', 'amigopet')]);
            return;
        }

        $data = [
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'slug' => sanitize_title($_POST['slug'] ?? ''),
            'description' => sanitize_textarea_field($_POST['description'] ?? ''),
            'required' => (bool) ($_POST['required'] ?? false),
            'status' => sanitize_text_field($_POST['status'] ?? 'active')
        ];

        $errors = $this->service->validate($data);
        if (!empty($errors)) {
            wp_send_json_error(['errors' => $errors]);
            return;
        }

        try {
            $termType = $this->service->update($id, $data);
            if (!$termType) {
                wp_send_json_error(['message' => esc_html__('Tipo de termo não encontrado', 'amigopet')]);
                return;
            }

            wp_send_json_success([
                'message' => esc_html__('Tipo de termo atualizado com sucesso', 'amigopet'),
                'data' => $termType->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function deleteTermType(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'] ?? '')), 'amigopet_delete_term_type')) {
            wp_send_json_error(['message' => esc_html__('Nonce inválido', 'amigopet')]);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => esc_html__('ID inválido', 'amigopet')]);
            return;
        }

        try {
            if ($this->service->delete($id)) {
                wp_send_json_success([
                    'message' => esc_html__('Tipo de termo excluído com sucesso', 'amigopet')
                ]);
            } else {
                wp_send_json_error([
                    'message' => esc_html__('Não foi possível excluir o tipo de termo', 'amigopet')
                ]);
            }
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
}