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

        // Tabela de espécies
        $table_name = $wpdb->prefix . 'apwp_pet_species';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            description text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY name (name)
        ) $charset_collate;";

        // Tabela de raças
        $table_name = $wpdb->prefix . 'apwp_pet_breeds';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            species_id bigint(20) NOT NULL,
            name varchar(100) NOT NULL,
            description text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY species_breed (species_id, name),
            FOREIGN KEY (species_id) REFERENCES {$wpdb->prefix}apwp_pet_species(id) ON DELETE CASCADE
        ) $charset_collate;";

        // Tabela de pets
        $table_name = $wpdb->prefix . 'apwp_pets';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            species_id bigint(20) NOT NULL,
            breed_id bigint(20),
            age varchar(50),
            gender enum('male', 'female'),
            size enum('small', 'medium', 'large'),
            color varchar(50),
            weight decimal(5,2),
            description text,
            photo_url varchar(255),
            status enum('available', 'adopted', 'pending', 'unavailable') DEFAULT 'available',
            advertiser_id bigint(20),
            organization_id bigint(20),
            adopter_id bigint(20),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            FOREIGN KEY (species_id) REFERENCES {$wpdb->prefix}apwp_pet_species(id),
            FOREIGN KEY (breed_id) REFERENCES {$wpdb->prefix}apwp_pet_breeds(id),
            FOREIGN KEY (advertiser_id) REFERENCES {$wpdb->prefix}apwp_advertisers(id),
            FOREIGN KEY (organization_id) REFERENCES {$wpdb->prefix}apwp_organizations(id),
            FOREIGN KEY (adopter_id) REFERENCES {$wpdb->users}(ID)
        ) $charset_collate;";

        // Tabela de logs
        $table_name = $wpdb->prefix . 'apwp_pet_logs';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            pet_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            action varchar(50) NOT NULL,
            description text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            FOREIGN KEY (pet_id) REFERENCES {$wpdb->prefix}apwp_pets(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID)
        ) $charset_collate;";

        // Tabela de anunciantes
        $table_name = $wpdb->prefix . 'apwp_advertisers';
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
            FOREIGN KEY (wp_user_id) REFERENCES {$wpdb->users}(ID)
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
            FOREIGN KEY (wp_user_id) REFERENCES {$wpdb->users}(ID)
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
            FOREIGN KEY (wp_user_id) REFERENCES {$wpdb->users}(ID)
        ) $charset_collate;";

        // Tabela de adoções
        $table_name = $wpdb->prefix . 'apwp_adoptions';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            pet_id bigint(20) NOT NULL,
            adopter_id bigint(20) NOT NULL,
            organization_id bigint(20),
            advertiser_id bigint(20),
            status enum('pending', 'approved', 'rejected') DEFAULT 'pending',
            adoption_date datetime,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            FOREIGN KEY (pet_id) REFERENCES {$wpdb->prefix}apwp_pets(id),
            FOREIGN KEY (adopter_id) REFERENCES {$wpdb->prefix}apwp_adopters(id),
            FOREIGN KEY (organization_id) REFERENCES {$wpdb->prefix}apwp_organizations(id),
            FOREIGN KEY (advertiser_id) REFERENCES {$wpdb->prefix}apwp_advertisers(id)
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
            FOREIGN KEY (pet_id) REFERENCES {$wpdb->prefix}apwp_pets(id) ON DELETE CASCADE
        ) $charset_collate;";

        // Tabela de tipos de termos
        $table_name = $wpdb->prefix . 'apwp_term_types';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
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

        // Tabela de templates de termos
        $table_name = $wpdb->prefix . 'apwp_term_templates';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            type_id bigint(20) NOT NULL,
            title varchar(255) NOT NULL,
            content longtext NOT NULL,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            FOREIGN KEY (type_id) REFERENCES {$wpdb->prefix}apwp_term_types(id) ON DELETE CASCADE
        ) $charset_collate;";

        // Tabela de termos assinados
        $table_name = $wpdb->prefix . 'apwp_signed_terms';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
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
            FOREIGN KEY (template_id) REFERENCES {$wpdb->prefix}apwp_term_templates(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Atualiza o banco de dados para a versão mais recente
     *
     * @since    1.0.0
     * @access   public
     */
    public function update() {
        $installed_version = get_option('amigopet_db_version');
        
        if ($installed_version != $this->version) {
            $this->migrate_to_terms();
            update_option('amigopet_db_version', $this->version);
        }
    }

    /**
     * Migra as tabelas de contratos para termos
     *
     * @since    1.0.0
     * @access   private
     */
    private function migrate_to_terms() {
        global $wpdb;

        // Renomeia a tabela de contratos para termos se ela existir
        $contracts_table = $wpdb->prefix . 'apwp_contracts';
        $terms_table = $wpdb->prefix . 'apwp_terms';

        // Verifica se a tabela de contratos existe
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$contracts_table'");
        
        if ($table_exists) {
            // Renomeia a tabela
            $wpdb->query("RENAME TABLE $contracts_table TO $terms_table");
            
            // Atualiza as opções relacionadas
            $template_path = get_option('amigopet_contract_template');
            if ($template_path) {
                update_option('amigopet_term_template', $template_path);
                delete_option('amigopet_contract_template');
            }
            
            // Atualiza o diretório de arquivos
            $upload_dir = wp_upload_dir();
            $old_dir = $upload_dir['basedir'] . '/apwp-contracts';
            $new_dir = $upload_dir['basedir'] . '/apwp-terms';
            
            if (file_exists($old_dir)) {
                rename($old_dir, $new_dir);
            }
        } else {
            // Se a tabela não existe, cria a nova tabela de termos
            $charset_collate = $wpdb->get_charset_collate();
            
            $sql = "CREATE TABLE IF NOT EXISTS $terms_table (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                pet_id bigint(20) NOT NULL,
                adopter_id bigint(20) NOT NULL,
                organization_id bigint(20) NOT NULL,
                filepath varchar(255) NOT NULL,
                fileurl varchar(255) NOT NULL,
                uid varchar(50) NOT NULL,
                status varchar(20) NOT NULL DEFAULT 'active',
                created_at datetime NOT NULL,
                updated_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY pet_id (pet_id),
                KEY adopter_id (adopter_id),
                KEY organization_id (organization_id)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
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
