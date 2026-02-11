<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Services;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Security\SecurityService;

class AdoptionWorkflowService {
    private $adoptionService;
    private $documentService;
    private $paymentService;
    private $security;
    
    public function __construct(
        AdoptionService $adoptionService,
        AdoptionDocumentService $documentService,
        AdoptionPaymentService $paymentService
    ) {
        $this->adoptionService = $adoptionService;
        $this->documentService = $documentService;
        $this->paymentService = $paymentService;
        $this->security = SecurityService::getInstance();
    }
    
    /**
     * Inicia o processo de adoção
     */
    public function startAdoption(array $data): int {
        // Sanitiza e valida input
        $data = $this->security->sanitizeInput($data, [
            'pet_id' => 'int',
            'adopter_id' => 'int',
            'notes' => 'text'
        ]);
        
        // Verifica se o pet está disponível
        $pet = $this->adoptionService->getPet($data['pet_id']);
        if (!$pet || $pet->getStatus() !== 'available') {
            throw new \Exception(esc_html__('Pet não está disponível para adoção', 'amigopet'));
        }
        
        // Verifica se o adotante está apto
        if (!$this->validateAdopter($data['adopter_id'])) {
            throw new \Exception(esc_html__('Adotante não está apto para adoção', 'amigopet'));
        }
        
        // Cria a adoção com status inicial
        return $this->adoptionService->createAdoption(array_merge($data, [
            'status' => 'pending_documents'
        ]));
    }
    
    /**
     * Valida documentos da adoção
     */
    public function validateDocuments(int $adoptionId): bool {
        $adoption = $this->adoptionService->getAdoption($adoptionId);
        if (!$adoption) {
            throw new \Exception(esc_html__('Adoção não encontrada', 'amigopet'));
        }
        
        // Lista documentos necessários
        $requiredDocs = $this->documentService->getRequiredDocuments();
        $submittedDocs = $this->documentService->getAdoptionDocuments($adoptionId);
        
        // Verifica se todos os documentos foram enviados e são válidos
        foreach ($requiredDocs as $docType) {
            $doc = $submittedDocs[$docType] ?? null;
            if (!$doc || !$this->documentService->validateDocument($doc)) {
                return false;
            }
        }
        
        // Atualiza status
        $this->adoptionService->updateStatus($adoptionId, 'pending_payment');
        return true;
    }
    
    /**
     * Valida pagamentos da adoção
     */
    public function validatePayments(int $adoptionId): bool {
        $adoption = $this->adoptionService->getAdoption($adoptionId);
        if (!$adoption) {
            throw new \Exception(esc_html__('Adoção não encontrada', 'amigopet'));
        }
        
        // Calcula valor total e pago
        $totalAmount = $this->paymentService->calculateAdoptionTotal($adoptionId);
        $paidAmount = $this->paymentService->calculatePaidAmount($adoptionId);
        
        if ($paidAmount < $totalAmount) {
            return false;
        }
        
        // Atualiza status
        $this->adoptionService->updateStatus($adoptionId, 'pending_approval');
        return true;
    }
    
    /**
     * Aprova a adoção
     */
    public function approveAdoption(int $adoptionId, int $approverId): void {
        // Verifica permissão
        if (!$this->security->validatePermission($approverId, 'approve_adoption')) {
            throw new \Exception(esc_html__('Sem permissão para aprovar adoção', 'amigopet'));
        }
        
        $adoption = $this->adoptionService->getAdoption($adoptionId);
        if (!$adoption) {
            throw new \Exception(esc_html__('Adoção não encontrada', 'amigopet'));
        }
        
        // Verifica se pode ser aprovada
        if (!$this->canBeApproved($adoption)) {
            throw new \Exception(esc_html__('Adoção não pode ser aprovada', 'amigopet'));
        }
        
        // Atualiza status
        $this->adoptionService->updateStatus($adoptionId, 'approved');
        
        // Atualiza status do pet
        $this->adoptionService->updatePetStatus($adoption->getPetId(), 'adopted');
        
        // Notifica interessados
        $this->notifyApproval($adoption);
    }
    
    /**
     * Verifica se adoção pode ser aprovada
     */
    private function canBeApproved($adoption): bool {
        return 
            $adoption->getStatus() === 'pending_approval' &&
            $this->validateDocuments($adoption->getId()) &&
            $this->validatePayments($adoption->getId());
    }
    
    /**
     * Valida se adotante está apto
     */
    private function validateAdopter(int $adopterId): bool {
        $adopter = $this->adoptionService->getAdopter($adopterId);
        if (!$adopter) {
            return false;
        }
        
        // Verifica idade
        if ($adopter->getAge() < 18) {
            return false;
        }
        
        // Verifica histórico
        $history = $this->adoptionService->getAdopterHistory($adopterId);
        if ($history['banned'] || $history['failed_adoptions'] > 2) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Notifica aprovação da adoção
     */
    private function notifyApproval($adoption): void {
        // Email para adotante
        wp_mail(
            $adoption->getAdopterEmail(),
            'Sua adoção foi aprovada!',
            'Parabéns! Sua adoção foi aprovada...'
        );
        
        // Email para organização
        wp_mail(
            get_option('admin_email'),
            'Nova adoção aprovada',
            'Uma nova adoção foi aprovada...'
        );
        
        // Notificação no sistema
        do_action('amigopet_adoption_approved', $adoption);
    }
}