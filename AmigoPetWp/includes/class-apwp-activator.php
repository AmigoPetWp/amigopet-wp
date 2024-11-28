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
            
            // Tabela de pets
            $table_name = $wpdb->prefix . 'apwp_pets';
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                advertiser_id bigint(20) NOT NULL,
                organization_id bigint(20),
                name varchar(100) NOT NULL,
                species varchar(50) NOT NULL,
                breed varchar(50),
                age int,
                gender enum('male', 'female'),
                size enum('small', 'medium', 'large'),
                weight decimal(5,2),
                description text,
                status enum('available', 'adopted', 'pending', 'unavailable') DEFAULT 'available',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                KEY advertiser_id (advertiser_id),
                KEY organization_id (organization_id)
            ) $charset_collate;";
            dbDelta($sql);
            
            // Tabela de adoções
            $table_name = $wpdb->prefix . 'apwp_adoptions';
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                pet_id bigint(20) NOT NULL,
                adopter_id bigint(20) NOT NULL,
                status enum('pending', 'approved', 'rejected') DEFAULT 'pending',
                notes text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                KEY pet_id (pet_id),
                KEY adopter_id (adopter_id)
            ) $charset_collate;";
            dbDelta($sql);
            
            // Tabela de adotantes
            $table_name = $wpdb->prefix . 'apwp_adopters';
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                user_id bigint(20) DEFAULT NULL,
                name varchar(100) NOT NULL,
                email varchar(100) NOT NULL,
                phone varchar(20) NOT NULL,
                address text NOT NULL,
                city varchar(100) DEFAULT NULL,
                state varchar(50) DEFAULT NULL,
                zip_code varchar(10) DEFAULT NULL,
                household_type enum('house', 'apartment', 'farm') DEFAULT NULL,
                has_yard tinyint(1) DEFAULT NULL,
                other_pets tinyint(1) DEFAULT NULL,
                children_at_home tinyint(1) DEFAULT NULL,
                status enum('active', 'inactive', 'pending_verification') DEFAULT 'active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY email (email),
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
        self::create_default_pages();
        
        // Limpa o cache de rewrite rules
        flush_rewrite_rules();
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
