<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Services;

if (!defined('ABSPATH')) {
    exit;
}

class DocumentProcessorService {
    private $pdfGenerator;
    private $templateService;

    public function __construct(
        PDFGeneratorService $pdfGenerator,
        DocumentTemplateService $templateService
    ) {
        $this->pdfGenerator = $pdfGenerator;
        $this->templateService = $templateService;
    }

    /**
     * Processa os placeholders em um texto de documento
     *
     * @param string $content Conteúdo do documento com placeholders
     * @param array $data Array com os dados para substituir os placeholders
     * @return string Conteúdo com placeholders substituídos
     */
    public function processPlaceholders(string $content, array $data): string {
        // Dados da organização
        $orgData = get_option('amigopet_settings', []);
        $data = array_merge($data, [
            'org_name' => $orgData['org_name'] ?? '',
            'org_cnpj' => $orgData['org_cnpj'] ?? '',
            'org_address' => $orgData['org_address'] ?? '',
            'org_phone' => $orgData['org_phone'] ?? '',
            'org_email' => $orgData['org_email'] ?? ''
        ]);
        
        // Data e hora atual
        $data = array_merge($data, [
            'current_date' => wp_date(get_option('date_format')),
            'current_time' => wp_date(get_option('time_format')),
            'current_datetime' => wp_date(get_option('date_format') . ' ' . get_option('time_format'))
        ]);
        
        // Substitui os placeholders
        $placeholders = array_map(function($key) {
            return '{{' . $key . '}}';
        }, array_keys($data));
        
        $values = array_values($data);
        
        return str_replace($placeholders, $values, $content);
    }

    /**
     * Processa um documento de adoção
     */
    public function processAdoptionDocument(string $documentType, int $adoptionId): string {
        // Busca o template ativo para o tipo de documento
        $template = $this->templateService->getActiveTemplate($documentType);
        if (!$template) {
            throw new \Exception(esc_html__('Template não encontrado para o tipo de documento', 'amigopet'));
        }

        // Busca os dados da adoção
        $adoption = $this->getAdoptionData($adoptionId);
        
        // Processa os placeholders do template com os dados da adoção
        return $this->processPlaceholders($template['content'], $adoption);
    }

    /**
     * Processa um documento de doação
     */
    public function processDonationDocument(string $documentType, int $donationId): string {
        // Busca o template ativo para o tipo de documento
        $template = $this->templateService->getActiveTemplate($documentType);
        if (!$template) {
            throw new \Exception(esc_html__('Template não encontrado para o tipo de documento', 'amigopet'));
        }

        // Busca os dados da doação
        $donation = $this->getDonationData($donationId);
        
        // Processa os placeholders do template com os dados da doação
        return $this->processPlaceholders($template['content'], $donation);
    }

    /**
     * Processa um documento de evento
     */
    public function processEventDocument(string $documentType, int $eventId): string {
        // Busca o template ativo para o tipo de documento
        $template = $this->templateService->getActiveTemplate($documentType);
        if (!$template) {
            throw new \Exception(esc_html__('Template não encontrado para o tipo de documento', 'amigopet'));
        }

        // Busca os dados do evento
        $event = $this->getEventData($eventId);
        
        // Processa os placeholders do template com os dados do evento
        return $this->processPlaceholders($template['content'], $event);
    }

    /**
     * Gera um PDF a partir do conteúdo processado
     */
    public function generatePDF(string $content, string $title, array $metadata = []): string {
        return $this->pdfGenerator->generate($content, $title, $metadata);
    }

    /**
     * Retorna o serviço de templates
     */
    public function getTemplateService(): DocumentTemplateService {
        return $this->templateService;
    }

    /**
     * Busca os dados de uma adoção
     */
    private function getAdoptionData(int $adoptionId): array {
        global $wpdb;
        
        $adoption = $wpdb->get_row($wpdb->prepare(
            "SELECT a.*, p.name as pet_name, ad.name as adopter_name, ad.document as adopter_document,
                    ad.email as adopter_email, ad.phone as adopter_phone, ad.address as adopter_address,
                    o.name as org_name, o.document as org_document, o.email as org_email,
                    o.phone as org_phone, o.address as org_address
             FROM {$wpdb->prefix}amigopet_adoptions a
             JOIN {$wpdb->prefix}amigopet_pets p ON a.pet_id = p.id
             JOIN {$wpdb->prefix}amigopet_adopters ad ON a.adopter_id = ad.id
             JOIN {$wpdb->prefix}amigopet_organizations o ON a.organization_id = o.id
             WHERE a.id = %d",
            $adoptionId
        ), ARRAY_A);

        if (!$adoption) {
            throw new \Exception(esc_html__('Adoção não encontrada', 'amigopet'));
        }

        return $adoption;
    }

    /**
     * Busca os dados de uma doação
     */
    private function getDonationData(int $donationId): array {
        global $wpdb;
        
        $donation = $wpdb->get_row($wpdb->prepare(
            "SELECT d.*, o.name as org_name, o.document as org_document,
                    o.email as org_email, o.phone as org_phone, o.address as org_address
             FROM {$wpdb->prefix}amigopet_donations d
             JOIN {$wpdb->prefix}amigopet_organizations o ON d.organization_id = o.id
             WHERE d.id = %d",
            $donationId
        ), ARRAY_A);

        if (!$donation) {
            throw new \Exception(esc_html__('Doação não encontrada', 'amigopet'));
        }

        return $donation;
    }

    /**
     * Busca os dados de um evento
     */
    private function getEventData(int $eventId): array {
        global $wpdb;
        
        $event = $wpdb->get_row($wpdb->prepare(
            "SELECT e.*, o.name as org_name, o.document as org_document,
                    o.email as org_email, o.phone as org_phone, o.address as org_address
             FROM {$wpdb->prefix}amigopet_events e
             JOIN {$wpdb->prefix}amigopet_organizations o ON e.organization_id = o.id
             WHERE e.id = %d",
            $eventId
        ), ARRAY_A);

        if (!$event) {
            throw new \Exception(esc_html__('Evento não encontrado', 'amigopet'));
        }

        return $event;
    }
}