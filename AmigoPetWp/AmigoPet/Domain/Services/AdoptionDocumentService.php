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
    public function updateDocument(int $id, array $data): void {
        $document = $this->repository->findById($id);
        if (!$document) {
            throw new \InvalidArgumentException("Documento não encontrado");
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
                $title,
                $metadata
            );
            $document->setDocumentUrl($documentUrl);
        }

        if (isset($data['status'])) {
            $document->setStatus($data['status']);
        }

        $this->repository->save($document);
    }

    /**
     * Assina um documento
     */
    public function signDocument(int $id): void {
        $document = $this->repository->findById($id);
        if (!$document) {
            throw new \InvalidArgumentException("Documento não encontrado");
        }

        $document->sign();
        $this->repository->save($document);

        // Notifica os envolvidos
        do_action('apwp_document_signed', $document);
    }

    /**
     * Cancela um documento
     */
    public function cancelDocument(int $id): void {
        $document = $this->repository->findById($id);
        if (!$document) {
            throw new \InvalidArgumentException("Documento não encontrado");
        }

        $document->cancel();
        $this->repository->save($document);

        // Notifica os envolvidos
        do_action('apwp_document_cancelled', $document);
    }

    /**
     * Marca um documento como expirado
     */
    public function expireDocument(int $id): void {
        $document = $this->repository->findById($id);
        if (!$document) {
            throw new \InvalidArgumentException("Documento não encontrado");
        }

        $document->expire();
        $this->repository->save($document);

        // Notifica os envolvidos
        do_action('apwp_document_expired', $document);
    }

    /**
     * Exclui um documento
     */
    public function deleteDocument(int $id): void {
        $document = $this->repository->findById($id);
        if (!$document) {
            throw new \InvalidArgumentException("Documento não encontrado");
        }

        if ($document->isSigned()) {
            throw new \InvalidArgumentException("Não é possível excluir um documento assinado");
        }

        if (!$this->repository->delete($id)) {
            throw new \RuntimeException("Erro ao excluir o documento");
        }

        // Notifica os envolvidos
        do_action('apwp_document_deleted', $document);
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
     * Lista documentos por status
     */
    public function findByStatus(string $status): array {
        return $this->repository->findByStatus($status);
    }

    /**
     * Retorna todos os tipos de documentos permitidos
     */
    public function getDocumentTypes(): array {
        return AdoptionDocument::getAllowedDocumentTypes();
    }

    /**
     * Retorna todos os status permitidos
     */
    public function getStatus(): array {
        return AdoptionDocument::getAllowedStatus();
    }
}
