<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin Name:       AmigoPet
 * Plugin URI:        https://github.com/wendelmax/amigopet
 * Description:       Sistema completo de gestão de adoção de animais para ONGs e abrigos.
 * Version:           2.1.3
 * Requires at least: 6.2
 * Requires PHP:      8.0
 * Author:            Jackson Sa
 * Author URI:        https://github.com/wendelmax
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       amigopet
 * Domain Path:       /languages
 * Plugin Icon:       assets/images/icon-128x128.png
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Carrega o autoloader do Composer se existir
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define('AMIGOPET_VERSION', '2.1.3');
define('AMIGOPET_PLUGIN_NAME', 'amigopet');
define('AMIGOPET_PLUGIN_FILE', __FILE__);
define('AMIGOPET_PLUGIN_DIR', __DIR__ . '/');
define('AMIGOPET_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Autoloader fallback para as classes do plugin
 * Caso o Composer não tenha sido carregado ou falhe
 */
spl_autoload_register(function ($class) {
    $normalizedClass = ltrim((string) $class, '\\');
    $prefix = 'AmigoPetWp\\';

    if (strncmp($normalizedClass, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($normalizedClass, strlen($prefix));
    $file = __DIR__ . '/AmigoPet/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

/**
 * Classe principal do plugin
 */
class AmigoPetWp
{
    private static $instance = null;
    private $database;
    private $admin_controllers = [];
    private $public_controllers = [];

    private function __construct()
    {
        // Registra os hooks de ativação e desativação
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        // Inicializa o banco de dados
        if (!class_exists('\AmigoPetWp\Domain\Database\Database')) {
            $databaseFile = __DIR__ . '/AmigoPet/Domain/Database/Database.php';
            if (file_exists($databaseFile)) {
                require_once $databaseFile;
            }
        }

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

    private function initAdminControllers()
    {
        // Inicializa as configurações do plugin
        \AmigoPetWp\Domain\Settings\Settings::register();

        // Controllers Admin
        $this->admin_controllers = [
            new \AmigoPetWp\Controllers\Admin\DashboardController(),
            new \AmigoPetWp\Controllers\Admin\AdminPetController(),
            new \AmigoPetWp\Controllers\Admin\AdminAdoptionController(),
            new \AmigoPetWp\Controllers\Admin\AdminAdoptionDocumentController(),
            new \AmigoPetWp\Controllers\Admin\AdminAdoptionPaymentController(),
            new \AmigoPetWp\Controllers\Admin\AdminDonationController(),
            new \AmigoPetWp\Controllers\Admin\AdminEventController(),
            new \AmigoPetWp\Controllers\Admin\AdminVolunteerController(),
            new \AmigoPetWp\Controllers\Admin\AdminOrganizationController(),
            new \AmigoPetWp\Controllers\Admin\AdminPetBreedController(),
            new \AmigoPetWp\Controllers\Admin\AdminPetSpeciesController(),
            new \AmigoPetWp\Controllers\Admin\AdminSignedTermController(),
            new \AmigoPetWp\Controllers\Admin\AdminTermController(),
            new \AmigoPetWp\Controllers\Admin\AdminTermTypeController(),
            new \AmigoPetWp\Controllers\Admin\AdminTermVersionController(),
            new \AmigoPetWp\Controllers\Admin\SettingsController()
        ];
    }

    private function initPublicControllers()
    {
        // Controllers Public
        $this->public_controllers = [
            new \AmigoPetWp\Controllers\PublicController()
        ];
    }

    public function enqueueAdminAssets()
    {
        // CSS Admin
        wp_enqueue_style(
            'amigopet-admin',
            AMIGOPET_PLUGIN_URL . 'assets/css/admin.css',
            [],
            AMIGOPET_VERSION
        );

        // JavaScript Admin
        wp_enqueue_script(
            'amigopet-admin',
            AMIGOPET_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery', 'jquery-ui-datepicker'],
            AMIGOPET_VERSION,
            true
        );

        // Localize script
        wp_localize_script('amigopet-admin', 'amigopetAdmin', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('amigopet-admin-nonce')
        ]);
    }

    public function enqueuePublicAssets()
    {
        // CSS Public
        wp_enqueue_style(
            'amigopet-public',
            AMIGOPET_PLUGIN_URL . 'assets/css/public.css',
            [],
            AMIGOPET_VERSION
        );

        // JavaScript Public
        wp_enqueue_script(
            'amigopet-public',
            AMIGOPET_PLUGIN_URL . 'assets/js/public.js',
            ['jquery'],
            AMIGOPET_VERSION,
            true
        );
    }

    /**
     * Singleton
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Ativação do plugin
     */
    public function activate(): void
    {
        try {
            // Executa migrations na ativação
            $migrationService = \AmigoPetWp\Domain\Database\MigrationService::getInstance();
            $results = $migrationService->migrate();

            // Verifica se houve erro nas migrations
            $errors = array_filter($results, function ($result) {
                return $result['status'] === 'error';
            });

            if (!empty($errors)) {
                $errorMessages = array_map(function ($error) {
                    return 'Migration ' . $error['version'] . ': ' . $error['message'];
                }, $errors);

                throw new \Exception(
                    sprintf(
                        /* translators: %s: migration error details */
                        esc_html__("Erros durante a execução das migrations:\n%s", 'amigopet'),
                        esc_html(implode("\n", array_map('sanitize_text_field', $errorMessages)))
                    )
                );
            }

            // Registra os papéis e capacidades
            try {
                \AmigoPetWp\Domain\Security\RoleManager::activate();
            } catch (\Exception $e) {
                throw new \Exception(
                    sprintf(
                        /* translators: %s: original error message */
                        esc_html__('Erro ao configurar papéis e capacidades: %s', 'amigopet'),
                        esc_html(sanitize_text_field($e->getMessage()))
                    )
                );
            }

            // Seeds são executados como parte das migrations

            try {
                // Adiciona as configurações padrão
                $this->addDefaultSettings();

                // Limpa o cache de rewrite rules
                flush_rewrite_rules();
            } catch (\Exception $e) {
                throw new \Exception(
                    sprintf(
                        /* translators: %s: original error message */
                        esc_html__('Erro ao configurar plugin: %s', 'amigopet'),
                        esc_html(sanitize_text_field($e->getMessage()))
                    )
                );
            }
        } catch (\Exception $e) {
            throw new \Exception(
                sprintf(
                    /* translators: %s: original error message */
                    esc_html__('Erro ao ativar plugin AmigoPet: %s', 'amigopet'),
                    esc_html(sanitize_text_field(wp_strip_all_tags($e->getMessage())))
                )
            );
        }
    }

    /**
     * Adiciona as configurações padrão do plugin
     */
    private function addDefaultSettings(): void
    {
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
    public function deactivate(): void
    {
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
            // Error captured silently to avoid deactivation issues
        }
    }

    /**
     * Remove as opções do plugin
     */
    private function removePluginOptions(): void
    {
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
    public function init(): void
    {
        // Registra os post types
        $this->registerPostTypes();

        // Registra as taxonomias
        $this->registerTaxonomies();
    }

    /**
     * Carrega o arquivo de tradução
     */
    public function loadTextdomain(): void
    {
    }

    /**
     * Registra os post types
     */
    private function registerPostTypes(): void
    {
        // Post type para pets
        register_post_type('pet', [
            'labels' => [
                'name' => __('Pets', 'amigopet'),
                'singular_name' => __('Pet', 'amigopet'),
                'add_new' => __('Adicionar Novo', 'amigopet'),
                'add_new_item' => __('Adicionar Novo Pet', 'amigopet'),
                'edit_item' => __('Editar Pet', 'amigopet'),
                'new_item' => __('Novo Pet', 'amigopet'),
                'view_item' => __('Ver Pet', 'amigopet'),
                'search_items' => __('Buscar Pets', 'amigopet'),
                'not_found' => __('Nenhum pet encontrado', 'amigopet'),
                'not_found_in_trash' => __('Nenhum pet encontrado na lixeira', 'amigopet'),
                'menu_name' => __('Pets', 'amigopet')
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
                'name' => __('Eventos', 'amigopet'),
                'singular_name' => __('Evento', 'amigopet'),
                'add_new' => __('Adicionar Novo', 'amigopet'),
                'add_new_item' => __('Adicionar Novo Evento', 'amigopet'),
                'edit_item' => __('Editar Evento', 'amigopet'),
                'new_item' => __('Novo Evento', 'amigopet'),
                'view_item' => __('Ver Evento', 'amigopet'),
                'search_items' => __('Buscar Eventos', 'amigopet'),
                'not_found' => __('Nenhum evento encontrado', 'amigopet'),
                'not_found_in_trash' => __('Nenhum evento encontrado na lixeira', 'amigopet'),
                'menu_name' => __('Eventos', 'amigopet')
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
                'name' => __('Doações', 'amigopet'),
                'singular_name' => __('Doação', 'amigopet'),
                'add_new' => __('Adicionar Nova', 'amigopet'),
                'add_new_item' => __('Adicionar Nova Doação', 'amigopet'),
                'edit_item' => __('Editar Doação', 'amigopet'),
                'new_item' => __('Nova Doação', 'amigopet'),
                'view_item' => __('Ver Doação', 'amigopet'),
                'search_items' => __('Buscar Doações', 'amigopet'),
                'not_found' => __('Nenhuma doação encontrada', 'amigopet'),
                'not_found_in_trash' => __('Nenhuma doação encontrada na lixeira', 'amigopet'),
                'menu_name' => __('Doações', 'amigopet')
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
    private function registerTaxonomies(): void
    {
        // Taxonomia para espécies
        register_taxonomy('pet_species', ['pet'], [
            'labels' => [
                'name' => __('Espécies', 'amigopet'),
                'singular_name' => __('Espécie', 'amigopet'),
                'search_items' => __('Buscar Espécies', 'amigopet'),
                'all_items' => __('Todas as Espécies', 'amigopet'),
                'edit_item' => __('Editar Espécie', 'amigopet'),
                'update_item' => __('Atualizar Espécie', 'amigopet'),
                'add_new_item' => __('Adicionar Nova Espécie', 'amigopet'),
                'new_item_name' => __('Nova Espécie', 'amigopet'),
                'menu_name' => __('Espécies', 'amigopet')
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
                'name' => __('Raças', 'amigopet'),
                'singular_name' => __('Raça', 'amigopet'),
                'search_items' => __('Buscar Raças', 'amigopet'),
                'all_items' => __('Todas as Raças', 'amigopet'),
                'edit_item' => __('Editar Raça', 'amigopet'),
                'update_item' => __('Atualizar Raça', 'amigopet'),
                'add_new_item' => __('Adicionar Nova Raça', 'amigopet'),
                'new_item_name' => __('Nova Raça', 'amigopet'),
                'menu_name' => __('Raças', 'amigopet')
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