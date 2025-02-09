<?php

namespace AmigoPetWp\Domain\Security;

class RoleManager {
    // Capabilities do plugin
    private static $capabilities = [
        'manage_amigopet',
        'manage_amigopet_pets',
        'manage_amigopet_adoptions',
        'manage_amigopet_events',
        'manage_amigopet_volunteers',
        'manage_amigopet_donations',
        'manage_amigopet_terms',
        'view_amigopet_reports',
        'manage_amigopet_settings',
        'edit_amigopet_pets',
        'delete_amigopet_pets',
        'publish_amigopet_pets',
        'edit_amigopet_adopters',
        'delete_amigopet_adopters',
        'edit_amigopet_adoptions',
        'delete_amigopet_adoptions',
        'edit_apwp_term',
        'read_apwp_term',
        'delete_apwp_term',
        'edit_apwp_terms',
        'edit_others_apwp_terms',
        'publish_apwp_terms',
        'read_private_apwp_terms'
    ];

    // Mapeamento de roles do WordPress para capabilities do plugin
    private static $roleCapabilities = [ilities = [
        'administrator' => [
            // Administrador tem acesso total
            'manage_amigopet' => true,
            'manage_amigopet_pets' => true,
            'manage_amigopet_adoptions' => true,
            'manage_amigopet_events' => true,
            'manage_amigopet_volunteers' => true,
            'manage_amigopet_donations' => true,
            'manage_amigopet_terms' => true,
            'view_amigopet_reports' => true,
            'manage_amigopet_settings' => true,
            
            // Capacidades para termos
            'edit_apwp_term' => true,
            'read_apwp_term' => true,
            'delete_apwp_term' => true,
            'edit_apwp_terms' => true,
            'edit_others_apwp_terms' => true,
            'publish_apwp_terms' => true,
            'read_private_apwp_terms' => true,
            'edit_amigopet_pets' => true,
            'delete_amigopet_pets' => true,
            'publish_amigopet_pets' => true,
            'edit_amigopet_adopters' => true,
            'delete_amigopet_adopters' => true,
            'edit_amigopet_adoptions' => true,
            'delete_amigopet_adoptions' => true
        ],
        'editor' => [
            // Editor tem acesso a gerenciamento de pets e adoções
            'manage_amigopet' => true,
            'manage_amigopet_pets' => true,
            'manage_amigopet_adoptions' => true,
            // Capabilities do post type Pet
            'edit_apwp_pet' => true,
            'read_apwp_pet' => true,
            'edit_apwp_pets' => true,
            'edit_others_apwp_pets' => true,
            'publish_apwp_pets' => true,
            // Outras capabilities
            'edit_amigopet_adopters' => true,
            'edit_amigopet_adoptions' => true
        ],
        'author' => [
            // Autor pode gerenciar pets
            'manage_amigopet' => true,
            'manage_amigopet_pets' => true,
            // Capabilities do post type Pet
            'edit_apwp_pet' => true,
            'read_apwp_pet' => true,
            'edit_apwp_pets' => true,
            'publish_apwp_pets' => true
        ]
    ];

    /**
     * Activate the role manager - called during plugin activation
     */
    public static function activate(): void {
        error_log('RoleManager::activate chamado');
        self::addRoles();
    }

    /**
     * Deactivate the role manager - called during plugin deactivation
     */
    public static function deactivate(): void {
        self::removeRoles();
    }

    /**
     * Add custom roles for the AmigoPet plugin
     */
    private static function addRoles(): void {
        error_log('RoleManager::addRoles chamado');
        
        // Adiciona capabilities aos papéis existentes do WordPress
        foreach (self::$roleCapabilities as $role_name => $capabilities) {
            error_log('Adicionando capabilities para role: ' . $role_name);
            $role = get_role($role_name);
            
            if ($role) {
                foreach ($capabilities as $cap => $grant) {
                    error_log('Adicionando capability: ' . $cap);
                    $role->add_cap($cap, $grant);
                }
            } else {
                error_log('Role não encontrada: ' . $role_name);
            }
        }
    }

    /**
     * Remove custom roles for the AmigoPet plugin
     */
    private static function removeRoles(): void {
        // Remove capabilities dos papéis existentes do WordPress
        foreach (self::$roleCapabilities as $role_name => $capabilities) {
            $role = get_role($role_name);
            
            if ($role) {
                foreach ($capabilities as $cap => $grant) {
                    $role->remove_cap($cap);
                }
            }
        }
    }

    /**
     * Uninstall the role manager - called during plugin uninstallation
     */
    public static function uninstall(): void {
        self::removeRoles();
    }
}
