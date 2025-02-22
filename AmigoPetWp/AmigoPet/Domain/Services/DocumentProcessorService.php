<?php
namespace AmigoPetWp\Domain\Services;

class DocumentProcessorService {
    private $pdfGenerator;
    private $templateService;

    public function __construct(
        PDFGeneratorService $pdfGenerator,
        TemplateService $templateService
    ) {
        $this->pdfGenerator = $pdfGenerator;
        $this->templateService = $templateService;
    }
    private $pdfGenerator;
    private $templateService;

    public function __construct(
        PDFGeneratorService $pdfGenerator,
        TemplateService $templateService
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
            return '{' . $key . '}';
        }, array_keys($data));
        
        return str_replace($placeholders, array_values($data), $content);
    }

    /**
     * Processa os placeholders para documento de adoção
     *
     * @param string $content Conteúdo do documento
     * @param int $adoptionId ID da adoção
     * @return string Conteúdo processado
     */
    public function processAdoptionDocument(string $documentType, int $adoptionId): array {
        // Busca o template ativo
        $template = $this->templateService->getActiveTemplate($documentType);
        if (!$template) {
            throw new \InvalidArgumentException("Template não encontrado para o tipo de documento: {$documentType}");
        }

        $content = $template['content'];
        global $wpdb;
        
        // Busca dados da adoção
        $adoption = $wpdb->get_row($wpdb->prepare("
            SELECT 
                a.*,
                p.*,
                u.display_name as adopter_name,
                um.meta_value as adopter_cpf,
                um2.meta_value as adopter_rg,
                um3.meta_value as adopter_birth,
                um4.meta_value as adopter_address,
                um5.meta_value as adopter_phone,
                u.user_email as adopter_email
            FROM {$wpdb->prefix}apwp_adoptions a
            JOIN {$wpdb->prefix}apwp_pets p ON a.pet_id = p.id
            JOIN {$wpdb->users} u ON a.adopter_id = u.ID
            LEFT JOIN {$wpdb->usermeta} um ON u.ID = um.user_id AND um.meta_key = 'cpf'
            LEFT JOIN {$wpdb->usermeta} um2 ON u.ID = um2.user_id AND um2.meta_key = 'rg'
            LEFT JOIN {$wpdb->usermeta} um3 ON u.ID = um3.user_id AND um3.meta_key = 'birth_date'
            LEFT JOIN {$wpdb->usermeta} um4 ON u.ID = um4.user_id AND um4.meta_key = 'address'
            LEFT JOIN {$wpdb->usermeta} um5 ON u.ID = um5.user_id AND um5.meta_key = 'phone'
            WHERE a.id = %d
        ", $adoptionId));
        
        if (!$adoption) {
            throw new \InvalidArgumentException("Adoção não encontrada");
        }
        
        return $this->processPlaceholders($content, [
            'adopter_name' => $adoption->adopter_name,
            'adopter_cpf' => $adoption->adopter_cpf,
            'adopter_rg' => $adoption->adopter_rg,
            'adopter_birth' => $adoption->adopter_birth,
            'adopter_address' => $adoption->adopter_address,
            'adopter_phone' => $adoption->adopter_phone,
            'adopter_email' => $adoption->adopter_email,
            'pet_name' => $adoption->name,
            'pet_species' => $adoption->species,
            'pet_breed' => $adoption->breed,
            'pet_age' => $adoption->age,
            'pet_gender' => $adoption->gender,
            'pet_size' => $adoption->size,
            'pet_chip' => $adoption->chip_number
        ]);

        // Gera o PDF
        $metadata = [
            'author' => $adoption->adopter_name,
            'subject' => sprintf('Documento de Adoção - %s', $adoption->name),
            'keywords' => 'adoção, pet, documento'
        ];

        $pdfResult = $this->pdfGenerator->generatePDF(
            $template['title'],
            $processedContent,
            $metadata
        );

        return [
            'content' => $processedContent,
            'pdf_url' => $pdfResult['url'],
            'pdf_path' => $pdfResult['path']
        ];

        // Gera o PDF
        $metadata = [
            'author' => $adoption->adopter_name,
            'subject' => sprintf('Documento de Adoção - %s', $adoption->name),
            'keywords' => 'adoção, pet, documento'
        ];

        $pdfResult = $this->pdfGenerator->generatePDF(
            $template['title'],
            $processedContent,
            $metadata
        );

        return [
            'content' => $processedContent,
            'pdf_url' => $pdfResult['url'],
            'pdf_path' => $pdfResult['path']
        ];
    }

    /**
     * Processa os placeholders para documento de voluntário
     *
     * @param string $content Conteúdo do documento
     * @param int $volunteerId ID do voluntário
     * @return string Conteúdo processado
     */
    public function processVolunteerDocument(string $content, int $volunteerId): string {
        global $wpdb;
        
        // Busca dados do voluntário
        $volunteer = $wpdb->get_row($wpdb->prepare("
            SELECT 
                v.*,
                u.display_name as volunteer_name,
                um.meta_value as volunteer_cpf,
                um2.meta_value as volunteer_rg,
                um3.meta_value as volunteer_birth,
                um4.meta_value as volunteer_address,
                um5.meta_value as volunteer_phone,
                u.user_email as volunteer_email
            FROM {$wpdb->prefix}apwp_volunteers v
            JOIN {$wpdb->users} u ON v.user_id = u.ID
            LEFT JOIN {$wpdb->usermeta} um ON u.ID = um.user_id AND um.meta_key = 'cpf'
            LEFT JOIN {$wpdb->usermeta} um2 ON u.ID = um2.user_id AND um2.meta_key = 'rg'
            LEFT JOIN {$wpdb->usermeta} um3 ON u.ID = um3.user_id AND um3.meta_key = 'birth_date'
            LEFT JOIN {$wpdb->usermeta} um4 ON u.ID = um4.user_id AND um4.meta_key = 'address'
            LEFT JOIN {$wpdb->usermeta} um5 ON u.ID = um5.user_id AND um5.meta_key = 'phone'
            WHERE v.id = %d
        ", $volunteerId));
        
        if (!$volunteer) {
            throw new \InvalidArgumentException("Voluntário não encontrado");
        }
        
        return $this->processPlaceholders($content, [
            'volunteer_name' => $volunteer->volunteer_name,
            'volunteer_cpf' => $volunteer->volunteer_cpf,
            'volunteer_rg' => $volunteer->volunteer_rg,
            'volunteer_birth' => $volunteer->volunteer_birth,
            'volunteer_address' => $volunteer->volunteer_address,
            'volunteer_phone' => $volunteer->volunteer_phone,
            'volunteer_email' => $volunteer->volunteer_email,
            'volunteer_role' => $volunteer->role,
            'volunteer_skills' => $volunteer->skills,
            'volunteer_availability' => $volunteer->availability
        ]);
    }

    /**
     * Processa os placeholders para documento de doação
     *
     * @param string $content Conteúdo do documento
     * @param int $donationId ID da doação
     * @return string Conteúdo processado
     */
    public function processDonationDocument(string $content, int $donationId): string {
        global $wpdb;
        
        // Busca dados da doação
        $donation = $wpdb->get_row($wpdb->prepare("
            SELECT 
                d.*,
                u.display_name as donor_name,
                um.meta_value as donor_cpf,
                um2.meta_value as donor_rg,
                um3.meta_value as donor_address,
                um4.meta_value as donor_phone,
                u.user_email as donor_email
            FROM {$wpdb->prefix}apwp_donations d
            JOIN {$wpdb->users} u ON d.donor_id = u.ID
            LEFT JOIN {$wpdb->usermeta} um ON u.ID = um.user_id AND um.meta_key = 'cpf'
            LEFT JOIN {$wpdb->usermeta} um2 ON u.ID = um2.user_id AND um2.meta_key = 'rg'
            LEFT JOIN {$wpdb->usermeta} um3 ON u.ID = um3.user_id AND um3.meta_key = 'address'
            LEFT JOIN {$wpdb->usermeta} um4 ON u.ID = um4.user_id AND um4.meta_key = 'phone'
            WHERE d.id = %d
        ", $donationId));
        
        if (!$donation) {
            throw new \InvalidArgumentException("Doação não encontrada");
        }
        
        return $this->processPlaceholders($content, [
            'donor_name' => $donation->donor_name,
            'donor_cpf' => $donation->donor_cpf,
            'donor_rg' => $donation->donor_rg,
            'donor_address' => $donation->donor_address,
            'donor_phone' => $donation->donor_phone,
            'donor_email' => $donation->donor_email,
            'donation_amount' => number_format($donation->amount, 2, ',', '.'),
            'donation_date' => wp_date(get_option('date_format'), strtotime($donation->created_at)),
            'donation_payment_method' => $donation->payment_method,
            'donation_transaction_id' => $donation->transaction_id
        ]);
    }

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
     * Gera um PDF a partir do conteúdo do documento
     *
     * @param string $content Conteúdo do documento
     * @param string $title Título do documento
     * @param array $metadata Metadados opcionais
     * @return string URL do arquivo PDF gerado
     */
    public function generatePDF(string $content, string $title, array $metadata = []): string {
        // Gera o PDF
        $result = $this->pdfGenerator->generatePDF($title, $content, $metadata);

        // Adiciona marca d'água se necessário
        if (isset($metadata['watermark'])) {
            $this->pdfGenerator->addWatermark($result['path'], $metadata['watermark']);
        }

        // Protege o PDF se necessário
        if (isset($metadata['protect']) && $metadata['protect']) {
            $this->pdfGenerator->protectPDF(
                $result['path'],
                $metadata['user_password'] ?? '',
                $metadata['owner_password'] ?? uniqid('apwp_')
            );
        }

        // Assina o PDF se o certificado estiver configurado
        $settings = get_option('amigopet_settings', []);
        if (!empty($settings['digital_certificate']) && !empty($settings['certificate_password'])) {
            $this->pdfGenerator->signPDF(
                $result['path'],
                $settings['digital_certificate'],
                $settings['certificate_password']
            );
        }

        return $result['url'];
    }
}
