<?php declare(strict_types=1);
namespace AmigoPetWp\Controllers\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Services\AdoptionDocumentService;
use AmigoPetWp\Domain\Services\DocumentProcessorService;

class AdminAdoptionDocumentController
{
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
    public function registerMenus(): void
    {
        add_submenu_page(
            'amigopetwp',
            esc_html__('Documentos de Adoção', 'amigopet'),
            esc_html__('Documentos de Adoção', 'amigopet'),
            'manage_adoption_documents',
            'amigopet-adoption-documents',
            [$this, 'renderDocumentsPage']
        );
    }

    /**
     * Renderiza a página principal de documentos
     */
    public function renderDocumentsPage(): void
    {
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
        include AMIGOPET_PLUGIN_DIR . '/AmigoPet/Views/admin/adoption-documents/index.php';
    }

    /**
     * Manipula o salvamento de um documento
     */
    public function handleSaveDocument(): void
    {
        check_ajax_referer('apwp_adoption_documents');

        try {
            if (!current_user_can('manage_adoption_documents')) {
                throw new \Exception(esc_html__('Você não tem permissão para gerenciar documentos.', 'amigopet'));
            }

            $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash((string) $_POST['title'])) : '';
            $description = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash((string) $_POST['description'])) : '';
            $template = isset($_POST['template']) ? sanitize_text_field(wp_unslash((string) $_POST['template'])) : '';
            $status = isset($_POST['status']) ? sanitize_text_field(wp_unslash((string) $_POST['status'])) : '';
            $requiredFields = isset($_POST['required_fields']) && is_array($_POST['required_fields'])
                ? array_map(static function ($field) {
                    return sanitize_text_field(wp_unslash((string) $field));
                }, $_POST['required_fields'])
                : [];
            $optionalFields = isset($_POST['optional_fields']) && is_array($_POST['optional_fields'])
                ? array_map(static function ($field) {
                    return sanitize_text_field(wp_unslash((string) $field));
                }, $_POST['optional_fields'])
                : [];

            $data = [
                'title' => $title,
                'description' => $description,
                'template' => $template,
                'status' => $status,
                'required_fields' => $requiredFields,
                'optional_fields' => $optionalFields
            ];

            if (isset($_POST['id'])) {
                $id = (int) wp_unslash((string) $_POST['id']);
                $this->documentService->updateDocument($id, $data);
                $message = esc_html__('Documento atualizado com sucesso!', 'amigopet');
            } else {
                $id = $this->documentService->createDocument($data);
                $message = esc_html__('Documento criado com sucesso!', 'amigopet');
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
    public function handleDeleteDocument(): void
    {
        check_ajax_referer('apwp_adoption_documents');

        try {
            if (!current_user_can('manage_adoption_documents')) {
                throw new \Exception(esc_html__('Você não tem permissão para excluir documentos.', 'amigopet'));
            }

            $id = isset($_POST['id']) ? (int) wp_unslash((string) $_POST['id']) : 0;
            $this->documentService->deleteDocument($id);

            wp_send_json_success([
                'message' => esc_html__('Documento excluído com sucesso!', 'amigopet')
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Manipula a prévia de um documento
     */
    public function handlePreviewDocument(): void
    {
        check_ajax_referer('apwp_adoption_documents');

        try {
            if (!current_user_can('manage_adoption_documents')) {
                throw new \Exception(esc_html__('Você não tem permissão para visualizar documentos.', 'amigopet'));
            }

            $documentId = isset($_POST['document_id']) ? (int) wp_unslash((string) $_POST['document_id']) : 0;
            $adoptionId = isset($_POST['adoption_id']) ? (int) wp_unslash((string) $_POST['adoption_id']) : 0;

            $result = $this->processorService->processAdoptionDocument('adoption', $adoptionId);

            wp_send_json_success([
                'content' => $result['content'],
                'pdf_url' => $result['pdf_url']
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Manipula o download de um documento
     */
    public function handleDownloadDocument(): void
    {
        check_ajax_referer('apwp_adoption_documents');

        try {
            if (!current_user_can('manage_adoption_documents')) {
                throw new \Exception(esc_html__('Você não tem permissão para baixar documentos.', 'amigopet'));
            }

            $documentId = isset($_POST['document_id']) ? (int) wp_unslash((string) $_POST['document_id']) : 0;
            $adoptionId = isset($_POST['adoption_id']) ? (int) wp_unslash((string) $_POST['adoption_id']) : 0;

            $result = $this->processorService->processAdoptionDocument('adoption', $adoptionId);
            $pdfPath = isset($result['pdf_path']) ? (string) $result['pdf_path'] : '';
            $pdfUrl = isset($result['pdf_url']) ? esc_url_raw((string) $result['pdf_url']) : '';

            if (!file_exists($pdfPath)) {
                throw new \Exception(esc_html__('Arquivo PDF não encontrado.', 'amigopet'));
            }

            wp_send_json_success([
                'document_id' => $documentId,
                'filename' => sanitize_file_name((string) basename($pdfPath)),
                'pdf_url' => $pdfUrl
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Retorna dados de exemplo para prévia/download
     */
    private function getSampleData(): array
    {
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
                'data' => gmdate('d/m/Y'),
                'local' => 'São Paulo - SP',
                'responsavel' => 'Maria Souza'
            ]
        ];
    }
}