<?php
namespace Domain\Api;

class QRCodeGenerator {
    private $baseUrl;
    
    public function __construct(string $baseUrl) {
        $this->baseUrl = rtrim($baseUrl, '/');
    }
    
    /**
     * Gera um QR Code para um pet
     */
    public function generateForPet(int $petId): array {
        $trackingUrl = $this->baseUrl . '/pet/' . $petId;
        $code = $this->generateUniqueCode();
        
        // Usa a API do Google Charts para gerar o QR Code
        $qrCodeUrl = 'https://chart.googleapis.com/chart?' . http_build_query([
            'cht' => 'qr',
            'chs' => '300x300',
            'chl' => $trackingUrl,
            'choe' => 'UTF-8'
        ]);
        
        // Faz o download da imagem
        $response = wp_remote_get($qrCodeUrl);
        if (is_wp_error($response)) {
            throw new \RuntimeException('Erro ao gerar QR Code: ' . $response->get_error_message());
        }
        
        // Salva a imagem no WordPress
        $upload = wp_upload_bits('qrcode-' . $code . '.png', null, wp_remote_retrieve_body($response));
        if ($upload['error']) {
            throw new \RuntimeException('Erro ao salvar QR Code: ' . $upload['error']);
        }
        
        return [
            'code' => $code,
            'tracking_url' => $trackingUrl,
            'qrcode_url' => $upload['url']
        ];
    }
    
    /**
     * Gera um código único para o QR Code
     */
    private function generateUniqueCode(): string {
        return substr(md5(uniqid(mt_rand(), true)), 0, 8);
    }
}
