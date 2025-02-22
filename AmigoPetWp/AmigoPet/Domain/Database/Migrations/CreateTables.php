<?php
namespace AmigoPetWp\Domain\Database\Migrations;

class CreateTables {
    private $wpdb;
    private $charset_collate;
    private $prefix;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->charset_collate = $wpdb->get_charset_collate();
        $this->prefix = $wpdb->prefix . 'amigopet_';
    }

    public function up(): void {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Tabelas base
        $this->createTermTypesTable();
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
        $this->createTermsTable();
        $this->createDonationsTable();
        $this->createEventsTable();
        $this->createAdoptionPaymentsTable();
        $this->createSignedTermsTable();
    }

    private function createAdoptersTable(): void {
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

    private function createPetsTable(): void {
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

    private function createAdoptionsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}adoptions (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            pet_id BIGINT(20) UNSIGNED NOT NULL,
            adopter_id BIGINT(20) UNSIGNED NOT NULL,
            organization_id BIGINT(20) UNSIGNED NOT NULL,
            status ENUM('pending', 'approved', 'rejected', 'cancelled', 'completed') DEFAULT 'pending',
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

    private function createOrganizationsTable(): void {
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

    private function createVolunteersTable(): void {
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

    private function createEventsTable(): void {
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

    private function createAdoptionDocumentsTable(): void {
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

    private function createDonationsTable(): void {
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
