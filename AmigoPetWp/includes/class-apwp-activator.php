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
     * Fired during plugin activation
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Tabela de Pets
        $table_name = $wpdb->prefix . 'apwp_pets';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            species varchar(50) NOT NULL,
            breed varchar(100),
            age int,
            gender varchar(20),
            size varchar(20),
            weight float,
            description text,
            status varchar(20) DEFAULT 'available',
            adopter_id bigint(20),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Tabela de Adotantes
        $table_adopters = $wpdb->prefix . 'apwp_adopters';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_adopters (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20),
            address text,
            city varchar(100),
            state varchar(50),
            zip varchar(20),
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Tabela de Adoções
        $table_adoptions = $wpdb->prefix . 'apwp_adoptions';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_adoptions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            pet_id bigint(20) NOT NULL,
            adopter_id bigint(20) NOT NULL,
            status varchar(20) DEFAULT 'pending',
            adoption_date datetime,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            FOREIGN KEY (pet_id) REFERENCES " . $wpdb->prefix . "apwp_pets(id),
            FOREIGN KEY (adopter_id) REFERENCES " . $wpdb->prefix . "apwp_adopters(id)
        ) $charset_collate;";

        // Tabela de Termos
        $table_terms = $wpdb->prefix . 'apwp_terms';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_terms (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            content longtext NOT NULL,
            type varchar(50) NOT NULL,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Tabela de Organizações
        $table_organizations = $wpdb->prefix . 'apwp_organizations';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_organizations (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(100),
            phone varchar(20),
            address text,
            city varchar(100),
            state varchar(50),
            zip varchar(20),
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Tabela de Tipos de Termos
        $table_term_types = $wpdb->prefix . 'apwp_term_types';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_term_types (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            slug varchar(100) NOT NULL,
            description text,
            roles text,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";

        // Tabela de Templates de Termos
        $table_term_templates = $wpdb->prefix . 'apwp_term_templates';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_term_templates (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            type_id bigint(20) NOT NULL,
            title varchar(255) NOT NULL,
            content longtext NOT NULL,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY type_id (type_id),
            FOREIGN KEY (type_id) REFERENCES " . $wpdb->prefix . "apwp_term_types(id)
        ) $charset_collate;";

        // Tabela de Termos Assinados
        $table_signed_terms = $wpdb->prefix . 'apwp_signed_terms';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_signed_terms (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            template_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            content longtext NOT NULL,
            signature text,
            ip_address varchar(45),
            signed_at datetime DEFAULT CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY template_id (template_id),
            KEY user_id (user_id),
            FOREIGN KEY (template_id) REFERENCES " . $wpdb->prefix . "apwp_term_templates(id),
            FOREIGN KEY (user_id) REFERENCES " . $wpdb->prefix . "users(ID)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

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
