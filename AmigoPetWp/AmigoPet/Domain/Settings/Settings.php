<?php
namespace AmigoPetWp\Domain\Settings;

class Settings {
    /**
     * Sanitiza um array de configurações
     *
     * @param array $array Array para sanitizar
     * @return array Array sanitizado
     */
    public static function sanitizeArrayOption($array) {
        if (!is_array($array)) {
            return [];
        }
        
        return array_map(function($value) {
            if (is_bool($value)) {
                return (bool) $value;
            }
            if (is_numeric($value)) {
                return (float) $value;
            }
            if (is_string($value)) {
                return sanitize_text_field($value);
            }
            return $value;
        }, $array);
    }

    public static function register(): void {
        // Configurações da organização
        register_setting('apwp_settings', 'apwp_organization_name');
        register_setting('apwp_settings', 'apwp_organization_email');
        register_setting('apwp_settings', 'apwp_organization_phone');

        // Configurações de API
        register_setting('apwp_settings', 'apwp_google_maps_key');
        register_setting('apwp_settings', 'apwp_petfinder_key');
        register_setting('apwp_settings', 'apwp_payment_gateway_key');
        register_setting('apwp_settings', 'apwp_payment_gateway_secret');
        register_setting('apwp_settings', 'apwp_payment_gateway_sandbox', [
            'type' => 'boolean',
            'default' => true
        ]);

        // Configurações de Email
        register_setting('apwp_settings', 'apwp_smtp_settings', [
            'type' => 'array',
            'sanitize_callback' => [self::class, 'sanitizeArrayOption'],
            'default' => [
                'host' => '',
                'port' => '',
                'secure' => 'tls',
                'auth' => true,
                'username' => '',
                'password' => ''
            ]
        ]);

        register_setting('apwp_settings', 'apwp_email_templates', [
            'type' => 'array',
            'sanitize_callback' => [self::class, 'sanitizeArrayOption'],
            'default' => [
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
            ]
        ]);

        // Configurações de QR Code
        register_setting('apwp_settings', 'apwp_qrcode_settings', [
            'type' => 'array',
            'sanitize_callback' => [self::class, 'sanitizeArrayOption'],
            'default' => [
                'size' => 300,
                'margin' => 10,
                'foreground_color' => '#000000',
                'background_color' => '#FFFFFF',
                'error_correction' => 'M',
                'logo_enabled' => true,
                'logo_size' => 50,
                'logo_path' => ''
            ]
        ]);

        // Configurações de workflow
        register_setting('apwp_settings', 'apwp_adoption_workflow', [
            'type' => 'array',
            'sanitize_callback' => [self::class, 'sanitizeArrayOption'],
            'default' => [
                'require_home_visit' => true,
                'require_adoption_fee' => true,
                'require_terms_acceptance' => true,
                'require_adopter_documents' => true
            ]
        ]);

        register_setting('apwp_settings', 'apwp_notification_workflow', [
            'type' => 'array',
            'sanitize_callback' => [self::class, 'sanitizeArrayOption'],
            'default' => [
                'notify_new_adoption' => true,
                'notify_adoption_status' => true,
                'notify_new_donation' => true,
                'notify_new_volunteer' => true
            ]
        ]);

        // Configurações de email
        register_setting('apwp_settings', 'apwp_email_settings', [
            'type' => 'array',
            'sanitize_callback' => [self::class, 'sanitizeArrayOption'],
            'default' => [
                'from_name' => get_bloginfo('name'),
                'from_email' => get_bloginfo('admin_email'),
                'adoption_approved_template' => 'Parabéns! Sua adoção foi aprovada.',
                'adoption_rejected_template' => 'Infelizmente sua adoção não foi aprovada.',
                'donation_received_template' => 'Obrigado pela sua doação!',
                'volunteer_application_template' => 'Obrigado por se voluntariar!'
            ]
        ]);

        // Termos e condições
        register_setting('apwp_settings', 'apwp_terms_settings', [
            'type' => 'array',
            'sanitize_callback' => [self::class, 'sanitizeArrayOption'],
            'default' => [
                'adoption_terms' => '',
                'donation_terms' => '',
                'volunteer_terms' => ''
            ]
        ]);
    }

    public static function getAll(): array {
        return [
            'organization_name' => get_option('apwp_organization_name'),
            'organization_email' => get_option('apwp_organization_email'),
            'organization_phone' => get_option('apwp_organization_phone'),
            'google_maps_key' => get_option('apwp_google_maps_key'),
            'petfinder_key' => get_option('apwp_petfinder_key'),
            'payment_gateway_key' => get_option('apwp_payment_gateway_key'),
            'payment_gateway_secret' => get_option('apwp_payment_gateway_secret'),
            'payment_gateway_sandbox' => get_option('apwp_payment_gateway_sandbox'),
            'smtp_settings' => get_option('apwp_smtp_settings'),
            'email_templates' => get_option('apwp_email_templates'),
            'qrcode_settings' => get_option('apwp_qrcode_settings'),
            'adoption_workflow' => get_option('apwp_adoption_workflow'),
            'notification_workflow' => get_option('apwp_notification_workflow'),
            'email_settings' => get_option('apwp_email_settings'),
            'terms_settings' => get_option('apwp_terms_settings')
        ];
    }

    public static function save(array $settings): void {
        foreach ($settings as $key => $value) {
            update_option('apwp_' . $key, $value);
        }
    }
}
