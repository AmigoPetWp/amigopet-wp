<?php
namespace Controllers;

use Domain\Database\Database;

class PublicController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        
        // Registra o endpoint para impressão do termo
        add_action('wp_ajax_print_adoption_terms', [$this, 'printAdoptionTerms']);
        add_action('wp_ajax_nopriv_print_adoption_terms', [$this, 'printAdoptionTerms']);
        
        // Registra os shortcodes
        add_shortcode('amigopet_pets', [$this, 'renderPetsGrid']);
        add_shortcode('amigopet_events', [$this, 'renderEventsGrid']);
        add_shortcode('amigopet_adoption_form', [$this, 'renderAdoptionForm']);
        add_shortcode('amigopet_donation_form', [$this, 'renderDonationForm']);
        add_shortcode('amigopet_volunteer_form', [$this, 'renderVolunteerForm']);
        
        // Registra os scripts e estilos do frontend
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
        
        // Registra os endpoints AJAX do frontend
        add_action('wp_ajax_nopriv_apwp_get_pets', [$this, 'getPets']);
        add_action('wp_ajax_apwp_get_pets', [$this, 'getPets']);
        
        add_action('wp_ajax_nopriv_apwp_get_events', [$this, 'getEvents']);
        add_action('wp_ajax_apwp_get_events', [$this, 'getEvents']);
        
        add_action('wp_ajax_apwp_submit_adoption', [$this, 'submitAdoption']);
        add_action('wp_ajax_apwp_submit_donation', [$this, 'submitDonation']);
        add_action('wp_ajax_apwp_submit_volunteer', [$this, 'submitVolunteer']);
    }
    
    /**
     * Registra scripts e estilos
     */
    public function enqueueAssets(): void {
        // Registra o CSS do frontend
        wp_enqueue_style(
            'amigopet-public',
            AMIGOPET_WP_PLUGIN_URL . 'assets/css/public.css',
            [],
            AMIGOPET_WP_VERSION
        );
        
        // Registra as dependências JS
        wp_enqueue_script(
            'jquery-mask',
            AMIGOPET_WP_PLUGIN_URL . 'assets/js/lib/jquery.mask.min.js',
            ['jquery'],
            '1.14.16',
            true
        );
        
        wp_enqueue_script(
            'jquery-validate',
            AMIGOPET_WP_PLUGIN_URL . 'assets/js/lib/jquery.validate.min.js',
            ['jquery'],
            '1.19.5',
            true
        );
        
        // Registra o JS do frontend
        wp_enqueue_script(
            'amigopet-public',
            AMIGOPET_WP_PLUGIN_URL . 'assets/js/public.js',
            ['jquery', 'jquery-mask', 'jquery-validate'],
            AMIGOPET_WP_VERSION,
            true
        );
        
        // Passa variáveis para o JS
        wp_localize_script('amigopet-public', 'apwp', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('apwp_nonce'),
            'i18n' => [
                'error' => __('Erro ao processar requisição.', 'amigopet-wp'),
                'success' => __('Operação realizada com sucesso.', 'amigopet-wp')
            ]
        ]);
    }
    
    /**
     * Renderiza grid de pets
     */
    public function renderPetsGrid($atts): string {
        $atts = shortcode_atts([
            'species' => '',
            'size' => '',
            'limit' => 12
        ], $atts);
        
        ob_start();
        require AMIGOPET_WP_PLUGIN_DIR . 'views/public/pets-grid.php';
        return ob_get_clean();
    }
    
    /**
     * Renderiza grid de eventos
     */
    public function renderEventsGrid($atts): string {
        $atts = shortcode_atts([
            'limit' => 6,
            'upcoming' => true
        ], $atts);
        
        ob_start();
        require AMIGOPET_WP_PLUGIN_DIR . 'views/public/events-grid.php';
        return ob_get_clean();
    }
    
    /**
     * Renderiza formulário de adoção
     */
    public function renderAdoptionForm($atts): string {
        $atts = shortcode_atts([
            'pet_id' => 0
        ], $atts);
        
        ob_start();
        require AMIGOPET_WP_PLUGIN_DIR . 'views/public/adoption-form.php';
        return ob_get_clean();
    }
    
    /**
     * Renderiza formulário de doação
     */
    public function renderDonationForm($atts): string {
        $atts = shortcode_atts([
            'organization_id' => 0
        ], $atts);
        
        ob_start();
        require AMIGOPET_WP_PLUGIN_DIR . 'views/public/donation-form.php';
        return ob_get_clean();
    }
    
    /**
     * Renderiza formulário de voluntário
     */
    public function renderVolunteerForm($atts): string {
        $atts = shortcode_atts([
            'organization_id' => 0
        ], $atts);
        
        ob_start();
        require AMIGOPET_WP_PLUGIN_DIR . 'views/public/volunteer-form.php';
        return ob_get_clean();
    }
    
    /**
     * Retorna lista de pets
     */
    public function getPets(): void {
        check_ajax_referer('apwp_nonce');
        
        $filters = [
            'species' => $_GET['species'] ?? '',
            'size' => $_GET['size'] ?? '',
            'limit' => (int) ($_GET['limit'] ?? 12),
            'page' => (int) ($_GET['page'] ?? 1)
        ];
        
        $pets = $this->db->getPetRepository()->findAvailable($filters);
        $total = $this->db->getPetRepository()->countAvailable($filters);
        
        wp_send_json_success([
            'pets' => $pets,
            'total' => $total,
            'pages' => ceil($total / $filters['limit'])
        ]);
    }
    
    /**
     * Retorna lista de eventos
     */
    public function getEvents(): void {
        check_ajax_referer('apwp_nonce');
        
        $filters = [
            'upcoming' => filter_var($_GET['upcoming'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'limit' => (int) ($_GET['limit'] ?? 6),
            'page' => (int) ($_GET['page'] ?? 1)
        ];
        
        $events = $this->db->getEventRepository()->findAll($filters);
        $total = $this->db->getEventRepository()->count($filters);
        
        wp_send_json_success([
            'events' => $events,
            'total' => $total,
            'pages' => ceil($total / $filters['limit'])
        ]);
    }
    
    /**
     * Processa submissão de adoção
     */
    public function submitAdoption(): void {
        check_ajax_referer('apwp_nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(__('Você precisa estar logado para adotar.', 'amigopet-wp'));
        }
        
        $petId = (int) ($_POST['pet_id'] ?? 0);
        if (!$petId) {
            wp_send_json_error(__('Pet não encontrado.', 'amigopet-wp'));
        }
        
        try {
            $adoptionService = new \AmigoPet\Domain\Services\AdoptionService(
                $this->db->getAdoptionRepository()
            );
            
            $adoptionId = $adoptionService->createAdoption(
                get_current_user_id(),
                $petId,
                $_POST['reason'] ?? '',
                $_POST['has_other_pets'] ?? false,
                $_POST['home_type'] ?? '',
                $_POST['yard_size'] ?? ''
            );
            
            wp_send_json_success([
                'adoption_id' => $adoptionId,
                'message' => __('Solicitação de adoção enviada com sucesso.', 'amigopet-wp')
            ]);
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }
    
    /**
     * Processa submissão de doação
     */
    public function submitDonation(): void {
        check_ajax_referer('apwp_nonce');
        
        $organizationId = (int) ($_POST['organization_id'] ?? 0);
        if (!$organizationId) {
            wp_send_json_error(__('Organização não encontrada.', 'amigopet-wp'));
        }
        
        try {
            $donationService = new \AmigoPet\Domain\Services\DonationService(
                $this->db->getDonationRepository()
            );
            
            $donationId = $donationService->createDonation(
                $organizationId,
                $_POST['donor_name'] ?? '',
                $_POST['donor_email'] ?? '',
                $_POST['donor_phone'] ?? '',
                $_POST['type'] ?? '',
                $_POST['description'] ?? '',
                (float) ($_POST['amount'] ?? 0)
            );
            
            wp_send_json_success([
                'donation_id' => $donationId,
                'message' => __('Doação registrada com sucesso.', 'amigopet-wp')
            ]);
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }
    
    /**
     * Processa submissão de voluntário
     */
    public function submitVolunteer(): void {
        check_ajax_referer('apwp_nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(__('Você precisa estar logado para se voluntariar.', 'amigopet-wp'));
        }
        
        $organizationId = (int) ($_POST['organization_id'] ?? 0);
        if (!$organizationId) {
            wp_send_json_error(__('Organização não encontrada.', 'amigopet-wp'));
        }
        
        try {
            $volunteerService = new \AmigoPet\Domain\Services\VolunteerService(
                $this->db->getVolunteerRepository()
            );
            
            $volunteerId = $volunteerService->createVolunteer(
                $organizationId,
                $_POST['name'] ?? '',
                $_POST['email'] ?? '',
                $_POST['phone'] ?? '',
                $_POST['availability'] ?? '',
                $_POST['skills'] ?? ''
            );
            
            wp_send_json_success([
                'volunteer_id' => $volunteerId,
                'message' => __('Cadastro de voluntário realizado com sucesso.', 'amigopet-wp')
            ]);
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }
    
    /**
     * Exibe o termo de adoção para impressão
     */
    public function printAdoptionTerms(): void {
        // Verifica se o usuário está logado
        if (!is_user_logged_in()) {
            wp_die(__('Você precisa estar logado para acessar o termo.', 'amigopet-wp'));
        }
        
        // Verifica se o pet existe
        $pet_id = (int) $_GET['pet_id'];
        $pet = get_post($pet_id);
        if (!$pet || $pet->post_type !== 'apwp_pet') {
            wp_die(__('Pet não encontrado.', 'amigopet-wp'));
        }
        
        // Carrega a view do termo
        require AMIGOPET_WP_PLUGIN_DIR . 'views/public/print-adoption-terms.php';
        exit;
    }
}
