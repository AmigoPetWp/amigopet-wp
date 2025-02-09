<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * @package    AmigoPet_Wp
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Important: We do not need to include the main plugin file here.
// WordPress loads only this file during uninstallation.

/**
 * Clean up plugin data
 */
function apwp_uninstall() {
    global $wpdb;

    // Only do cleanup if the AMIGOPET_WP_DELETE_DATA constant is true
    // This constant can be defined in wp-config.php
    if (!defined('AMIGOPET_WP_DELETE_DATA') || !AMIGOPET_WP_DELETE_DATA) {
        return;
    }

    // Remove papéis e capacidades
    require_once plugin_dir_path(__FILE__) . 'domain/security/RoleManager.php';
    AmigoPet\Domain\Security\RoleManager::uninstall();

    // Get all plugin tables
    $tables = array(
        // Tabelas base
        $wpdb->prefix . 'amigopet_term_types',
        $wpdb->prefix . 'amigopet_pet_species',
        $wpdb->prefix . 'amigopet_pet_breeds',
        
        // Tabelas principais
        $wpdb->prefix . 'amigopet_organizations',
        $wpdb->prefix . 'amigopet_qrcodes',
        $wpdb->prefix . 'amigopet_pets',
        $wpdb->prefix . 'amigopet_adopters',
        $wpdb->prefix . 'amigopet_adoptions',
        $wpdb->prefix . 'amigopet_volunteers',
        $wpdb->prefix . 'amigopet_terms',
        $wpdb->prefix . 'amigopet_donations',
        $wpdb->prefix . 'amigopet_events',
        $wpdb->prefix . 'amigopet_adoption_payments',
        $wpdb->prefix . 'amigopet_signed_terms'
    );

    // Drop all plugin tables in reverse order (devido às foreign keys)
    foreach (array_reverse($tables) as $table) {
        $wpdb->query("DROP TABLE IF EXISTS $table");
    }

    // Delete all plugin options
    $options = array(
        'apwp_display_settings',
        'apwp_general_settings',
        'apwp_version',
        'apwp_db_version',
        'apwp_installation_time',
        'apwp_do_activation_redirect'
    );

    foreach ($options as $option) {
        delete_option($option);
    }

    // Delete all plugin transients
    delete_transient('apwp_display_settings_cache');
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_apwp_%'");
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_apwp_%'");

    // Remove custom roles
    remove_role('apwp_adopter');
    remove_role('apwp_advertiser');
    remove_role('apwp_organization');

    // Remove capabilities from administrator
    $admin_role = get_role('administrator');
    if ($admin_role) {
        $caps_to_remove = array(
            'apwp_manage_plugin',
            'apwp_manage_settings',
            'apwp_manage_roles',
            'apwp_import_export',
            'apwp_manage_all_animals',
            'apwp_manage_animals',
            'apwp_create_animal',
            'apwp_edit_own_animals',
            'apwp_delete_own_animals',
            'apwp_submit_application',
            'apwp_view_own_applications',
            'apwp_view_applications',
            'apwp_process_applications',
            'apwp_manage_all_applications',
            'apwp_manage_organizations',
            'apwp_manage_organization',
            'apwp_edit_organization',
            'apwp_view_organizations',
            'apwp_view_animals',
            'apwp_edit_own_profile',
            'apwp_view_statistics'
        );

        foreach ($caps_to_remove as $cap) {
            $admin_role->remove_cap($cap);
        }
    }

    // Clean up upload directories
    $upload_dir = wp_upload_dir();
    
    // Remove plugin upload directory
    $apwp_upload_dir = $upload_dir['basedir'] . '/amigopet-wp';
    apwp_remove_directory($apwp_upload_dir);
    
    // Remove temporary directory
    $apwp_temp_dir = $upload_dir['basedir'] . '/apwp-temp';
    apwp_remove_directory($apwp_temp_dir);

    // Clear any scheduled cron jobs
    wp_clear_scheduled_hook('apwp_daily_cleanup');

    // Clear any user meta data
    $wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key LIKE 'apwp_%'");
}

/**
 * Helper function to recursively remove a directory
 *
 * @param string $dir Directory path
 * @return bool
 */
function apwp_remove_directory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $files = array_diff(scandir($dir), array('.', '..'));
    
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        
        if (is_dir($path)) {
            apwp_remove_directory($path);
        } else {
            unlink($path);
        }
    }
    
    return rmdir($dir);
}

// Run uninstall function
apwp_uninstall();
