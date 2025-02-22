<?php
namespace AmigoPetWp\Domain\Settings;

class Settings {
    public static function register(): void {
        // Configurações da organização
        register_setting('apwp_settings', 'apwp_organization_name');
        register_setting('apwp_settings', 'apwp_organization_email');
        register_setting('apwp_settings', 'apwp_organization_phone');

        // Configurações de API
        register_setting('apwp_settings', 'apwp_google_maps_key');

        // Configurações de workflow
        register_setting('apwp_settings', 'apwp_adoption_workflow', [
            'type' => 'array',
            'sanitize_callback' => 'array_map',
            'default' => [
                'require_home_visit' => true,
                'require_adoption_fee' => true,
                'require_terms_acceptance' => true,
                'require_adopter_documents' => true
            ]
        ]);

        register_setting('apwp_settings', 'apwp_notification_workflow', [
            'type' => 'array',
            'sanitize_callback' => 'array_map',
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
            'sanitize_callback' => 'array_map',
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
            'sanitize_callback' => 'array_map',
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
