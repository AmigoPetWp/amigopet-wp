<?php
namespace AmigoPetWp\Controllers;

use AmigoPetWp\Domain\Database\Database;
use AmigoPetWp\Domain\Database\AdoptionRepository;
use AmigoPetWp\Domain\Database\PetRepository;
use AmigoPetWp\Domain\Database\AdopterRepository;
use AmigoPetWp\Domain\Entities\Pet;

class AdminController {
    private $db;
    private $adoptionRepository;
    private $petRepository;
    private $adopterRepository;
    
    public function __construct() {
        error_log('Construtor do AdminController chamado');
        $this->db = Database::getInstance();
        
        // Adiciona ações para gerenciar todas as entidades
        add_action('admin_menu', [$this, 'addMenus']);
        
        // Ações para pets
        add_action('admin_post_apwp_save_pet', [$this, 'savePet']);
        add_action('admin_post_nopriv_apwp_save_pet', [$this, 'savePet']);
        add_action('admin_post_apwp_delete_pet', [$this, 'deletePet']);
        add_action('admin_post_nopriv_apwp_delete_pet', [$this, 'deletePet']);
        
        // Ações para adoções
        add_action('admin_post_apwp_save_adoption', [$this, 'saveAdoption']);
        add_action('admin_post_nopriv_apwp_save_adoption', [$this, 'saveAdoption']);
        add_action('admin_post_apwp_delete_adoption', [$this, 'deleteAdoption']);
        add_action('admin_post_nopriv_apwp_delete_adoption', [$this, 'deleteAdoption']);
        
        // Ações para eventos
        add_action('admin_post_apwp_save_event', [$this, 'saveEvent']);
        add_action('admin_post_nopriv_apwp_save_event', [$this, 'saveEvent']);
        add_action('admin_post_apwp_delete_event', [$this, 'deleteEvent']);
        add_action('admin_post_nopriv_apwp_delete_event', [$this, 'deleteEvent']);
        
        // Ações para voluntários
        add_action('admin_post_apwp_save_volunteer', [$this, 'saveVolunteer']);
        add_action('admin_post_nopriv_apwp_save_volunteer', [$this, 'saveVolunteer']);
        add_action('admin_post_apwp_delete_volunteer', [$this, 'deleteVolunteer']);
        add_action('admin_post_nopriv_apwp_delete_volunteer', [$this, 'deleteVolunteer']);
        
        // Ações para doações
        add_action('admin_post_apwp_save_donation', [$this, 'saveDonation']);
        add_action('admin_post_nopriv_apwp_save_donation', [$this, 'saveDonation']);
        add_action('admin_post_apwp_delete_donation', [$this, 'deleteDonation']);
        add_action('admin_post_nopriv_apwp_delete_donation', [$this, 'deleteDonation']);
        
        // Ações para termos
        add_action('admin_post_apwp_save_term', [$this, 'saveTerm']);
        add_action('admin_post_nopriv_apwp_save_term', [$this, 'saveTerm']);
        add_action('admin_post_apwp_delete_term', [$this, 'deleteTerm']);
        add_action('admin_post_nopriv_apwp_delete_term', [$this, 'deleteTerm']);
        global $wpdb;
        $this->adoptionRepository = new AdoptionRepository($wpdb);
        $this->petRepository = new PetRepository($wpdb);
        $this->adopterRepository = new AdopterRepository($wpdb);
        
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
    public function addMenus(): void {
        // Menu Pets
        add_submenu_page(
            'amigopet-wp',
            __('Pets', 'amigopet-wp'),
            __('Pets', 'amigopet-wp'),
            'edit_apwp_pets',
            'apwp-pets',
            [$this, 'renderPetsList']
        );
        
        add_submenu_page(
            'amigopet-wp',
            __('Adicionar Novo Pet', 'amigopet-wp'),
            __('Adicionar Novo', 'amigopet-wp'),
            'edit_apwp_pets',
            'apwp-pet-form',
            [$this, 'renderPetForm']
        );
        
        // Menu Adoções
        add_submenu_page(
            'amigopet-wp',
            __('Adoções', 'amigopet-wp'),
            __('Adoções', 'amigopet-wp'),
            'edit_apwp_adoptions',
            'apwp-adoptions',
            [$this, 'renderAdoptionsList']
        );
        
        add_submenu_page(
            'amigopet-wp',
            __('Adicionar Nova Adoção', 'amigopet-wp'),
            __('Adicionar Nova', 'amigopet-wp'),
            'edit_apwp_adoptions',
            'apwp-adoption-form',
            [$this, 'renderAdoptionForm']
        );
        
        // Menu Eventos
        add_submenu_page(
            'amigopet-wp',
            __('Eventos', 'amigopet-wp'),
            __('Eventos', 'amigopet-wp'),
            'edit_apwp_events',
            'apwp-events',
            [$this, 'renderEventsList']
        );
        
        add_submenu_page(
            'amigopet-wp',
            __('Adicionar Novo Evento', 'amigopet-wp'),
            __('Adicionar Novo', 'amigopet-wp'),
            'edit_apwp_events',
            'apwp-event-form',
            [$this, 'renderEventForm']
        );
        
        // Menu Voluntários
        add_submenu_page(
            'amigopet-wp',
            __('Voluntários', 'amigopet-wp'),
            __('Voluntários', 'amigopet-wp'),
            'edit_apwp_volunteers',
            'apwp-volunteers',
            [$this, 'renderVolunteersList']
        );
        
        add_submenu_page(
            'amigopet-wp',
            __('Adicionar Novo Voluntário', 'amigopet-wp'),
            __('Adicionar Novo', 'amigopet-wp'),
            'edit_apwp_volunteers',
            'apwp-volunteer-form',
            [$this, 'renderVolunteerForm']
        );
        
        // Menu Doações
        add_submenu_page(
            'amigopet-wp',
            __('Doações', 'amigopet-wp'),
            __('Doações', 'amigopet-wp'),
            'edit_apwp_donations',
            'apwp-donations',
            [$this, 'renderDonationsList']
        );
        
        add_submenu_page(
            'amigopet-wp',
            __('Adicionar Nova Doação', 'amigopet-wp'),
            __('Adicionar Nova', 'amigopet-wp'),
            'edit_apwp_donations',
            'apwp-donation-form',
            [$this, 'renderDonationForm']
        );
        
        // Menu Termos
        add_submenu_page(
            'amigopet-wp',
            __('Termos', 'amigopet-wp'),
            __('Termos', 'amigopet-wp'),
            'edit_apwp_terms',
            'apwp-terms',
            [$this, 'renderTermsList']
        );
        
        add_submenu_page(
            'amigopet-wp',
            __('Adicionar Novo Termo', 'amigopet-wp'),
            __('Adicionar Novo', 'amigopet-wp'),
            'edit_apwp_terms',
            'apwp-term-form',
            [$this, 'renderTermForm']
        );
    }
    
    public function renderPetsList(): void {
        require_once plugin_dir_path(dirname(__FILE__)) . 'views/admin/pets-list.php';
    }
    
    public function renderPetForm(): void {
        $pet_id = isset($_GET['pet_id']) ? intval($_GET['pet_id']) : 0;
        $pet = $pet_id ? get_post($pet_id) : null;
        
        if ($pet_id && (!$pet || $pet->post_type !== 'apwp_pet')) {
            wp_die(__('Pet não encontrado', 'amigopet-wp'));
        }
        
        require_once plugin_dir_path(dirname(__FILE__)) . 'views/admin/pet-form.php';
    }
    
    public function renderAdoptionsList(): void {
        require_once plugin_dir_path(dirname(__FILE__)) . 'views/admin/adoptions-list.php';
    }
    
    public function renderAdoptionForm(): void {
        $adoption_id = isset($_GET['adoption_id']) ? intval($_GET['adoption_id']) : 0;
        $adoption = $adoption_id ? get_post($adoption_id) : null;
        
        if ($adoption_id && (!$adoption || $adoption->post_type !== 'apwp_adoption')) {
            wp_die(__('Adoção não encontrada', 'amigopet-wp'));
        }
        
        require_once plugin_dir_path(dirname(__FILE__)) . 'views/admin/adoption-form.php';
    }
    
    public function renderEventsList(): void {
        require_once plugin_dir_path(dirname(__FILE__)) . 'views/admin/events-list.php';
    }
    
    public function renderEventForm(): void {
        $event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
        $event = $event_id ? get_post($event_id) : null;
        
        if ($event_id && (!$event || $event->post_type !== 'apwp_event')) {
            wp_die(__('Evento não encontrado', 'amigopet-wp'));
        }
        
        require_once plugin_dir_path(dirname(__FILE__)) . 'views/admin/event-form.php';
    }
    
    public function renderVolunteersList(): void {
        require_once plugin_dir_path(dirname(__FILE__)) . 'views/admin/volunteers-list.php';
    }
    
    public function renderVolunteerForm(): void {
        $volunteer_id = isset($_GET['volunteer_id']) ? intval($_GET['volunteer_id']) : 0;
        $volunteer = $volunteer_id ? get_post($volunteer_id) : null;
        
        if ($volunteer_id && (!$volunteer || $volunteer->post_type !== 'apwp_volunteer')) {
            wp_die(__('Voluntário não encontrado', 'amigopet-wp'));
        }
        
        require_once plugin_dir_path(dirname(__FILE__)) . 'views/admin/volunteer-form.php';
    }
    
    public function renderDonationsList(): void {
        require_once plugin_dir_path(dirname(__FILE__)) . 'views/admin/donations-list.php';
    }
    
    public function renderDonationForm(): void {
        $donation_id = isset($_GET['donation_id']) ? intval($_GET['donation_id']) : 0;
        $donation = $donation_id ? get_post($donation_id) : null;
        
        if ($donation_id && (!$donation || $donation->post_type !== 'apwp_donation')) {
            wp_die(__('Doação não encontrada', 'amigopet-wp'));
        }
        
        require_once plugin_dir_path(dirname(__FILE__)) . 'views/admin/donation-form.php';
    }
    
    public function savePet(): void {
        if (!isset($_POST['apwp_pet_nonce']) || !wp_verify_nonce($_POST['apwp_pet_nonce'], 'apwp_save_pet')) {
            wp_die(__('Ação não autorizada', 'amigopet-wp'));
        }
        
        if (!current_user_can('edit_apwp_pets')) {
            wp_die(__('Você não tem permissão para realizar esta ação', 'amigopet-wp'));
        }
        
        $pet_id = isset($_POST['pet_id']) ? intval($_POST['pet_id']) : 0;
        
        $pet_data = [
            'post_type' => 'apwp_pet',
            'post_status' => 'publish',
            'post_title' => sanitize_text_field($_POST['pet_name']),
        ];
        
        if ($pet_id) {
            $pet_data['ID'] = $pet_id;
            $pet_id = wp_update_post($pet_data);
        } else {
            $pet_id = wp_insert_post($pet_data);
        }
        
        if (is_wp_error($pet_id)) {
            wp_die($pet_id->get_error_message());
        }
        
        // Salva os meta dados
        $meta_fields = [
            'pet_name',
            'pet_type',
            'pet_breed',
            'pet_age',
            'pet_size',
            'pet_gender',
            'pet_vaccinated',
            'pet_neutered',
            'pet_description'
        ];
        
        foreach ($meta_fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($pet_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
        
        // Processa a foto
        if (!empty($_FILES['pet_photo']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            
            $attachment_id = media_handle_upload('pet_photo', $pet_id);
            
            if (!is_wp_error($attachment_id)) {
                set_post_thumbnail($pet_id, $attachment_id);
            }
        }
        
        wp_redirect(admin_url('admin.php?page=apwp-pets&message=1'));
        exit;
    }
    
    public function saveAdoption(): void {
        if (!isset($_POST['apwp_adoption_nonce']) || !wp_verify_nonce($_POST['apwp_adoption_nonce'], 'apwp_save_adoption')) {
            wp_die(__('Ação não autorizada', 'amigopet-wp'));
        }
        
        if (!current_user_can('edit_apwp_adoptions')) {
            wp_die(__('Você não tem permissão para editar adoções', 'amigopet-wp'));
        }
        
        $adoption_id = isset($_POST['adoption_id']) ? intval($_POST['adoption_id']) : 0;
        $pet_id = intval($_POST['pet_id']);
        
        $adoption_data = array(
            'post_title'    => sprintf(__('Adoção de %s', 'amigopet-wp'), get_post_meta($pet_id, 'pet_name', true)),
            'post_type'     => 'apwp_adoption',
            'post_status'   => 'publish'
        );
        
        if ($adoption_id) {
            $adoption_data['ID'] = $adoption_id;
            $adoption_id = wp_update_post($adoption_data);
        } else {
            $adoption_id = wp_insert_post($adoption_data);
        }
        
        if (is_wp_error($adoption_id)) {
            wp_die($adoption_id->get_error_message());
        }
        
        // Atualiza os meta dados
        update_post_meta($adoption_id, 'pet_id', $pet_id);
        update_post_meta($adoption_id, 'adopter_name', sanitize_text_field($_POST['adopter_name']));
        update_post_meta($adoption_id, 'adopter_email', sanitize_email($_POST['adopter_email']));
        update_post_meta($adoption_id, 'adopter_phone', sanitize_text_field($_POST['adopter_phone']));
        update_post_meta($adoption_id, 'adopter_address', sanitize_textarea_field($_POST['adopter_address']));
        update_post_meta($adoption_id, 'adoption_date', sanitize_text_field($_POST['adoption_date']));
        update_post_meta($adoption_id, 'adoption_status', sanitize_text_field($_POST['adoption_status']));
        update_post_meta($adoption_id, 'adoption_notes', sanitize_textarea_field($_POST['adoption_notes']));
        
        wp_redirect(admin_url('admin.php?page=apwp-adoptions&message=1'));
        exit;
    }
    
    public function saveEvent(): void {
        if (!isset($_POST['apwp_event_nonce']) || !wp_verify_nonce($_POST['apwp_event_nonce'], 'apwp_save_event')) {
            wp_die(__('Ação não autorizada', 'amigopet-wp'));
        }
        
        if (!current_user_can('edit_apwp_events')) {
            wp_die(__('Você não tem permissão para editar eventos', 'amigopet-wp'));
        }
        
        $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
        
        $event_data = array(
            'post_title'    => sanitize_text_field($_POST['event_title']),
            'post_content'  => wp_kses_post($_POST['event_description']),
            'post_type'     => 'apwp_event',
            'post_status'   => 'publish'
        );
        
        if ($event_id) {
            $event_data['ID'] = $event_id;
            $event_id = wp_update_post($event_data);
        } else {
            $event_id = wp_insert_post($event_data);
        }
        
        if (is_wp_error($event_id)) {
            wp_die($event_id->get_error_message());
        }
        
        // Atualiza os meta dados
        update_post_meta($event_id, 'event_title', sanitize_text_field($_POST['event_title']));
        update_post_meta($event_id, 'event_type', sanitize_text_field($_POST['event_type']));
        update_post_meta($event_id, 'event_date', sanitize_text_field($_POST['event_date']));
        update_post_meta($event_id, 'event_time', sanitize_text_field($_POST['event_time']));
        update_post_meta($event_id, 'event_location', sanitize_text_field($_POST['event_location']));
        update_post_meta($event_id, 'event_description', sanitize_textarea_field($_POST['event_description']));
        update_post_meta($event_id, 'event_organizer', sanitize_text_field($_POST['event_organizer']));
        update_post_meta($event_id, 'event_contact', sanitize_text_field($_POST['event_contact']));
        
        // Processa o upload da foto
        if (!empty($_FILES['event_photo']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            
            $attachment_id = media_handle_upload('event_photo', $event_id);
            
            if (!is_wp_error($attachment_id)) {
                set_post_thumbnail($event_id, $attachment_id);
            }
        }
        
        wp_redirect(admin_url('admin.php?page=apwp-events&message=1'));
        exit;
    }
    
    public function saveVolunteer(): void {
        if (!isset($_POST['apwp_volunteer_nonce']) || !wp_verify_nonce($_POST['apwp_volunteer_nonce'], 'apwp_save_volunteer')) {
            wp_die(__('Ação não autorizada', 'amigopet-wp'));
        }
        
        if (!current_user_can('edit_apwp_volunteers')) {
            wp_die(__('Você não tem permissão para editar voluntários', 'amigopet-wp'));
        }
        
        $volunteer_id = isset($_POST['volunteer_id']) ? intval($_POST['volunteer_id']) : 0;
        
        $volunteer_data = array(
            'post_title'    => sanitize_text_field($_POST['volunteer_name']),
            'post_type'     => 'apwp_volunteer',
            'post_status'   => 'publish'
        );
        
        if ($volunteer_id) {
            $volunteer_data['ID'] = $volunteer_id;
            $volunteer_id = wp_update_post($volunteer_data);
        } else {
            $volunteer_id = wp_insert_post($volunteer_data);
        }
        
        if (is_wp_error($volunteer_id)) {
            wp_die($volunteer_id->get_error_message());
        }
        
        // Atualiza os meta dados
        update_post_meta($volunteer_id, 'volunteer_name', sanitize_text_field($_POST['volunteer_name']));
        update_post_meta($volunteer_id, 'volunteer_email', sanitize_email($_POST['volunteer_email']));
        update_post_meta($volunteer_id, 'volunteer_phone', sanitize_text_field($_POST['volunteer_phone']));
        update_post_meta($volunteer_id, 'volunteer_address', sanitize_textarea_field($_POST['volunteer_address']));
        update_post_meta($volunteer_id, 'volunteer_birth_date', sanitize_text_field($_POST['volunteer_birth_date']));
        update_post_meta($volunteer_id, 'volunteer_availability', $_POST['volunteer_availability']);
        update_post_meta($volunteer_id, 'volunteer_skills', $_POST['volunteer_skills']);
        update_post_meta($volunteer_id, 'volunteer_status', sanitize_text_field($_POST['volunteer_status']));
        update_post_meta($volunteer_id, 'volunteer_notes', sanitize_textarea_field($_POST['volunteer_notes']));
        
        // Processa o upload da foto
        if (!empty($_FILES['volunteer_photo']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            
            $attachment_id = media_handle_upload('volunteer_photo', $volunteer_id);
            
            if (!is_wp_error($attachment_id)) {
                set_post_thumbnail($volunteer_id, $attachment_id);
            }
        }
        
        wp_redirect(admin_url('admin.php?page=apwp-volunteers&message=1'));
        exit;
    }
    
    public function saveDonation(): void {
        if (!isset($_POST['apwp_donation_nonce']) || !wp_verify_nonce($_POST['apwp_donation_nonce'], 'apwp_save_donation')) {
            wp_die(__('Ação não autorizada', 'amigopet-wp'));
        }
        
        if (!current_user_can('edit_apwp_donations')) {
            wp_die(__('Você não tem permissão para editar doações', 'amigopet-wp'));
        }
        
        $donation_id = isset($_POST['donation_id']) ? intval($_POST['donation_id']) : 0;
        
        $donation_data = array(
            'post_title'    => sprintf(__('Doação de %s', 'amigopet-wp'), sanitize_text_field($_POST['donor_name'])),
            'post_type'     => 'apwp_donation',
            'post_status'   => 'publish'
        );
        
        if ($donation_id) {
            $donation_data['ID'] = $donation_id;
            $donation_id = wp_update_post($donation_data);
        } else {
            $donation_id = wp_insert_post($donation_data);
        }
        
        if (is_wp_error($donation_id)) {
            wp_die($donation_id->get_error_message());
        }
        
        // Atualiza os meta dados
        update_post_meta($donation_id, 'donor_name', sanitize_text_field($_POST['donor_name']));
        update_post_meta($donation_id, 'donor_email', sanitize_email($_POST['donor_email']));
        update_post_meta($donation_id, 'donor_phone', sanitize_text_field($_POST['donor_phone']));
        update_post_meta($donation_id, 'donation_type', sanitize_text_field($_POST['donation_type']));
        update_post_meta($donation_id, 'donation_amount', sanitize_text_field($_POST['donation_amount']));
        update_post_meta($donation_id, 'donation_items', sanitize_textarea_field($_POST['donation_items']));
        update_post_meta($donation_id, 'donation_date', sanitize_text_field($_POST['donation_date']));
        update_post_meta($donation_id, 'donation_status', sanitize_text_field($_POST['donation_status']));
        update_post_meta($donation_id, 'donation_notes', sanitize_textarea_field($_POST['donation_notes']));
        
        wp_redirect(admin_url('admin.php?page=apwp-donations&message=1'));
        exit;
    }
    
    public function deletePet(): void {
        if (!isset($_GET['pet_id']) || !isset($_GET['_wpnonce'])) {
            wp_die(__('Parâmetros inválidos', 'amigopet-wp'));
        }
        
        $pet_id = intval($_GET['pet_id']);
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_pet_' . $pet_id)) {
            wp_die(__('Ação não autorizada', 'amigopet-wp'));
        }
        
        if (!current_user_can('delete_apwp_pets')) {
            wp_die(__('Você não tem permissão para excluir pets', 'amigopet-wp'));
        }
        
        $result = wp_delete_post($pet_id, true);
        
        if (!$result) {
            wp_die(__('Erro ao excluir o pet', 'amigopet-wp'));
        }
        
        wp_redirect(admin_url('admin.php?page=apwp-pets&message=2'));
        exit;
    }
    
    public function deleteAdoption(): void {
        if (!isset($_GET['adoption_id']) || !isset($_GET['_wpnonce'])) {
            wp_die(__('Parâmetros inválidos', 'amigopet-wp'));
        }
        
        $adoption_id = intval($_GET['adoption_id']);
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_adoption_' . $adoption_id)) {
            wp_die(__('Ação não autorizada', 'amigopet-wp'));
        }
        
        if (!current_user_can('delete_apwp_adoptions')) {
            wp_die(__('Você não tem permissão para excluir adoções', 'amigopet-wp'));
        }
        
        $result = wp_delete_post($adoption_id, true);
        
        if (!$result) {
            wp_die(__('Erro ao excluir a adoção', 'amigopet-wp'));
        }
        
        wp_redirect(admin_url('admin.php?page=apwp-adoptions&message=2'));
        exit;
    }
    
    public function deleteEvent(): void {
        if (!isset($_GET['event_id']) || !isset($_GET['_wpnonce'])) {
            wp_die(__('Parâmetros inválidos', 'amigopet-wp'));
        }
        
        $event_id = intval($_GET['event_id']);
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_event_' . $event_id)) {
            wp_die(__('Ação não autorizada', 'amigopet-wp'));
        }
        
        if (!current_user_can('delete_apwp_events')) {
            wp_die(__('Você não tem permissão para excluir eventos', 'amigopet-wp'));
        }
        
        $result = wp_delete_post($event_id, true);
        
        if (!$result) {
            wp_die(__('Erro ao excluir o evento', 'amigopet-wp'));
        }
        
        wp_redirect(admin_url('admin.php?page=apwp-events&message=2'));
        exit;
    }
    
    public function deleteVolunteer(): void {
        if (!isset($_GET['volunteer_id']) || !isset($_GET['_wpnonce'])) {
            wp_die(__('Parâmetros inválidos', 'amigopet-wp'));
        }
        
        $volunteer_id = intval($_GET['volunteer_id']);
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_volunteer_' . $volunteer_id)) {
            wp_die(__('Ação não autorizada', 'amigopet-wp'));
        }
        
        if (!current_user_can('delete_apwp_volunteers')) {
            wp_die(__('Você não tem permissão para excluir voluntários', 'amigopet-wp'));
        }
        
        $result = wp_delete_post($volunteer_id, true);
        
        if (!$result) {
            wp_die(__('Erro ao excluir o voluntário', 'amigopet-wp'));
        }
        
        wp_redirect(admin_url('admin.php?page=apwp-volunteers&message=2'));
        exit;
    }
    
    public function deleteDonation(): void {
        if (!isset($_GET['donation_id']) || !isset($_GET['_wpnonce'])) {
            wp_die(__('Parâmetros inválidos', 'amigopet-wp'));
        }
        
        $donation_id = intval($_GET['donation_id']);
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_donation_' . $donation_id)) {
            wp_die(__('Ação não autorizada', 'amigopet-wp'));
        }
        
        if (!current_user_can('delete_apwp_donations')) {
            wp_die(__('Você não tem permissão para excluir doações', 'amigopet-wp'));
        }
        
        $result = wp_delete_post($donation_id, true);
        
        if (!$result) {
            wp_die(__('Erro ao excluir a doação', 'amigopet-wp'));
        }
        
        wp_redirect(admin_url('admin.php?page=apwp-donations&message=2'));
        exit;
    }
    
    public function renderTermsList(): void {
        require_once plugin_dir_path(dirname(__FILE__)) . 'views/admin/terms-list.php';
    }
    
    public function renderTermForm(): void {
        $term_id = isset($_GET['term_id']) ? intval($_GET['term_id']) : 0;
        $term = $term_id ? get_post($term_id) : null;
        
        if ($term_id && (!$term || $term->post_type !== 'apwp_term')) {
            wp_die(__('Termo não encontrado', 'amigopet-wp'));
        }
        
        require_once plugin_dir_path(dirname(__FILE__)) . 'views/admin/term-form.php';
    }
    
    public function saveTerm(): void {
        if (!isset($_POST['apwp_term_nonce']) || !wp_verify_nonce($_POST['apwp_term_nonce'], 'apwp_save_term')) {
            wp_die(__('Ação não autorizada', 'amigopet-wp'));
        }
        
        if (!current_user_can('edit_apwp_terms')) {
            wp_die(__('Você não tem permissão para editar termos', 'amigopet-wp'));
        }
        
        $term_id = isset($_POST['term_id']) ? intval($_POST['term_id']) : 0;
        
        $term_data = array(
            'post_title'    => sanitize_text_field($_POST['term_title']),
            'post_content'  => wp_kses_post($_POST['term_content']),
            'post_type'     => 'apwp_term',
            'post_status'   => 'publish'
        );
        
        if ($term_id) {
            $term_data['ID'] = $term_id;
            $term_id = wp_update_post($term_data);
        } else {
            $term_id = wp_insert_post($term_data);
        }
        
        if (is_wp_error($term_id)) {
            wp_die($term_id->get_error_message());
        }
        
        // Atualiza os meta dados
        update_post_meta($term_id, 'term_type', sanitize_text_field($_POST['term_type']));
        update_post_meta($term_id, 'term_status', sanitize_text_field($_POST['term_status']));
        
        wp_redirect(admin_url('admin.php?page=apwp-terms&message=1'));
        exit;
    }
    
    public function deleteTerm(): void {
        if (!isset($_GET['term_id']) || !isset($_GET['_wpnonce'])) {
            wp_die(__('Parâmetros inválidos', 'amigopet-wp'));
        }
        
        $term_id = intval($_GET['term_id']);
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_term_' . $term_id)) {
            wp_die(__('Ação não autorizada', 'amigopet-wp'));
        }
        
        if (!current_user_can('delete_apwp_terms')) {
            wp_die(__('Você não tem permissão para excluir termos', 'amigopet-wp'));
        }
        
        $result = wp_delete_post($term_id, true);
        
        if (!$result) {
            wp_die(__('Erro ao excluir o termo', 'amigopet-wp'));
        }
        
        wp_redirect(admin_url('admin.php?page=apwp-terms&message=2'));
        exit;
    }
    
    public function registerMenus(): void {
        error_log('Método registerMenus chamado');
        $user = wp_get_current_user();
        error_log('User roles: ' . implode(', ', $user->roles));
        error_log('User can manage_amigopet: ' . (current_user_can('manage_amigopet') ? 'true' : 'false'));
        // Menu principal
        add_menu_page(
            __('AmigoPetWP', 'amigopet-wp'),
            __('AmigoPetWP', 'amigopet-wp'),
            'manage_amigopet',
            'amigopet-wp',
            [$this, 'renderDashboard'],
            plugin_dir_url(__DIR__) . 'assets/images/menu-icon.png',
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
            __('Pets', 'amigopet-wp'),
            __('Pets', 'amigopet-wp'),
            'manage_amigopet_pets',
            'amigopet-wp-pets',
            [$this, 'renderPets']
        );
        
        add_submenu_page(
            'amigopet-wp',
            __('Adoções', 'amigopet-wp'),
            __('Adoções', 'amigopet-wp'),
            'manage_amigopet_adoptions',
            'amigopet-wp-adoptions',
            [$this, 'renderAdoptions']
        );
        
        add_submenu_page(
            'amigopet-wp',
            __('Eventos', 'amigopet-wp'),
            __('Eventos', 'amigopet-wp'),
            'manage_amigopet_events',
            'amigopet-wp-events',
            [$this, 'renderEvents']
        );
        
        add_submenu_page(
            'amigopet-wp',
            __('Voluntários', 'amigopet-wp'),
            __('Voluntários', 'amigopet-wp'),
            'manage_amigopet_volunteers',
            'amigopet-wp-volunteers',
            [$this, 'renderVolunteers']
        );
        
        add_submenu_page(
            'amigopet-wp',
            __('Doações', 'amigopet-wp'),
            __('Doações', 'amigopet-wp'),
            'manage_amigopet_donations',
            'amigopet-wp-donations',
            [$this, 'renderDonations']
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
