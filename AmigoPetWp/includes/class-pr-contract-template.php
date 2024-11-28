<?php

/**
 * Classe responsável pelo gerenciamento de templates de contrato personalizados
 *
 * @since      1.0.0
 * @package    AmigoPetWp
 * @subpackage AmigoPetWp/includes
 */
class APWP_Contract_Template {

    /**
     * Lista de palavras mágicas obrigatórias no contrato
     *
     * @since    1.0.0
     * @var      array
     */
    private $magic_words = [
        // Termos legais
        'adoção',
        'responsabilidade',
        'compromisso',
        'bem-estar',
        'animal',
        
        // Obrigações
        'cuidar',
        'alimentação',
        'saúde',
        'veterinário',
        
        // Restrições
        'não abandonar',
        'proibido',
        'multa',
        
        // Direitos
        'direito',
        'garantia',
        
        // Identificação
        'nome completo',
        'documento',
        'identificação',
        
        // Termos específicos de adoção
        'termo de adoção',
        'guarda responsável',
        'adotante',
        'doador',
        
        // Aspectos éticos
        'proteção animal',
        'bem-estar animal',
        
        // Aspectos legais
        'declaração',
        'concordância',
        'obrigação legal'
    ];

    /**
     * Lista de tags dinâmicas para substituição no contrato
     *
     * @since    1.0.0
     * @var      array
     */
    private $dynamic_tags = [
        // Dados do Adotante
        '[[NOME_ADOTANTE]]',
        '[[CPF_ADOTANTE]]',
        '[[RG_ADOTANTE]]',
        '[[ENDERECO_ADOTANTE]]',
        '[[TELEFONE_ADOTANTE]]',
        '[[EMAIL_ADOTANTE]]',

        // Dados do Animal
        '[[NOME_ANIMAL]]',
        '[[ESPECIE_ANIMAL]]',
        '[[RACA_ANIMAL]]',
        '[[IDADE_ANIMAL]]',
        '[[SEXO_ANIMAL]]',

        // Dados da Organização
        '[[NOME_ORGANIZACAO]]',
        '[[CNPJ_ORGANIZACAO]]',
        '[[ENDERECO_ORGANIZACAO]]',

        // Dados do Contrato
        '[[DATA_CONTRATO]]',
        '[[NUMERO_CONTRATO]]'
    ];

    /**
     * Valida o documento verificando a presença de palavras mágicas
     *
     * @since    1.0.0
     * @param    string   $content    Conteúdo do documento
     * @return   array               Resultado da validação
     */
    public function validate_document($content) {
        // Converte o conteúdo para minúsculas para comparação
        $content_lower = mb_strtolower($content, 'UTF-8');
        
        // Palavras ausentes
        $missing_words = [];
        
        // Verifica cada palavra mágica
        foreach ($this->magic_words as $word) {
            // Usa mb_strpos para suporte a caracteres especiais
            if (mb_strpos($content_lower, mb_strtolower($word, 'UTF-8')) === false) {
                $missing_words[] = $word;
            }
        }
        
        return [
            'is_valid' => empty($missing_words),
            'missing_words' => $missing_words,
            'required_word_count' => count($this->magic_words),
            'found_word_count' => count($this->magic_words) - count($missing_words)
        ];
    }

    /**
     * Valida a presença de tags dinâmicas no documento
     *
     * @since    1.0.0
     * @param    string   $content    Conteúdo do documento
     * @return   array               Resultado da validação
     */
    public function validate_dynamic_tags($content) {
        $missing_tags = [];
        
        foreach ($this->dynamic_tags as $tag) {
            if (strpos($content, $tag) === false) {
                $missing_tags[] = $tag;
            }
        }
        
        return [
            'is_valid' => empty($missing_tags),
            'missing_tags' => $missing_tags,
            'total_tags' => count($this->dynamic_tags),
            'found_tags' => count($this->dynamic_tags) - count($missing_tags)
        ];
    }

    /**
     * Upload de template com validação de palavras mágicas
     *
     * @since    1.0.0
     * @param    array    $file    Arquivo enviado
     * @return   string|WP_Error   Caminho do arquivo ou erro
     */
    public function upload_template($file) {
        // Verifica se o arquivo foi enviado corretamente
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        // Configurações de upload
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($file, $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            // Lê o conteúdo do arquivo
            $file_content = '';
            $file_extension = strtolower(pathinfo($movefile['file'], PATHINFO_EXTENSION));

            // Extrai texto de diferentes tipos de arquivo
            switch ($file_extension) {
                case 'pdf':
                    $file_content = $this->extract_text_from_pdf($movefile['file']);
                    break;
                case 'docx':
                    $file_content = $this->extract_text_from_docx($movefile['file']);
                    break;
                case 'doc':
                    $file_content = $this->extract_text_from_doc($movefile['file']);
                    break;
                case 'txt':
                    $file_content = file_get_contents($movefile['file']);
                    break;
                default:
                    return new WP_Error('invalid_file_type', 'Tipo de arquivo não suportado');
            }

            // Valida o documento
            $validation = $this->validate_document($file_content);

            if ($validation['is_valid']) {
                // Salva o caminho do arquivo no banco de dados
                update_option('amigopet_contract_template', $movefile['file']);
                return $movefile['file'];
            } else {
                // Remove o arquivo se não passar na validação
                unlink($movefile['file']);
                return new WP_Error('invalid_document', 'O documento não contém todas as palavras necessárias', [
                    'missing_words' => $validation['missing_words']
                ]);
            }
        }

        return new WP_Error('upload_error', $movefile['error']);
    }

    /**
     * Substitui tags dinâmicas no documento
     *
     * @since    1.0.0
     * @param    string   $content    Conteúdo do documento
     * @param    array    $data       Dados para substituição
     * @return   string               Documento com tags substituídas
     */
    public function replace_dynamic_tags($content, $data) {
        // Dados do Adotante
        $content = str_replace('[[NOME_ADOTANTE]]', $data['adopter']->name ?? '', $content);
        $content = str_replace('[[CPF_ADOTANTE]]', $data['adopter']->cpf ?? '', $content);
        $content = str_replace('[[RG_ADOTANTE]]', $data['adopter']->rg ?? '', $content);
        $content = str_replace('[[ENDERECO_ADOTANTE]]', $data['adopter']->address ?? '', $content);
        $content = str_replace('[[TELEFONE_ADOTANTE]]', $data['adopter']->phone ?? '', $content);
        $content = str_replace('[[EMAIL_ADOTANTE]]', $data['adopter']->email ?? '', $content);

        // Dados do Animal
        $content = str_replace('[[NOME_ANIMAL]]', $data['animal']->name ?? '', $content);
        $content = str_replace('[[ESPECIE_ANIMAL]]', $data['animal']->species ?? '', $content);
        $content = str_replace('[[RACA_ANIMAL]]', $data['animal']->breed ?? '', $content);
        $content = str_replace('[[IDADE_ANIMAL]]', $data['animal']->age ?? '', $content);
        $content = str_replace('[[SEXO_ANIMAL]]', $data['animal']->gender ?? '', $content);

        // Dados da Organização
        $content = str_replace('[[NOME_ORGANIZACAO]]', $data['organization']->name ?? '', $content);
        $content = str_replace('[[CNPJ_ORGANIZACAO]]', $data['organization']->cnpj ?? '', $content);
        $content = str_replace('[[ENDERECO_ORGANIZACAO]]', $data['organization']->address ?? '', $content);

        // Dados do Contrato
        $content = str_replace('[[DATA_CONTRATO]]', $data['date'] ?? date('d/m/Y'), $content);
        $content = str_replace('[[NUMERO_CONTRATO]]', $data['contract_number'] ?? '', $content);

        return $content;
    }

    /**
     * Extrai texto de PDF
     *
     * @since    1.0.0
     * @param    string   $file_path   Caminho do arquivo PDF
     * @return   string               Texto extraído
     */
    private function extract_text_from_pdf($file_path) {
        $pdf_parser_paths = [
            // Caminho do vendor dentro do plugin
            plugin_dir_path(dirname(__FILE__)) . '../vendor/smalot/pdfparser/src/Smalot/PdfParser/Parser.php',
            
            // Caminho padrão do Composer
            plugin_dir_path(dirname(__FILE__)) . '../../../../vendor/smalot/pdfparser/src/Smalot/PdfParser/Parser.php'
        ];

        $parser_path = null;
        foreach ($pdf_parser_paths as $path) {
            if (file_exists($path)) {
                $parser_path = $path;
                break;
            }
        }

        if (!$parser_path) {
            return new WP_Error('pdf_parser_not_found', 'Biblioteca de parse de PDF não encontrada');
        }

        require_once $parser_path;

        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($file_path);
        return $pdf->getText();
    }

    /**
     * Extrai texto de DOCX
     *
     * @since    1.0.0
     * @param    string   $file_path   Caminho do arquivo DOCX
     * @return   string               Texto extraído
     */
    private function extract_text_from_docx($file_path) {
        $phpword_paths = [
            // Caminho do vendor dentro do plugin
            plugin_dir_path(dirname(__FILE__)) . '../vendor/phpoffice/phpword/bootstrap.php',
            
            // Caminho padrão do Composer
            plugin_dir_path(dirname(__FILE__)) . '../../../../vendor/phpoffice/phpword/bootstrap.php'
        ];

        $phpword_path = null;
        foreach ($phpword_paths as $path) {
            if (file_exists($path)) {
                $phpword_path = $path;
                break;
            }
        }

        if (!$phpword_path) {
            return new WP_Error('phpword_not_found', 'Biblioteca PhpWord não encontrada');
        }

        require_once $phpword_path;

        $phpWord = \PhpOffice\PhpWord\IOFactory::load($file_path);
        $text = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    $text .= $element->getText();
                }
            }
        }
        return $text;
    }

    /**
     * Extrai texto de DOC (legado)
     *
     * @since    1.0.0
     * @param    string   $file_path   Caminho do arquivo DOC
     * @return   string               Texto extraído
     */
    private function extract_text_from_doc($file_path) {
        // Usa comando system para extrair texto
        $text = shell_exec("antiword " . escapeshellarg($file_path));
        return $text ?? '';
    }

    /**
     * Processa o upload de um novo template de contrato
     *
     * @since    1.0.0
     * @param    array    $file    Arquivo enviado via upload
     * @return   string|WP_Error   Caminho do arquivo salvo ou objeto de erro
     */
    public function upload_template_original($file) {
        // Verifica se o arquivo foi enviado corretamente
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        // Configurações de upload
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($file, $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            // Salva o caminho do arquivo no banco de dados
            update_option('amigopet_contract_template', $movefile['file']);
            return $movefile['file'];
        }

        return new WP_Error('upload_error', $movefile['error']);
    }

    /**
     * Substitui tags no template de contrato
     *
     * @since    1.0.0
     * @param    string   $template_path   Caminho do arquivo de template
     * @param    array    $data            Dados para substituição
     * @return   string                    Conteúdo do template com tags substituídas
     */
    public function replace_tags($template_path, $data) {
        // Tags padrão para substituição
        $tags = [
            '{{ORGANIZACAO_NOME}}' => $data['organization']->name,
            '{{ORGANIZACAO_CNPJ}}' => $data['organization']->cnpj ?? '',
            '{{ADOTANTE_NOME}}' => $data['adopter']->name,
            '{{ADOTANTE_CPF}}' => $data['adopter']->cpf ?? '',
            '{{ANIMAL_NOME}}' => $data['animal']->name,
            '{{ANIMAL_ESPECIE}}' => $data['animal']->species ?? '',
            '{{ANIMAL_RACA}}' => $data['animal']->breed ?? '',
            '{{DATA_ATUAL}}' => $data['date'],
        ];

        // Lê o conteúdo do arquivo
        $template_content = file_get_contents($template_path);

        // Substitui as tags
        foreach ($tags as $tag => $replacement) {
            $template_content = str_replace($tag, $replacement, $template_content);
        }

        return $template_content;
    }

    /**
     * Converte documento para PDF com dados substituídos
     *
     * @since    1.0.0
     * @param    string   $content    Conteúdo do documento
     * @param    array    $data       Dados do contrato
     * @return   string               Caminho do PDF gerado
     */
    public function convert_to_pdf($content, $data) {
        if (!class_exists('TCPDF')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/tecnickcom/tcpdf/tcpdf.php';
        }

        // Cria o PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($data['organization']->name);
        $pdf->SetTitle('Contrato de Adoção');
        $pdf->AddPage();

        // Adiciona o conteúdo ao PDF
        $pdf->writeHTML($content, true, false, true, false, '');

        // Define o diretório para salvar os contratos
        $upload_dir = wp_upload_dir();
        $contracts_dir = $upload_dir['basedir'] . '/pr-contracts';
        
        if (!file_exists($contracts_dir)) {
            wp_mkdir_p($contracts_dir);
        }

        // Nome do arquivo
        $filename = sprintf(
            'contrato-adocao-%s-%s-%s.pdf',
            sanitize_title($data['animal']->name),
            sanitize_title($data['adopter']->name),
            date('Y-m-d-His')
        );

        $filepath = $contracts_dir . '/' . $filename;

        // Salva o PDF
        $pdf->Output($filepath, 'F');

        return $filepath;
    }

    /**
     * Gera contrato com template personalizado
     *
     * @since    1.0.0
     * @param    array    $data    Dados do contrato
     * @return   string            Caminho do PDF gerado
     */
    public function generate_custom_contract($data) {
        // Recupera o template salvo
        $template_path = get_option('amigopet_contract_template');

        if (!$template_path || !file_exists($template_path)) {
            // Usa o template padrão se nenhum for encontrado
            return (new APWP_Contract())->generate($data);
        }

        // Valida tags dinâmicas
        $tag_validation = $this->validate_dynamic_tags(file_get_contents($template_path));

        if (!$tag_validation['is_valid']) {
            return new WP_Error('invalid_template', 'O modelo de contrato não contém todas as tags necessárias', [
                'missing_tags' => $tag_validation['missing_tags']
            ]);
        }

        // Lê o conteúdo do arquivo
        $template_content = file_get_contents($template_path);

        // Substitui tags dinâmicas
        $replaced_content = $this->replace_dynamic_tags($template_content, $data);

        // Converte para PDF
        return $this->convert_to_pdf($replaced_content, $data);
    }
}
