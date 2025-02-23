<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\AdoptionDocumentRepository;
use AmigoPetWp\Domain\Entities\AdoptionDocument;

class AdoptionDocumentService {
    private $repository;
    private $documentProcessor;

    public function __construct(
        AdoptionDocumentRepository $repository,
        DocumentProcessorService $documentProcessor
    ) {
        $this->repository = $repository;
        $this->documentProcessor = $documentProcessor;
    }

    /**
     * Cria um novo documento
     */
    public function createDocument(array $data): int {
        // Processa o conteúdo do documento
        $content = $this->documentProcessor->processAdoptionDocument(
            $data['document_type'],
            $data['adoption_id']
        );

        // Busca o template para o título
        $template = $this->documentProcessor->getTemplateService()->getActiveTemplate($data['document_type']);
        $title = $template ? $template['title'] : 'Documento de Adoção';

        // Prepara os metadados do PDF
        $metadata = [
            'author' => get_userdata($data['signed_by'])->display_name,
            'subject' => 'Documento de Adoção - ' . $data['document_type'],
            'keywords' => 'adoção, pet, documento, ' . $data['document_type'],
            'watermark' => $data['status'] === 'draft' ? 'RASCUNHO' : null,
            'protect' => true,
            'user_password' => '', // Permite visualização
            'owner_password' => uniqid('apwp_') // Senha para edição
        ];

        // Gera o PDF
        $documentUrl = $this->documentProcessor->generatePDF(
            $content,
            $title,
            $metadata
        );

        $document = new AdoptionDocument(
            $data['adoption_id'],
            $data['document_type'],
            $content,
            $data['signed_by'],
            $documentUrl,
            $data['status'] ?? 'pending'
        );

        return $this->repository->save($document);
    }

    /**
     * Atualiza um documento existente
     */
    public function updateDocument(int $id, array $data): bool {
        $document = $this->repository->findById($id);
        if (!$document) {
            return false;
        }

        // Se o conteúdo foi alterado, processa novamente
        if (isset($data['content'])) {
            $content = $this->documentProcessor->processAdoptionDocument(
                $data['content'],
                $document->getAdoptionId()
            );
            $document->setContent($content);

            // Prepara os metadados do PDF
            $metadata = [
                'author' => get_userdata($document->getSignedBy())->display_name,
                'subject' => 'Documento de Adoção - ' . $document->getDocumentType(),
                'keywords' => 'adoção, pet, documento, ' . $document->getDocumentType(),
                'watermark' => $document->getStatus() === 'draft' ? 'RASCUNHO' : null,
                'protect' => true,
                'user_password' => '', // Permite visualização
                'owner_password' => uniqid('apwp_') // Senha para edição
            ];

            // Gera novo PDF
            $documentUrl = $this->documentProcessor->generatePDF(
                $content,
                $document->getTitle(),
                $metadata
            );
            $document->setDocumentUrl($documentUrl);
        }

        if (isset($data['status'])) {
            $document->setStatus($data['status']);
        }

        return $this->repository->save($document) > 0;
    }

    /**
     * Assina um documento
     */
    public function signDocument(int $id): bool {
        $document = $this->repository->findById($id);
        if (!$document) {
            return false;
        }

        $document->sign();
        if ($this->repository->save($document) > 0) {
            // Notifica os envolvidos
            do_action('apwp_document_signed', $document);
            return true;
        }

        return false;
    }

    /**
     * Cancela um documento
     */
    public function cancelDocument(int $id): bool {
        $document = $this->repository->findById($id);
        if (!$document) {
            return false;
        }

        $document->cancel();
        if ($this->repository->save($document) > 0) {
            // Notifica os envolvidos
            do_action('apwp_document_cancelled', $document);
            return true;
        }

        return false;
    }

    /**
     * Marca um documento como expirado
     */
    public function expireDocument(int $id): bool {
        $document = $this->repository->findById($id);
        if (!$document) {
            return false;
        }

        $document->expire();
        if ($this->repository->save($document) > 0) {
            // Notifica os envolvidos
            do_action('apwp_document_expired', $document);
            return true;
        }

        return false;
    }

    /**
     * Exclui um documento
     */
    public function deleteDocument(int $id): bool {
        $document = $this->repository->findById($id);
        if (!$document) {
            return false;
        }

        if ($document->isSigned()) {
            return false;
        }

        if ($this->repository->delete($id)) {
            // Notifica os envolvidos
            do_action('apwp_document_deleted', $document);
            return true;
        }

        return false;
    }

    /**
     * Busca um documento por ID
     */
    public function findById(int $id): ?AdoptionDocument {
        return $this->repository->findById($id);
    }

    /**
     * Lista documentos por adoção
     */
    public function findByAdoption(int $adoptionId): array {
        return $this->repository->findByAdoption($adoptionId);
    }

    /**
     * Lista documentos por tipo
     */
    public function findByType(string $documentType): array {
        return $this->repository->findByType($documentType);
    }

    /**
     * Lista documentos por assinante
     */
    public function findBySignedBy(int $signedBy): array {
        return $this->repository->findBySignedBy($signedBy);
    }

    /**
     * Lista documentos por período
     */
    public function findByDateRange(string $startDate, string $endDate): array {
        return $this->repository->findAll([
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
                'column' => 'signed_at'
            ]
        ]);
    }

    /**
     * Lista documentos por critérios
     */
    public function findDocuments(array $criteria = []): array {
        return $this->repository->findAll($criteria);
    }

    /**
     * Gera relatório de documentos
     */
    public function getReport(?string $startDate = null, ?string $endDate = null): array {
        return $this->repository->getReport($startDate, $endDate);
    }

    /**
     * Valida um documento
     */
    public function validateDocument(AdoptionDocument $document): array {
        $errors = [];

        if (empty($document->getDocumentType())) {
            $errors[] = 'O tipo de documento é obrigatório';
        }

        if (empty($document->getDocumentUrl())) {
            $errors[] = 'A URL do documento é obrigatória';
        }

        if (empty($document->getSignedBy())) {
            $errors[] = 'O assinante é obrigatório';
        }

        if (!$document->getSignedAt()) {
            $errors[] = 'A data de assinatura é obrigatória';
        }

        return $errors;
    }

    /**
     * Verifica se um documento já existe
     */
    public function exists(string $documentType, int $signedBy): bool {
        $documents = $this->repository->findAll([
            'document_type' => $documentType,
            'signed_by' => $signedBy
        ]);

        return !empty($documents);
    }
}
