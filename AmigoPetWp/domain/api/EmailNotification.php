<?php
namespace Domain\Api;

class EmailNotification {
    private $fromEmail;
    private $fromName;
    
    public function __construct(string $fromEmail, string $fromName) {
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }
    
    /**
     * Envia email de confirmação de adoção
     */
    public function sendAdoptionConfirmation(string $toEmail, array $data): void {
        $subject = 'Confirmação de Adoção - AmigoPet';
        $message = $this->renderTemplate('adoption-confirmation', $data);
        
        $this->send($toEmail, $subject, $message);
    }
    
    /**
     * Envia email de confirmação de doação
     */
    public function sendDonationConfirmation(string $toEmail, array $data): void {
        $subject = 'Confirmação de Doação - AmigoPet';
        $message = $this->renderTemplate('donation-confirmation', $data);
        
        $this->send($toEmail, $subject, $message);
    }
    
    /**
     * Envia email de confirmação de evento
     */
    public function sendEventConfirmation(string $toEmail, array $data): void {
        $subject = 'Confirmação de Evento - AmigoPet';
        $message = $this->renderTemplate('event-confirmation', $data);
        
        $this->send($toEmail, $subject, $message);
    }
    
    /**
     * Envia email de confirmação de voluntariado
     */
    public function sendVolunteerConfirmation(string $toEmail, array $data): void {
        $subject = 'Confirmação de Voluntariado - AmigoPet';
        $message = $this->renderTemplate('volunteer-confirmation', $data);
        
        $this->send($toEmail, $subject, $message);
    }
    
    /**
     * Envia email de notificação para a organização
     */
    public function sendOrganizationNotification(string $toEmail, string $type, array $data): void {
        $subject = 'Nova Notificação - AmigoPet';
        $message = $this->renderTemplate('organization-' . $type, $data);
        
        $this->send($toEmail, $subject, $message);
    }
    
    /**
     * Renderiza um template de email
     */
    private function renderTemplate(string $template, array $data): string {
        $templatePath = plugin_dir_path(__FILE__) . 'templates/email/' . $template . '.php';
        if (!file_exists($templatePath)) {
            throw new \RuntimeException('Template de email não encontrado: ' . $template);
        }
        
        ob_start();
        extract($data);
        include $templatePath;
        return ob_get_clean();
    }
    
    /**
     * Envia um email usando o WordPress
     */
    private function send(string $to, string $subject, string $message): void {
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>'
        ];
        
        $sent = wp_mail($to, $subject, $message, $headers);
        if (!$sent) {
            throw new \RuntimeException('Erro ao enviar email');
        }
    }
}
