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
    // Log para debug
    error_log("Tentando carregar classe: " . $class);

    // Prefixo base do namespace do plugin
    $prefix = 'AmigoPetWp\\';
    $base_dir = plugin_dir_path(__FILE__) . 'AmigoPet/';

    // Verifica se a classe usa o prefixo do namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        error_log("Classe não pertence ao nosso namespace: " . $class);
        return;
    }

    // Pega o caminho relativo da classe
    $relative_class = substr($class, $len);

    // Substitui o namespace pelo caminho do diretório e \ por /
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Log do caminho do arquivo
    error_log("Tentando carregar arquivo: " . $file);

    // Se o arquivo existir, carrega-o
    if (file_exists($file)) {
        error_log("Arquivo encontrado: " . $file);
        require $file;
    } else {
        error_log("Arquivo não encontrado: " . $file);
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
        error_log("Iniciando construtor do AmigoPetWp");

        // Registra os hooks de ativação e desativação
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        // Inicializa os controllers
        if (is_admin()) {
            error_log("Tentando criar AdminController");
            $this->admin_controller = new \AmigoPetWp\Controllers\AdminController();
            error_log("AdminController criado com sucesso");
        }
        error_log("Tentando criar PublicController");
        $this->public_controller = new \AmigoPetWp\Controllers\PublicController();
        error_log("PublicController criado com sucesso");

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
        $migration = new \AmigoPetWp\Domain\Database\Migrations\CreateTables();
        $migration->up();

        // Registra os papéis e capacidades
        \AmigoPetWp\Domain\Security\RoleManager::activate();

        // Limpa o cache de rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Desativação do plugin
     */
    public function deactivate(): void {
        // Remove os papéis e capacidades
        \AmigoPetWp\Domain\Security\RoleManager::deactivate();

        // Limpa o cache de rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Inicialização do plugin
     */
    public function init(): void {
        // Registra os post types
        $this->registerPostTypes();
        error_log('Registrando post types');

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
        // Post type para termos
        register_post_type('apwp_term', [
            'labels' => [
                'name'               => __('Termos', 'amigopet-wp'),
                'singular_name'      => __('Termo', 'amigopet-wp'),
                'add_new'            => __('Adicionar Novo', 'amigopet-wp'),
                'add_new_item'       => __('Adicionar Novo Termo', 'amigopet-wp'),
                'edit_item'          => __('Editar Termo', 'amigopet-wp'),
                'new_item'           => __('Novo Termo', 'amigopet-wp'),
                'view_item'          => __('Ver Termo', 'amigopet-wp'),
                'search_items'       => __('Buscar Termos', 'amigopet-wp'),
                'not_found'          => __('Nenhum termo encontrado', 'amigopet-wp'),
                'not_found_in_trash' => __('Nenhum termo encontrado na lixeira', 'amigopet-wp'),
                'menu_name'          => __('Termos', 'amigopet-wp')
            ],
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'capability_type'     => 'apwp_term',
            'map_meta_cap'        => true,
            'supports'            => ['title', 'editor'],
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => null,
            'menu_icon'           => 'dashicons-media-document',
            'show_in_rest'        => true,
            'rest_base'           => 'terms',
            'capabilities'        => [
                'edit_post'          => 'edit_apwp_term',
                'read_post'          => 'read_apwp_term',
                'delete_post'        => 'delete_apwp_term',
                'edit_posts'         => 'edit_apwp_terms',
                'edit_others_posts'  => 'edit_others_apwp_terms',
                'publish_posts'      => 'publish_apwp_terms',
                'read_private_posts' => 'read_private_apwp_terms'
            ]
        ]);
        
        // Post type para pets
        register_post_type('apwp_pet', [
            'labels' => [
                'name'               => __('Pets', 'amigopet-wp'),
                'singular_name'      => __('Pet', 'amigopet-wp'),
                'add_new'            => __('Adicionar Novo', 'amigopet-wp'),
                'add_new_item'       => __('Adicionar Novo Pet', 'amigopet-wp'),
                'edit_item'          => __('Editar Pet', 'amigopet-wp'),
                'new_item'           => __('Novo Pet', 'amigopet-wp'),
                'view_item'          => __('Ver Pet', 'amigopet-wp'),
                'search_items'       => __('Buscar Pets', 'amigopet-wp'),
                'not_found'          => __('Nenhum pet encontrado', 'amigopet-wp'),
                'not_found_in_trash' => __('Nenhum pet encontrado na lixeira', 'amigopet-wp'),
                'menu_name'          => __('Pets', 'amigopet-wp')
            ],
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => 'amigopet-wp',
            'capability_type'     => 'apwp_pet',
            'map_meta_cap'        => true,
            'supports'            => ['title', 'editor', 'thumbnail', 'custom-fields'],
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => null,
            'menu_icon'           => 'dashicons-pets',
            'show_in_rest'        => true,
            'rest_base'           => 'pets',
            'capabilities'        => [
                'edit_post'          => 'edit_apwp_pet',
                'read_post'          => 'read_apwp_pet',
                'delete_post'        => 'delete_apwp_pet',
                'edit_posts'         => 'edit_apwp_pets',
                'edit_others_posts'  => 'edit_others_apwp_pets',
                'publish_posts'      => 'publish_apwp_pets',
                'read_private_posts' => 'read_private_apwp_pets'
            ]
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
