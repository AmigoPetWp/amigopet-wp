<?php
namespace AmigoPetWp\Domain\Api;

class DocumentSigner {
    private $apiKey;
    private $secretKey;
    private $sandbox;
    
    public function __construct(string $apiKey, string $secretKey, bool $sandbox = false) {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->sandbox = $sandbox;
    }
    
    /**
     * Cria um documento para assinatura
     */
    public function createDocument(array $documentData): array {
        // Valida os dados do documento
        $this->validateDocumentData($documentData);
        
        // Monta os dados para a API
        $data = [
            'document' => [
                'type' => $documentData['type'],
                'name' => $documentData['name'],
                'content' => base64_encode($documentData['content'])
            ],
            'signers' => array_map(function($signer) {
                return [
                    'name' => $signer['name'],
                    'email' => $signer['email'],
                    'authentication' => [
                        'type' => 'email'
                    ]
                ];
            }, $documentData['signers'])
        ];
        
        // Faz a requisição para a API
        $response = wp_remote_post($this->getApiUrl() . '/documents', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($data)
        ]);
        
        if (is_wp_error($response)) {
            throw new \RuntimeException('Erro ao criar documento: ' . $response->get_error_message());
        }
        
        $result = json_decode(wp_remote_retrieve_body($response), true);
        if (!$result) {
            throw new \RuntimeException('Erro ao decodificar resposta da API');
        }
        
        return $result;
    }
    
    /**
     * Consulta o status de um documento
     */
    public function getDocumentStatus(string $documentId): array {
        $response = wp_remote_get($this->getApiUrl() . '/documents/' . $documentId, [
            'headers' => ['Authorization' => 'Bearer ' . $this->apiKey]
        ]);
        
        if (is_wp_error($response)) {
            throw new \RuntimeException('Erro ao consultar documento: ' . $response->get_error_message());
        }
        
        $result = json_decode(wp_remote_retrieve_body($response), true);
        if (!$result) {
            throw new \RuntimeException('Erro ao decodificar resposta da API');
        }
        
        return $result;
    }
    
    /**
     * Baixa um documento assinado
     */
    public function downloadSignedDocument(string $documentId): string {
        $response = wp_remote_get($this->getApiUrl() . '/documents/' . $documentId . '/download', [
            'headers' => ['Authorization' => 'Bearer ' . $this->apiKey]
        ]);
        
        if (is_wp_error($response)) {
            throw new \RuntimeException('Erro ao baixar documento: ' . $response->get_error_message());
        }
        
        return wp_remote_retrieve_body($response);
    }
    
    /**
     * Valida os dados do documento
     */
    private function validateDocumentData(array $data): void {
        $required = [
            'type',
            'name',
            'content',
            'signers'
        ];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException('Campo obrigatório não informado: ' . $field);
            }
        }
        
        if (empty($data['signers'])) {
            throw new \InvalidArgumentException('É necessário informar pelo menos um assinante');
        }
        
        foreach ($data['signers'] as $signer) {
            if (empty($signer['name']) || empty($signer['email'])) {
                throw new \InvalidArgumentException('Nome e email são obrigatórios para os assinantes');
            }
        }
    }
    
    /**
     * Retorna a URL da API
     */
    private function getApiUrl(): string {
        return $this->sandbox
            ? 'https://api.sandbox.signer.com/v1'
            : 'https://api.signer.com/v1';
    }
}
