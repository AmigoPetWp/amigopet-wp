<?php declare(strict_types=1);
namespace AmigoPetWp\Controllers\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Services\SettingsService;
use AmigoPetWp\Domain\Services\TemplateTermsService;
use AmigoPetWp\Domain\Database\Repositories\SettingsRepository;
use AmigoPetWp\Domain\Database\Repositories\TemplateTermsRepository;

class SettingsController extends BaseAdminController
{
    private SettingsService $settingsService;
    private TemplateTermsService $termsService;

    public function __construct()
    {
        parent::__construct();
        global $wpdb;
        $this->settingsService = new SettingsService(new SettingsRepository());
        $this->termsService = new TemplateTermsService(new TemplateTermsRepository($wpdb));
    }
    public function registerHooks(): void
    {
        // Registra os endpoints AJAX
        add_action('wp_ajax_apwp_get_settings', [$this, 'getSettings']);
        add_action('wp_ajax_apwp_save_settings', [$this, 'saveSettings']);
    }

    public function renderSettings(): void
    {
        $this->checkPermission('manage_amigopet_settings');
        $settings = $this->settingsService->getAllSettings();
        $templates = $this->termsService->getAllTemplates();
        $terms_types = [
            'adoption' => [
                'title' => esc_html__('Termo de Adoção', 'amigopet'),
                'description' => esc_html__('Template para o termo de adoção responsável', 'amigopet'),
                'placeholders' => [
                    '{organization_name}' => esc_html__('Nome da organização', 'amigopet'),
                    '{organization_address}' => esc_html__('Endereço da organização', 'amigopet'),
                    '{adopter_name}' => esc_html__('Nome do adotante', 'amigopet'),
                    '{adopter_document}' => esc_html__('Documento do adotante', 'amigopet'),
                    '{adopter_address}' => esc_html__('Endereço do adotante', 'amigopet'),
                    '{pet_name}' => esc_html__('Nome do animal', 'amigopet'),
                    '{pet_type}' => esc_html__('Tipo do animal', 'amigopet'),
                    '{pet_breed}' => esc_html__('Raça do animal', 'amigopet'),
                    '{current_date}' => esc_html__('Data atual', 'amigopet')
                ]
            ],
            'volunteer' => [
                'title' => esc_html__('Termo de Voluntariado', 'amigopet'),
                'description' => esc_html__('Template para o termo de voluntariado', 'amigopet'),
                'placeholders' => [
                    '{organization_name}' => esc_html__('Nome da organização', 'amigopet'),
                    '{organization_address}' => esc_html__('Endereço da organização', 'amigopet'),
                    '{volunteer_name}' => esc_html__('Nome do voluntário', 'amigopet'),
                    '{volunteer_document}' => esc_html__('Documento do voluntário', 'amigopet'),
                    '{volunteer_address}' => esc_html__('Endereço do voluntário', 'amigopet'),
                    '{current_date}' => esc_html__('Data atual', 'amigopet')
                ]
            ],
            'donation' => [
                'title' => esc_html__('Termo de Doação', 'amigopet'),
                'description' => esc_html__('Template para o termo de doação', 'amigopet'),
                'placeholders' => [
                    '{organization_name}' => esc_html__('Nome da organização', 'amigopet'),
                    '{organization_address}' => esc_html__('Endereço da organização', 'amigopet'),
                    '{donor_name}' => esc_html__('Nome do doador', 'amigopet'),
                    '{donor_document}' => esc_html__('Documento do doador', 'amigopet'),
                    '{donor_address}' => esc_html__('Endereço do doador', 'amigopet'),
                    '{donation_amount}' => esc_html__('Valor da doação', 'amigopet'),
                    '{current_date}' => esc_html__('Data atual', 'amigopet')
                ]
            ]
        ];

        $this->loadView('admin/settings', [
            'settings' => $settings,
            'templates' => $templates,
            'terms_types' => $terms_types
        ]);
    }

    public function getSettings(): void
    {
        $this->checkPermission('manage_amigopet_settings');

        // Verifica o nonce
        if (!check_ajax_referer('apwp_settings_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => esc_html__('Nonce inválido', 'amigopet')]);
            return;
        }

        $settings = $this->settingsService->getAllSettings();
        $templates = $this->termsService->getAllTemplates();
        wp_send_json_success([
            'settings' => $settings,
            'templates' => $templates
        ]);
    }

    public function saveSettings(): void
    {
        $this->checkPermission('manage_amigopet_settings');

        // Verifica o nonce do formulário
        if (!isset($_POST['apwp_settings_nonce']) || !wp_verify_nonce($_POST['apwp_settings_nonce'], 'apwp_settings_nonce')) {
            wp_send_json_error(['message' => esc_html__('Nonce inválido', 'amigopet')]);
            return;
        }

        $result = $this->settingsService->saveSettings($_POST);

        // Salva os templates se houver
        if (isset($_POST['templates'])) {
            foreach ($_POST['templates'] as $type => $data) {
                if (isset($data['id'])) {
                    $this->termsService->updateTemplate(
                        (int) $data['id'],
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