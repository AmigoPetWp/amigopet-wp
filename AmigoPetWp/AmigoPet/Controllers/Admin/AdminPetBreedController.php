<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Database\Database;
use AmigoPetWp\Domain\Services\PetBreedService;
use AmigoPetWp\Domain\Services\PetSpeciesService;
use WP_Error;

class AdminPetBreedController extends BaseAdminController {
    private $service;
    private $speciesService;

    public function __construct() {
        parent::__construct();
        $database = Database::getInstance();
        $this->service = new PetBreedService(
            $database->getPetBreedRepository(),
            $database->getPetSpeciesRepository()
        );
        $this->speciesService = new PetSpeciesService(
            $database->getPetSpeciesRepository()
        );
    }

    public function registerHooks(): void {
        add_action('admin_menu', [$this, 'addMenuItems']);
        add_action('wp_ajax_amigopet_list_breeds', [$this, 'listBreeds']);
        add_action('wp_ajax_amigopet_create_breed', [$this, 'createBreed']);
        add_action('wp_ajax_amigopet_update_breed', [$this, 'updateBreed']);
        add_action('wp_ajax_amigopet_delete_breed', [$this, 'deleteBreed']);
    }

    public function addMenuItems(): void {
        add_submenu_page(
            'amigopet',
            __('Raças', 'amigopet-wp'),
            __('Raças', 'amigopet-wp'),
            'manage_options',
            'amigopet-breeds',
            [$this, 'renderPage']
        );
    }

    public function renderPage(): void {
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
        }

        $this->loadView('admin/breeds/index', [
            'breeds' => $this->service->findAll(['status' => 'active']),
            'species' => $this->speciesService->findAll(['status' => 'active'])
        ]);
    }

    public function listBreeds(): void {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissão negada', 'amigopet-wp')]);
            return;
        }

        $args = [
            'species_id' => (int) ($_GET['species_id'] ?? 0),
            'search' => sanitize_text_field($_GET['search'] ?? ''),
            'status' => sanitize_text_field($_GET['status'] ?? 'active')
        ];

        $breeds = $this->service->findAll($args);
        $data = array_map(function($breed) {
            return $breed->toArray();
        }, $breeds);

        wp_send_json_success(['data' => $data]);
    }

    public function createBreed(): void {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissão negada', 'amigopet-wp')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_create_breed')) {
            wp_send_json_error(['message' => __('Nonce inválido', 'amigopet-wp')]);
            return;
        }

        $data = [
            'species_id' => (int) ($_POST['species_id'] ?? 0),
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'description' => sanitize_textarea_field($_POST['description'] ?? ''),
            'characteristics' => $this->sanitizeCharacteristics($_POST['characteristics'] ?? ''),
            'status' => sanitize_text_field($_POST['status'] ?? 'active')
        ];

        $errors = $this->service->validate($data);
        if (!empty($errors)) {
            wp_send_json_error(['errors' => $errors]);
            return;
        }

        try {
            $breed = $this->service->create($data);
            wp_send_json_success([
                'message' => __('Raça criada com sucesso', 'amigopet-wp'),
                'data' => $breed->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function updateBreed(): void {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissão negada', 'amigopet-wp')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_update_breed')) {
            wp_send_json_error(['message' => __('Nonce inválido', 'amigopet-wp')]);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => __('ID inválido', 'amigopet-wp')]);
            return;
        }

        $data = [
            'species_id' => (int) ($_POST['species_id'] ?? 0),
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'description' => sanitize_textarea_field($_POST['description'] ?? ''),
            'characteristics' => $this->sanitizeCharacteristics($_POST['characteristics'] ?? ''),
            'status' => sanitize_text_field($_POST['status'] ?? 'active')
        ];

        $errors = $this->service->validate($data);
        if (!empty($errors)) {
            wp_send_json_error(['errors' => $errors]);
            return;
        }

        try {
            $breed = $this->service->update($id, $data);
            if (!$breed) {
                wp_send_json_error(['message' => __('Raça não encontrada', 'amigopet-wp')]);
                return;
            }

            wp_send_json_success([
                'message' => __('Raça atualizada com sucesso', 'amigopet-wp'),
                'data' => $breed->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function deleteBreed(): void {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissão negada', 'amigopet-wp')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_delete_breed')) {
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
                    'message' => __('Raça excluída com sucesso', 'amigopet-wp')
                ]);
            } else {
                wp_send_json_error([
                    'message' => __('Não foi possível excluir a raça', 'amigopet-wp')
                ]);
            }
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    private function sanitizeCharacteristics($characteristics): array {
        if (is_string($characteristics)) {
            $characteristics = json_decode($characteristics, true);
        }

        if (!is_array($characteristics)) {
            return [];
        }

        return array_map(function($item) {
            return is_string($item) ? sanitize_text_field($item) : '';
        }, $characteristics);
    }
}
