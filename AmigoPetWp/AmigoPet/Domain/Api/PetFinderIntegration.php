<?php
namespace AmigoPetWp\Domain\Api;

use AmigoPetWp\Domain\Settings\Settings;

class PetFinderIntegration {
    private string $apiKey;
    
    public function __construct() {
        $settings = Settings::getAll();
        $this->apiKey = $settings['petfinder_key'] ?? '';
    }
    private $apiKey;
    
    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
    }
    
    public function getAvailablePets(): array {
        $response = wp_remote_get('https://api.petfinder.com/v2/animals', [
            'headers' => ['Authorization' => 'Bearer ' . $this->apiKey]
        ]);
        
        if (is_wp_error($response)) {
            throw new \RuntimeException('Erro na integração: ' . $response->get_error_message());
        }
        
        return json_decode(wp_remote_retrieve_body($response), true);
    }
}
