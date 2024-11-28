<?php

/**
 * A classe principal do plugin.
 *
 * Esta é usada para definir internacionalização, hooks do admin
 * e hooks do lado público do site.
 *
 * @since      1.0.0
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 */
class AmigoPet_Wp {

    /**
     * O loader responsável por manter e registrar todos os hooks que alimentam o plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      APWP_Loader    $loader    Mantém e registra todos os hooks para o plugin.
     */
    protected $loader;

    /**
     * O identificador único deste plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    O nome ou identificador único deste plugin.
     */
    protected $plugin_name;

    /**
     * A versão atual do plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    A versão atual do plugin.
     */
    protected $version;

    /**
     * Define a funcionalidade principal do plugin.
     *
     * Define o nome do plugin e a versão que podem ser utilizados em todo o plugin.
     * Carrega as dependências, define a localização, e define os hooks para o admin
     * e o lado público do site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('AMIGOPET_WP_VERSION')) {
            $this->version = AMIGOPET_WP_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'amigopet-wp';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_widget_hooks();
    }

    /**
     * Carrega as dependências necessárias para este plugin.
     *
     * Inclui os seguintes arquivos que compõem o plugin:
     *
     * - APWP_Loader. Orquestra os hooks do plugin.
     * - APWP_i18n. Define a funcionalidade de internacionalização.
     * - APWP_Admin. Define todos os hooks do lado administrativo.
     * - APWP_Public. Define todos os hooks do lado público.
     * - APWP_Animals_Widget. Define o widget de exibição de animais.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-roles.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-animal.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-adopter.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-organization.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-display-settings.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-animals-widget.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-apwp-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-apwp-public.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-database.php';

        $this->loader = new APWP_Loader();
    }

    /**
     * Define a localização do plugin para internacionalização.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new APWP_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Registra todos os hooks relacionados à área administrativa do plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new APWP_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        
        // Adiciona link de configurações
        $plugin_basename = plugin_basename(plugin_dir_path(dirname(__FILE__)) . $this->plugin_name . '.php');
        $this->loader->add_filter('plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links');
    }

    /**
     * Registra todos os hooks relacionados à funcionalidade pública do plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new APWP_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('init', $plugin_public, 'register_post_types');
        $this->loader->add_action('init', $plugin_public, 'register_taxonomies');
    }

    /**
     * Registra todos os hooks relacionados aos widgets do plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_widget_hooks() {
        $this->loader->add_action('widgets_init', $this, 'register_widgets');
    }

    /**
     * Registra os widgets do plugin.
     *
     * @since    1.0.0
     */
    public function register_widgets() {
        register_widget('APWP_Animals_Widget');
    }

    /**
     * Executa o loader para executar todos os hooks com WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * O nome do plugin usado para identificá-lo unicamente no contexto do
     * WordPress e para definir funcionalidade de internacionalização.
     *
     * @since     1.0.0
     * @return    string    O nome do plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Retorna a referência à classe que orquestra os hooks do plugin.
     *
     * @since     1.0.0
     * @return    APWP_Loader    Orquestra os hooks do plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Recupera o número da versão do plugin.
     *
     * @since     1.0.0
     * @return    string    O número da versão do plugin.
     */
    public function get_version() {
        return $this->version;
    }
}

class APWP_Database {

    private function create_database_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Tabela de Adotantes
        $adopters_table = $wpdb->prefix . 'apwp_adopters';
        $adopters_sql = "CREATE TABLE $adopters_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            cpf varchar(14) DEFAULT '' NOT NULL,
            rg varchar(20) DEFAULT '' NOT NULL,
            email varchar(100) DEFAULT '',
            phone varchar(20) DEFAULT '',
            address text DEFAULT '',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY cpf (cpf)
        ) $charset_collate;";

        // Tabela de Animais
        $animals_table = $wpdb->prefix . 'apwp_animals';
        $animals_sql = "CREATE TABLE $animals_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            species varchar(100) NOT NULL,
            breed varchar(100) DEFAULT '',
            age varchar(50) DEFAULT '',
            gender varchar(20) NOT NULL,
            description text DEFAULT '',
            status varchar(50) NOT NULL DEFAULT 'available',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Tabela de Contratos
        $contracts_table = $wpdb->prefix . 'apwp_contracts';
        $contracts_sql = "CREATE TABLE $contracts_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            adopter_id mediumint(9) NOT NULL,
            animal_id mediumint(9) NOT NULL,
            contract_number varchar(50) NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'active',
            signed_date datetime NOT NULL,
            pdf_path varchar(255) DEFAULT '',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (adopter_id) REFERENCES $adopters_table(id),
            FOREIGN KEY (animal_id) REFERENCES $animals_table(id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($adopters_sql);
        dbDelta($animals_sql);
        dbDelta($contracts_sql);

        // Adiciona índices para melhorar performance
        $wpdb->query("CREATE INDEX idx_adopter_name ON $adopters_table (name)");
        $wpdb->query("CREATE INDEX idx_animal_status ON $animals_table (status)");
        $wpdb->query("CREATE INDEX idx_contract_status ON $contracts_table (status)");
    }
}
