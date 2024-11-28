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
     * Método executado durante a ativação do plugin.
     *
     * Cria as tabelas necessárias no banco de dados e configura
     * as opções iniciais do plugin.
     *
     * @since    1.0.0
     */
    public static function activate() {
        global $wpdb;
        
        // Versão do banco de dados
        $db_version = get_option('amigopet_wp_db_version', '0');
        
        // Se a versão do banco for menor que a atual, atualiza as tabelas
        if (version_compare($db_version, AMIGOPET_WP_VERSION, '<')) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            
            // Charset do banco de dados
            $charset_collate = $wpdb->get_charset_collate();
            
            // Tabela de adotantes
            $table_name = $wpdb->prefix . 'apwp_adopters';
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                user_id bigint(20) NOT NULL,
                name varchar(100) NOT NULL,
                email varchar(100) NOT NULL,
                phone varchar(20) NOT NULL,
                address text NOT NULL,
                city varchar(100) NOT NULL,
                state varchar(50) NOT NULL,
                zip_code varchar(10) NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                KEY user_id (user_id)
            ) $charset_collate;";
            dbDelta($sql);
            
            // Tabela de organizações
            $table_name = $wpdb->prefix . 'apwp_organizations';
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                user_id bigint(20) NOT NULL,
                name varchar(100) NOT NULL,
                email varchar(100) NOT NULL,
                phone varchar(20) NOT NULL,
                address text NOT NULL,
                city varchar(100) NOT NULL,
                state varchar(50) NOT NULL,
                zip_code varchar(10) NOT NULL,
                description text,
                website varchar(255),
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                KEY user_id (user_id)
            ) $charset_collate;";
            dbDelta($sql);
            
            // Atualiza a versão do banco de dados
            update_option('amigopet_wp_db_version', AMIGOPET_WP_VERSION);
        }
        
        // Cria as páginas padrão do plugin se não existirem
        self::create_pages();
        
        // Limpa o cache de rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Cria as páginas padrão do plugin.
     *
     * @since    1.0.0
     */
    private static function create_pages() {
        $pages = array(
            'animals' => array(
                'title' => __('Animais para Adoção', 'amigopet-wp'),
                'content' => '[apwp_animals_grid]'
            ),
            'organizations' => array(
                'title' => __('ONGs e Abrigos', 'amigopet-wp'),
                'content' => '[apwp_organizations_grid]'
            ),
            'adoption-form' => array(
                'title' => __('Formulário de Adoção', 'amigopet-wp'),
                'content' => '[apwp_adoption_form]'
            )
        );
        
        foreach ($pages as $slug => $page) {
            // Verifica se a página já existe
            $page_exists = get_page_by_path($slug);
            
            if (!$page_exists) {
                // Cria a página
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
