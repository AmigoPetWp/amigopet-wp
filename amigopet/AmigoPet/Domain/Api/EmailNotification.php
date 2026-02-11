<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Api;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Settings\Settings;

class EmailNotification
{
    private array $emailSettings;
    private $fromEmail;
    private $fromName;

    public function __construct(?string $fromEmail = null, ?string $fromName = null)
    {
        $settings = Settings::getAll();
        $this->emailSettings = $settings['email_settings'] ?? [];

        $this->fromEmail = $fromEmail ?? ($this->emailSettings['from_email'] ?? get_bloginfo('admin_email'));
        $this->fromName = $fromName ?? ($this->emailSettings['from_name'] ?? get_bloginfo('name'));
    }

    /**
     * Envia email de confirmação de adoção
     */
    public function sendAdoptionConfirmation(string $toEmail, array $data): void
    {
        $subject = 'Confirmação de Adoção - AmigoPet';
        $message = $this->renderTemplate('adoption-confirmation', $data);

        $this->send($toEmail, $subject, $message);
    }

    /**
     * Envia email de confirmação de doação
     */
    public function sendDonationConfirmation(string $toEmail, array $data): void
    {
        $subject = 'Confirmação de Doação - AmigoPet';
        $message = $this->renderTemplate('donation-confirmation', $data);

        $this->send($toEmail, $subject, $message);
    }

    /**
     * Envia email de confirmação de evento
     */
    public function sendEventConfirmation(string $toEmail, array $data): void
    {
        $subject = 'Confirmação de Evento - AmigoPet';
        $message = $this->renderTemplate('event-confirmation', $data);

        $this->send($toEmail, $subject, $message);
    }

    /**
     * Envia email de confirmação de voluntariado
     */
    public function sendVolunteerConfirmation(string $toEmail, array $data): void
    {
        $subject = 'Confirmação de Voluntariado - AmigoPet';
        $message = $this->renderTemplate('volunteer-confirmation', $data);

        $this->send($toEmail, $subject, $message);
    }

    /**
     * Envia email de notificação para a organização
     */
    public function sendOrganizationNotification(string $toEmail, string $type, array $data): void
    {
        $subject = 'Nova Notificação - AmigoPet';
        $message = $this->renderTemplate('organization-' . $type, $data);

        $this->send($toEmail, $subject, $message);
    }

    /**
     * Renderiza um template de email
     */
    private function renderTemplate(string $template, array $data): string
    {
        $templatePath = plugin_dir_path(__FILE__) . 'templates/email/' . $template . '.php';
        if (!file_exists($templatePath)) {
            // translators: %s: placeholder
            throw new \RuntimeException(esc_html(sprintf(esc_html__('Template de email não encontrado: %s', 'amigopet'), esc_html($template))));
        }

        ob_start();
        extract($data);
        include $templatePath;
        return ob_get_clean();
    }

    /**
     * Envia um email usando o WordPress
     */
    private function send(string $to, string $subject, string $message): void
    {
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>'
        ];

        $sent = wp_mail($to, $subject, $message, $headers);
        if (!$sent) {
            throw new \RuntimeException(esc_html__('Erro ao enviar email', 'amigopet'));
        }
    }
}