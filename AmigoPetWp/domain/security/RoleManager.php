<?php
namespace AmigoPet\Domain\Security;

class RoleManager {
    /**
     * Inicializa os papéis e capacidades
     */
    public static function init(): void {
        self::addRoles();
        self::addCapabilities();
    }

    /**
     * Adiciona os papéis personalizados
     */
    private static function addRoles(): void {
        // Papel para ONGs e abrigos
        add_role(
            'apwp_organization',
            __('Organização', 'amigopet-wp'),
            [
                'read' => true,
                'upload_files' => true,
                'apwp_manage_pets' => true,
                'apwp_manage_adoptions' => true,
                'apwp_view_reports' => true,
                'apwp_manage_events' => true,
                'apwp_manage_volunteers' => true,
                'apwp_manage_donations' => true
            ]
        );

        // Papel para adotantes
        add_role(
            'apwp_adopter',
            __('Adotante', 'amigopet-wp'),
            [
                'read' => true,
                'apwp_view_pets' => true,
                'apwp_apply_adoption' => true,
                'apwp_view_events' => true,
                'apwp_make_donation' => true
            ]
        );

        // Papel para voluntários
        add_role(
            'apwp_volunteer',
            __('Voluntário', 'amigopet-wp'),
            [
                'read' => true,
                'upload_files' => true,
                'apwp_view_pets' => true,
                'apwp_assist_adoptions' => true,
                'apwp_view_events' => true,
                'apwp_manage_events' => true
            ]
        );
    }

    /**
     * Adiciona capacidades aos papéis existentes
     */
    private static function addCapabilities(): void {
        // Adiciona capacidades ao administrador
        $admin = get_role('administrator');
        if ($admin) {
            $capabilities = [
                'apwp_manage_pets',
                'apwp_manage_adoptions',
                'apwp_view_reports',
                'apwp_manage_events',
                'apwp_manage_volunteers',
                'apwp_manage_donations',
                'apwp_manage_organizations',
                'apwp_manage_settings'
            ];

            foreach ($capabilities as $cap) {
                $admin->add_cap($cap);
            }
        }
    }

    /**
     * Remove todos os papéis e capacidades
     */
    public static function uninstall(): void {
        // Remove os papéis
        remove_role('apwp_organization');
        remove_role('apwp_adopter');
        remove_role('apwp_volunteer');

        // Remove capacidades do administrador
        $admin = get_role('administrator');
        if ($admin) {
            $capabilities = [
                'apwp_manage_pets',
                'apwp_manage_adoptions',
                'apwp_view_reports',
                'apwp_manage_events',
                'apwp_manage_volunteers',
                'apwp_manage_donations',
                'apwp_manage_organizations',
                'apwp_manage_settings'
            ];

            foreach ($capabilities as $cap) {
                $admin->remove_cap($cap);
            }
        }
    }
}
