<?php
/**
 * Classe para gerenciar atividades pendentes
 */
class APWP_Pending_Activities {
    
    /**
     * Inicializa os hooks necessários
     */
    public function __construct() {
        add_action('wp_ajax_apwp_get_pending_activities', array($this, 'get_pending_activities'));
        add_action('wp_ajax_apwp_handle_adoption_action', array($this, 'handle_adoption_action'));
        add_action('wp_ajax_apwp_handle_verification_review', array($this, 'handle_verification_review'));
        add_action('wp_ajax_apwp_handle_followup_complete', array($this, 'handle_followup_complete'));
    }

    /**
     * Retorna todas as atividades pendentes
     */
    public function get_pending_activities() {
        check_ajax_referer('apwp_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permissão negada.', 'amigopet-wp')));
        }

        global $wpdb;
        
        // Busca adoções pendentes
        $adoptions = $wpdb->get_results("
            SELECT a.id, a.pet_id, a.adopter_id, p.name as pet_name, ad.name as adopter_name
            FROM {$wpdb->prefix}apwp_adoptions a
            LEFT JOIN {$wpdb->prefix}apwp_pets p ON a.pet_id = p.id
            LEFT JOIN {$wpdb->prefix}apwp_adopters ad ON a.adopter_id = ad.id
            WHERE a.status = 'pending'
            ORDER BY a.created_at DESC
            LIMIT 5
        ");

        // Busca verificações pendentes
        $verifications = $wpdb->get_results("
            SELECT v.id, v.type, v.adopter_id, v.status, ad.name as adopter_name,
                   CASE 
                       WHEN v.type = 'document' THEN CONCAT('Verificar documentos de ', ad.name)
                       WHEN v.type = 'home' THEN CONCAT('Visita domiciliar para ', ad.name)
                       ELSE CONCAT('Verificação pendente para ', ad.name)
                   END as message
            FROM {$wpdb->prefix}apwp_verifications v
            LEFT JOIN {$wpdb->prefix}apwp_adopters ad ON v.adopter_id = ad.id
            WHERE v.status = 'pending'
            ORDER BY v.created_at DESC
            LIMIT 5
        ");

        // Busca acompanhamentos pendentes
        $followups = $wpdb->get_results("
            SELECT f.id, f.adoption_id, f.scheduled_date, 
                   CONCAT('Acompanhamento de ', p.name, ' com ', ad.name, ' em ', DATE_FORMAT(f.scheduled_date, '%d/%m/%Y')) as message
            FROM {$wpdb->prefix}apwp_followups f
            LEFT JOIN {$wpdb->prefix}apwp_adoptions a ON f.adoption_id = a.id
            LEFT JOIN {$wpdb->prefix}apwp_pets p ON a.pet_id = p.id
            LEFT JOIN {$wpdb->prefix}apwp_adopters ad ON a.adopter_id = ad.id
            WHERE f.status = 'pending'
            AND f.scheduled_date <= DATE_ADD(NOW(), INTERVAL 7 DAY)
            ORDER BY f.scheduled_date ASC
            LIMIT 5
        ");

        wp_send_json_success(array(
            'adoptions' => $adoptions,
            'verifications' => $verifications,
            'followups' => $followups
        ));
    }

    /**
     * Processa ações de adoção (aprovar/rejeitar)
     */
    public function handle_adoption_action() {
        check_ajax_referer('apwp_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permissão negada.', 'amigopet-wp')));
        }

        $adoption_id = intval($_POST['adoption_id']);
        $action = sanitize_text_field($_POST['adoption_action']);

        if (!in_array($action, array('approve', 'reject'))) {
            wp_send_json_error(array('message' => __('Ação inválida.', 'amigopet-wp')));
        }

        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'apwp_adoptions',
            array(
                'status' => $action === 'approve' ? 'approved' : 'rejected',
                'updated_at' => current_time('mysql')
            ),
            array('id' => $adoption_id),
            array('%s', '%s'),
            array('%d')
        );

        if ($result === false) {
            wp_send_json_error(array('message' => __('Erro ao processar a adoção.', 'amigopet-wp')));
        }

        wp_send_json_success();
    }

    /**
     * Processa revisão de verificação
     */
    public function handle_verification_review() {
        check_ajax_referer('apwp_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permissão negada.', 'amigopet-wp')));
        }

        $verification_id = intval($_POST['verification_id']);

        global $wpdb;
        $verification = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}apwp_verifications WHERE id = %d",
            $verification_id
        ));

        if (!$verification) {
            wp_send_json_error(array('message' => __('Verificação não encontrada.', 'amigopet-wp')));
        }

        // Aqui você pode adicionar lógica adicional para redirecionar para a página
        // de revisão ou mostrar um modal com os detalhes da verificação

        wp_send_json_success(array(
            'verification' => $verification
        ));
    }

    /**
     * Marca um acompanhamento como concluído
     */
    public function handle_followup_complete() {
        check_ajax_referer('apwp_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permissão negada.', 'amigopet-wp')));
        }

        $followup_id = intval($_POST['followup_id']);

        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'apwp_followups',
            array(
                'status' => 'completed',
                'completed_at' => current_time('mysql')
            ),
            array('id' => $followup_id),
            array('%s', '%s'),
            array('%d')
        );

        if ($result === false) {
            wp_send_json_error(array('message' => __('Erro ao completar o acompanhamento.', 'amigopet-wp')));
        }

        wp_send_json_success();
    }
}

// Inicializa a classe
new APWP_Pending_Activities();
