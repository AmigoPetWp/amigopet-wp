<?php declare(strict_types=1);
namespace AmigoPetWp\Controllers\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Services\ReportService;

class AdminMainController extends BaseAdminController
{
    private $reportService;

    public function __construct()
    {
        parent::__construct();
        $this->reportService = new ReportService(
            $this->db->getPetRepository(),
            $this->db->getAdoptionRepository(),
            $this->db->getDonationRepository(),
            $this->db->getEventRepository()
        );
    }

    protected function registerHooks(): void
    {
        // Menu principal
        add_action('admin_menu', [$this, 'addMenus']);

        // Registra os scripts e estilos do admin
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);

        // Registra a aba de ajuda
        add_action('admin_head', [$this, 'addHelpTab']);

        // Registra os endpoints AJAX do admin
        add_action('wp_ajax_apwp_get_dashboard_data', [$this, 'getDashboardData']);
        add_action('wp_ajax_apwp_get_reports', [$this, 'getReports']);
        add_action('wp_ajax_apwp_get_settings', [$this, 'getSettings']);
        add_action('wp_ajax_apwp_save_settings', [$this, 'saveSettings']);
    }

    public function addMenus(): void
    {
        // Menu principal
        add_menu_page(
            esc_html__('AmigoPet', 'amigopet'),
            esc_html__('AmigoPet', 'amigopet'),
            'manage_amigopet',
            'amigopet',
            [$this, 'renderDashboard'],
            'dashicons-pets',
            25
        );

        // Submenu do Dashboard
        add_submenu_page(
            'amigopet',
            esc_html__('Dashboard', 'amigopet'),
            esc_html__('Dashboard', 'amigopet'),
            'manage_amigopet',
            'amigopet',
            [$this, 'renderDashboard']
        );

        // Submenu de Relatórios
        add_submenu_page(
            'amigopet',
            esc_html__('Relatórios', 'amigopet'),
            esc_html__('Relatórios', 'amigopet'),
            'view_amigopet_reports',
            'amigopet-reports',
            [$this, 'renderReports']
        );

        // Submenu de Configurações
        add_submenu_page(
            'amigopet',
            esc_html__('Configurações', 'amigopet'),
            esc_html__('Configurações', 'amigopet'),
            'manage_amigopet_settings',
            'amigopet-settings',
            [$this, 'renderSettings']
        );
    }

    public function enqueueAssets(): void
    {
        $screen = get_current_screen();

        if (strpos($screen->id, 'amigopet') === false) {
            return;
        }

        // Registra o CSS principal do admin
        wp_enqueue_style(
            'amigopet-admin',
            plugin_dir_url(AMIGOPET_PLUGIN_FILE) . 'AmigoPet/assets/css/admin.css',
            [],
            AMIGOPET_VERSION
        );

        // Registra o CSS específico para adoções
        wp_enqueue_style(
            'amigopet-admin-adoptions',
            plugin_dir_url(AMIGOPET_PLUGIN_FILE) . 'AmigoPet/assets/css/admin-adoptions.css',
            ['amigopet-admin'],
            AMIGOPET_VERSION
        );

        // Registra o CSS específico para doações
        wp_enqueue_style(
            'amigopet-admin-donations',
            plugin_dir_url(AMIGOPET_PLUGIN_FILE) . 'AmigoPet/assets/css/admin-donations.css',
            ['amigopet-admin'],
            AMIGOPET_VERSION
        );

        // Registra o JS do admin
        wp_enqueue_script(
            'amigopet-admin',
            plugin_dir_url(AMIGOPET_PLUGIN_FILE) . 'AmigoPet/assets/js/admin.js',
            ['jquery'],
            AMIGOPET_VERSION,
            true
        );

        // Registra o jQuery Mask plugin
        wp_enqueue_script(
            'jquery-mask',
            plugin_dir_url(AMIGOPET_PLUGIN_FILE) . 'AmigoPet/assets/js/lib/jquery.mask.min.js',
            ['jquery'],
            '1.14.16',
            true
        );

        // Passa variáveis para o JS
        wp_localize_script('amigopet-admin', 'apwp', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('apwp_nonce')
        ]);
    }

    public function renderDashboard(): void
    {
        $this->checkPermission('manage_amigopet');

        $dashboard_data = $this->reportService->getDashboardData();

        $this->loadView('admin/dashboard', [
            'dashboard_data' => $dashboard_data
        ]);
    }

    public function renderReports(): void
    {
        $this->checkPermission('view_amigopet_reports');

        $reports_data = $this->reportService->getReportsData();

        $this->loadView('admin/reports', [
            'reports_data' => $reports_data
        ]);
    }

    public function renderSettings(): void
    {
        $this->checkPermission('manage_amigopet_settings');

        $settings = get_option('amigopet_wp_settings', []);

        $this->loadView('admin/settings', [
            'settings' => $settings
        ]);
    }

    public function getDashboardData(): void
    {
        $this->checkPermission('manage_amigopet');
        check_ajax_referer('apwp_nonce');

        $data = $this->reportService->getDashboardData();
        wp_send_json_success($data);
    }

    public function getReports(): void
    {
        $this->checkPermission('view_amigopet_reports');
        check_ajax_referer('apwp_nonce');

        $data = $this->reportService->getReportsData();
        wp_send_json_success($data);
    }

    public function getSettings(): void
    {
        $this->checkPermission('manage_amigopet_settings');
        check_ajax_referer('apwp_nonce');

        $settings = get_option('amigopet_wp_settings', []);
        wp_send_json_success($settings);
    }

    public function saveSettings(): void
    {
        $this->checkPermission('manage_amigopet_settings');
        check_ajax_referer('apwp_nonce');

        $settings = isset($_POST['settings']) ? $_POST['settings'] : [];
        update_option('amigopet_wp_settings', $settings);

        wp_send_json_success(esc_html__('Configurações salvas com sucesso!', 'amigopet'));
    }

    public function addHelpTab(): void
    {
        $screen = get_current_screen();

        if (strpos($screen->id, 'amigopet') === false) {
            return;
        }

        $screen->add_help_tab([
            'id' => 'amigopet-help',
            'title' => esc_html__('Ajuda', 'amigopet'),
            'content' => $this->loadView('admin/help', [], true)
        ]);
    }
}