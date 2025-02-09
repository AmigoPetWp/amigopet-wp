<?php
/**
 * Plugin Name:       AmigoPet WP
 * Plugin URI:        https://github.com/wendelmax/amigopet-wp
 * Description:       Sistema completo de gestao de adocao de animais para ONGs e abrigos.
 * Version:           1.1.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Jackson Sa
 * Author URI:        https://github.com/wendelmax
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       amigopet-wp
 * Domain Path:       /languages
 * Plugin Icon:       assets/images/icon-128x128.png
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define('AMIGOPET_WP_VERSION', '1.1.0');
define('AMIGOPET_WP_PLUGIN_NAME', 'amigopet-wp');
define('AMIGOPET_WP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AMIGOPET_WP_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Autoloader para as classes do plugin
 */
spl_autoload_register(function ($class) {
    // Prefixo base do namespace do plugin
    $prefix = 'AmigoPet\\';
    $base_dir = plugin_dir_path(__FILE__);

    // Verifica se a classe usa o prefixo do namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Pega o caminho relativo da classe
    $relative_class = substr($class, $len);

    // Substitui o namespace pelo caminho do diretório e \ por /
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Se o arquivo existir, carrega-o
    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Classe principal do plugin
 */
class AmigoPetWp {
    private static $instance = null;
    private $admin_controller;
    private $public_controller;

    private function __construct() {
        // Registra os hooks de ativação e desativação
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        // Inicializa os controllers
        if (is_admin()) {
            $this->admin_controller = new \AmigoPet\Controllers\AdminController();
        }
        $this->public_controller = new \AmigoPet\Controllers\PublicController();

        // Registra o hook de inicialização
        add_action('init', [$this, 'init']);

        // Registra o hook de internacionalização
        add_action('plugins_loaded', [$this, 'loadTextdomain']);
    }

    /**
     * Singleton
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Ativação do plugin
     */
    public function activate(): void {
        // Cria as tabelas do banco de dados
        $migration = new AmigoPet\Domain\Database\Migrations\CreateTables();
        $migration->up();

        // Registra os papéis e capacidades
        AmigoPet\Domain\Security\RoleManager::activate();

        // Limpa o cache de rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Desativação do plugin
     */
    public function deactivate(): void {
        // Remove os papéis e capacidades
        AmigoPet\Domain\Security\RoleManager::deactivate();

        // Limpa o cache de rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Inicialização do plugin
     */
    public function init(): void {
        // Registra os post types
        $this->registerPostTypes();

        // Registra as taxonomias
        $this->registerTaxonomies();
    }

    /**
     * Carrega o arquivo de tradução
     */
    public function loadTextdomain(): void {
        load_plugin_textdomain(
            'amigopet-wp',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }

    /**
     * Registra os post types
     */
    private function registerPostTypes(): void {
        // Post type para pets
        register_post_type('apwp_pet', [
            'labels' => [
                'name' => __('Pets', 'amigopet-wp'),
                'singular_name' => __('Pet', 'amigopet-wp')
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'menu_icon' => 'dashicons-pets',
            'show_in_rest' => true
        ]);

        // Post type para eventos
        register_post_type('apwp_event', [
            'labels' => [
                'name' => __('Eventos', 'amigopet-wp'),
                'singular_name' => __('Evento', 'amigopet-wp')
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'menu_icon' => 'dashicons-calendar-alt',
            'show_in_rest' => true
        ]);
    }

    /**
     * Registra as taxonomias
     */
    private function registerTaxonomies(): void {
        // Taxonomia para espécies
        register_taxonomy('apwp_species', ['apwp_pet'], [
            'labels' => [
                'name' => __('Espécies', 'amigopet-wp'),
                'singular_name' => __('Espécie', 'amigopet-wp')
            ],
            'hierarchical' => true,
            'show_in_rest' => true
        ]);

        // Taxonomia para raças
        register_taxonomy('apwp_breed', ['apwp_pet'], [
            'labels' => [
                'name' => __('Raças', 'amigopet-wp'),
                'singular_name' => __('Raça', 'amigopet-wp')
            ],
            'hierarchical' => true,
            'show_in_rest' => true
        ]);

        // Taxonomia para tipos de eventos
        register_taxonomy('apwp_event_type', ['apwp_event'], [
            'labels' => [
                'name' => __('Tipos de Evento', 'amigopet-wp'),
                'singular_name' => __('Tipo de Evento', 'amigopet-wp')
            ],
            'hierarchical' => true,
            'show_in_rest' => true
        ]);
    }
}

// Inicializa o plugin
AmigoPetWp::getInstance();
