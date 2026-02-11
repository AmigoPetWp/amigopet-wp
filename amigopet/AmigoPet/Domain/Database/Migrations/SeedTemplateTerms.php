<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

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
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'template_terms');
        if (!is_string($table) || $table === '') {
            return;
        }
        $this->wpdb->query("DELETE FROM `{$table}`");
    }
}