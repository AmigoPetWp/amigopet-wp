<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Services\SettingsService;
use AmigoPetWp\Domain\Services\TemplateTermsService;
use AmigoPetWp\Domain\Database\Repositories\SettingsRepository;
use AmigoPetWp\Domain\Database\Repositories\TemplateTermsRepository;

class SettingsController extends BaseAdminController {
    private SettingsService $settingsService;
    private TemplateTermsService $termsService;

    public function __construct() {
        parent::__construct();
        global $wpdb;
        $this->settingsService = new SettingsService(new SettingsRepository());
        $this->termsService = new TemplateTermsService(new TemplateTermsRepository($wpdb));
    }
    public function registerHooks(): void {
        // Registra os endpoints AJAX
        add_action('wp_ajax_apwp_get_settings', [$this, 'getSettings']);
        add_action('wp_ajax_apwp_save_settings', [$this, 'saveSettings']);
    }

    public function renderSettings(): void {
        $this->checkPermission('manage_amigopet_settings');
        $settings = $this->settingsService->getAllSettings();
        $templates = $this->termsService->getAllTemplates();
        $terms_types = [
            'adoption' => [
                'title' => __('Termo de Adoção', 'amigopet-wp'),
                'description' => __('Template para o termo de adoção responsável', 'amigopet-wp'),
                'placeholders' => [
                    '{organization_name}' => __('Nome da organização', 'amigopet-wp'),
                    '{organization_address}' => __('Endereço da organização', 'amigopet-wp'),
                    '{adopter_name}' => __('Nome do adotante', 'amigopet-wp'),
                    '{adopter_document}' => __('Documento do adotante', 'amigopet-wp'),
                    '{adopter_address}' => __('Endereço do adotante', 'amigopet-wp'),
                    '{pet_name}' => __('Nome do animal', 'amigopet-wp'),
                    '{pet_type}' => __('Tipo do animal', 'amigopet-wp'),
                    '{pet_breed}' => __('Raça do animal', 'amigopet-wp'),
                    '{current_date}' => __('Data atual', 'amigopet-wp')
                ]
            ],
            'volunteer' => [
                'title' => __('Termo de Voluntariado', 'amigopet-wp'),
                'description' => __('Template para o termo de voluntariado', 'amigopet-wp'),
                'placeholders' => [
                    '{organization_name}' => __('Nome da organização', 'amigopet-wp'),
                    '{organization_address}' => __('Endereço da organização', 'amigopet-wp'),
                    '{volunteer_name}' => __('Nome do voluntário', 'amigopet-wp'),
                    '{volunteer_document}' => __('Documento do voluntário', 'amigopet-wp'),
                    '{volunteer_address}' => __('Endereço do voluntário', 'amigopet-wp'),
                    '{current_date}' => __('Data atual', 'amigopet-wp')
                ]
            ],
            'donation' => [
                'title' => __('Termo de Doação', 'amigopet-wp'),
                'description' => __('Template para o termo de doação', 'amigopet-wp'),
                'placeholders' => [
                    '{organization_name}' => __('Nome da organização', 'amigopet-wp'),
                    '{organization_address}' => __('Endereço da organização', 'amigopet-wp'),
                    '{donor_name}' => __('Nome do doador', 'amigopet-wp'),
                    '{donor_document}' => __('Documento do doador', 'amigopet-wp'),
                    '{donor_address}' => __('Endereço do doador', 'amigopet-wp'),
                    '{donation_amount}' => __('Valor da doação', 'amigopet-wp'),
                    '{current_date}' => __('Data atual', 'amigopet-wp')
                ]
            ]
        ];

        $this->loadView('admin/settings', [
            'settings' => $settings,
            'templates' => $templates,
            'terms_types' => $terms_types
        ]);
    }

    public function getSettings(): void {
        $this->checkPermission('manage_amigopet_settings');
        
        // Verifica o nonce
        if (!check_ajax_referer('apwp_settings_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => __('Nonce inválido', 'amigopet-wp')]);
            return;
        }

        $settings = $this->settingsService->getAllSettings();
        $templates = $this->termsService->getAllTemplates();
        wp_send_json_success([
            'settings' => $settings,
            'templates' => $templates
        ]);
    }

    public function saveSettings(): void {
        $this->checkPermission('manage_amigopet_settings');
        
        // Verifica o nonce do formulário
        if (!isset($_POST['apwp_settings_nonce']) || !wp_verify_nonce($_POST['apwp_settings_nonce'], 'apwp_settings_nonce')) {
            wp_send_json_error(['message' => __('Nonce inválido', 'amigopet-wp')]);
            return;
        }

        $result = $this->settingsService->saveSettings($_POST);

        // Salva os templates se houver
        if (isset($_POST['templates'])) {
            foreach ($_POST['templates'] as $type => $data) {
                if (isset($data['id'])) {
                    $this->termsService->updateTemplate(
                        (int)$data['id'],
                        [
                            'title' => $data['title'],
                            'content' => $data['content'],
                            'description' => $data['description'] ?? null
                        ]
                    );
                } else {
                    $this->termsService->saveTemplate(
                        $type,
                        $data['title'],
                        $data['content'],
                        $data['description'] ?? null
                    );
                }
            }
        }
        
        if ($result['success']) {
            wp_send_json_success($result['message']);
        } else {
            wp_send_json_error(['message' => $result['message']]);
        }
    }
}
