<?php

/**
 * Classe responsável pelo gerenciamento de contratos de adoção.
 *
 * @since      1.0.0
 * @package    AmigoPetWp
 * @subpackage AmigoPetWp/includes
 */
class APWP_Contract {

    /**
     * ID do contrato no banco de dados.
     *
     * @since    1.0.0
     * @access   private
     * @var      integer    $id    ID do contrato.
     */
    private $id;

    /**
     * Construtor.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Inicialização
    }

    /**
     * Gera um contrato de adoção em PDF
     *
     * @param    array    $data    Dados do contrato (pet_id, adopter_id, organization_id).
     * @return   array    Array com o caminho do arquivo PDF e a URL
     */
    public function generate($data) {
        // Obtém os dados necessários
        $pet = new APWP_Pet();
        $pet_data = $pet->get($data['pet_id']);
        
        $adopter = new APWP_Adopter();
        $adopter_data = $adopter->get($data['adopter_id']);
        
        $org = new APWP_Organization();
        $org_data = $org->get($data['organization_id']);

        // Verifica se todos os dados existem
        if (!$pet_data || !$adopter_data || !$org_data) {
            return false;
        }

        // Inicializa o TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Define informações do documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($org_data->name);
        $pdf->SetTitle('Contrato de Adoção - ' . $pet_data->name);

        // Prepara os dados para o template
        $template_data = array(
            'date' => current_time('d/m/Y'),
            'pet' => $pet_data,
            'adopter' => $adopter_data,
            'organization' => $org_data
        );

        // Gera o nome do arquivo
        $upload_dir = wp_upload_dir();
        $filename = sprintf(
            'contrato-adocao-%s-%s-%s.pdf',
            $data['pet_id'],
            sanitize_title($pet_data->name),
            date('Y-m-d-H-i-s')
        );

        // Define o caminho completo do arquivo
        $filepath = $upload_dir['path'] . '/' . $filename;
        $fileurl = $upload_dir['url'] . '/' . $filename;

        // Gera o PDF
        $template = new APWP_Contract_Template();
        $content = $template->render($template_data);

        $pdf->AddPage();
        $pdf->writeHTML($content, true, false, true, false, '');
        $pdf->Output($filepath, 'F');

        // Salva o registro do contrato no banco
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_contracts';
        
        $wpdb->insert(
            $table_name,
            array(
                'pet_id' => absint($data['pet_id']),
                'adopter_id' => absint($data['adopter_id']),
                'organization_id' => absint($data['organization_id']),
                'filepath' => $filepath,
                'fileurl' => $fileurl,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%d', '%d', '%s', '%s', '%s', '%s')
        );

        return array(
            'path' => $filepath,
            'url' => $fileurl
        );
    }

    /**
     * Salva os dados do contrato no banco de dados.
     *
     * @since    1.0.0
     * @param    array    $data    Dados do contrato.
     * @param    string   $uid     UID do contrato.
     * @return   integer|WP_Error  ID do contrato inserido ou objeto de erro.
     */
    public function save($data, $uid) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_contracts';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'pet_id' => absint($data['pet_id']),
                'adopter_id' => absint($data['adopter_id']),
                'organization_id' => absint($data['organization_id']),
                'uid' => $uid,
                'status' => 'active',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%d', '%d', '%s', '%s', '%s', '%s')
        );

        if ($result === false) {
            return new WP_Error('db_insert_error', 'Não foi possível salvar o contrato.');
        }

        return $wpdb->insert_id;
    }

    /**
     * Obtém o template do contrato.
     *
     * @since    1.0.0
     * @param    array    $data    Dados para o template.
     * @return   string            HTML do contrato.
     */
    private function get_contract_template($data) {
        ob_start();
        ?>
        <h1 style="text-align: center;">CONTRATO DE ADOÇÃO DE PET</h1>
        
        <p><strong>ORGANIZAÇÃO:</strong> <?php echo esc_html($data['organization']->name); ?></p>
        <p><strong>ADOTANTE:</strong> <?php echo esc_html($data['adopter']->name); ?></p>
        <p><strong>PET:</strong> <?php echo esc_html($data['pet']->name); ?></p>
        <p><strong>DATA:</strong> <?php echo esc_html($data['date']); ?></p>

        <h2>TERMOS E CONDIÇÕES</h2>

        <p>1. O ADOTANTE se compromete a:</p>
        <ul>
            <li>Proporcionar boas condições de alojamento e alimentação ao pet;</li>
            <li>Levar o pet ao veterinário sempre que necessário;</li>
            <li>Não abandonar o pet em hipótese alguma;</li>
            <li>Informar à ORGANIZAÇÃO qualquer mudança significativa na situação do pet.</li>
        </ul>

        <p>2. A ORGANIZAÇÃO se compromete a:</p>
        <ul>
            <li>Fornecer todas as informações conhecidas sobre o pet;</li>
            <li>Estar disponível para orientações sobre os cuidados com o pet;</li>
            <li>Realizar visitas de acompanhamento quando necessário.</li>
        </ul>

        <p style="margin-top: 50px;">
        _____________________________________________<br>
        Assinatura do Adotante
        </p>

        <p>
        _____________________________________________<br>
        Assinatura do Representante da Organização
        </p>
        <?php
        return ob_get_clean();
    }

    /**
     * Obtém um contrato pelo ID.
     *
     * @since    1.0.0
     * @param    integer    $id    ID do contrato.
     * @return   object|null       Dados do contrato ou null se não encontrado.
     */
    public function get($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_contracts';
        
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $id
            )
        );
    }

    /**
     * Lista todos os contratos.
     *
     * @since    1.0.0
     * @param    array    $args    Argumentos de filtragem.
     * @return   array             Lista de contratos.
     */
    public function list($args = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_contracts';
        
        $query = "SELECT * FROM $table_name";
        
        if (!empty($args['status'])) {
            $query .= $wpdb->prepare(" WHERE status = %s", $args['status']);
        }
        
        $query .= " ORDER BY created_at DESC";
        
        return $wpdb->get_results($query);
    }
}
