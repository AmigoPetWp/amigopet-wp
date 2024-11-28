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
 * @author     Jackson Sá <wendelmax@gmail.com>
 */
class APWP_Database {

    /**
     * Cria as tabelas necessárias no banco de dados.
     *
     * @since    1.0.0
     */
    public static function create_database_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Tabela de organizações
        $table_name = $wpdb->prefix . 'apwp_organizations';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            wp_user_id bigint(20) NOT NULL,
            name varchar(100) NOT NULL,
            cnpj varchar(20),
            phone varchar(20),
            email varchar(100),
            address text,
            city varchar(100),
            state varchar(50),
            country varchar(50),
            logo_url text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY wp_user_id (wp_user_id)
        ) $charset_collate;";

        // Tabela de adotantes
        $table_name = $wpdb->prefix . 'apwp_adopters';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            wp_user_id bigint(20) NOT NULL,
            name varchar(100) NOT NULL,
            cpf varchar(20),
            phone varchar(20),
            email varchar(100),
            address text,
            city varchar(100),
            state varchar(50),
            country varchar(50),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY wp_user_id (wp_user_id)
        ) $charset_collate;";

        // Tabela de animais
        $table_name = $wpdb->prefix . 'apwp_animals';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            organization_id bigint(20) NOT NULL,
            wp_post_id bigint(20) NOT NULL,
            name varchar(100) NOT NULL,
            species varchar(50),
            breed varchar(100),
            age varchar(50),
            gender varchar(20),
            size varchar(20),
            weight decimal(5,2),
            color varchar(50),
            health_status text,
            temperament text,
            description text,
            adoption_status varchar(20) DEFAULT 'available',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY organization_id (organization_id),
            KEY wp_post_id (wp_post_id)
        ) $charset_collate;";

        // Tabela de contratos
        $table_name = $wpdb->prefix . 'apwp_contracts';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            animal_id bigint(20) NOT NULL,
            adopter_id bigint(20) NOT NULL,
            organization_id bigint(20) NOT NULL,
            status varchar(20) NOT NULL,
            contract_number varchar(50) NOT NULL,
            contract_date datetime DEFAULT CURRENT_TIMESTAMP,
            signature_date datetime,
            expiration_date datetime,
            contract_data longtext NOT NULL,
            terms_accepted tinyint(1) DEFAULT 0,
            signature_data text,
            qr_code_url text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY animal_id (animal_id),
            KEY adopter_id (adopter_id),
            KEY organization_id (organization_id)
        ) $charset_collate;";

        // Tabela de templates de contrato
        $table_name = $wpdb->prefix . 'apwp_contract_templates';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            organization_id bigint(20) NOT NULL,
            template_name varchar(100) NOT NULL,
            template_content longtext NOT NULL,
            is_default tinyint(1) DEFAULT 0,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY organization_id (organization_id)
        ) $charset_collate;";

        // Tabela de fotos dos animais
        $table_name = $wpdb->prefix . 'apwp_animal_photos';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            animal_id bigint(20) NOT NULL,
            wp_attachment_id bigint(20) NOT NULL,
            photo_url text NOT NULL,
            is_primary tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY animal_id (animal_id),
            KEY wp_attachment_id (wp_attachment_id)
        ) $charset_collate;";

        // Tabela de histórico de adoções
        $table_name = $wpdb->prefix . 'apwp_adoption_history';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            animal_id bigint(20) NOT NULL,
            adopter_id bigint(20) NOT NULL,
            organization_id bigint(20) NOT NULL,
            contract_id bigint(20) NOT NULL,
            status varchar(20) NOT NULL,
            adoption_date datetime DEFAULT CURRENT_TIMESTAMP,
            return_date datetime,
            return_reason text,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY animal_id (animal_id),
            KEY adopter_id (adopter_id),
            KEY organization_id (organization_id),
            KEY contract_id (contract_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

}
