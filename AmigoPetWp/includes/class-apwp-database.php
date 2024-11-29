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
     * Cria as tabelas do banco de dados
     * 
     * @return bool True se todas as tabelas foram criadas com sucesso, False caso contrário
     */
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $success = true;

        // Primeiro criamos todas as tabelas sem foreign keys
        $tables = array(
            'term_types' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_term_types (
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
            ) $charset_collate",

            'pet_species' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_pet_species (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                name varchar(100) NOT NULL,
                description text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY name (name)
            ) $charset_collate",

            'advertisers' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_advertisers (
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
                UNIQUE KEY email (email)
            ) $charset_collate",

            'organizations' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_organizations (
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
                UNIQUE KEY email (email)
            ) $charset_collate",

            'adopters' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_adopters (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                wp_user_id bigint(20) NOT NULL,
                name varchar(255) NOT NULL,
                email varchar(255) NOT NULL,
                phone varchar(20),
                cpf varchar(14),
                birth_date date,
                occupation varchar(100),
                income decimal(10,2),
                address text,
                address_number varchar(20),
                address_complement varchar(100),
                neighborhood varchar(100),
                city varchar(100),
                state varchar(50),
                zip varchar(10),
                has_other_pets tinyint(1) DEFAULT 0,
                house_type enum('house','apartment','other') DEFAULT 'house',
                house_size varchar(50),
                has_yard tinyint(1) DEFAULT 0,
                lives_with_family tinyint(1) DEFAULT 0,
                family_agrees tinyint(1) DEFAULT 0,
                previous_adoptions text,
                why_adopt text,
                status varchar(20) DEFAULT 'pending',
                verified tinyint(1) DEFAULT 0,
                verification_date datetime,
                verification_notes text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY email (email),
                UNIQUE KEY cpf (cpf)
            ) $charset_collate",

            'terms' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_terms (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                type_id bigint(20) NOT NULL,
                title varchar(255) NOT NULL,
                content longtext NOT NULL,
                version varchar(10) NOT NULL DEFAULT '1.0',
                status varchar(20) DEFAULT 'draft',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",

            'term_templates' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_term_templates (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                type_id bigint(20) NOT NULL,
                title varchar(255) NOT NULL,
                content longtext NOT NULL,
                status varchar(20) DEFAULT 'active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY  (id)
            ) $charset_collate",

            'pet_breeds' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_pet_breeds (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                species_id bigint(20) NOT NULL,
                name varchar(100) NOT NULL,
                description text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY breed_species (name, species_id)
            ) $charset_collate",

            'pets' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_pets (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                name varchar(100) NOT NULL,
                description text,
                species_id bigint(20),
                breed_id bigint(20),
                age int,
                gender varchar(20),
                size varchar(20),
                status varchar(20) DEFAULT 'available',
                advertiser_id bigint(20),
                organization_id bigint(20),
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",

            'pet_photos' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_pet_photos (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                pet_id bigint(20) NOT NULL,
                photo_url varchar(255) NOT NULL,
                is_primary tinyint(1) DEFAULT 0,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",

            'pet_logs' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_pet_logs (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                pet_id bigint(20) NOT NULL,
                user_id bigint(20) NOT NULL,
                action varchar(50) NOT NULL,
                description text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",

            'qrcodes' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_qrcodes (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                pet_id bigint(20) NOT NULL,
                code varchar(50) NOT NULL,
                url varchar(255) NOT NULL,
                scans int DEFAULT 0,
                status varchar(20) DEFAULT 'active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY code (code)
            ) $charset_collate",

            'signed_terms' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_signed_terms (
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
                PRIMARY KEY (id)
            ) $charset_collate",

            'adoptions' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_adoptions (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                pet_id bigint(20) NOT NULL,
                adopter_id bigint(20) NOT NULL,
                organization_id bigint(20),
                advertiser_id bigint(20),
                status enum('pending','in_review','approved','rejected','cancelled','completed') DEFAULT 'pending',
                adoption_date datetime,
                completion_date datetime,
                cancellation_date datetime,
                cancellation_reason text,
                adoption_fee decimal(10,2),
                payment_status enum('pending','paid','refunded') DEFAULT 'pending',
                payment_method varchar(50),
                payment_date datetime,
                contract_signed tinyint(1) DEFAULT 0,
                contract_sign_date datetime,
                contract_file_url varchar(255),
                home_visit_required tinyint(1) DEFAULT 0,
                home_visit_date datetime,
                home_visit_notes text,
                followup_date datetime,
                followup_notes text,
                notes text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",

            'adoption_history' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_adoption_history (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                adoption_id bigint(20) NOT NULL,
                status varchar(50) NOT NULL,
                notes text,
                changed_by bigint(20),
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",

            'adoption_requirements' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_adoption_requirements (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                organization_id bigint(20),
                name varchar(255) NOT NULL,
                description text,
                required tinyint(1) DEFAULT 1,
                applies_to enum('all','dogs','cats','other') DEFAULT 'all',
                min_age int,
                max_age int,
                min_income decimal(10,2),
                requires_house tinyint(1) DEFAULT 0,
                requires_yard tinyint(1) DEFAULT 0,
                requires_family_agreement tinyint(1) DEFAULT 1,
                requires_home_visit tinyint(1) DEFAULT 0,
                requires_previous_experience tinyint(1) DEFAULT 0,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",

            'adoption_requirement_checks' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_adoption_requirement_checks (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                adoption_id bigint(20) NOT NULL,
                requirement_id bigint(20) NOT NULL,
                status enum('pending','approved','rejected') DEFAULT 'pending',
                notes text,
                checked_by bigint(20),
                checked_at datetime,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",

            'adoption_followups' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_adoption_followups (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                adoption_id bigint(20) NOT NULL,
                scheduled_date datetime NOT NULL,
                completed_date datetime,
                status enum('scheduled','completed','missed','rescheduled') DEFAULT 'scheduled',
                type enum('phone','visit','photo','video','other') DEFAULT 'phone',
                notes text,
                photos text,
                created_by bigint(20),
                completed_by bigint(20),
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",

            // Configurações do sistema
            'settings' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_settings (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                setting_key varchar(100) NOT NULL,
                setting_value longtext,
                autoload enum('yes','no') DEFAULT 'yes',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY setting_key (setting_key)
            ) $charset_collate",

            // Configurações por organização
            'organization_settings' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_organization_settings (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                organization_id bigint(20) NOT NULL,
                setting_key varchar(100) NOT NULL,
                setting_value longtext,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY org_setting (organization_id, setting_key)
            ) $charset_collate",

            // Templates de e-mail
            'email_templates' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_email_templates (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                name varchar(100) NOT NULL,
                slug varchar(100) NOT NULL,
                subject varchar(255) NOT NULL,
                content longtext NOT NULL,
                description text,
                variables text,
                status varchar(20) DEFAULT 'active',
                organization_id bigint(20),
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY template_org (slug, organization_id)
            ) $charset_collate",

            // Configurações de formulários
            'form_settings' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_form_settings (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                form_type varchar(50) NOT NULL,
                organization_id bigint(20),
                fields longtext NOT NULL,
                validation_rules longtext,
                required_fields text,
                custom_messages longtext,
                status varchar(20) DEFAULT 'active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY form_org (form_type, organization_id)
            ) $charset_collate",

            // Configurações de notificações
            'notification_settings' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_notification_settings (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                event_type varchar(50) NOT NULL,
                organization_id bigint(20),
                notify_admin tinyint(1) DEFAULT 1,
                notify_organization tinyint(1) DEFAULT 1,
                notify_adopter tinyint(1) DEFAULT 1,
                notify_advertiser tinyint(1) DEFAULT 0,
                email_template_id bigint(20),
                additional_emails text,
                status varchar(20) DEFAULT 'active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY event_org (event_type, organization_id)
            ) $charset_collate",

            // Configurações de páginas
            'page_settings' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_page_settings (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                page_type varchar(50) NOT NULL,
                organization_id bigint(20),
                title varchar(255),
                content longtext,
                meta_title varchar(255),
                meta_description text,
                layout varchar(50),
                sidebar_widgets text,
                custom_css text,
                custom_js text,
                status varchar(20) DEFAULT 'active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY page_org (page_type, organization_id)
            ) $charset_collate",

            // Configurações de SEO
            'seo_settings' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_seo_settings (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                page_type varchar(50) NOT NULL,
                organization_id bigint(20),
                title_template varchar(255),
                description_template text,
                keywords text,
                og_title varchar(255),
                og_description text,
                og_image varchar(255),
                twitter_card varchar(255),
                robots text,
                custom_meta text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY seo_org (page_type, organization_id)
            ) $charset_collate",

            // Configurações de integração
            'integration_settings' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_integration_settings (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                integration_type varchar(50) NOT NULL,
                organization_id bigint(20),
                api_key varchar(255),
                api_secret varchar(255),
                endpoint_url varchar(255),
                settings longtext,
                status varchar(20) DEFAULT 'active',
                last_sync datetime,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY integration_org (integration_type, organization_id)
            ) $charset_collate",

            // Configurações de pagamento
            'payment_settings' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_payment_settings (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                organization_id bigint(20),
                gateway varchar(50) NOT NULL,
                credentials longtext,
                test_mode tinyint(1) DEFAULT 0,
                default_currency varchar(3) DEFAULT 'BRL',
                minimum_amount decimal(10,2),
                maximum_amount decimal(10,2),
                processing_fee decimal(10,2),
                fee_type enum('fixed','percentage') DEFAULT 'fixed',
                status varchar(20) DEFAULT 'active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY payment_org (gateway, organization_id)
            ) $charset_collate",

            // Categorias de pets (ex: cachorro pequeno, gato adulto, etc)
            'pet_categories' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_pet_categories (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                name varchar(100) NOT NULL,
                slug varchar(100) NOT NULL,
                description text,
                parent_id bigint(20),
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY slug (slug)
            ) $charset_collate",

            // Características dos pets (ex: vacinado, castrado, etc)
            'pet_characteristics' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_pet_characteristics (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                name varchar(100) NOT NULL,
                slug varchar(100) NOT NULL,
                description text,
                type enum('boolean','text','select','number') DEFAULT 'boolean',
                options text,
                required tinyint(1) DEFAULT 0,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY slug (slug)
            ) $charset_collate",

            // Valores das características dos pets
            'pet_characteristic_values' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_pet_characteristic_values (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                pet_id bigint(20) NOT NULL,
                characteristic_id bigint(20) NOT NULL,
                value text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",

            // Favoritos dos usuários
            'favorites' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_favorites (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                user_id bigint(20) NOT NULL,
                pet_id bigint(20) NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY user_pet (user_id, pet_id)
            ) $charset_collate",

            // Avaliações de adoções
            'adoption_reviews' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_adoption_reviews (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                adoption_id bigint(20) NOT NULL,
                reviewer_id bigint(20) NOT NULL,
                rating int NOT NULL,
                comment text,
                status varchar(20) DEFAULT 'pending',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",

            // Notificações
            'notifications' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_notifications (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                user_id bigint(20) NOT NULL,
                title varchar(255) NOT NULL,
                message text NOT NULL,
                type varchar(50) DEFAULT 'info',
                read_at datetime DEFAULT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",

            // Logs do sistema
            'system_logs' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_system_logs (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                user_id bigint(20),
                action varchar(100) NOT NULL,
                object_type varchar(50),
                object_id bigint(20),
                details text,
                ip_address varchar(45),
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",

            // Documentos necessários para adoção
            'required_documents' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_required_documents (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL,
                description text,
                required tinyint(1) DEFAULT 1,
                document_type varchar(50) DEFAULT 'file',
                allowed_types varchar(255) DEFAULT 'pdf,jpg,jpeg,png',
                max_size int DEFAULT 2048,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",

            // Documentos enviados pelos adotantes
            'submitted_documents' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}apwp_submitted_documents (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                adoption_id bigint(20) NOT NULL,
                document_id bigint(20) NOT NULL,
                file_url varchar(255) NOT NULL,
                status varchar(20) DEFAULT 'pending',
                notes text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate",
        );

        // Criar cada tabela
        foreach ($tables as $table_name => $sql) {
            $result = $wpdb->query($sql);
            if ($result === false) {
                error_log("Erro ao criar a tabela {$wpdb->prefix}apwp_$table_name");
                error_log("Erro do MySQL: " . $wpdb->last_error);
                $success = false;
            }
        }

        // Agora adicionamos as foreign keys
        $foreign_keys = array(
            // Usuários
            "ALTER TABLE {$wpdb->prefix}apwp_advertisers ADD FOREIGN KEY (wp_user_id) REFERENCES {$wpdb->users}(ID)",
            "ALTER TABLE {$wpdb->prefix}apwp_organizations ADD FOREIGN KEY (wp_user_id) REFERENCES {$wpdb->users}(ID)",
            "ALTER TABLE {$wpdb->prefix}apwp_adopters ADD FOREIGN KEY (wp_user_id) REFERENCES {$wpdb->users}(ID)",

            // Termos
            "ALTER TABLE {$wpdb->prefix}apwp_terms ADD FOREIGN KEY (type_id) REFERENCES {$wpdb->prefix}apwp_term_types(id) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_term_templates ADD FOREIGN KEY (type_id) REFERENCES {$wpdb->prefix}apwp_term_types(id) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_signed_terms ADD FOREIGN KEY (template_id) REFERENCES {$wpdb->prefix}apwp_term_templates(id) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_signed_terms ADD FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID)",

            // Pets e relacionados
            "ALTER TABLE {$wpdb->prefix}apwp_pet_breeds ADD FOREIGN KEY (species_id) REFERENCES {$wpdb->prefix}apwp_pet_species(id) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_pets ADD FOREIGN KEY (species_id) REFERENCES {$wpdb->prefix}apwp_pet_species(id) ON DELETE SET NULL",
            "ALTER TABLE {$wpdb->prefix}apwp_pets ADD FOREIGN KEY (breed_id) REFERENCES {$wpdb->prefix}apwp_pet_breeds(id) ON DELETE SET NULL",
            "ALTER TABLE {$wpdb->prefix}apwp_pets ADD FOREIGN KEY (advertiser_id) REFERENCES {$wpdb->prefix}apwp_advertisers(id) ON DELETE SET NULL",
            "ALTER TABLE {$wpdb->prefix}apwp_pets ADD FOREIGN KEY (organization_id) REFERENCES {$wpdb->prefix}apwp_organizations(id) ON DELETE SET NULL",
            "ALTER TABLE {$wpdb->prefix}apwp_pet_photos ADD FOREIGN KEY (pet_id) REFERENCES {$wpdb->prefix}apwp_pets(id) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_pet_logs ADD FOREIGN KEY (pet_id) REFERENCES {$wpdb->prefix}apwp_pets(id) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_pet_logs ADD FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID)",
            "ALTER TABLE {$wpdb->prefix}apwp_qrcodes ADD FOREIGN KEY (pet_id) REFERENCES {$wpdb->prefix}apwp_pets(id) ON DELETE CASCADE",

            // Adoções
            "ALTER TABLE {$wpdb->prefix}apwp_adoptions ADD FOREIGN KEY (pet_id) REFERENCES {$wpdb->prefix}apwp_pets(id)",
            "ALTER TABLE {$wpdb->prefix}apwp_adoptions ADD FOREIGN KEY (adopter_id) REFERENCES {$wpdb->prefix}apwp_adopters(id)",
            "ALTER TABLE {$wpdb->prefix}apwp_adoptions ADD FOREIGN KEY (organization_id) REFERENCES {$wpdb->prefix}apwp_organizations(id)",
            "ALTER TABLE {$wpdb->prefix}apwp_adoptions ADD FOREIGN KEY (advertiser_id) REFERENCES {$wpdb->prefix}apwp_advertisers(id)",

            // Novas foreign keys
            "ALTER TABLE {$wpdb->prefix}apwp_pet_categories ADD FOREIGN KEY (parent_id) REFERENCES {$wpdb->prefix}apwp_pet_categories(id) ON DELETE SET NULL",
            
            "ALTER TABLE {$wpdb->prefix}apwp_pet_characteristic_values ADD FOREIGN KEY (pet_id) REFERENCES {$wpdb->prefix}apwp_pets(id) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_pet_characteristic_values ADD FOREIGN KEY (characteristic_id) REFERENCES {$wpdb->prefix}apwp_pet_characteristics(id) ON DELETE CASCADE",
            
            "ALTER TABLE {$wpdb->prefix}apwp_favorites ADD FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_favorites ADD FOREIGN KEY (pet_id) REFERENCES {$wpdb->prefix}apwp_pets(id) ON DELETE CASCADE",
            
            "ALTER TABLE {$wpdb->prefix}apwp_adoption_reviews ADD FOREIGN KEY (adoption_id) REFERENCES {$wpdb->prefix}apwp_adoptions(id) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_adoption_reviews ADD FOREIGN KEY (reviewer_id) REFERENCES {$wpdb->users}(ID)",
            
            "ALTER TABLE {$wpdb->prefix}apwp_notifications ADD FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID) ON DELETE CASCADE",
            
            "ALTER TABLE {$wpdb->prefix}apwp_system_logs ADD FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID) ON DELETE SET NULL",
            
            "ALTER TABLE {$wpdb->prefix}apwp_submitted_documents ADD FOREIGN KEY (adoption_id) REFERENCES {$wpdb->prefix}apwp_adoptions(id) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_submitted_documents ADD FOREIGN KEY (document_id) REFERENCES {$wpdb->prefix}apwp_required_documents(id) ON DELETE CASCADE",

            // Novas foreign keys para adoções
            "ALTER TABLE {$wpdb->prefix}apwp_adoption_history ADD FOREIGN KEY (adoption_id) REFERENCES {$wpdb->prefix}apwp_adoptions(id) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_adoption_history ADD FOREIGN KEY (changed_by) REFERENCES {$wpdb->users}(ID) ON DELETE SET NULL",

            "ALTER TABLE {$wpdb->prefix}apwp_adoption_requirements ADD FOREIGN KEY (organization_id) REFERENCES {$wpdb->prefix}apwp_organizations(id) ON DELETE CASCADE",

            "ALTER TABLE {$wpdb->prefix}apwp_adoption_requirement_checks ADD FOREIGN KEY (adoption_id) REFERENCES {$wpdb->prefix}apwp_adoptions(id) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_adoption_requirement_checks ADD FOREIGN KEY (requirement_id) REFERENCES {$wpdb->prefix}apwp_adoption_requirements(id) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_adoption_requirement_checks ADD FOREIGN KEY (checked_by) REFERENCES {$wpdb->users}(ID) ON DELETE SET NULL",

            "ALTER TABLE {$wpdb->prefix}apwp_adoption_followups ADD FOREIGN KEY (adoption_id) REFERENCES {$wpdb->prefix}apwp_adoptions(id) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_adoption_followups ADD FOREIGN KEY (created_by) REFERENCES {$wpdb->users}(ID) ON DELETE SET NULL",
            "ALTER TABLE {$wpdb->prefix}apwp_adoption_followups ADD FOREIGN KEY (completed_by) REFERENCES {$wpdb->users}(ID) ON DELETE SET NULL",

            // Foreign keys para configurações
            "ALTER TABLE {$wpdb->prefix}apwp_organization_settings ADD FOREIGN KEY (organization_id) REFERENCES {$wpdb->prefix}apwp_organizations(id) ON DELETE CASCADE",
            
            "ALTER TABLE {$wpdb->prefix}apwp_email_templates ADD FOREIGN KEY (organization_id) REFERENCES {$wpdb->prefix}apwp_organizations(id) ON DELETE CASCADE",
            
            "ALTER TABLE {$wpdb->prefix}apwp_form_settings ADD FOREIGN KEY (organization_id) REFERENCES {$wpdb->prefix}apwp_organizations(id) ON DELETE CASCADE",
            
            "ALTER TABLE {$wpdb->prefix}apwp_notification_settings ADD FOREIGN KEY (organization_id) REFERENCES {$wpdb->prefix}apwp_organizations(id) ON DELETE CASCADE",
            "ALTER TABLE {$wpdb->prefix}apwp_notification_settings ADD FOREIGN KEY (email_template_id) REFERENCES {$wpdb->prefix}apwp_email_templates(id) ON DELETE SET NULL",
            
            "ALTER TABLE {$wpdb->prefix}apwp_page_settings ADD FOREIGN KEY (organization_id) REFERENCES {$wpdb->prefix}apwp_organizations(id) ON DELETE CASCADE",
            
            "ALTER TABLE {$wpdb->prefix}apwp_seo_settings ADD FOREIGN KEY (organization_id) REFERENCES {$wpdb->prefix}apwp_organizations(id) ON DELETE CASCADE",
            
            "ALTER TABLE {$wpdb->prefix}apwp_integration_settings ADD FOREIGN KEY (organization_id) REFERENCES {$wpdb->prefix}apwp_organizations(id) ON DELETE CASCADE",
            
            "ALTER TABLE {$wpdb->prefix}apwp_payment_settings ADD FOREIGN KEY (organization_id) REFERENCES {$wpdb->prefix}apwp_organizations(id) ON DELETE CASCADE",
        );

        // Adicionar as foreign keys
        foreach ($foreign_keys as $sql) {
            $result = $wpdb->query($sql);
            if ($result === false) {
                error_log("Erro ao adicionar foreign key: $sql");
                error_log("Erro do MySQL: " . $wpdb->last_error);
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Força a criação das tabelas do banco de dados
     *
     * @since    1.0.0
     */
    public static function force_create_tables() {
        return self::create_tables();
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
        return self::create_tables();
    }

}
