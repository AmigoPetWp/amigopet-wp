<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class SettingsRepository {
    private const PREFIX = 'apwp_';
    private const VALID_SECTIONS = ['general', 'api', 'terms', 'workflow', 'email'];

    /**
     * Obtém todas as configurações
     *
     * @return array
     */
    public function getAll(): array {
        $settings = [];
        foreach (self::VALID_SECTIONS as $section) {
            $settings[$section] = $this->getBySection($section);
        }
        return $settings;
    }

    /**
     * Obtém configurações de uma seção específica
     *
     * @param string $section
     * @return array
     */
    public function getBySection(string $section): array {
        if (!in_array($section, self::VALID_SECTIONS)) {
            return [];
        }
        return get_option(self::PREFIX . $section, []);
    }

    /**
     * Salva configurações de uma seção
     *
     * @param string $section
     * @param array $values
     * @return bool
     */
    public function saveSection(string $section, array $values): bool {
        if (!in_array($section, self::VALID_SECTIONS)) {
            return false;
        }
        return update_option(self::PREFIX . $section, $values);
    }

    /**
     * Retorna as seções válidas
     *
     * @return array
     */
    public function getValidSections(): array {
        return self::VALID_SECTIONS;
    }
}