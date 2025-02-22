<?php
namespace AmigoPetWp\Domain\Database;

use AmigoPetWp\Domain\Database\Repositories\AdopterRepository;
use AmigoPetWp\Domain\Database\Repositories\AdoptionRepository;
use AmigoPetWp\Domain\Database\Repositories\AdoptionPaymentRepository;
use AmigoPetWp\Domain\Database\Repositories\DonationRepository;
use AmigoPetWp\Domain\Database\Repositories\EventRepository;
use AmigoPetWp\Domain\Database\Repositories\OrganizationRepository;
use AmigoPetWp\Domain\Database\Repositories\PetRepository;
use AmigoPetWp\Domain\Database\Repositories\PetBreedRepository;
use AmigoPetWp\Domain\Database\Repositories\PetSpeciesRepository;
use AmigoPetWp\Domain\Database\Repositories\QRCodeRepository;
use AmigoPetWp\Domain\Database\Repositories\SignedTermRepository;
use AmigoPetWp\Domain\Database\Repositories\TermRepository;
use AmigoPetWp\Domain\Database\Repositories\TermTypeRepository;
use AmigoPetWp\Domain\Database\Repositories\VolunteerRepository;

class Database {
    private static $instance = null;
    private $wpdb;

    // Repositórios
    private $adopterRepository;
    private $adoptionRepository;
    private $adoptionPaymentRepository;
    private $donationRepository;
    private $eventRepository;
    private $organizationRepository;
    private $petRepository;
    private $petBreedRepository;
    private $petSpeciesRepository;
    private $qrcodeRepository;
    private $signedTermRepository;
    private $termRepository;
    private $termTypeRepository;
    private $volunteerRepository;

    private function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Getters para repositórios com lazy loading
    public function getAdopterRepository(): AdopterRepository {
        if ($this->adopterRepository === null) {
            $this->adopterRepository = new AdopterRepository($this->wpdb);
        }
        return $this->adopterRepository;
    }

    public function getAdoptionRepository(): AdoptionRepository {
        if ($this->adoptionRepository === null) {
            $this->adoptionRepository = new AdoptionRepository($this->wpdb);
        }
        return $this->adoptionRepository;
    }

    public function getDonationRepository(): DonationRepository {
        if ($this->donationRepository === null) {
            $this->donationRepository = new DonationRepository($this->wpdb);
        }
        return $this->donationRepository;
    }

    public function getEventRepository(): EventRepository {
        if ($this->eventRepository === null) {
            $this->eventRepository = new EventRepository($this->wpdb);
        }
        return $this->eventRepository;
    }

    public function getOrganizationRepository(): OrganizationRepository {
        if ($this->organizationRepository === null) {
            $this->organizationRepository = new OrganizationRepository($this->wpdb);
        }
        return $this->organizationRepository;
    }

    public function getPetRepository(): PetRepository {
        if ($this->petRepository === null) {
            $this->petRepository = new PetRepository($this->wpdb);
        }
        return $this->petRepository;
    }

    public function getQRCodeRepository(): QRCodeRepository {
        if ($this->qrcodeRepository === null) {
            $this->qrcodeRepository = new QRCodeRepository($this->wpdb);
        }
        return $this->qrcodeRepository;
    }

    public function getTermRepository(): TermRepository {
        if ($this->termRepository === null) {
            $this->termRepository = new TermRepository($this->wpdb);
        }
        return $this->termRepository;
    }

    public function getVolunteerRepository(): VolunteerRepository {
        if ($this->volunteerRepository === null) {
            $this->volunteerRepository = new VolunteerRepository($this->wpdb);
        }
        return $this->volunteerRepository;
    }

    public function getAdoptionPaymentRepository(): AdoptionPaymentRepository {
        if ($this->adoptionPaymentRepository === null) {
            $this->adoptionPaymentRepository = new AdoptionPaymentRepository($this->wpdb);
        }
        return $this->adoptionPaymentRepository;
    }

    public function getPetBreedRepository(): PetBreedRepository {
        if ($this->petBreedRepository === null) {
            $this->petBreedRepository = new PetBreedRepository($this->wpdb);
        }
        return $this->petBreedRepository;
    }

    public function getPetSpeciesRepository(): PetSpeciesRepository {
        if ($this->petSpeciesRepository === null) {
            $this->petSpeciesRepository = new PetSpeciesRepository($this->wpdb);
        }
        return $this->petSpeciesRepository;
    }

    public function getSignedTermRepository(): SignedTermRepository {
        if ($this->signedTermRepository === null) {
            $this->signedTermRepository = new SignedTermRepository($this->wpdb);
        }
        return $this->signedTermRepository;
    }

    public function getTermTypeRepository(): TermTypeRepository {
        if ($this->termTypeRepository === null) {
            $this->termTypeRepository = new TermTypeRepository($this->wpdb);
        }
        return $this->termTypeRepository;
    }

    /**
     * Inicializa as tabelas do banco de dados
     */
    public function initializeTables(): void {
        $migrations = new Migrations\CreateTables();
        $migrations->up();
    }

    private function createAdoptersTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->getPrefix()}adopters (
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
        ) {$this->wpdb->get_charset_collate()};";

        dbDelta($sql);
    }

    private function createAdoptionsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->getPrefix()}adoptions (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            pet_id BIGINT(20) UNSIGNED NOT NULL,
            adopter_id BIGINT(20) UNSIGNED NOT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (pet_id) REFERENCES {$this->getPrefix()}pets(id),
            FOREIGN KEY (adopter_id) REFERENCES {$this->getPrefix()}adopters(id)
        ) {$this->wpdb->get_charset_collate()};";

        dbDelta($sql);
    }

    private function createOrganizationsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->getPrefix()}organizations (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            website VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email)
        ) {$this->wpdb->get_charset_collate()};";

        dbDelta($sql);
    }

    private function createVolunteersTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->getPrefix()}volunteers (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            role ENUM('rescue', 'shelter', 'adoption') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email)
        ) {$this->wpdb->get_charset_collate()};";

        dbDelta($sql);
    }
}
