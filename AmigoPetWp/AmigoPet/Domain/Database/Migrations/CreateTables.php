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
        
        $this->createAdoptersTable();
        $this->createPetsTable();
        $this->createAdoptionsTable();
        $this->createOrganizationsTable();
        $this->createVolunteersTable();
        $this->createEventsTable();
        $this->createDonationsTable();
    }

    private function createAdoptersTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}adopters (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            document VARCHAR(14) NOT NULL,
            adoption_history TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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
            species VARCHAR(50) NOT NULL,
            breed VARCHAR(100),
            age INT,
            size VARCHAR(20),
            description TEXT,
            status VARCHAR(20) NOT NULL DEFAULT 'available',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createAdoptionsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}adoptions (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            pet_id BIGINT(20) UNSIGNED NOT NULL,
            adopter_id BIGINT(20) UNSIGNED NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            adoption_date TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (pet_id) REFERENCES {$this->prefix}pets(id),
            FOREIGN KEY (adopter_id) REFERENCES {$this->prefix}adopters(id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createOrganizationsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}organizations (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            document VARCHAR(14) NOT NULL,
            address TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            UNIQUE KEY document (document)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createVolunteersTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}volunteers (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            document VARCHAR(14) NOT NULL,
            skills TEXT,
            availability TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            UNIQUE KEY document (document)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createEventsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}events (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            event_date TIMESTAMP NOT NULL,
            location TEXT,
            status VARCHAR(20) NOT NULL DEFAULT 'scheduled',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function createDonationsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->prefix}donations (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            donor_name VARCHAR(255),
            donor_email VARCHAR(100),
            amount DECIMAL(10,2) NOT NULL,
            payment_method VARCHAR(50),
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            transaction_id VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }
}
