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
     * Gera um novo contrato de adoção.
     *
     * @since    1.0.0
     * @param    array    $data    Dados do contrato (animal_id, adopter_id, organization_id).
     * @return   string|WP_Error   Caminho do arquivo PDF gerado ou objeto de erro.
     */
    public function generate($data) {
        if (!class_exists('TCPDF')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/tecnickcom/tcpdf/tcpdf.php';
        }

        // Obtém os dados necessários
        $animal = new APWP_Animal();
        $animal_data = $animal->get($data['animal_id']);

        $adopter = new APWP_Adopter();
        $adopter_data = $adopter->get($data['adopter_id']);

        $organization = new APWP_Organization();
        $org_data = $organization->get($data['organization_id']);

        if (!$animal_data || !$adopter_data || !$org_data) {
            return new WP_Error('invalid_data', 'Dados inválidos para geração do contrato.');
        }

        // Cria o PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Configura o PDF
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($org_data->name);
        $pdf->SetTitle('Contrato de Adoção - ' . $animal_data->name);

        // Adiciona uma página
        $pdf->AddPage();

        // Conteúdo do contrato
        $html = $this->get_contract_template([
            'animal' => $animal_data,
            'adopter' => $adopter_data,
            'organization' => $org_data,
            'date' => current_time('d/m/Y')
        ]);

        // Adiciona o conteúdo ao PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Gera o QR Code com os dados do contrato
        $qr = new APWP_QRCode();
        $uid = substr(uniqid(), -8);
        $qr_data = $qr->generate([
            'contract_id' => $this->save($data, $uid),
            'animal_id' => $data['animal_id'],
            'adopter_id' => $data['adopter_id'],
            'date' => current_time('mysql')
        ]);

        // Adiciona o QR Code ao PDF
        $pdf->write2DBarcode(
            $qr_data,
            'QRCODE,H',
            160,
            240,
            40,
            40,
            ['border' => 0, 'vpadding' => 'auto', 'hpadding' => 'auto']
        );

        // Define o diretório para salvar os contratos
        $upload_dir = wp_upload_dir();
        $contracts_dir = $upload_dir['basedir'] . '/pr-contracts';
        
        if (!file_exists($contracts_dir)) {
            wp_mkdir_p($contracts_dir);
        }

        // Nome do arquivo
        $filename = sprintf(
            'contrato-adocao-%s-%s-%s-%s.pdf',
            sanitize_title($animal_data->name),
            sanitize_title($adopter_data->name),
            $uid,
            date('Y-m-d-His')
        );

        $filepath = $contracts_dir . '/' . $filename;

        // Salva o PDF
        $pdf->Output($filepath, 'F');

        return $filepath;
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
                'animal_id' => absint($data['animal_id']),
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
        <h1 style="text-align: center;">CONTRATO DE ADOÇÃO DE ANIMAL</h1>
        
        <p><strong>ORGANIZAÇÃO:</strong> <?php echo esc_html($data['organization']->name); ?></p>
        <p><strong>ADOTANTE:</strong> <?php echo esc_html($data['adopter']->name); ?></p>
        <p><strong>ANIMAL:</strong> <?php echo esc_html($data['animal']->name); ?></p>
        <p><strong>DATA:</strong> <?php echo esc_html($data['date']); ?></p>

        <h2>TERMOS E CONDIÇÕES</h2>

        <p>1. O ADOTANTE se compromete a:</p>
        <ul>
            <li>Proporcionar boas condições de alojamento e alimentação ao animal;</li>
            <li>Levar o animal ao veterinário sempre que necessário;</li>
            <li>Não abandonar o animal em hipótese alguma;</li>
            <li>Informar à ORGANIZAÇÃO qualquer mudança significativa na situação do animal.</li>
        </ul>

        <p>2. A ORGANIZAÇÃO se compromete a:</p>
        <ul>
            <li>Fornecer todas as informações conhecidas sobre o animal;</li>
            <li>Estar disponível para orientações sobre os cuidados com o animal;</li>
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
