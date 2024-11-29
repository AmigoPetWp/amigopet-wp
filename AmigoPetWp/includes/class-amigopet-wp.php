<?php

/**
 * A classe principal do plugin.
 *
 * Esta é usada para definir internacionalização, hooks do admin
 * e hooks do lado público do site.
 *
 * @since      1.7.7
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 * @author     Jackson Sá <jacksonwendel@gmail.com>
 */
class AmigoPet_Wp {

    /**
     * O loader que é responsável por manter e registrar todos os hooks que alimentam o plugin.
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
        $this->define_hooks();
    }

    /**
     * Carrega as dependências necessárias para este plugin.
     *
     * Inclui os seguintes arquivos que compõem o plugin:
     *
     * - APWP_Loader. Orquestra os hooks do plugin.
     * - APWP_i18n. Define a funcionalidade de internacionalização.
     * - APWP_Pet. Define a classe Pet.
     * - APWP_Adopter. Define a classe Adopter.
     * - APWP_Adoption. Define a classe Adoption.
     * - APWP_Organization. Define a classe Organization.
     * - APWP_Display_Settings. Define a classe Display_Settings.
     * - APWP_Pets_Widget. Define o widget de exibição de pets.
     * - APWP_Database. Define a classe Database.
     * - APWP_Admin. Define todos os hooks do lado administrativo.
     * - APWP_Public. Define todos os hooks do lado público.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        // Carrega as classes principais
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-i18n.php';
        
        // Classes de gerenciamento de pets
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-pet.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-species.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-breed.php';
        
        // Classes de gerenciamento de termos
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-term.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-term-template.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-term-type.php';
        
        // Classes de gerenciamento de adotantes e organizações
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-adopter.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-apwp-organization.php';
        
        // Classes de administração
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
     * Define as funções administrativas e do site.
     *
     * - APWP_Admin. Define todos os hooks da área administrativa.
     * - APWP_Public. Define todos os hooks do site.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_hooks() {
        $plugin_admin = new APWP_Admin($this->get_plugin_name(), $this->get_version());
        $plugin_public = new APWP_Public($this->get_plugin_name(), $this->get_version());
        $plugin_database = new APWP_Database();

        // Hooks de ativação e desativação
        $this->loader->add_action('activate_' . $this->plugin_name, $plugin_database, 'install');
        $this->loader->add_action('activate_' . $this->plugin_name, $plugin_database, 'update');

        // Hooks administrativos
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // Hooks públicos
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Registra os widgets do plugin.
     *
     * @since    1.0.0
     */
    public function register_widgets() {
        register_widget('APWP_Pets_Widget');
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
