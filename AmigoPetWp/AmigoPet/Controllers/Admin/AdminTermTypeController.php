<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Database\Database;
use AmigoPetWp\Domain\Services\TermTypeService;
use WP_Error;

class AdminTermTypeController extends BaseAdminController {
    private $service;

    public function __construct() {
        parent::__construct();
        $this->service = new TermTypeService(
            Database::getInstance()->getTermTypeRepository()
        );
    }

    public function registerHooks(): void {
        add_action('admin_menu', [$this, 'addMenuItems']);
        add_action('wp_ajax_amigopet_list_term_types', [$this, 'listTermTypes']);
        add_action('wp_ajax_amigopet_create_term_type', [$this, 'createTermType']);
        add_action('wp_ajax_amigopet_update_term_type', [$this, 'updateTermType']);
        add_action('wp_ajax_amigopet_delete_term_type', [$this, 'deleteTermType']);
    }

    public function addMenuItems(): void {
        add_submenu_page(
            'amigopet',
            __('Tipos de Termos', 'amigopet-wp'),
            __('Tipos de Termos', 'amigopet-wp'),
            'manage_options',
            'amigopet-term-types',
            [$this, 'renderPage']
        );
    }

    public function renderPage(): void {
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
        }

        $this->loadView('admin/term-types/index', [
            'termTypes' => $this->service->findAll(['status' => 'active'])
        ]);
    }

    public function listTermTypes(): void {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissão negada', 'amigopet-wp')]);
            return;
        }

        $args = [
            'search' => sanitize_text_field($_GET['search'] ?? ''),
            'status' => sanitize_text_field($_GET['status'] ?? 'active'),
            'required' => isset($_GET['required']) ? (bool) $_GET['required'] : null
        ];

        $termTypes = $this->service->findAll($args);
        $data = array_map(function($termType) {
            return $termType->toArray();
        }, $termTypes);

        wp_send_json_success(['data' => $data]);
    }

    public function createTermType(): void {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissão negada', 'amigopet-wp')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_create_term_type')) {
            wp_send_json_error(['message' => __('Nonce inválido', 'amigopet-wp')]);
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
                'message' => __('Tipo de termo criado com sucesso', 'amigopet-wp'),
                'data' => $termType->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function updateTermType(): void {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissão negada', 'amigopet-wp')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_update_term_type')) {
            wp_send_json_error(['message' => __('Nonce inválido', 'amigopet-wp')]);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => __('ID inválido', 'amigopet-wp')]);
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
                wp_send_json_error(['message' => __('Tipo de termo não encontrado', 'amigopet-wp')]);
                return;
            }

            wp_send_json_success([
                'message' => __('Tipo de termo atualizado com sucesso', 'amigopet-wp'),
                'data' => $termType->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function deleteTermType(): void {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissão negada', 'amigopet-wp')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_delete_term_type')) {
            wp_send_json_error(['message' => __('Nonce inválido', 'amigopet-wp')]);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => __('ID inválido', 'amigopet-wp')]);
            return;
        }

        try {
            if ($this->service->delete($id)) {
                wp_send_json_success([
                    'message' => __('Tipo de termo excluído com sucesso', 'amigopet-wp')
                ]);
            } else {
                wp_send_json_error([
                    'message' => __('Não foi possível excluir o tipo de termo', 'amigopet-wp')
                ]);
            }
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
}
