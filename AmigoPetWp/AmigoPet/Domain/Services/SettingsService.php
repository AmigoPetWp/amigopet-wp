<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\SettingsRepository;

class SettingsService {
    private SettingsRepository $repository;

    public function __construct(SettingsRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Obtém todas as configurações
     *
     * @return array
     */
    public function getAllSettings(): array {
        return $this->repository->getAll();
    }

    /**
     * Salva configurações agrupadas por seção
     *
     * @param array $formData
     * @return array ['success' => bool, 'message' => string]
     */
    public function saveSettings(array $formData): array {
        // Remove campos especiais do formulário
        unset($formData['action']);
        unset($formData['apwp_settings_nonce']);

        $settings = [];
        $valid_sections = $this->repository->getValidSections();

        // Agrupa configurações por seção
        foreach ($formData as $key => $value) {
            foreach ($valid_sections as $section) {
                if (strpos($key, $section . '_') === 0) {
                    $settings[$section][$key] = $value;
                    break;
                }
            }
        }

        if (empty($settings)) {
            return [
                'success' => false,
                'message' => __('Nenhuma configuração enviada', 'amigopet-wp')
            ];
        }

        // Salva configurações por seção
        $success = true;
        foreach ($settings as $section => $values) {
            if (!$this->repository->saveSection($section, $values)) {
                $success = false;
            }
        }

        return [
            'success' => $success,
            'message' => $success 
                ? __('Configurações salvas com sucesso', 'amigopet-wp')
                : __('Erro ao salvar algumas configurações', 'amigopet-wp')
        ];
    }
}
