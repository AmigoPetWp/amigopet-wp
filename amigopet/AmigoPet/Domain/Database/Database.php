<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Repositories\AdopterRepository;
use AmigoPetWp\Domain\Database\Repositories\AdoptionRepository;
use AmigoPetWp\Domain\Database\Repositories\AdoptionDocumentRepository;
use AmigoPetWp\Domain\Database\Repositories\AdoptionPaymentRepository;
use AmigoPetWp\Domain\Database\Repositories\DonationRepository;
use AmigoPetWp\Domain\Database\Repositories\EventRepository;
use AmigoPetWp\Domain\Database\Repositories\OrganizationRepository;
use AmigoPetWp\Domain\Database\Repositories\PetRepository;
use AmigoPetWp\Domain\Database\Repositories\PetBreedRepository;
use AmigoPetWp\Domain\Database\Repositories\PetSpeciesRepository;
use AmigoPetWp\Domain\Database\Repositories\VolunteerRepository;
use AmigoPetWp\Domain\Database\Repositories\SignedTermRepository;
use AmigoPetWp\Domain\Database\Repositories\TermRepository;
use AmigoPetWp\Domain\Database\Repositories\TermTypeRepository;
use AmigoPetWp\Domain\Database\Repositories\TermVersionRepository;
use AmigoPetWp\Domain\Database\Repositories\QRCodeRepository;
use AmigoPetWp\Domain\Database\Repositories\TemplateTermsRepository;
use AmigoPetWp\Domain\Database\Repositories\SettingsRepository;

class Database
{
    private static $instance = null;
    private $repositories = [];

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->repositories['adopter'] = new AdopterRepository();
        $this->repositories['adoption'] = new AdoptionRepository();
        $this->repositories['adoption_document'] = new AdoptionDocumentRepository();
        $this->repositories['adoption_payment'] = new AdoptionPaymentRepository();
        $this->repositories['donation'] = new DonationRepository();
        $this->repositories['event'] = new EventRepository();
        $this->repositories['organization'] = new OrganizationRepository();
        $this->repositories['pet'] = new PetRepository();
        $this->repositories['pet_breed'] = new PetBreedRepository();
        $this->repositories['pet_species'] = new PetSpeciesRepository();
        $this->repositories['volunteer'] = new VolunteerRepository();
        $this->repositories['signed_term'] = new SignedTermRepository();
        $this->repositories['term'] = new TermRepository();
        $this->repositories['term_type'] = new TermTypeRepository();
        $this->repositories['term_version'] = new TermVersionRepository();
        $this->repositories['qrcode'] = new QRCodeRepository();
        $this->repositories['template_terms'] = new TemplateTermsRepository();
        $this->repositories['settings'] = new SettingsRepository();
    }

    public function getRepository(string $name)
    {
        return $this->repositories[$name] ?? null;
    }

    public function getAdopterRepository()
    {
        return $this->getRepository('adopter');
    }
    public function getAdoptionRepository()
    {
        return $this->getRepository('adoption');
    }
    public function getAdoptionDocumentRepository()
    {
        return $this->getRepository('adoption_document');
    }
    public function getAdoptionPaymentRepository()
    {
        return $this->getRepository('adoption_payment');
    }
    public function getDonationRepository()
    {
        return $this->getRepository('donation');
    }
    public function getEventRepository()
    {
        return $this->getRepository('event');
    }
    public function getOrganizationRepository()
    {
        return $this->getRepository('organization');
    }
    public function getPetRepository()
    {
        return $this->getRepository('pet');
    }
    public function getPetBreedRepository()
    {
        return $this->getRepository('pet_breed');
    }
    public function getPetSpeciesRepository()
    {
        return $this->getRepository('pet_species');
    }
    public function getVolunteerRepository()
    {
        return $this->getRepository('volunteer');
    }
    public function getSignedTermRepository()
    {
        return $this->getRepository('signed_term');
    }
    public function getTermRepository()
    {
        return $this->getRepository('term');
    }
    public function getTermTypeRepository()
    {
        return $this->getRepository('term_type');
    }
    public function getTermVersionRepository()
    {
        return $this->getRepository('term_version');
    }
    public function getQRCodeRepository()
    {
        return $this->getRepository('qrcode');
    }
    public function getTemplateTermsRepository()
    {
        return $this->getRepository('template_terms');
    }
    public function getSettingsRepository()
    {
        return $this->getRepository('settings');
    }

    public static function getPrefix(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'apwp_';
    }

}