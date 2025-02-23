<?php
namespace AmigoPetWp\Domain\Database\Migrations;

use AmigoPetWp\Domain\Database\Migrations\Migration;

class CreateSettings extends Migration {
    private array $default_settings = [
        'organization_name' => '',
        'organization_email' => '',
        'organization_phone' => '',
        'google_maps_key' => '',
        'petfinder_key' => '',
        'payment_gateway_key' => '',
        'payment_gateway_secret' => '',
        'payment_gateway_sandbox' => true,

        // QR Code
        'qrcode_settings' => [
            'size' => 300,
            'margin' => 10,
            'foreground_color' => '#000000',
            'background_color' => '#FFFFFF',
            'error_correction' => 'M',
            'logo_enabled' => true,
            'logo_size' => 50,
            'logo_path' => ''
        ],
        'smtp_settings' => [
            'host' => '',
            'port' => '',
            'secure' => 'tls',
            'auth' => true,
            'username' => '',
            'password' => ''
        ],
        'email_templates' => [
            'adoption_approved' => [
                'subject' => 'Parabéns! Sua adoção foi aprovada',
                'body' => 'Olá {adopter_name},\n\nParabéns! Sua adoção do pet {pet_name} foi aprovada.'
            ],
            'adoption_rejected' => [
                'subject' => 'Atualização sobre sua adoção',
                'body' => 'Olá {adopter_name},\n\nInfelizmente sua adoção do pet {pet_name} não foi aprovada.'
            ],
            'donation_received' => [
                'subject' => 'Obrigado pela sua doação!',
                'body' => 'Olá {donor_name},\n\nMuito obrigado pela sua doação de {donation_amount}.'
            ],
            'volunteer_application' => [
                'subject' => 'Recebemos sua inscrição para voluntariado',
                'body' => 'Olá {volunteer_name},\n\nRecebemos sua inscrição para ser voluntário.'
            ]
        ],
        'qrcode_settings' => [
            'size' => 300,
            'margin' => 10,
            'foreground_color' => '#000000',
            'background_color' => '#FFFFFF',
            'error_correction' => 'M',
            'logo_enabled' => true,
            'logo_size' => 50,
            'logo_path' => ''
        ],
        'adoption_workflow' => [
            'require_home_visit' => true,
            'require_adoption_fee' => true,
            'require_terms_acceptance' => true,
            'require_adopter_documents' => true
        ],
        'notification_workflow' => [
            'notify_new_adoption' => true,
            'notify_adoption_status' => true,
            'notify_new_donation' => true,
            'notify_new_volunteer' => true
        ],
        'terms_settings' => [
            'adoption_terms' => '',
            'donation_terms' => '',
            'volunteer_terms' => ''
        ]
    ];

    public function getVersion(): string {
        return '1.0.0';
    }

    public function getDescription(): string {
        return 'Cria as configurações padrão do sistema';
    }

    public function up(): void {
        foreach ($this->default_settings as $key => $value) {
            update_option('apwp_' . $key, $value, false);
        }
    }

    public function down(): void {
        foreach (array_keys($this->default_settings) as $key) {
            delete_option('apwp_' . $key);
        }
    }
}
