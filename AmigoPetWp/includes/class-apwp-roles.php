<?php

/**
 * Gerencia os papéis e capacidades do plugin.
 *
 * @since      1.0.0
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 */
class APWP_Roles {

    /**
     * Inicializa os papéis e capacidades do plugin.
     *
     * @since    1.0.0
     */
    public static function init() {
        // Adiciona os papéis personalizados
        self::add_roles();
        
        // Adiciona as capacidades aos papéis existentes
        self::add_capabilities();
    }

    /**
     * Adiciona os papéis personalizados do plugin.
     *
     * @since    1.0.0
     */
    private static function add_roles() {
        // Papel para ONGs e abrigos
        add_role(
            'apwp_organization',
            __('Organização', 'amigopet-wp'),
            array(
                'read' => true,
                'edit_posts' => false,
                'delete_posts' => false,
                'upload_files' => true,
                'apwp_manage_pets' => true,
                'apwp_manage_adoptions' => true,
                'apwp_view_reports' => true
            )
        );

        // Papel para adotantes
        add_role(
            'apwp_adopter',
            __('Adotante', 'amigopet-wp'),
            array(
                'read' => true,
                'edit_posts' => false,
                'delete_posts' => false,
                'apwp_submit_adoption' => true,
                'apwp_view_adoption_status' => true
            )
        );
    }

    /**
     * Adiciona as capacidades aos papéis existentes.
     *
     * @since    1.0.0
     */
    private static function add_capabilities() {
        // Obtém o papel de administrador
        $admin = get_role('administrator');

        // Capacidades para gerenciar pets
        $admin->add_cap('apwp_manage_pets');
        $admin->add_cap('apwp_edit_pet');
        $admin->add_cap('apwp_delete_pet');
        $admin->add_cap('apwp_view_pets');

        // Capacidades para gerenciar adoções
        $admin->add_cap('apwp_manage_adoptions');
        $admin->add_cap('apwp_edit_adoption');
        $admin->add_cap('apwp_delete_adoption');
        $admin->add_cap('apwp_view_adoptions');

        // Capacidades para gerenciar organizações
        $admin->add_cap('apwp_manage_organizations');
        $admin->add_cap('apwp_edit_organization');
        $admin->add_cap('apwp_delete_organization');
        $admin->add_cap('apwp_view_organizations');

        // Capacidades para gerenciar adotantes
        $admin->add_cap('apwp_manage_adopters');
        $admin->add_cap('apwp_edit_adopter');
        $admin->add_cap('apwp_delete_adopter');
        $admin->add_cap('apwp_view_adopters');

        // Capacidades para relatórios
        $admin->add_cap('apwp_view_reports');
        $admin->add_cap('apwp_export_reports');
    }

    /**
     * Remove os papéis e capacidades do plugin.
     *
     * @since    1.0.0
     */
    public static function remove_roles() {
        // Remove os papéis personalizados
        remove_role('apwp_organization');
        remove_role('apwp_adopter');

        // Remove as capacidades do administrador
        $admin = get_role('administrator');
        if ($admin) {
            // Capacidades para gerenciar pets
            $admin->remove_cap('apwp_manage_pets');
            $admin->remove_cap('apwp_edit_pet');
            $admin->remove_cap('apwp_delete_pet');
            $admin->remove_cap('apwp_view_pets');

            // Capacidades para gerenciar adoções
            $admin->remove_cap('apwp_manage_adoptions');
            $admin->remove_cap('apwp_edit_adoption');
            $admin->remove_cap('apwp_delete_adoption');
            $admin->remove_cap('apwp_view_adoptions');

            // Capacidades para gerenciar organizações
            $admin->remove_cap('apwp_manage_organizations');
            $admin->remove_cap('apwp_edit_organization');
            $admin->remove_cap('apwp_delete_organization');
            $admin->remove_cap('apwp_view_organizations');

            // Capacidades para gerenciar adotantes
            $admin->remove_cap('apwp_manage_adopters');
            $admin->remove_cap('apwp_edit_adopter');
            $admin->remove_cap('apwp_delete_adopter');
            $admin->remove_cap('apwp_view_adopters');

            // Capacidades para relatórios
            $admin->remove_cap('apwp_view_reports');
            $admin->remove_cap('apwp_export_reports');
        }
    }
}
