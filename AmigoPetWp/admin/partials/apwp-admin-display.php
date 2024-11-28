<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/
 * @since      1.0.0
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/admin/partials
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    <div class="amigopet-admin-header">
        <img src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'images/logo.svg'; ?>" alt="AmigoPet WP Logo" class="amigopet-logo">
    </div>
    <!-- Add your admin page content here -->
    <div class="amigopet-admin-content">
        <h3><?php _e('Welcome to AmigoPet WP', 'amigopet-wp'); ?></h3>
        <p><?php _e('Configure your pet adoption management settings here.', 'amigopet-wp'); ?></p>
    </div>
</div>
