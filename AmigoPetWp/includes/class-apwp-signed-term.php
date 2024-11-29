<?php
/**
 * Classe para gerenciar termos assinados
 */
class APWP_Signed_Term {
    private $id;
    private $template_id;
    private $user_id;
    private $content;
    private $signature;
    private $ip_address;
    private $signed_at;
    private $status;
    private $created_at;
    private $updated_at;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->set_data($data);
        }
    }

    private function set_data($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Lista os termos assinados
     */
    public function list($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'template_id' => 0,
            'user_id' => 0,
            'status' => 'active',
            'orderby' => 'signed_at',
            'order' => 'DESC',
            'per_page' => 20,
            'offset' => 0,
            'search' => ''
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array('1=1');
        $values = array();
        
        if (!empty($args['template_id'])) {
            $where[] = 'st.template_id = %d';
            $values[] = $args['template_id'];
        }
        
        if (!empty($args['user_id'])) {
            $where[] = 'st.user_id = %d';
            $values[] = $args['user_id'];
        }
        
        if (!empty($args['status'])) {
            $where[] = 'st.status = %s';
            $values[] = $args['status'];
        }
        
        if (!empty($args['search'])) {
            $where[] = '(u.display_name LIKE %s OR u.user_email LIKE %s)';
            $values[] = '%' . $wpdb->esc_like($args['search']) . '%';
            $values[] = '%' . $wpdb->esc_like($args['search']) . '%';
        }
        
        $sql = sprintf(
            "SELECT st.*, tt.title as template_title, tt.type_id, 
                    u.display_name as user_name, u.user_email,
                    tty.name as term_type_name
             FROM {$wpdb->prefix}apwp_signed_terms st
             LEFT JOIN {$wpdb->prefix}apwp_term_templates tt ON st.template_id = tt.id
             LEFT JOIN {$wpdb->prefix}apwp_term_types tty ON tt.type_id = tty.id
             LEFT JOIN {$wpdb->users} u ON st.user_id = u.ID
             WHERE %s
             ORDER BY %s %s
             LIMIT %d OFFSET %d",
            implode(' AND ', $where),
            esc_sql($args['orderby']),
            esc_sql($args['order']),
            (int) $args['per_page'],
            (int) $args['offset']
        );
        
        if (!empty($values)) {
            $sql = $wpdb->prepare($sql, $values);
        }
        
        return $wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * Obtém um termo assinado pelo ID
     */
    public function get($id) {
        global $wpdb;
        
        $sql = $wpdb->prepare(
            "SELECT st.*, tt.title as template_title, tt.type_id,
                    u.display_name as user_name, u.user_email,
                    tty.name as term_type_name
             FROM {$wpdb->prefix}apwp_signed_terms st
             LEFT JOIN {$wpdb->prefix}apwp_term_templates tt ON st.template_id = tt.id
             LEFT JOIN {$wpdb->prefix}apwp_term_types tty ON tt.type_id = tty.id
             LEFT JOIN {$wpdb->users} u ON st.user_id = u.ID
             WHERE st.id = %d",
            $id
        );
        
        $result = $wpdb->get_row($sql, ARRAY_A);
        
        if ($result) {
            $this->set_data($result);
            return $result;
        }
        
        return false;
    }

    /**
     * Salva o termo assinado
     */
    public function save() {
        global $wpdb;
        
        $data = array(
            'template_id' => $this->template_id,
            'user_id' => $this->user_id,
            'content' => $this->content,
            'signature' => $this->signature,
            'ip_address' => $this->ip_address,
            'signed_at' => current_time('mysql'),
            'status' => $this->status
        );
        
        $format = array('%d', '%d', '%s', '%s', '%s', '%s', '%s');
        
        if ($this->id) {
            $result = $wpdb->update(
                $wpdb->prefix . 'apwp_signed_terms',
                $data,
                array('id' => $this->id),
                $format,
                array('%d')
            );
            
            return $result !== false ? $this->id : false;
        } else {
            $result = $wpdb->insert(
                $wpdb->prefix . 'apwp_signed_terms',
                $data,
                $format
            );
            
            return $result ? $wpdb->insert_id : false;
        }
    }

    /**
     * Verifica se um usuário já assinou um termo específico
     */
    public function has_user_signed($user_id, $template_id) {
        global $wpdb;
        
        $sql = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}apwp_signed_terms
             WHERE user_id = %d AND template_id = %d AND status = 'active'",
            $user_id,
            $template_id
        );
        
        return (int) $wpdb->get_var($sql) > 0;
    }

    /**
     * Gera um PDF do termo assinado
     */
    public function generate_pdf() {
        if (!class_exists('TCPDF')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/lib/tcpdf/tcpdf.php';
        }
        
        // Cria nova instância do TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Define informações do documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(get_bloginfo('name'));
        $pdf->SetTitle($this->template_title . ' - ' . $this->user_name);
        
        // Remove cabeçalho e rodapé padrão
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Adiciona uma página
        $pdf->AddPage();
        
        // Define fonte
        $pdf->SetFont('helvetica', '', 12);
        
        // Adiciona conteúdo
        $pdf->writeHTML($this->content, true, false, true, false, '');
        
        // Adiciona assinatura
        if (!empty($this->signature)) {
            $pdf->Ln(10);
            $pdf->Cell(0, 10, 'Assinado por: ' . $this->user_name, 0, 1);
            $pdf->Cell(0, 10, 'Data: ' . date_i18n(get_option('date_format'), strtotime($this->signed_at)), 0, 1);
            $pdf->Cell(0, 10, 'IP: ' . $this->ip_address, 0, 1);
        }
        
        return $pdf->Output('', 'S');
    }

    /**
     * Envia o termo por email
     */
    public function send_email() {
        $user = get_userdata($this->user_id);
        if (!$user) {
            return false;
        }
        
        $pdf = $this->generate_pdf();
        
        $to = $user->user_email;
        $subject = sprintf(
            __('Seu termo assinado: %s', 'amigopet-wp'),
            $this->template_title
        );
        
        $message = sprintf(
            __('Olá %s,

Anexo está seu termo assinado: %s

Este é um email automático, por favor não responda.

Atenciosamente,
%s', 'amigopet-wp'),
            $user->display_name,
            $this->template_title,
            get_bloginfo('name')
        );
        
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        // Anexa o PDF
        $attachments = array(
            array(
                'name' => sanitize_file_name($this->template_title . '.pdf'),
                'type' => 'application/pdf',
                'content' => $pdf
            )
        );
        
        return wp_mail($to, $subject, $message, $headers, $attachments);
    }

    /**
     * Obtém estatísticas dos termos assinados
     */
    public static function get_stats($type_id = 0) {
        global $wpdb;
        
        $where = array('1=1');
        $values = array();
        
        if ($type_id) {
            $where[] = 'tt.type_id = %d';
            $values[] = $type_id;
        }
        
        $sql = sprintf(
            "SELECT COUNT(*) as total,
                    SUM(CASE WHEN st.status = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN st.status = 'inactive' THEN 1 ELSE 0 END) as inactive,
                    DATE(st.signed_at) as date
             FROM {$wpdb->prefix}apwp_signed_terms st
             LEFT JOIN {$wpdb->prefix}apwp_term_templates tt ON st.template_id = tt.id
             WHERE %s
             GROUP BY DATE(st.signed_at)
             ORDER BY date DESC
             LIMIT 30",
            implode(' AND ', $where)
        );
        
        if (!empty($values)) {
            $sql = $wpdb->prepare($sql, $values);
        }
        
        return $wpdb->get_results($sql, ARRAY_A);
    }
}
