<?php
namespace Domain\Services;

use Domain\Database\TermsRepository;
use Domain\Entities\Terms;

class TermsService {
    private $repository;

    public function __construct(TermsRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Cria um novo termo
     */
    public function createTerms(
        string $documentType,
        string $documentUrl,
        string $signedBy,
        \DateTimeInterface $signedAt
    ): int {
        $terms = new Terms(
            $documentType,
            $documentUrl,
            $signedBy,
            $signedAt
        );

        return $this->repository->save($terms);
    }

    /**
     * Atualiza um termo existente
     */
    public function updateTerms(
        int $id,
        string $documentType,
        string $documentUrl,
        string $signedBy,
        \DateTimeInterface $signedAt
    ): void {
        $terms = $this->repository->findById($id);
        if (!$terms) {
            throw new \InvalidArgumentException("Termo não encontrado");
        }

        $terms = new Terms(
            $documentType,
            $documentUrl,
            $signedBy,
            $signedAt
        );

        $this->repository->save($terms);
    }

    /**
     * Marca um termo como inativo
     */
    public function deactivate(int $termsId): void {
        $terms = $this->repository->findById($termsId);
        if (!$terms) {
            throw new \InvalidArgumentException("Termo não encontrado");
        }

        $terms->deactivate();
        $this->repository->save($terms);
    }

    /**
     * Busca um termo por ID
     */
    public function findById(int $id): ?Terms {
        return $this->repository->findById($id);
    }

    /**
     * Lista termos por tipo
     */
    public function findByType(string $documentType): array {
        return $this->repository->findByType($documentType);
    }

    /**
     * Lista termos por assinante
     */
    public function findBySignedBy(string $signedBy): array {
        return $this->repository->findBySignedBy($signedBy);
    }
    
    /**
     * Processa os placeholders em um texto de termo
     *
     * @param string $content Conteúdo do termo com placeholders
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
     * Processa os placeholders para termo de adoção
     *
     * @param string $content Conteúdo do termo
     * @param int $adoptionId ID da adoção
     * @return string Conteúdo processado
     */
    public function processAdoptionTerms(string $content, int $adoptionId): string {
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
            FROM {$wpdb->prefix}amigopet_adoptions a
            JOIN {$wpdb->prefix}amigopet_pets p ON a.pet_id = p.id
            JOIN {$wpdb->users} u ON a.user_id = u.ID
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
    }
    
    /**
     * Processa os placeholders para termo de voluntariado
     *
     * @param string $content Conteúdo do termo
     * @param int $volunteerId ID do voluntário
     * @return string Conteúdo processado
     */
    public function processVolunteerTerms(string $content, int $volunteerId): string {
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
            FROM {$wpdb->prefix}amigopet_volunteers v
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
            'volunteer_email' => $volunteer->volunteer_email
        ]);
    }
    
    /**
     * Processa os placeholders para termo de doação
     *
     * @param string $content Conteúdo do termo
     * @param int $donationId ID da doação
     * @return string Conteúdo processado
     */
    public function processDonationTerms(string $content, int $donationId): string {
        global $wpdb;
        
        // Busca dados da doação
        $donation = $wpdb->get_row($wpdb->prepare("
            SELECT 
                d.*,
                u.display_name as donor_name,
                um.meta_value as donor_cpf,
                um2.meta_value as donor_phone,
                u.user_email as donor_email
            FROM {$wpdb->prefix}amigopet_donations d
            JOIN {$wpdb->users} u ON d.user_id = u.ID
            LEFT JOIN {$wpdb->usermeta} um ON u.ID = um.user_id AND um.meta_key = 'cpf'
            LEFT JOIN {$wpdb->usermeta} um2 ON u.ID = um2.user_id AND um2.meta_key = 'phone'
            WHERE d.id = %d
        ", $donationId));
        
        if (!$donation) {
            throw new \InvalidArgumentException("Doação não encontrada");
        }
        
        return $this->processPlaceholders($content, [
            'donor_name' => $donation->donor_name,
            'donor_cpf' => $donation->donor_cpf,
            'donor_email' => $donation->donor_email,
            'donor_phone' => $donation->donor_phone,
            'donation_amount' => 'R$ ' . number_format($donation->amount, 2, ',', '.'),
            'donation_date' => wp_date(get_option('date_format'), strtotime($donation->created_at))
        ]);
    }
}
