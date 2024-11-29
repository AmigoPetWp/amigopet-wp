<?php
/**
 * Plugin Name:       AmigoPet WP
 * Plugin URI:        https://github.com/wendelmax/amigopet-wp
 * Description:       Sistema completo de gestao de adocao de animais para ONGs e abrigos.
 * Version:           1.7.7
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Jackson Sa
 * Author URI:        https://github.com/wendelmax
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       amigopet-wp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define('AMIGOPET_WP_VERSION', '1.0.0');
define('AMIGOPET_WP_PLUGIN_NAME', 'amigopet-wp');
define('AMIGOPET_WP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AMIGOPET_WP_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_amigopet_wp() {
    require_once AMIGOPET_WP_PLUGIN_DIR . 'includes/class-apwp-activator.php';
    require_once AMIGOPET_WP_PLUGIN_DIR . 'includes/class-apwp-roles.php';
    
    APWP_Activator::activate();
    APWP_Roles::init();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_amigopet_wp() {
    require_once AMIGOPET_WP_PLUGIN_DIR . 'includes/class-apwp-deactivator.php';
    require_once AMIGOPET_WP_PLUGIN_DIR . 'includes/class-apwp-roles.php';
    
    APWP_Deactivator::deactivate();
    APWP_Roles::remove_roles();
}

register_activation_hook(__FILE__, 'activate_amigopet_wp');
register_deactivation_hook(__FILE__, 'deactivate_amigopet_wp');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require AMIGOPET_WP_PLUGIN_DIR . 'includes/class-amigopet-wp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_amigopet_wp() {
    $plugin = new AmigoPet_Wp();
    $plugin->run();
}

run_amigopet_wp();
