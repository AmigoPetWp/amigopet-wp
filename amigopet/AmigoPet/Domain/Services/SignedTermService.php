<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Services;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Repositories\SignedTermRepository;
use AmigoPetWp\Domain\Database\Repositories\TermRepository;
use AmigoPetWp\Domain\Entities\SignedTerm;

class SignedTermService {
    private $repository;
    private $termRepository;

    public function __construct(
        SignedTermRepository $repository,
        TermRepository $termRepository
    ) {
        $this->repository = $repository;
        $this->termRepository = $termRepository;
    }

    public function create(array $data): SignedTerm {
        $signedTerm = new SignedTerm(
            $data['term_id'],
            $data['user_id'],
            $data['adoption_id'] ?? null,
            $data['ip_address'],
            $data['user_agent'],
            $data['document_url'] ?? null,
            $data['status'] ?? 'signed'
        );

        $id = $this->repository->save($signedTerm);
        $signedTerm->setId($id);

        return $signedTerm;
    }

    public function update(int $id, array $data): ?SignedTerm {
        $signedTerm = $this->repository->findById($id);
        
        if (!$signedTerm) {
            return null;
        }

        if (isset($data['term_id'])) {
            $signedTerm->setTermId($data['term_id']);
        }

        if (isset($data['user_id'])) {
            $signedTerm->setUserId($data['user_id']);
        }

        if (isset($data['adoption_id'])) {
            $signedTerm->setAdoptionId($data['adoption_id']);
        }

        if (isset($data['ip_address'])) {
            $signedTerm->setIpAddress($data['ip_address']);
        }

        if (isset($data['user_agent'])) {
            $signedTerm->setUserAgent($data['user_agent']);
        }

        if (isset($data['document_url'])) {
            $signedTerm->setDocumentUrl($data['document_url']);
        }

        if (isset($data['status'])) {
            $signedTerm->setStatus($data['status']);
        }

        $this->repository->save($signedTerm);

        return $signedTerm;
    }

    public function delete(int $id): bool {
        return $this->repository->delete($id);
    }

    public function findById(int $id): ?SignedTerm {
        return $this->repository->findById($id);
    }

    public function findByTermAndUser(int $termId, int $userId): ?SignedTerm {
        return $this->repository->findByTermAndUser($termId, $userId);
    }

    public function findByAdoption(int $adoptionId): array {
        return $this->repository->findByAdoption($adoptionId);
    }

    public function findAll(array $args = []): array {
        return $this->repository->findAll($args);
    }

    public function revoke(int $id): ?SignedTerm {
        return $this->update($id, ['status' => 'revoked']);
    }

    public function expire(int $id): ?SignedTerm {
        return $this->update($id, ['status' => 'expired']);
    }

    public function validate(array $data): array {
        $errors = [];

        if (empty($data['term_id'])) {
            $errors['term_id'] = __('Termo é obrigatório', 'amigopet');
        } elseif (!$this->termRepository->findById($data['term_id'])) {
            $errors['term_id'] = __('Termo não encontrado', 'amigopet');
        }

        if (empty($data['user_id'])) {
            $errors['user_id'] = __('Usuário é obrigatório', 'amigopet');
        } elseif (!get_user_by('ID', $data['user_id'])) {
            $errors['user_id'] = __('Usuário não encontrado', 'amigopet');
        }

        if (empty($data['ip_address'])) {
            $errors['ip_address'] = __('Endereço IP é obrigatório', 'amigopet');
        }

        if (empty($data['user_agent'])) {
            $errors['user_agent'] = __('User Agent é obrigatório', 'amigopet');
        }

        if (isset($data['status']) && !in_array($data['status'], ['signed', 'revoked', 'expired'])) {
            $errors['status'] = __('Status inválido', 'amigopet');
        }

        return $errors;
    }

    public function hasValidSignature(int $termId, int $userId): bool {
        $signedTerm = $this->findByTermAndUser($termId, $userId);
        
        if (!$signedTerm) {
            return false;
        }

        return $signedTerm->getStatus() === 'signed';
    }
}