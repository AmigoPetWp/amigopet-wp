<?php
namespace AmigoPetWp\Domain\Api;

use AmigoPetWp\Domain\Settings\Settings;

class LocationService {
    private string $apiKey;
    
    public function __construct() {
        $settings = Settings::getAll();
        $this->apiKey = $settings['google_maps_key'] ?? '';
    }
    
    /**
     * Busca coordenadas a partir de um endereço
     */
    public function geocode(string $address): array {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query([
            'address' => $address,
            'key' => $this->apiKey
        ]);
        
        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            throw new \RuntimeException('Erro na geocodificação: ' . $response->get_error_message());
        }
        
        $result = json_decode(wp_remote_retrieve_body($response), true);
        if (!$result || $result['status'] !== 'OK') {
            throw new \RuntimeException('Erro ao geocodificar endereço');
        }
        
        $location = $result['results'][0]['geometry']['location'];
        return [
            'lat' => $location['lat'],
            'lng' => $location['lng']
        ];
    }
    
    /**
     * Busca endereço a partir de coordenadas
     */
    public function reverseGeocode(float $lat, float $lng): array {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query([
            'latlng' => $lat . ',' . $lng,
            'key' => $this->apiKey
        ]);
        
        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            throw new \RuntimeException('Erro na geocodificação reversa: ' . $response->get_error_message());
        }
        
        $result = json_decode(wp_remote_retrieve_body($response), true);
        if (!$result || $result['status'] !== 'OK') {
            throw new \RuntimeException('Erro ao geocodificar coordenadas');
        }
        
        return $this->parseAddressComponents($result['results'][0]['address_components']);
    }
    
    /**
     * Calcula a distância entre dois pontos
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float {
        $earthRadius = 6371; // km
        
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);
        
        $a = sin($latDelta/2) * sin($latDelta/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lngDelta/2) * sin($lngDelta/2);
            
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
    
    /**
     * Processa os componentes do endereço retornados pela API
     */
    private function parseAddressComponents(array $components): array {
        $address = [
            'street_number' => '',
            'street' => '',
            'neighborhood' => '',
            'city' => '',
            'state' => '',
            'country' => '',
            'postal_code' => ''
        ];
        
        foreach ($components as $component) {
            $type = $component['types'][0];
            switch ($type) {
                case 'street_number':
                    $address['street_number'] = $component['long_name'];
                    break;
                case 'route':
                    $address['street'] = $component['long_name'];
                    break;
                case 'sublocality':
                    $address['neighborhood'] = $component['long_name'];
                    break;
                case 'locality':
                    $address['city'] = $component['long_name'];
                    break;
                case 'administrative_area_level_1':
                    $address['state'] = $component['short_name'];
                    break;
                case 'country':
                    $address['country'] = $component['long_name'];
                    break;
                case 'postal_code':
                    $address['postal_code'] = $component['long_name'];
                    break;
            }
        }
        
        return $address;
    }
}
