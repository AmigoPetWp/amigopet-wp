<?php
namespace AmigoPetWp\Domain\Database\Migrations;

use AmigoPetWp\Domain\Database\Migration;

class Migration_2_0_0 extends Migration {
    public function getVersion(): string {
        return '2.0.0';
    }
    
    public function getDescription(): string {
        return 'Adiciona tabelas para histórico médico e fotos de pets';
    }
    
    public function up(): void {
        $queries = [];
        
        // Tabela de registros médicos
        $queries[] = "CREATE TABLE IF NOT EXISTS `{$this->wpdb->prefix}apwp_pet_medical_records` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `pet_id` bigint(20) unsigned NOT NULL,
            `date` date NOT NULL,
            `type` varchar(50) NOT NULL,
            `description` text NOT NULL,
            `veterinarian` varchar(100) NOT NULL,
            `attachments` text,
            `created_at` datetime NOT NULL,
            `updated_at` datetime NOT NULL,
            PRIMARY KEY (`id`),
            KEY `pet_id` (`pet_id`),
            KEY `type` (`type`),
            KEY `date` (`date`),
            CONSTRAINT `fk_medical_record_pet` FOREIGN KEY (`pet_id`) 
                REFERENCES `{$this->wpdb->prefix}apwp_pets` (`id`) 
                ON DELETE CASCADE
        ) {$this->charset_collate};";
        
        // Tabela de fotos
        $queries[] = "CREATE TABLE IF NOT EXISTS `{$this->wpdb->prefix}apwp_pet_photos` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `pet_id` bigint(20) unsigned NOT NULL,
            `filename` varchar(255) NOT NULL,
            `title` varchar(100),
            `description` text,
            `is_profile` tinyint(1) NOT NULL DEFAULT '0',
            `created_at` datetime NOT NULL,
            PRIMARY KEY (`id`),
            KEY `pet_id` (`pet_id`),
            KEY `is_profile` (`is_profile`),
            CONSTRAINT `fk_photo_pet` FOREIGN KEY (`pet_id`) 
                REFERENCES `{$this->wpdb->prefix}apwp_pets` (`id`) 
                ON DELETE CASCADE
        ) {$this->charset_collate};";
        
        // Adiciona coluna de foto de perfil na tabela de pets
        $queries[] = "ALTER TABLE `{$this->wpdb->prefix}apwp_pets` 
            ADD COLUMN `profile_photo_id` bigint(20) unsigned DEFAULT NULL,
            ADD CONSTRAINT `fk_pet_profile_photo` FOREIGN KEY (`profile_photo_id`) 
                REFERENCES `{$this->wpdb->prefix}apwp_pet_photos` (`id`) 
                ON DELETE SET NULL;";
        
        // Adiciona coluna de status na tabela de adoções
        $queries[] = "ALTER TABLE `{$this->wpdb->prefix}apwp_adoptions` 
            MODIFY COLUMN `status` enum('pending_documents','pending_payment','pending_approval','approved','rejected','cancelled') NOT NULL DEFAULT 'pending_documents';";
        
        // Cria tabela de log de acesso
        $queries[] = "CREATE TABLE IF NOT EXISTS `{$this->wpdb->prefix}apwp_access_log` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) unsigned NOT NULL,
            `action` varchar(100) NOT NULL,
            `success` tinyint(1) NOT NULL DEFAULT '0',
            `ip` varchar(45) NOT NULL,
            `user_agent` varchar(255) NOT NULL,
            `timestamp` datetime NOT NULL,
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`),
            KEY `action` (`action`),
            KEY `timestamp` (`timestamp`)
        ) {$this->charset_collate};";
        
        $this->executeQueries($queries);
    }
    
    public function down(): void {
        // Remove as constraints primeiro
        $this->wpdb->query("ALTER TABLE `{$this->wpdb->prefix}apwp_pets` 
            DROP FOREIGN KEY `fk_pet_profile_photo`;");
            
        $this->wpdb->query("ALTER TABLE `{$this->wpdb->prefix}apwp_pets` 
            DROP COLUMN `profile_photo_id`;");
        
        // Remove as tabelas
        $this->wpdb->query("DROP TABLE IF EXISTS `{$this->wpdb->prefix}apwp_pet_medical_records`;");
        $this->wpdb->query("DROP TABLE IF EXISTS `{$this->wpdb->prefix}apwp_pet_photos`;");
        $this->wpdb->query("DROP TABLE IF EXISTS `{$this->wpdb->prefix}apwp_access_log`;");
        
        // Reverte a coluna de status
        $this->wpdb->query("ALTER TABLE `{$this->wpdb->prefix}apwp_adoptions` 
            MODIFY COLUMN `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending';");
    }
}
