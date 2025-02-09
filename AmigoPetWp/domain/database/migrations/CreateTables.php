<?php
namespace Domain\Database\Migrations;

class CreateTables {
    private $wpdb;
    private $charset_collate;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->charset_collate = $wpdb->get_charset_collate();
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
        $this->createVolunteersTable();
        $this->createTermsTable();
        $this->createDonationsTable();
        $this->createEventsTable();
        $this->createAdoptionPaymentsTable();
        $this->createSignedTermsTable();
    }

    private function createAdoptionPaymentsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}amigopet_adoption_payments (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            adoption_id BIGINT(20) UNSIGNED NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            payment_method ENUM('cash', 'pix', 'bank_transfer') NOT NULL,
            status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
            paid_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (adoption_id) REFERENCES {$this->wpdb->prefix}amigopet_adoptions(id) ON DELETE CASCADE
        ) {$this->charset_collate};";

        dbDelta($sql);
    }
    private function createTermTypesTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}amigopet_term_types (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL,
            description TEXT,
            roles TEXT,
            status VARCHAR(20) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createPetSpeciesTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}amigopet_pet_species (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL,
            description TEXT,
            status VARCHAR(20) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createPetBreedsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}amigopet_pet_breeds (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            species_id BIGINT(20) UNSIGNED NOT NULL,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL,
            description TEXT,
            status VARCHAR(20) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug),
            FOREIGN KEY (species_id) REFERENCES {$this->wpdb->prefix}amigopet_pet_species(id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createSignedTermsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}amigopet_signed_terms (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            term_id BIGINT(20) UNSIGNED NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            signed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (term_id) REFERENCES {$this->wpdb->prefix}amigopet_terms(id),
            FOREIGN KEY (user_id) REFERENCES {$this->wpdb->users}(ID)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createQRCodesTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}amigopet_qrcodes (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            pet_id BIGINT(20) UNSIGNED NOT NULL,
            code VARCHAR(100) NOT NULL,
            tracking_url VARCHAR(500) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY code (code)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createDonationsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}amigopet_donations (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            organization_id BIGINT(20) UNSIGNED NOT NULL,
            donor_name VARCHAR(255) NOT NULL,
            donor_email VARCHAR(100) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            payment_method VARCHAR(50) NOT NULL,
            status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (organization_id) REFERENCES {$this->wpdb->prefix}amigopet_organizations(id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createEventsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}amigopet_events (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            organization_id BIGINT(20) UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            location TEXT NOT NULL,
            event_date DATETIME NOT NULL,
            status ENUM('scheduled', 'ongoing', 'completed', 'cancelled') DEFAULT 'scheduled',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (organization_id) REFERENCES {$this->wpdb->prefix}amigopet_organizations(id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createTermsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}amigopet_terms (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            adoption_id BIGINT(20) UNSIGNED NOT NULL,
            document_type VARCHAR(50) NOT NULL,
            signed_by VARCHAR(255) NOT NULL,
            signed_at TIMESTAMP NOT NULL,
            document_url VARCHAR(500) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (adoption_id) REFERENCES {$this->wpdb->prefix}amigopet_adoptions(id) ON DELETE CASCADE
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createPetsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}amigopet_pets (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            species VARCHAR(50) NOT NULL,
            breed VARCHAR(50),
            age INT,
            size ENUM('small', 'medium', 'large') NOT NULL,
            description TEXT,
            status ENUM('available', 'adopted', 'rescued') DEFAULT 'available',
            organization_id BIGINT(20) UNSIGNED NOT NULL,
            qrcode_id BIGINT(20) UNSIGNED,
            rga VARCHAR(50) NOT NULL,
            microchip_number VARCHAR(50) NOT NULL,
            health_info JSON NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (organization_id) REFERENCES {$this->wpdb->prefix}amigopet_organizations(id),
            UNIQUE KEY rga (rga),
            UNIQUE KEY microchip (microchip_number)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createAdoptersTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}amigopet_adopters (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            document VARCHAR(14) NOT NULL,
            address TEXT NOT NULL,
            city VARCHAR(100) NOT NULL,
            state CHAR(2) NOT NULL,
            zip_code VARCHAR(9) NOT NULL,
            pet_experience TEXT,
            home_type ENUM('house', 'apartment', 'farm', 'other') NOT NULL,
            has_other_pets BOOLEAN DEFAULT FALSE,
            other_pets_info TEXT,
            adoption_history JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            UNIQUE KEY document (document)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createAdoptionsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}amigopet_adoptions (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            pet_id BIGINT(20) UNSIGNED NOT NULL,
            adopter_id BIGINT(20) UNSIGNED NOT NULL,
            organization_id BIGINT(20) UNSIGNED NOT NULL,
            status ENUM(
                'pending',
                'approved',
                'rejected',
                'awaiting_payment',
                'paid',
                'completed'
            ) DEFAULT 'pending',
            adoption_reason TEXT NOT NULL,
            pet_experience TEXT NOT NULL,
            reviewer_id BIGINT(20) UNSIGNED,
            review_notes TEXT,
            review_date TIMESTAMP NULL,
            completed_date TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (pet_id) REFERENCES {$this->wpdb->prefix}amigopet_pets(id),
            FOREIGN KEY (adopter_id) REFERENCES {$this->wpdb->prefix}amigopet_adopters(id),
            FOREIGN KEY (organization_id) REFERENCES {$this->wpdb->prefix}amigopet_organizations(id),
            FOREIGN KEY (reviewer_id) REFERENCES {$this->wpdb->prefix}amigopet_volunteers(id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createOrganizationsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}amigopet_organizations (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            website VARCHAR(255),
            address TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createVolunteersTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}amigopet_volunteers (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            organization_id BIGINT(20) UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            role ENUM('rescue', 'shelter', 'adoption') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            FOREIGN KEY (organization_id) REFERENCES {$this->wpdb->prefix}amigopet_organizations(id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }
}
