<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Admin\Settings;

class DashboardController extends BaseAdminController {
    public function registerHooks(): void {
        // Menu principal
        add_action('admin_menu', [$this, 'addMenus']);

        // Registra as configurações
        add_action('admin_init', [Settings::class, 'register']);

        // Registra os scripts e estilos do admin
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        
        // Registra a aba de ajuda
        add_action('admin_head', [$this, 'addHelpTab']);
        
        // Registra os endpoints AJAX
        add_action('wp_ajax_apwp_get_dashboard_data', [$this, 'getDashboardData']);
        add_action('wp_ajax_apwp_get_reports', [$this, 'getReports']);
        add_action('wp_ajax_apwp_get_settings', [$this, 'getSettings']);
        add_action('wp_ajax_apwp_save_settings', [$this, 'saveSettings']);
    }

    public function addMenus(): void {
        // Menu principal
        add_menu_page(
            __('AmigoPetWP', 'amigopet-wp'),
            __('AmigoPetWP', 'amigopet-wp'),
            'manage_amigopet',
            'amigopet-wp',
            [$this, 'renderDashboard'],
            'dashicons-pets',
            25
        );

        // Submenus
        add_submenu_page(
            'amigopet-wp',
            __('Dashboard', 'amigopet-wp'),
            __('Dashboard', 'amigopet-wp'),
            'manage_amigopet',
            'amigopet-wp',
            [$this, 'renderDashboard']
        );

        add_submenu_page(
            'amigopet-wp',
            __('Relatórios', 'amigopet-wp'),
            __('Relatórios', 'amigopet-wp'),
            'view_amigopet_reports',
            'amigopet-wp-reports',
            [$this, 'renderReports']
        );

        add_submenu_page(
            'amigopet-wp',
            __('Configurações', 'amigopet-wp'),
            __('Configurações', 'amigopet-wp'),
            'manage_amigopet_settings',
            'amigopet-wp-settings',
            [$this, 'renderSettings']
        );
    }

    public function enqueueAssets(): void {
        wp_enqueue_style(
            'amigopet-wp-admin',
            AMIGOPET_WP_PLUGIN_URL . 'assets/css/admin.css',
            [],
            AMIGOPET_WP_VERSION
        );

        wp_enqueue_script(
            'amigopet-wp-admin',
            AMIGOPET_WP_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            AMIGOPET_WP_VERSION,
            true
        );

        wp_localize_script('amigopet-wp-admin', 'apwp', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('amigopet-wp-admin'),
            'i18n' => [
                'confirm_delete' => __('Tem certeza que deseja excluir?', 'amigopet-wp'),
                'error' => __('Erro ao processar requisição', 'amigopet-wp'),
                'success' => __('Operação realizada com sucesso', 'amigopet-wp')
            ]
        ]);
    }

    public function addHelpTab(): void {
        $screen = get_current_screen();
        
        if (!$screen || !str_starts_with($screen->id, 'amigopet-wp')) {
            return;
        }

        $screen->add_help_tab([
            'id' => 'amigopet-wp-help',
            'title' => __('Ajuda', 'amigopet-wp'),
            'content' => $this->loadView('admin/help', [], true)
        ]);
    }

    public function renderDashboard(): void {
        $this->checkPermission('manage_amigopet');
        $this->loadView('admin/dashboard');
    }

    public function renderReports(): void {
        $this->checkPermission('view_amigopet_reports');
        $this->loadView('admin/reports');
    }

    public function renderSettings(): void {
        $this->checkPermission('manage_amigopet_settings');
        $this->loadView('admin/settings');
    }

    public function getDashboardData(): void {
        $this->checkPermission('manage_amigopet');
        $this->verifyNonce('amigopet-wp-admin');

        wp_send_json_success([
            'pets' => [
                'total' => $this->db->getPetRepository()->count(),
                'available' => $this->db->getPetRepository()->countByStatus('available')
            ],
            'adoptions' => [
                'total' => $this->db->getAdoptionRepository()->count(),
                'pending' => $this->db->getAdoptionRepository()->countByStatus('pending')
            ],
            'donations' => [
                'total' => $this->db->getDonationRepository()->count(),
                'amount' => $this->db->getDonationRepository()->sumAmount()
            ],
            'events' => [
                'total' => $this->db->getEventRepository()->count(),
                'upcoming' => $this->db->getEventRepository()->countUpcoming()
            ]
        ]);
    }

    public function getReports(): void {
        $this->checkPermission('view_amigopet_reports');
        $this->verifyNonce('amigopet-wp-admin');

        $type = $_GET['type'] ?? '';
        $start = $_GET['start'] ?? '';
        $end = $_GET['end'] ?? '';

        switch ($type) {
            case 'adoptions':
                $data = $this->db->getAdoptionRepository()->getReport($start, $end);
                break;
            case 'donations':
                $data = $this->db->getDonationRepository()->getReport($start, $end);
                break;
            case 'events':
                $data = $this->db->getEventRepository()->getReport($start, $end);
                break;
            default:
                wp_send_json_error(__('Tipo de relatório inválido', 'amigopet-wp'));
        }

        wp_send_json_success($data);
    }

    public function getSettings(): void {
        $this->checkPermission('manage_amigopet_settings');
        $this->verifyNonce('amigopet-wp-admin');

        wp_send_json_success([
            'general' => get_option('amigopet_wp_general', []),
            'payment' => get_option('amigopet_wp_payment', []),
            'email' => get_option('amigopet_wp_email', [])
        ]);
    }

    public function saveSettings(): void {
        $this->checkPermission('manage_amigopet_settings');
        $this->verifyNonce('amigopet-wp-admin');

        $settings = $_POST['settings'] ?? [];
        
        if (empty($settings)) {
            wp_send_json_error(__('Nenhuma configuração enviada', 'amigopet-wp'));
        }

        foreach ($settings as $key => $value) {
            update_option("amigopet_wp_{$key}", $value);
        }

        wp_send_json_success(__('Configurações salvas com sucesso', 'amigopet-wp'));
    }
}
