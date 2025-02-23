<?php
namespace AmigoPetWp\Domain\Api;

use AmigoPetWp\Domain\Settings\Settings;

class PaymentGateway {
    private string $apiKey;
    
    public function __construct() {
        $settings = Settings::getAll();
        $this->apiKey = $settings['payment_gateway_key'] ?? '';
    }
    private $apiKey;
    private $secretKey;
    private $sandbox;
    
    public function __construct(string $apiKey, string $secretKey, bool $sandbox = false) {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->sandbox = $sandbox;
    }
    
    /**
     * Processa um pagamento
     */
    public function processPayment(array $paymentData): array {
        // Valida os dados do pagamento
        $this->validatePaymentData($paymentData);
        
        // Monta os dados para a API
        $data = [
            'amount' => $paymentData['amount'],
            'currency' => 'BRL',
            'description' => $paymentData['description'],
            'payment_method' => $paymentData['payment_method'],
            'customer' => [
                'name' => $paymentData['customer_name'],
                'email' => $paymentData['customer_email'],
                'document' => $paymentData['customer_document']
            ]
        ];
        
        // Faz a requisição para a API
        $response = wp_remote_post($this->getApiUrl() . '/payments', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($data)
        ]);
        
        if (is_wp_error($response)) {
            throw new \RuntimeException('Erro no gateway de pagamento: ' . $response->get_error_message());
        }
        
        $result = json_decode(wp_remote_retrieve_body($response), true);
        if (!$result) {
            throw new \RuntimeException('Erro ao decodificar resposta do gateway');
        }
        
        return $result;
    }
    
    /**
     * Consulta o status de um pagamento
     */
    public function getPaymentStatus(string $paymentId): array {
        $response = wp_remote_get($this->getApiUrl() . '/payments/' . $paymentId, [
            'headers' => ['Authorization' => 'Bearer ' . $this->apiKey]
        ]);
        
        if (is_wp_error($response)) {
            throw new \RuntimeException('Erro ao consultar pagamento: ' . $response->get_error_message());
        }
        
        $result = json_decode(wp_remote_retrieve_body($response), true);
        if (!$result) {
            throw new \RuntimeException('Erro ao decodificar resposta do gateway');
        }
        
        return $result;
    }
    
    /**
     * Processa um reembolso
     */
    public function processRefund(string $paymentId, float $amount): array {
        $data = ['amount' => $amount];
        
        $response = wp_remote_post($this->getApiUrl() . '/payments/' . $paymentId . '/refund', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($data)
        ]);
        
        if (is_wp_error($response)) {
            throw new \RuntimeException('Erro ao processar reembolso: ' . $response->get_error_message());
        }
        
        $result = json_decode(wp_remote_retrieve_body($response), true);
        if (!$result) {
            throw new \RuntimeException('Erro ao decodificar resposta do gateway');
        }
        
        return $result;
    }
    
    /**
     * Valida os dados do pagamento
     */
    private function validatePaymentData(array $data): void {
        $required = [
            'amount',
            'description',
            'payment_method',
            'customer_name',
            'customer_email',
            'customer_document'
        ];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException('Campo obrigatório não informado: ' . $field);
            }
        }
        
        if ($data['amount'] <= 0) {
            throw new \InvalidArgumentException('Valor deve ser maior que zero');
        }
    }
    
    /**
     * Retorna a URL da API
     */
    private function getApiUrl(): string {
        return $this->sandbox
            ? 'https://api.sandbox.gateway.com/v1'
            : 'https://api.gateway.com/v1';
    }
}
