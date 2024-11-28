<?php

/**
 * Classe responsável pelo gerenciamento de QR Codes.
 *
 * @since      1.0.0
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 */
class APWP_QRCode {

    /**
     * URL base para verificação do QR Code.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $verification_url    URL base para verificação.
     */
    private $verification_url;

    /**
     * Construtor.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->verification_url = home_url('/verificar-adocao/');
    }

    /**
     * Gera um QR Code com os dados fornecidos.
     *
     * @since    1.0.0
     * @param    array     $data    Dados para gerar o QR Code.
     * @return   string             String codificada para o QR Code.
     */
    public function generate($data) {
        // Gera um token único para o QR Code
        $token = $this->generate_token($data);
        
        // Salva o token e os dados no banco de dados
        $this->save_token($token, $data);
        
        // Retorna a URL completa para verificação
        return $this->verification_url . '?token=' . $token;
    }

    /**
     * Gera um token único para o QR Code.
     *
     * @since    1.0.0
     * @param    array     $data    Dados para gerar o token.
     * @return   string             Token gerado.
     */
    private function generate_token($data) {
        $string = json_encode($data) . time() . wp_rand();
        return hash('sha256', $string);
    }

    /**
     * Salva o token e os dados associados no banco de dados.
     *
     * @since    1.0.0
     * @param    string    $token    Token gerado.
     * @param    array     $data     Dados associados ao token.
     * @return   boolean             True em caso de sucesso.
     */
    private function save_token($token, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_qrcodes';
        
        return $wpdb->insert(
            $table_name,
            array(
                'token' => $token,
                'contract_id' => absint($data['contract_id']),
                'animal_id' => absint($data['animal_id']),
                'adopter_id' => absint($data['adopter_id']),
                'created_at' => current_time('mysql')
            ),
            array('%s', '%d', '%d', '%d', '%s')
        );
    }

    /**
     * Verifica um token de QR Code.
     *
     * @since    1.0.0
     * @param    string          $token    Token a ser verificado.
     * @return   object|WP_Error          Dados do QR Code ou erro se inválido.
     */
    public function verify($token) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_qrcodes';
        
        $qr_data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE token = %s",
                $token
            )
        );

        if (!$qr_data) {
            return new WP_Error('invalid_token', 'QR Code inválido ou expirado.');
        }

        // Obtém os dados relacionados
        $contract = new APWP_Contract();
        $contract_data = $contract->get($qr_data->contract_id);

        $animal = new APWP_Animal();
        $animal_data = $animal->get($qr_data->animal_id);

        $adopter = new APWP_Adopter();
        $adopter_data = $adopter->get($qr_data->adopter_id);

        return (object) array(
            'contract' => $contract_data,
            'animal' => $animal_data,
            'adopter' => $adopter_data,
            'created_at' => $qr_data->created_at
        );
    }

    /**
     * Cria uma página de verificação de QR Code.
     *
     * @since    1.0.0
     * @return   void
     */
    public function create_verification_page() {
        // Verifica se a página já existe
        $page = get_page_by_path('verificar-adocao');
        
        if (!$page) {
            // Cria a página
            wp_insert_post(array(
                'post_title' => 'Verificar Adoção',
                'post_name' => 'verificar-adocao',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[apwp_verificar_adocao]' // Shortcode que será processado
            ));
        }
    }

    /**
     * Renderiza a página de verificação.
     *
     * @since    1.0.0
     * @return   string    HTML da página de verificação.
     */
    public function render_verification_page() {
        $token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
        
        if (empty($token)) {
            return '<p>Nenhum QR Code fornecido para verificação.</p>';
        }

        $result = $this->verify($token);
        
        if (is_wp_error($result)) {
            return '<p class="error">' . esc_html($result->get_error_message()) . '</p>';
        }

        ob_start();
        ?>
        <div class="apwp-verification-result">
            <h2>Informações da Adoção</h2>
            
            <div class="apwp-verification-data">
                <p><strong>Animal:</strong> <?php echo esc_html($result->animal->name); ?></p>
                <p><strong>Adotante:</strong> <?php echo esc_html($result->adopter->name); ?></p>
                <p><strong>Data da Adoção:</strong> <?php echo esc_html(date('d/m/Y', strtotime($result->created_at))); ?></p>
                <p><strong>Status do Contrato:</strong> <?php echo esc_html($result->contract->status); ?></p>
            </div>

            <?php if ($result->contract->status === 'active'): ?>
                <p class="apwp-verification-valid">✓ Esta é uma adoção válida e ativa.</p>
            <?php else: ?>
                <p class="apwp-verification-invalid">⚠ Este contrato de adoção não está mais ativo.</p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
