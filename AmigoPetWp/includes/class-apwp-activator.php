<?php

/**
 * Acionado durante a ativação do plugin.
 *
 * Esta classe define tudo o que precisa acontecer durante
 * a ativação do plugin.
 *
 * @since      1.0.0
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 */
class APWP_Activator {

    /**
     * Executa na ativação do plugin
     */
    public static function activate() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-apwp-database.php';
        APWP_Database::create_database_tables();
        self::create_default_pages();
    }

    /**
     * Cria as páginas padrão do plugin
     */
    private static function create_default_pages() {
        $pages = array(
            'pets' => array(
                'title' => __('Pets para Adoção', 'amigopet-wp'),
                'content' => '[apwp_pets_grid]'
            ),
            'adoption-form' => array(
                'title' => __('Formulário de Adoção', 'amigopet-wp'),
                'content' => '[apwp_adoption_form]'
            ),
            'about' => array(
                'title' => __('Sobre Nós', 'amigopet-wp'),
                'content' => __('Somos uma organização dedicada a encontrar lares amorosos para pets que precisam de uma segunda chance.', 'amigopet-wp')
            )
        );

        foreach ($pages as $slug => $page) {
            // Verifica se a página já existe
            $existing_page = get_page_by_path($slug);

            if (!$existing_page) {
                wp_insert_post(array(
                    'post_title' => $page['title'],
                    'post_content' => $page['content'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_name' => $slug
                ));
            }
        }
    }
}
