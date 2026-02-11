<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Api;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Settings\Settings;

class PetFinderIntegration
{
    private string $apiKey;

    public function __construct(string $apiKey = '')
    {
        if (empty($apiKey)) {
            $settings = Settings::getAll();
            $this->apiKey = $settings['petfinder_key'] ?? '';
        } else {
            $this->apiKey = $apiKey;
        }
    }


    public function getAvailablePets(): array
    {
        $response = wp_remote_get('https://api.petfinder.com/v2/animals', [
            'headers' => ['Authorization' => 'Bearer ' . $this->apiKey]
        ]);

        if (is_wp_error($response)) {
            // translators: %s: placeholder
            throw new \RuntimeException(esc_html(sprintf(esc_html__('Erro na integração: %s', 'amigopet'), esc_html($response->get_error_message()))));
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }
}