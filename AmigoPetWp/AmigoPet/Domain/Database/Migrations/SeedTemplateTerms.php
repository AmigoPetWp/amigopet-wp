<?php

namespace AmigoPetWp\Domain\Database\Migrations;

use AmigoPetWp\Domain\Database\Migration;
use AmigoPetWp\Domain\Services\TemplateTermsService;
use AmigoPetWp\Domain\Database\Seeds\TemplateTermsSeeder;
use AmigoPetWp\Domain\Database\Repositories\TemplateTermsRepository;

class SeedTemplateTerms extends Migration
{
    public function __construct()
    {
        parent::__construct();
        $this->prefix = $this->wpdb->prefix . 'apwp_';
    }

    public function getVersion(): string
    {
        return '2.0.1';
    }

    public function getDescription(): string
    {
        return 'Insere os templates de termos padrão';
    }

    public function up(): void
    {
        $repository = new TemplateTermsRepository($this->wpdb);
        $service = new TemplateTermsService($repository);
        $seeder = new TemplateTermsSeeder($service);
        $seeder->seed();
    }

    public function down(): void
    {
        $this->wpdb->query("TRUNCATE TABLE {$this->prefix}template_terms");
    }
}
