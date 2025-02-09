<?php
namespace AmigoPet\Controllers;

use AmigoPet\Domain\Database\Database;
use AmigoPet\Domain\Database\AdoptionRepository;
use AmigoPet\Domain\Database\PetRepository;
use AmigoPet\Domain\Database\AdopterRepository;
use AmigoPet\Domain\Entities\Pet;

class AdminController {
    private $db;
    private $adoptionRepository;
    private $petRepository;
    private $adopterRepository;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->adoptionRepository = new AdoptionRepository();
        $this->petRepository = new PetRepository();
        $this->adopterRepository = new AdopterRepository();
        
        // Registra os menus do admin
        add_action('admin_menu', [$this, 'registerMenus']);
        
        // Registra os scripts e estilos do admin
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        
        // Registra a aba de ajuda
        add_action('admin_head', [$this, 'addHelpTab']);
        
        // Registra os endpoints AJAX do admin
        add_action('wp_ajax_apwp_get_dashboard_data', [$this, 'getDashboardData']);
        add_action('wp_ajax_apwp_get_reports', [$this, 'getReports']);
        add_action('wp_ajax_apwp_get_settings', [$this, 'getSettings']);
        add_action('wp_ajax_apwp_save_settings', [$this, 'saveSettings']);
        add_action('wp_ajax_apwp_load_adoptions', [$this, 'loadAdoptions']);
        add_action('wp_ajax_apwp_approve_adoption', [$this, 'approveAdoption']);
        add_action('wp_ajax_apwp_reject_adoption', [$this, 'rejectAdoption']);
    }
    
    /**
     * Registra os menus do admin
     */
    public function registerMenus(): void {
        // Menu principal
        add_menu_page(
            __('AmigoPet', 'amigopet-wp'),
            __('AmigoPet', 'amigopet-wp'),
            'manage_options',
            'amigopet',
            [$this, 'renderDashboard'],
            'dashicons-pets',
            30
        );
        
        // Submenus
        add_submenu_page(
            'amigopet',
            __('Dashboard', 'amigopet-wp'),
            __('Dashboard', 'amigopet-wp'),
            'manage_options',
            'amigopet',
            [$this, 'renderDashboard']
        );
        
        add_submenu_page(
            'amigopet',
            __('Pets', 'amigopet-wp'),
            __('Pets', 'amigopet-wp'),
            'apwp_manage_pets',
            'amigopet-pets',
            [$this, 'renderPets']
        );
        
        add_submenu_page(
            'amigopet',
            __('Adoções', 'amigopet-wp'),
            __('Adoções', 'amigopet-wp'),
            'apwp_manage_adoptions',
            'amigopet-adoptions',
            [$this, 'renderAdoptions']
        );
        
        add_submenu_page(
            'amigopet',
            __('Eventos', 'amigopet-wp'),
            __('Eventos', 'amigopet-wp'),
            'apwp_manage_events',
            'amigopet-events',
            [$this, 'renderEvents']
        );
        
        add_submenu_page(
            'amigopet',
            __('Voluntários', 'amigopet-wp'),
            __('Voluntários', 'amigopet-wp'),
            'apwp_manage_volunteers',
            'amigopet-volunteers',
            [$this, 'renderVolunteers']
        );
        
        add_submenu_page(
            'amigopet',
            __('Doações', 'amigopet-wp'),
            __('Doações', 'amigopet-wp'),
            'apwp_manage_donations',
            'amigopet-donations',
            [$this, 'renderDonations']
        );
        
        add_submenu_page(
            'amigopet',
            __('Relatórios', 'amigopet-wp'),
            __('Relatórios', 'amigopet-wp'),
            'apwp_view_reports',
            'amigopet-reports',
            [$this, 'renderReports']
        );
        
        add_submenu_page(
            'amigopet',
            __('Configurações', 'amigopet-wp'),
            __('Configurações', 'amigopet-wp'),
            'manage_options',
            'amigopet-settings',
            [$this, 'renderSettings']
        );
    }
    
    /**
     * Registra scripts e estilos
     */
    public function enqueueAssets(): void {
        // Registra o CSS do admin
        wp_enqueue_style(
            'amigopet-admin',
            AMIGOPET_WP_PLUGIN_URL . 'assets/css/admin.css',
            [],
            AMIGOPET_WP_VERSION
        );
        
        // Registra o JS do admin
        wp_enqueue_script(
            'amigopet-admin',
            AMIGOPET_WP_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            AMIGOPET_WP_VERSION,
            true
        );
        
        // Passa variáveis para o JS
        wp_localize_script('amigopet-admin', 'apwp', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('apwp_nonce')
        ]);
    }
    
    /**
     * Renderiza a página de dashboard
     */
    public function renderDashboard(): void {
        require_once AMIGOPET_WP_PLUGIN_DIR . 'views/admin/dashboard.php';
    }
    
    /**
     * Renderiza a página de pets
     */
    public function renderPets(): void {
        require_once AMIGOPET_WP_PLUGIN_DIR . 'views/admin/pets.php';
    }
    
    /**
     * Renderiza a página de adoções
     */
    public function renderAdoptions(): void {
        $perPage = 10;
        $currentPage = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        
        $adoptions = $this->adoptionRepository->findAll($perPage, $currentPage);
        $totalItems = $this->adoptionRepository->count();
        $totalPages = ceil($totalItems / $perPage);
        
        include AMIGOPET_WP_PLUGIN_DIR . 'views/admin/adoptions/list.php';
    }
    
    /**
     * Carrega a lista de adoções via AJAX
     */
    public function loadAdoptions(): void {
        check_ajax_referer('apwp_nonce', '_ajax_nonce');
        
        try {
            $perPage = 10;
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
            $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
            
            $adoptions = $this->adoptionRepository->findAll($perPage, $page, [
                'status' => $status,
                'search' => $search
            ]);
            
            $totalItems = $this->adoptionRepository->count([
                'status' => $status,
                'search' => $search
            ]);
            
            $totalPages = ceil($totalItems / $perPage);
            
            ob_start();
            include AMIGOPET_WP_PLUGIN_DIR . 'views/admin/adoptions/list-partial.php';
            $html = ob_get_clean();
            
            wp_send_json_success([
                'html' => $html,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
    
    /**
     * Aprova uma adoção
     */
    public function approveAdoption(): void {
        check_ajax_referer('apwp_nonce', '_ajax_nonce');
        
        try {
            $adoptionId = isset($_POST['adoption_id']) ? intval($_POST['adoption_id']) : 0;
            $adoption = $this->adoptionRepository->findById($adoptionId);
            
            if (!$adoption) {
                throw new \Exception(__('Adoção não encontrada', 'amigopet-wp'));
            }
            
            $adoption->approve();
            $this->adoptionRepository->update($adoption);
            
            wp_send_json_success(['message' => __('Adoção aprovada com sucesso', 'amigopet-wp')]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
    
    /**
     * Rejeita uma adoção
     */
    public function rejectAdoption(): void {
        check_ajax_referer('apwp_nonce', '_ajax_nonce');
        
        try {
            $adoptionId = isset($_POST['adoption_id']) ? intval($_POST['adoption_id']) : 0;
            $adoption = $this->adoptionRepository->findById($adoptionId);
            
            if (!$adoption) {
                throw new \Exception(__('Adoção não encontrada', 'amigopet-wp'));
            }
            
            $adoption->reject();
            $this->adoptionRepository->update($adoption);
            
            wp_send_json_success(['message' => __('Adoção rejeitada com sucesso', 'amigopet-wp')]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
    
    /**
     * Renderiza a página de eventos
     */
    public function renderEvents(): void {
        require_once AMIGOPET_WP_PLUGIN_DIR . 'views/admin/events.php';
    }
    
    /**
     * Renderiza a página de voluntários
     */
    public function renderVolunteers(): void {
        require_once AMIGOPET_WP_PLUGIN_DIR . 'views/admin/volunteers.php';
    }
    
    /**
     * Renderiza a página de doações
     */
    public function renderDonations(): void {
        require_once AMIGOPET_WP_PLUGIN_DIR . 'views/admin/donations.php';
    }
    
    /**
     * Renderiza a página de relatórios
     */
    public function renderReports(): void {
        require_once AMIGOPET_WP_PLUGIN_DIR . 'views/admin/reports.php';
    }
    
    /**
     * Renderiza a página de configurações
     */
    public function renderSettings(): void {
        require_once AMIGOPET_WP_PLUGIN_DIR . 'views/admin/settings.php';
    }
    
    /**
     * Retorna dados para o dashboard
     */
    public function getDashboardData(): void {
        check_ajax_referer('apwp_nonce');
        
        $data = [
            'pets' => [
                'total' => $this->db->getPetRepository()->count(),
                'available' => $this->db->getPetRepository()->countAvailable(),
                'adopted' => $this->db->getPetRepository()->countAdopted()
            ],
            'adoptions' => [
                'total' => $this->db->getAdoptionRepository()->count(),
                'pending' => $this->db->getAdoptionRepository()->countPending(),
                'completed' => $this->db->getAdoptionRepository()->countCompleted()
            ],
            'events' => [
                'total' => $this->db->getEventRepository()->count(),
                'upcoming' => $this->db->getEventRepository()->countUpcoming()
            ],
            'donations' => [
                'total' => $this->db->getDonationRepository()->count(),
                'amount' => $this->db->getDonationRepository()->sumAmount()
            ]
        ];
        
        wp_send_json_success($data);
    }
    
    /**
     * Retorna dados para relatórios
     */
    public function getReports(): void {
        check_ajax_referer('apwp_nonce');
        
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
                $data = [];
        }
        
        wp_send_json_success($data);
    }
    
    /**
     * Retorna configurações
     */
    public function getSettings(): void {
        check_ajax_referer('apwp_nonce');
        
        $settings = [
            'general' => [
                'organization_name' => get_option('apwp_organization_name'),
                'organization_email' => get_option('apwp_organization_email'),
                'organization_phone' => get_option('apwp_organization_phone')
            ],
            'api' => [
                'google_maps_key' => get_option('apwp_google_maps_key'),
                'petfinder_key' => get_option('apwp_petfinder_key'),
                'payment_gateway_key' => get_option('apwp_payment_gateway_key')
            ],
            'email' => [
                'from_name' => get_option('apwp_email_from_name'),
                'from_email' => get_option('apwp_email_from_email'),
                'template_header' => get_option('apwp_email_template_header'),
                'template_footer' => get_option('apwp_email_template_footer')
            ]
        ];
        
        wp_send_json_success($settings);
    }
    
    /**
     * Salva configurações
     */
    public function saveSettings(): void {
        check_ajax_referer('apwp_nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Você não tem permissão para fazer isso.', 'amigopet-wp'));
        }
        
        $settings = $_POST['settings'] ?? [];
        if (empty($settings)) {
            wp_send_json_error(__('Nenhuma configuração fornecida.', 'amigopet-wp'));
        }
        
        foreach ($settings as $key => $value) {
            update_option('apwp_' . $key, sanitize_text_field($value));
        }
        
        wp_send_json_success(__('Configurações salvas com sucesso.', 'amigopet-wp'));
    }
    
    /**
     * Adiciona a aba de ajuda no admin
     */
    public function addHelpTab() {
        $screen = get_current_screen();
        
        // Só adiciona a aba de ajuda nas páginas do plugin
        if (strpos($screen->id, 'amigopet') !== false) {
            $screen->add_help_tab(array(
                'id' => 'amigopet_help',
                'title' => __('Ajuda do AmigoPet', 'amigopet-wp'),
                'content' => $this->loadView('admin/help', array(), true)
            ));
        }
    }
}
