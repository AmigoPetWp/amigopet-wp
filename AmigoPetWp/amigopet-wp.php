<?php
/**
 * Plugin Name:       AmigoPet WP
 * Plugin URI:        https://github.com/wendelmax/amigopet-wp
 * Description:       Sistema completo de gestão de adoção de animais para ONGs e abrigos.
 * Version:           2.0.0
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
define('AMIGOPET_WP_VERSION', '2.0.0');
define('AMIGOPET_WP_PLUGIN_NAME', 'amigopet-wp');
define('AMIGOPET_WP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AMIGOPET_WP_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Autoloader para as classes do plugin
 */
spl_autoload_register(function ($class) {
    // Prefixo base do namespace do plugin
    $prefix = 'AmigoPetWp\\';
    $base_dir = plugin_dir_path(__FILE__) . 'AmigoPet/';

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
    } else {
        error_log('AmigoPet WP: Arquivo não encontrado: ' . $file);
    }
});

/**
 * Classe principal do plugin
 */
class AmigoPetWp {
    private static $instance = null;
    private $database;
    private $admin_controllers = [];
    private $public_controllers = [];

    private function __construct() {
        // Registra os hooks de ativação e desativação
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        // Inicializa o banco de dados
        $this->database = \AmigoPetWp\Domain\Database\Database::getInstance();

        // Inicializa os controllers administrativos
        if (is_admin()) {
            $this->initAdminControllers();
        }

        // Inicializa os controllers públicos
        $this->initPublicControllers();

        // Registra os hooks
        add_action('init', [$this, 'init']);
        add_action('plugins_loaded', [$this, 'loadTextdomain']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueuePublicAssets']);
    }

    private function initAdminControllers() {
        // Inicializa as configurações do plugin
        \AmigoPetWp\Domain\Settings\Settings::register();

        // Controllers Admin
        $this->admin_controllers = [
            new \AmigoPetWp\Controllers\Admin\DashboardController()
        ];

        // Registra os hooks de cada controller
        foreach ($this->admin_controllers as $controller) {
            if (method_exists($controller, 'registerHooks')) {
                $controller->registerHooks();
            }
        }
    }

    private function initPublicControllers() {
        // Controllers Public
        $this->public_controllers = [
            // Adicione os controllers públicos aqui
        ];

        // Registra os hooks de cada controller
        foreach ($this->public_controllers as $controller) {
            if (method_exists($controller, 'registerHooks')) {
                $controller->registerHooks();
            }
        }
    }

    public function enqueueAdminAssets() {
        // CSS Admin
        wp_enqueue_style(
            'amigopet-admin',
            AMIGOPET_WP_PLUGIN_URL . 'assets/css/admin.css',
            [],
            AMIGOPET_WP_VERSION
        );

        // JavaScript Admin
        wp_enqueue_script(
            'amigopet-admin',
            AMIGOPET_WP_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery', 'jquery-ui-datepicker'],
            AMIGOPET_WP_VERSION,
            true
        );

        // Localize script
        wp_localize_script('amigopet-admin', 'amigopetAdmin', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('amigopet-admin-nonce')
        ]);
    }

    public function enqueuePublicAssets() {
        // CSS Public
        wp_enqueue_style(
            'amigopet-public',
            AMIGOPET_WP_PLUGIN_URL . 'assets/css/public.css',
            [],
            AMIGOPET_WP_VERSION
        );

        // JavaScript Public
        wp_enqueue_script(
            'amigopet-public',
            AMIGOPET_WP_PLUGIN_URL . 'assets/js/public.js',
            ['jquery'],
            AMIGOPET_WP_VERSION,
            true
        );
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
        try {
            // Executa migrations na ativação
            $migrationService = \AmigoPetWp\Domain\Database\MigrationService::getInstance();
            $results = $migrationService->migrate();

            // Verifica se houve erro nas migrations
            $errors = array_filter($results, function($result) {
                return $result['status'] === 'error';
            });

            if (!empty($errors)) {
                $errorMessages = array_map(function($error) {
                    return 'Migration ' . $error['version'] . ': ' . $error['message'];
                }, $errors);
                
                throw new \Exception(
                    "Erros durante a execução das migrations:\n" . 
                    implode("\n", $errorMessages)
                );
            }

            // Registra os papéis e capacidades
            try {
                \AmigoPetWp\Domain\Security\RoleManager::activate();
            } catch (\Exception $e) {
                error_log('AmigoPet WP: Erro ao configurar papéis e capacidades: ' . $e->getMessage());
                throw new \Exception('Erro ao configurar papéis e capacidades: ' . $e->getMessage());
            }

            // Seeds são executados como parte das migrations

            try {
                // Adiciona as configurações padrão
                $this->addDefaultSettings();

                // Limpa o cache de rewrite rules
                flush_rewrite_rules();
            } catch (\Exception $e) {
                error_log('AmigoPet WP: Erro ao configurar plugin: ' . $e->getMessage());
                throw new \Exception('Erro ao configurar plugin: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            $errorMessage = 'Erro ao ativar plugin AmigoPet WP: ' . $e->getMessage();
            error_log('AmigoPet WP: ' . $errorMessage);
            throw new \Exception($errorMessage);
        }
    }

    /**
     * Adiciona as configurações padrão do plugin
     */
    private function addDefaultSettings(): void {
        // Configurações da organização
        add_option('apwp_organization_name', '');
        add_option('apwp_organization_email', '');
        add_option('apwp_organization_phone', '');
        add_option('apwp_google_maps_key', '');
        
        // Configurações de workflow
        add_option('apwp_adoption_workflow', [
            'require_home_visit' => true,
            'require_adoption_fee' => true,
            'require_terms_acceptance' => true,
            'require_adopter_documents' => true
        ]);

        add_option('apwp_notification_workflow', [
            'notify_new_adoption' => true,
            'notify_adoption_status' => true,
            'notify_new_donation' => true,
            'notify_new_volunteer' => true
        ]);

        // Configurações de email
        add_option('apwp_email_settings', [
            'from_name' => get_bloginfo('name'),
            'from_email' => get_bloginfo('admin_email'),
            'adoption_approved_template' => 'Parabéns! Sua adoção foi aprovada.',
            'adoption_rejected_template' => 'Infelizmente sua adoção não foi aprovada.',
            'donation_received_template' => 'Obrigado pela sua doação!',
            'volunteer_application_template' => 'Obrigado por se voluntariar!'
        ]);

        // Termos e condições
        add_option('apwp_terms_settings', [
            'adoption_terms' => '',
            'donation_terms' => '',
            'volunteer_terms' => ''
        ]);
    }



    /**
     * Desativação do plugin
     */
    public function deactivate(): void {
        try {
            // Remove todas as tabelas do plugin
            $migrationService = \AmigoPetWp\Domain\Database\MigrationService::getInstance();
            $migrationService->dropAllTables();

            // Remove os papéis e capacidades
            \AmigoPetWp\Domain\Security\RoleManager::deactivate();

            // Remove as opções do plugin
            $this->removePluginOptions();

            // Limpa o cache de rewrite rules
            flush_rewrite_rules();
        } catch (\Exception $e) {
            error_log('AmigoPet WP: Erro ao desativar plugin: ' . $e->getMessage());
        }
    }

    /**
     * Remove as opções do plugin
     */
    private function removePluginOptions(): void {
        delete_option('apwp_organization_name');
        delete_option('apwp_organization_email');
        delete_option('apwp_organization_phone');
        delete_option('apwp_google_maps_key');
        delete_option('apwp_adoption_workflow');
        delete_option('apwp_notification_workflow');
        delete_option('apwp_email_settings');
        delete_option('apwp_terms_settings');
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
        register_post_type('pet', [
            'labels' => [
                'name' => __('Pets', 'amigopet-wp'),
                'singular_name' => __('Pet', 'amigopet-wp'),
                'add_new' => __('Adicionar Novo', 'amigopet-wp'),
                'add_new_item' => __('Adicionar Novo Pet', 'amigopet-wp'),
                'edit_item' => __('Editar Pet', 'amigopet-wp'),
                'new_item' => __('Novo Pet', 'amigopet-wp'),
                'view_item' => __('Ver Pet', 'amigopet-wp'),
                'search_items' => __('Buscar Pets', 'amigopet-wp'),
                'not_found' => __('Nenhum pet encontrado', 'amigopet-wp'),
                'not_found_in_trash' => __('Nenhum pet encontrado na lixeira', 'amigopet-wp'),
                'menu_name' => __('Pets', 'amigopet-wp')
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'menu_icon' => 'dashicons-pets',
            'rewrite' => ['slug' => 'pets']
        ]);

        // Post type para eventos
        register_post_type('event', [
            'labels' => [
                'name' => __('Eventos', 'amigopet-wp'),
                'singular_name' => __('Evento', 'amigopet-wp'),
                'add_new' => __('Adicionar Novo', 'amigopet-wp'),
                'add_new_item' => __('Adicionar Novo Evento', 'amigopet-wp'),
                'edit_item' => __('Editar Evento', 'amigopet-wp'),
                'new_item' => __('Novo Evento', 'amigopet-wp'),
                'view_item' => __('Ver Evento', 'amigopet-wp'),
                'search_items' => __('Buscar Eventos', 'amigopet-wp'),
                'not_found' => __('Nenhum evento encontrado', 'amigopet-wp'),
                'not_found_in_trash' => __('Nenhum evento encontrado na lixeira', 'amigopet-wp'),
                'menu_name' => __('Eventos', 'amigopet-wp')
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'menu_icon' => 'dashicons-calendar-alt',
            'rewrite' => ['slug' => 'eventos']
        ]);

        // Post type para doações
        register_post_type('donation', [
            'labels' => [
                'name' => __('Doações', 'amigopet-wp'),
                'singular_name' => __('Doação', 'amigopet-wp'),
                'add_new' => __('Adicionar Nova', 'amigopet-wp'),
                'add_new_item' => __('Adicionar Nova Doação', 'amigopet-wp'),
                'edit_item' => __('Editar Doação', 'amigopet-wp'),
                'new_item' => __('Nova Doação', 'amigopet-wp'),
                'view_item' => __('Ver Doação', 'amigopet-wp'),
                'search_items' => __('Buscar Doações', 'amigopet-wp'),
                'not_found' => __('Nenhuma doação encontrada', 'amigopet-wp'),
                'not_found_in_trash' => __('Nenhuma doação encontrada na lixeira', 'amigopet-wp'),
                'menu_name' => __('Doações', 'amigopet-wp')
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'menu_icon' => 'dashicons-heart',
            'rewrite' => ['slug' => 'doacoes']
        ]);
    }

    /**
     * Registra as taxonomias
     */
    private function registerTaxonomies(): void {
        // Taxonomia para espécies
        register_taxonomy('pet_species', ['pet'], [
            'labels' => [
                'name' => __('Espécies', 'amigopet-wp'),
                'singular_name' => __('Espécie', 'amigopet-wp'),
                'search_items' => __('Buscar Espécies', 'amigopet-wp'),
                'all_items' => __('Todas as Espécies', 'amigopet-wp'),
                'edit_item' => __('Editar Espécie', 'amigopet-wp'),
                'update_item' => __('Atualizar Espécie', 'amigopet-wp'),
                'add_new_item' => __('Adicionar Nova Espécie', 'amigopet-wp'),
                'new_item_name' => __('Nova Espécie', 'amigopet-wp'),
                'menu_name' => __('Espécies', 'amigopet-wp')
            ],
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'especies']
        ]);

        // Taxonomia para raças
        register_taxonomy('pet_breed', ['pet'], [
            'labels' => [
                'name' => __('Raças', 'amigopet-wp'),
                'singular_name' => __('Raça', 'amigopet-wp'),
                'search_items' => __('Buscar Raças', 'amigopet-wp'),
                'all_items' => __('Todas as Raças', 'amigopet-wp'),
                'edit_item' => __('Editar Raça', 'amigopet-wp'),
                'update_item' => __('Atualizar Raça', 'amigopet-wp'),
                'add_new_item' => __('Adicionar Nova Raça', 'amigopet-wp'),
                'new_item_name' => __('Nova Raça', 'amigopet-wp'),
                'menu_name' => __('Raças', 'amigopet-wp')
            ],
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'racas']
        ]);
    }
}

// Inicializa o plugin
AmigoPetWp::getInstance();
