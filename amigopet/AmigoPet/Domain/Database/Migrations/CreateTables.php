<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Migration;

class CreateTables extends Migration
{

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getDescription(): string
    {
        return 'Cria as tabelas iniciais do sistema';
    }

    public function getAffectedTables(): array
    {
        return [
            'adoption_payments',
            'events',
            'donations',
            'signed_terms',
            'terms',
            'volunteers',
            'adoption_documents',
            'adoptions',
            'adopters',
            'pets',
            'qrcodes',
            'organizations',
            'pet_breeds',
            'pet_species',
            'term_types'
        ];
    }

    public function down(): void
    {
        // Remove as tabelas na ordem inversa de criação
        $tables = [
            'adoption_payments',
            'events',
            'donations',
            'signed_terms',
            'terms',
            'volunteers',
            'adoption_documents',
            'adoptions',
            'adopters',
            'pets',
            'qrcodes',
            'organizations',
            'pet_breeds',
            'pet_species',
            'term_types'
        ];

        foreach ($tables as $table) {
            $this->wpdb->query("DROP TABLE IF EXISTS {$this->prefix}{$table};");
        }
    }

    public function up(): void
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Tabelas base
        $this->createTermTypesTable();
        $this->createTermsTable();
        $this->createPetSpeciesTable();
        $this->createPetBreedsTable();

        // Tabelas principais
        $this->createOrganizationsTable();
        $this->createQRCodesTable();
        $this->createPetsTable();
        $this->createAdoptersTable();
        $this->createAdoptionsTable();
        $this->createAdoptionDocumentsTable();
        $this->createVolunteersTable();
        $this->createDonationsTable();
        $this->createEventsTable();
        $this->createAdoptionPaymentsTable();
        $this->createSignedTermsTable();
    }

    private function createAdoptersTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}adopters (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            document VARCHAR(14) NOT NULL,
            address TEXT NOT NULL,
            adoption_history TEXT,
            status ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            UNIQUE KEY document (document)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createTermTypesTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}term_types (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY name (name)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createPetSpeciesTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}pet_species (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY name (name)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createPetBreedsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}pet_breeds (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            species_id BIGINT(20) UNSIGNED NOT NULL,
            description TEXT,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (species_id) REFERENCES {$this->prefix}pet_species(id),
            UNIQUE KEY breed_species (name, species_id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createPetsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}pets (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            species_id BIGINT(20) UNSIGNED NOT NULL,
            breed_id BIGINT(20) UNSIGNED,
            age INT,
            size ENUM('small', 'medium', 'large') NOT NULL,
            description TEXT,
            status ENUM('available', 'adopted', 'rescued', 'unavailable') DEFAULT 'available',
            organization_id BIGINT(20) UNSIGNED NOT NULL,
            qrcode_id BIGINT(20) UNSIGNED,
            rga VARCHAR(50),
            microchip_number VARCHAR(50),
            health_info JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (species_id) REFERENCES {$this->prefix}pet_species(id),
            FOREIGN KEY (breed_id) REFERENCES {$this->prefix}pet_breeds(id),
            FOREIGN KEY (organization_id) REFERENCES {$this->prefix}organizations(id),
            UNIQUE KEY rga (rga),
            UNIQUE KEY microchip (microchip_number)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createAdoptionsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}adoptions (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            pet_id BIGINT(20) UNSIGNED NOT NULL,
            adopter_id BIGINT(20) UNSIGNED NOT NULL,
            organization_id BIGINT(20) UNSIGNED NOT NULL,
            status ENUM('pending_documents', 'pending_payment', 'pending_approval', 'approved', 'rejected', 'cancelled') DEFAULT 'pending_documents',
            adoption_date TIMESTAMP NULL,
            rejection_reason TEXT,
            cancellation_reason TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (pet_id) REFERENCES {$this->prefix}pets(id),
            FOREIGN KEY (adopter_id) REFERENCES {$this->prefix}adopters(id),
            FOREIGN KEY (organization_id) REFERENCES {$this->prefix}organizations(id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createQRCodesTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}qrcodes (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            code VARCHAR(255) NOT NULL,
            type ENUM('pet', 'organization', 'adopter') NOT NULL,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY code (code)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createOrganizationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}organizations (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            document VARCHAR(14) NOT NULL,
            address TEXT NOT NULL,
            website VARCHAR(255),
            status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            UNIQUE KEY document (document)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createTermsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}terms (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            type_id BIGINT(20) UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            version VARCHAR(10) NOT NULL,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (type_id) REFERENCES {$this->prefix}term_types(id),
            UNIQUE KEY term_version (type_id, version)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }


    private function createAdoptionPaymentsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}adoption_payments (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            adoption_id BIGINT(20) UNSIGNED NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            payment_method ENUM('credit_card', 'pix', 'bank_transfer', 'cash', 'other') NOT NULL,
            payment_status ENUM('pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled') DEFAULT 'pending',
            transaction_id VARCHAR(100),
            payer_name VARCHAR(255),
            payer_email VARCHAR(100),
            payer_document VARCHAR(20),
            payment_date TIMESTAMP NULL,
            refund_date TIMESTAMP NULL,
            refund_reason TEXT,
            gateway_response JSON,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (adoption_id) REFERENCES {$this->prefix}adoptions(id),
            KEY payment_method (payment_method),
            KEY payment_status (payment_status),
            KEY payment_date (payment_date),
            KEY transaction_id (transaction_id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createSignedTermsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}signed_terms (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            term_id BIGINT(20) UNSIGNED NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            signed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (term_id) REFERENCES {$this->prefix}terms(id),
            FOREIGN KEY (user_id) REFERENCES {$this->wpdb->users}(ID),
            UNIQUE KEY user_term (user_id, term_id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createVolunteersTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}volunteers (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            organization_id BIGINT(20) UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            document VARCHAR(14) NOT NULL,
            address TEXT NOT NULL,
            skills TEXT,
            availability TEXT,
            status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (organization_id) REFERENCES {$this->prefix}organizations(id),
            UNIQUE KEY email (email),
            UNIQUE KEY document (document)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createEventsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}events (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            organization_id BIGINT(20) UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            location TEXT NOT NULL,
            event_date DATETIME NOT NULL,
            max_participants INT,
            status ENUM('scheduled', 'ongoing', 'completed', 'cancelled') DEFAULT 'scheduled',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (organization_id) REFERENCES {$this->prefix}organizations(id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createAdoptionDocumentsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}adoption_documents (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            adoption_id BIGINT(20) UNSIGNED NOT NULL,
            document_type VARCHAR(50) NOT NULL,
            content LONGTEXT NOT NULL,
            signed_by BIGINT(20) UNSIGNED NOT NULL,
            signed_at DATETIME NOT NULL,
            document_url VARCHAR(255) NOT NULL,
            status ENUM('pending', 'signed', 'rejected', 'expired') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (adoption_id) REFERENCES {$this->prefix}adoptions(id) ON DELETE CASCADE,
            FOREIGN KEY (signed_by) REFERENCES {$this->wpdb->users}(ID),
            KEY document_type (document_type),
            KEY status (status)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createDonationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}donations (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            organization_id BIGINT(20) UNSIGNED NOT NULL,
            donor_name VARCHAR(255) NOT NULL,
            donor_email VARCHAR(100) NOT NULL,
            donor_phone VARCHAR(20),
            amount DECIMAL(10,2) NOT NULL,
            payment_method ENUM('pix', 'credit_card', 'bank_transfer') NOT NULL,
            status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
            transaction_id VARCHAR(100),
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (organization_id) REFERENCES {$this->prefix}organizations(id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }
}