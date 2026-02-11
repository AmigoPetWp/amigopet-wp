<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Api;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Settings\Settings;

class PaymentGateway
{
    private string $apiKey;
    private string $secretKey;
    private bool $sandbox;

    public function __construct(string $apiKey = '', string $secretKey = '', bool $sandbox = false)
    {
        if (empty($apiKey)) {
            $settings = Settings::getAll();
            $this->apiKey = $settings['payment_gateway_key'] ?? '';
            $this->secretKey = $settings['payment_gateway_secret'] ?? '';
            $this->sandbox = (bool) ($settings['payment_gateway_sandbox'] ?? false);
        } else {
            $this->apiKey = $apiKey;
            $this->secretKey = $secretKey;
            $this->sandbox = $sandbox;
        }
    }


    /**
     * Processa um pagamento
     */
    public function processPayment(array $paymentData): array
    {
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
            // translators: %s: placeholder
            throw new \RuntimeException(esc_html(sprintf(esc_html__('Erro no gateway de pagamento: %s', 'amigopet'), esc_html($response->get_error_message()))));
        }

        $result = json_decode(wp_remote_retrieve_body($response), true);
        if (!$result) {
            throw new \RuntimeException(esc_html__('Erro ao decodificar resposta do gateway', 'amigopet'));
        }

        return $result;
    }

    /**
     * Consulta o status de um pagamento
     */
    public function getPaymentStatus(string $paymentId): array
    {
        $response = wp_remote_get($this->getApiUrl() . '/payments/' . $paymentId, [
            'headers' => ['Authorization' => 'Bearer ' . $this->apiKey]
        ]);

        if (is_wp_error($response)) {
            // translators: %s: placeholder
            throw new \RuntimeException(esc_html(sprintf(esc_html__('Erro ao consultar pagamento: %s', 'amigopet'), esc_html($response->get_error_message()))));
        }

        $result = json_decode(wp_remote_retrieve_body($response), true);
        if (!$result) {
            throw new \RuntimeException(esc_html__('Erro ao decodificar resposta do gateway', 'amigopet'));
        }

        return $result;
    }

    /**
     * Processa um reembolso
     */
    public function processRefund(string $paymentId, float $amount): array
    {
        $data = ['amount' => $amount];

        $response = wp_remote_post($this->getApiUrl() . '/payments/' . $paymentId . '/refund', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($data)
        ]);

        if (is_wp_error($response)) {
            // translators: %s: placeholder
            throw new \RuntimeException(esc_html(sprintf(esc_html__('Erro ao processar reembolso: %s', 'amigopet'), esc_html($response->get_error_message()))));
        }

        $result = json_decode(wp_remote_retrieve_body($response), true);
        if (!$result) {
            throw new \RuntimeException(esc_html__('Erro ao decodificar resposta do gateway', 'amigopet'));
        }

        return $result;
    }

    /**
     * Valida os dados do pagamento
     */
    private function validatePaymentData(array $data): void
    {
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
                // translators: %s: placeholder
                throw new \InvalidArgumentException(esc_html(sprintf(esc_html__('Campo obrigatório não informado: %s', 'amigopet'), esc_html($field))));
            }
        }

        if ($data['amount'] <= 0) {
            throw new \InvalidArgumentException(esc_html__('Valor deve ser maior que zero', 'amigopet'));
        }
    }

    /**
     * Retorna a URL da API
     */
    private function getApiUrl(): string
    {
        return $this->sandbox
            ? 'https://api.sandbox.gateway.com/v1'
            : 'https://api.gateway.com/v1';
    }
}