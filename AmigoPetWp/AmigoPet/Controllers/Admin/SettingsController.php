<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Services\SettingsService;
use AmigoPetWp\Domain\Database\Repositories\SettingsRepository;

class SettingsController extends BaseAdminController {
    private SettingsService $service;

    public function __construct() {
        parent::__construct();
        $this->service = new SettingsService(new SettingsRepository());
    }
    public function registerHooks(): void {
        // Registra os endpoints AJAX
        add_action('wp_ajax_apwp_get_settings', [$this, 'getSettings']);
        add_action('wp_ajax_apwp_save_settings', [$this, 'saveSettings']);
    }

    public function renderSettings(): void {
        $this->checkPermission('manage_amigopet_settings');
        $settings = $this->service->getAllSettings();
        $this->loadView('admin/settings', ['settings' => $settings]);
    }

    public function getSettings(): void {
        $this->checkPermission('manage_amigopet_settings');
        
        // Verifica o nonce
        if (!check_ajax_referer('apwp_settings_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => __('Nonce inválido', 'amigopet-wp')]);
            return;
        }

        $settings = $this->service->getAllSettings();
        wp_send_json_success($settings);
    }

    public function saveSettings(): void {
        $this->checkPermission('manage_amigopet_settings');
        
        // Verifica o nonce do formulário
        if (!isset($_POST['apwp_settings_nonce']) || !wp_verify_nonce($_POST['apwp_settings_nonce'], 'apwp_settings_nonce')) {
            wp_send_json_error(['message' => __('Nonce inválido', 'amigopet-wp')]);
            return;
        }

        $result = $this->service->saveSettings($_POST);
        
        if ($result['success']) {
            wp_send_json_success($result['message']);
        } else {
            wp_send_json_error(['message' => $result['message']]);
        }
    }
}
