<?php declare(strict_types=1);
namespace AmigoPetWp\Controllers\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Database;
use AmigoPetWp\Domain\Services\PetSpeciesService;
use WP_Error;

class AdminPetSpeciesController extends BaseAdminController
{
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new PetSpeciesService(
            Database::getInstance()->getPetSpeciesRepository()
        );
    }

    public function registerHooks(): void
    {
        add_action('admin_menu', [$this, 'addMenuItems']);
        add_action('wp_ajax_amigopet_list_species', [$this, 'listSpecies']);
        add_action('wp_ajax_amigopet_create_species', [$this, 'createSpecies']);
        add_action('wp_ajax_amigopet_update_species', [$this, 'updateSpecies']);
        add_action('wp_ajax_amigopet_delete_species', [$this, 'deleteSpecies']);
    }

    public function addMenuItems(): void
    {
        add_submenu_page(
            'amigopet',
            esc_html__('Espécies', 'amigopet'),
            esc_html__('Espécies', 'amigopet'),
            'manage_options',
            'amigopet-species',
            [$this, 'renderPage']
        );
    }

    public function renderPage(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Você não tem permissão para acessar esta página.', 'amigopet'));
        }

        $this->loadView('admin/species/index', [
            'species' => $this->service->findAll(['status' => 'active'])
        ]);
    }

    public function listSpecies(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        $args = [
            'search' => sanitize_text_field($_GET['search'] ?? ''),
            'status' => sanitize_text_field($_GET['status'] ?? 'active')
        ];

        $species = $this->service->findAll($args);
        $data = array_map(function ($species) {
            return $species->toArray();
        }, $species);

        wp_send_json_success(['data' => $data]);
    }

    public function createSpecies(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_create_species')) {
            wp_send_json_error(['message' => esc_html__('Nonce inválido', 'amigopet')]);
            return;
        }

        $data = [
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'description' => sanitize_textarea_field($_POST['description'] ?? ''),
            'status' => sanitize_text_field($_POST['status'] ?? 'active')
        ];

        $errors = $this->service->validate($data);
        if (!empty($errors)) {
            wp_send_json_error(['errors' => $errors]);
            return;
        }

        try {
            $species = $this->service->create($data);
            wp_send_json_success([
                'message' => esc_html__('Espécie criada com sucesso', 'amigopet'),
                'data' => $species->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function updateSpecies(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_update_species')) {
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
            'description' => sanitize_textarea_field($_POST['description'] ?? ''),
            'status' => sanitize_text_field($_POST['status'] ?? 'active')
        ];

        $errors = $this->service->validate($data);
        if (!empty($errors)) {
            wp_send_json_error(['errors' => $errors]);
            return;
        }

        try {
            $species = $this->service->update($id, $data);
            if (!$species) {
                wp_send_json_error(['message' => esc_html__('Espécie não encontrada', 'amigopet')]);
                return;
            }

            wp_send_json_success([
                'message' => esc_html__('Espécie atualizada com sucesso', 'amigopet'),
                'data' => $species->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function deleteSpecies(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_delete_species')) {
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
                    'message' => esc_html__('Espécie excluída com sucesso', 'amigopet')
                ]);
            } else {
                wp_send_json_error([
                    'message' => esc_html__('Não foi possível excluir a espécie', 'amigopet')
                ]);
            }
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
}