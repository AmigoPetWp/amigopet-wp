<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Services;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Repositories\PetMedicalRecordRepository;
use AmigoPetWp\Domain\Entities\PetMedicalRecord;

class PetMedicalService {
    private $repository;
    private $recordTypes = [
        'vaccine' => 'Vacina',
        'exam' => 'Exame',
        'surgery' => 'Cirurgia',
        'consultation' => 'Consulta',
        'medication' => 'Medicação',
        'other' => 'Outro'
    ];
    
    public function __construct(PetMedicalRecordRepository $repository) {
        $this->repository = $repository;
    }
    
    /**
     * Adiciona um registro médico
     */
    public function addRecord(array $data): int {
        // Valida tipo
        if (!array_key_exists($data['type'], $this->recordTypes)) {
            throw new \Exception(esc_html__('Tipo de registro inválido', 'amigopet'));
        }
        
        // Cria o registro
        $record = new PetMedicalRecord(
            $data['pet_id'],
            new \DateTime($data['date']),
            $data['type'],
            $data['description'],
            $data['veterinarian'],
            $data['attachments'] ?? []
        );
        
        // Salva no banco
        return $this->repository->save($record);
    }

    /**
     * Atualiza um registro médico
     */
    public function updateRecord(int $id, array $data): bool {
        $record = $this->repository->findById($id);
        if (!$record) {
            return false;
        }

        if (isset($data['date'])) {
            $record->setDate(new \DateTime($data['date']));
        }

        if (isset($data['type'])) {
            if (!array_key_exists($data['type'], $this->recordTypes)) {
                throw new \Exception(esc_html__('Tipo de registro inválido', 'amigopet'));
            }
            $record->setType($data['type']);
        }

        if (isset($data['description'])) {
            $record->setDescription($data['description']);
        }

        if (isset($data['veterinarian'])) {
            $record->setVeterinarian($data['veterinarian']);
        }

        if (isset($data['attachments'])) {
            $record->setAttachments($data['attachments']);
        }

        return $this->repository->save($record) > 0;
    }

    /**
     * Remove um registro médico
     */
    public function deleteRecord(int $id): bool {
        return $this->repository->delete($id);
    }

    /**
     * Busca um registro médico por ID
     */
    public function getRecord(int $id): ?PetMedicalRecord {
        return $this->repository->findById($id);
    }

    /**
     * Lista registros médicos de um pet
     */
    public function listRecords(int $petId, array $filters = []): array {
        $filters['pet_id'] = $petId;
        return $this->repository->findAll($filters);
    }

    /**
     * Retorna os tipos de registro disponíveis
     */
    public function getRecordTypes(): array {
        return $this->recordTypes;
    }

    /**
     * Gera relatório de registros médicos
     */
    public function getReport(int $petId, ?string $startDate = null, ?string $endDate = null): array {
        return $this->repository->getReport($petId, $startDate, $endDate);
    }

    /**
     * Adiciona anexos a um registro médico
     */
    public function addAttachments(int $recordId, array $files): bool {
        $record = $this->repository->findById($recordId);
        if (!$record) {
            return false;
        }

        $attachments = $record->getAttachments();
        foreach ($files as $file) {
            $attachments[] = $this->uploadAttachment($file);
        }

        $record->setAttachments($attachments);
        return $this->repository->save($record) > 0;
    }

    /**
     * Remove anexos de um registro médico
     */
    public function removeAttachments(int $recordId, array $fileIds): bool {
        $record = $this->repository->findById($recordId);
        if (!$record) {
            return false;
        }

        $attachments = $record->getAttachments();
        foreach ($fileIds as $fileId) {
            $key = array_search($fileId, array_column($attachments, 'id'));
            if ($key !== false) {
                $this->deleteAttachment($attachments[$key]);
                unset($attachments[$key]);
            }
        }

        $record->setAttachments(array_values($attachments));
        return $this->repository->save($record) > 0;
    }

    /**
     * Upload de arquivo de anexo
     */
    private function uploadAttachment(array $file): array {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $uploadedFile = wp_handle_upload($file, ['test_form' => false]);

        if (isset($uploadedFile['error'])) {
            throw new \Exception(esc_html($uploadedFile['error']));
        }

        return [
            'id' => uniqid('med_'),
            'name' => basename($file['name']),
            'url' => $uploadedFile['url'],
            'type' => $uploadedFile['type'],
            'path' => $uploadedFile['file']
        ];
    }

    /**
     * Remove arquivo de anexo
     */
    private function deleteAttachment(array $attachment): void {
        if (file_exists($attachment['path'])) {
            wp_delete_file($attachment['path']);
        }
    }
}