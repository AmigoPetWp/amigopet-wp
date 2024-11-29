<?php

/**
 * Classe responsável por gerenciar o banco de dados do plugin.
 *
 * @link       https://github.com/wendelmax/amigopet-wp
 * @since      1.0.0
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 */

/**
 * Classe responsável por gerenciar o banco de dados do plugin.
 *
 * Esta classe define todas as funções necessárias para criar e gerenciar
 * as tabelas do banco de dados utilizadas pelo plugin.
 *
 * @since      1.0.0
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 * @author     Jackson Sá <jacksonwendel@gmail.com>
 */
class APWP_Database {

    /**
     * Cria as tabelas do plugin
     */
    private function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Tabela de anunciantes
        $table_name = $wpdb->prefix . 'apwp_advertisers';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            wp_user_id bigint(20) NOT NULL,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(20),
            address text,
            city varchar(100),
            state varchar(50),
            zip varchar(10),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY email (email),
            KEY wp_user_id (wp_user_id)
        ) $charset_collate;";

        // Tabela de pets
        $table_name = $wpdb->prefix . 'apwp_pets';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            advertiser_id bigint(20) NOT NULL,
            organization_id bigint(20),
            name varchar(255) NOT NULL,
            species varchar(100) NOT NULL,
            breed varchar(100),
            age int,
            gender varchar(20),
            size varchar(20),
            weight decimal(5,2),
            description text,
            status varchar(20) DEFAULT 'available',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY advertiser_id (advertiser_id),
            KEY organization_id (organization_id),
            KEY status (status)
        ) $charset_collate;";

        // Tabela de organizações
        $table_name = $wpdb->prefix . 'apwp_organizations';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            wp_user_id bigint(20) NOT NULL,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(20),
            address text,
            city varchar(100),
            state varchar(50),
            zip varchar(10),
            description text,
            logo_url varchar(255),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY email (email),
            KEY wp_user_id (wp_user_id)
        ) $charset_collate;";

        // Tabela de adotantes
        $table_name = $wpdb->prefix . 'apwp_adopters';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            wp_user_id bigint(20) NOT NULL,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(20),
            address text,
            city varchar(100),
            state varchar(50),
            zip varchar(10),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY email (email),
            KEY wp_user_id (wp_user_id)
        ) $charset_collate;";

        // Tabela de adoções
        $table_name = $wpdb->prefix . 'apwp_adoptions';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            pet_id bigint(20) NOT NULL,
            adopter_id bigint(20) NOT NULL,
            organization_id bigint(20) NOT NULL,
            advertiser_id bigint(20) NOT NULL,
            status varchar(20) DEFAULT 'pending',
            adoption_date datetime,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY pet_id (pet_id),
            KEY adopter_id (adopter_id),
            KEY organization_id (organization_id),
            KEY advertiser_id (advertiser_id),
            KEY status (status)
        ) $charset_collate;";

        // Tabela de fotos dos pets
        $table_name = $wpdb->prefix . 'apwp_pet_photos';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            pet_id bigint(20) NOT NULL,
            photo_url varchar(255) NOT NULL,
            is_primary tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY pet_id (pet_id)
        ) $charset_collate;";

        // Tabela de contratos
        $table_name = $wpdb->prefix . 'apwp_contracts';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            adoption_id bigint(20) NOT NULL,
            filepath varchar(255) NOT NULL,
            fileurl varchar(255) NOT NULL,
            status varchar(20) DEFAULT 'pending',
            signed_at datetime,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY adoption_id (adoption_id),
            KEY status (status)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Cria as tabelas necessárias no banco de dados.
     *
     * @since    1.0.0
     */
    public static function create_database_tables() {
        $instance = new self();
        $instance->create_tables();
    }

}
