<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Settings\Settings;
use AmigoPetWp\Controllers\Admin\SettingsController;

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
    }

    public function enqueueAssets(string $hook = ''): void {
        // Só carrega nas páginas do plugin
        if (!empty($hook) && strpos($hook, 'amigopet-wp') === false) {
            return;
        }

        // Select2
        wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery']);
        wp_enqueue_script('select2-pt-BR', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js', ['select2']);

        // Estilos
        wp_enqueue_style(
            'amigopet-admin',
            AMIGOPET_WP_PLUGIN_URL . 'AmigoPet/assets/css/admin.css',
            [],
            AMIGOPET_WP_VERSION
        );

        // CSS do ícone do menu
        $menu_icon_css = "
            #adminmenu .toplevel_page_amigopet-wp .wp-menu-image {
                display: flex;
                align-items: center;
                justify-content: center;
                background: none !important;
            }
            #adminmenu .toplevel_page_amigopet-wp .wp-menu-image img {
                width: 18px;
                height: 18px;
                padding: 0;
                opacity: 1;
                filter: none !important;
                -webkit-filter: none !important;
                background: none !important;
            }
            #adminmenu .toplevel_page_amigopet-wp:hover .wp-menu-image img,
            #adminmenu .toplevel_page_amigopet-wp.current .wp-menu-image img,
            .folded #adminmenu .toplevel_page_amigopet-wp:hover .wp-menu-image img,
            .folded #adminmenu .toplevel_page_amigopet-wp.current .wp-menu-image img {
                opacity: 1 !important;
                filter: none !important;
                -webkit-filter: none !important;
                background: none !important;
            }
        ";
        wp_add_inline_style('amigopet-admin', $menu_icon_css);
        
        // Scripts
        wp_enqueue_script(
            'amigopet-admin',
            AMIGOPET_WP_PLUGIN_URL . 'AmigoPet/assets/js/admin.js',
            ['jquery'],
            AMIGOPET_WP_VERSION,
            true
        );

        // Localização
        wp_localize_script('amigopet-admin', 'apwp', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('apwp_nonce'),
            'i18n' => [
                'saved' => __('Configurações salvas com sucesso!', 'amigopet-wp'),
                'error' => __('Erro ao salvar as configurações.', 'amigopet-wp'),
                'confirm_delete' => __('Tem certeza que deseja excluir?', 'amigopet-wp')
            ]
        ]);
    }

    private function getMenuIcon(): string {
        $svg_file = AMIGOPET_WP_PLUGIN_DIR . 'AmigoPet/assets/images/logo.svg';
        if (!file_exists($svg_file)) {
            return 'dashicons-pets';
        }

        $svg_content = file_get_contents($svg_file);
        if (!$svg_content) {
            return 'dashicons-pets';
        }

        // Limpa o SVG e remove quebras de linha
        $svg_content = preg_replace('/\s+/', ' ', trim($svg_content));
        return 'data:image/svg+xml;base64,' . base64_encode($svg_content);
    }

    public function addMenus(): void {
        // Menu principal
        add_menu_page(
            __('AmigoPetWP', 'amigopet-wp'),
            __('AmigoPetWP', 'amigopet-wp'),
            'manage_amigopet',
            'amigopet-wp',
            [$this, 'renderDashboard'],
            $this->getMenuIcon(),
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
            __('Configurações', 'amigopet-wp'),
            __('Configurações', 'amigopet-wp'),
            'manage_amigopet_settings',
            'amigopet-wp-settings',
            [new SettingsController(), 'renderSettings']
        );
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
        
        $logo_url = AMIGOPET_WP_PLUGIN_URL . 'AmigoPet/assets/images/logo.svg';
        $welcome_message = [
            'title' => '🐾 Bem-vindo ao AmigoPet WP! 🐾',
            'subtitle' => 'Juntos por uma adoção responsável 💕',
            'message' => [
                '🏠 Um pet não é apenas um animal, é um novo membro da família!',
                '💖 Adotar é um ato de amor que transforma duas vidas.',
                '🤝 Comprometimento e responsabilidade são essenciais.',
                '🌟 Cada animal merece um lar cheio de carinho e respeito.',
                '🎯 Nossa missão é unir pets e famílias com responsabilidade.'
            ]
        ];
        
        $this->loadView('admin/dashboard', [
            'logo_url' => $logo_url,
            'welcome' => $welcome_message
        ]);
    }

    public function renderReports(): void {
        $this->checkPermission('view_amigopet_reports');
        $this->loadView('admin/reports');
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


}
