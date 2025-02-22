<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Services\AdoptionDocumentService;
use AmigoPetWp\Domain\Services\DocumentProcessorService;

class AdminAdoptionDocumentController {
    private $documentService;
    private $processorService;

    public function __construct(
        AdoptionDocumentService $documentService,
        DocumentProcessorService $processorService
    ) {
        $this->documentService = $documentService;
        $this->processorService = $processorService;

        // Registra os menus
        add_action('admin_menu', [$this, 'registerMenus']);

        // Registra os endpoints AJAX
        add_action('wp_ajax_apwp_save_adoption_document', [$this, 'handleSaveDocument']);
        add_action('wp_ajax_apwp_delete_adoption_document', [$this, 'handleDeleteDocument']);
        add_action('wp_ajax_apwp_preview_adoption_document', [$this, 'handlePreviewDocument']);
        add_action('wp_ajax_apwp_download_adoption_document', [$this, 'handleDownloadDocument']);
    }

    /**
     * Registra os menus do admin
     */
    public function registerMenus(): void {
        add_submenu_page(
            'amigopet',
            'Documentos de Adoção',
            'Documentos de Adoção',
            'manage_adoption_documents',
            'amigopet-adoption-documents',
            [$this, 'renderDocumentsPage']
        );
    }

    /**
     * Renderiza a página principal de documentos
     */
    public function renderDocumentsPage(): void {
        // Carrega os dados necessários
        $documents = $this->documentService->getAllDocuments();
        $templates = $this->processorService->getAvailableTemplates();

        // Inclui os assets necessários
        wp_enqueue_style('apwp-admin');
        wp_enqueue_script('apwp-adoption-documents');
        wp_localize_script('apwp-adoption-documents', 'apwpAdoptionDocuments', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('apwp_adoption_documents')
        ]);

        // Renderiza o template
        include AMIGOPET_WP_PLUGIN_DIR . '/AmigoPet/Views/admin/adoption-documents/index.php';
    }

    /**
     * Manipula o salvamento de um documento
     */
    public function handleSaveDocument(): void {
        check_ajax_referer('apwp_adoption_documents');

        try {
            if (!current_user_can('manage_adoption_documents')) {
                throw new \Exception('Você não tem permissão para gerenciar documentos.');
            }

            $data = [
                'title' => sanitize_text_field($_POST['title']),
                'description' => sanitize_textarea_field($_POST['description']),
                'template' => sanitize_text_field($_POST['template']),
                'status' => sanitize_text_field($_POST['status']),
                'required_fields' => isset($_POST['required_fields']) ? array_map('sanitize_text_field', $_POST['required_fields']) : [],
                'optional_fields' => isset($_POST['optional_fields']) ? array_map('sanitize_text_field', $_POST['optional_fields']) : []
            ];

            if (isset($_POST['id'])) {
                $id = (int)$_POST['id'];
                $this->documentService->updateDocument($id, $data);
                $message = 'Documento atualizado com sucesso!';
            } else {
                $id = $this->documentService->createDocument($data);
                $message = 'Documento criado com sucesso!';
            }

            wp_send_json_success([
                'message' => $message,
                'id' => $id
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Manipula a exclusão de um documento
     */
    public function handleDeleteDocument(): void {
        check_ajax_referer('apwp_adoption_documents');

        try {
            if (!current_user_can('manage_adoption_documents')) {
                throw new \Exception('Você não tem permissão para excluir documentos.');
            }

            $id = (int)$_POST['id'];
            $this->documentService->deleteDocument($id);

            wp_send_json_success([
                'message' => 'Documento excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Manipula a prévia de um documento
     */
    public function handlePreviewDocument(): void {
        check_ajax_referer('apwp_adoption_documents');

        try {
            if (!current_user_can('manage_adoption_documents')) {
                throw new \Exception('Você não tem permissão para visualizar documentos.');
            }

            $documentId = (int)$_POST['document_id'];
            $adoptionId = (int)$_POST['adoption_id'];

            $result = $this->processorService->processAdoptionDocument('adoption', $adoptionId);

            wp_send_json_success([
                'content' => $result['content'],
                'pdf_url' => $result['pdf_url']
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
        check_ajax_referer('apwp_adoption_documents');

        try {
            $id = (int)$_POST['id'];
            $document = $this->documentService->getDocument($id);
            if (!$document) {
                throw new \Exception('Documento não encontrado.');
            }

            // Gera dados de exemplo para a prévia
            $sampleData = $this->getSampleData();

            // Gera a prévia do documento
            $preview = $this->processorService->generatePreview($document, $sampleData);

            wp_send_json_success([
                'preview' => $preview
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Manipula o download de um documento
     */
    public function handleDownloadDocument(): void {
        check_ajax_referer('apwp_adoption_documents');

        try {
            if (!current_user_can('manage_adoption_documents')) {
                throw new \Exception('Você não tem permissão para baixar documentos.');
            }

            $documentId = (int)$_POST['document_id'];
            $adoptionId = (int)$_POST['adoption_id'];

            $result = $this->processorService->processAdoptionDocument('adoption', $adoptionId);
            $pdfPath = $result['pdf_path'];
            
            if (!file_exists($pdfPath)) {
                throw new \Exception('Arquivo PDF não encontrado.');
            }
            
            // Headers para download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($pdfPath) . '"');
            header('Content-Length: ' . filesize($pdfPath));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            // Envia o arquivo
            readfile($pdfPath);
            exit;
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
        check_ajax_referer('apwp_adoption_documents');

        try {
            $id = (int)$_GET['id'];
            $document = $this->documentService->getDocument($id);
            if (!$document) {
                throw new \Exception('Documento não encontrado.');
            }

            // Gera dados de exemplo para o download
            $sampleData = $this->getSampleData();

            // Gera o PDF
            $pdfContent = $this->processorService->generatePDF($document, $sampleData);

            // Configura os headers para download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . sanitize_file_name($document->getTitle()) . '.pdf"');
            header('Content-Length: ' . strlen($pdfContent));

            // Envia o PDF
            echo $pdfContent;
            exit;
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }

    /**
     * Retorna dados de exemplo para prévia/download
     */
    private function getSampleData(): array {
        return [
            'adotante' => [
                'nome' => 'João da Silva',
                'cpf' => '123.456.789-00',
                'rg' => '12.345.678-9',
                'endereco' => 'Rua das Flores, 123',
                'bairro' => 'Centro',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cep' => '01234-567',
                'telefone' => '(11) 98765-4321',
                'email' => 'joao@email.com'
            ],
            'animal' => [
                'nome' => 'Rex',
                'especie' => 'Cachorro',
                'raca' => 'SRD',
                'cor' => 'Caramelo',
                'idade' => '2 anos',
                'sexo' => 'Macho',
                'microchip' => '123456789'
            ],
            'adocao' => [
                'data' => date('d/m/Y'),
                'local' => 'São Paulo - SP',
                'responsavel' => 'Maria Souza'
            ]
        ];
    }
}
