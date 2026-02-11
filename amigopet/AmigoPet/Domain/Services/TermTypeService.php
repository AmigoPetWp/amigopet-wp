<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Services;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Repositories\TermTypeRepository;
use AmigoPetWp\Domain\Entities\TermType;

class TermTypeService {
    private $repository;

    public function __construct(TermTypeRepository $repository) {
        $this->repository = $repository;
    }

    public function create(array $data): TermType {
        $type = new TermType(
            $data['name'],
            $this->generateSlug($data['name']),
            $data['description'] ?? null,
            $data['required'] ?? false,
            $data['status'] ?? 'active'
        );

        $id = $this->repository->save($type);
        $type->setId($id);

        return $type;
    }

    public function update(int $id, array $data): ?TermType {
        $type = $this->repository->findById($id);
        
        if (!$type) {
            return null;
        }

        if (isset($data['name'])) {
            $type->setName($data['name']);
            if (!isset($data['slug'])) {
                $type->setSlug($this->generateSlug($data['name']));
            }
        }

        if (isset($data['slug'])) {
            $type->setSlug($data['slug']);
        }

        if (isset($data['description'])) {
            $type->setDescription($data['description']);
        }

        if (isset($data['required'])) {
            $type->setRequired($data['required']);
        }

        if (isset($data['status'])) {
            $type->setStatus($data['status']);
        }

        $this->repository->save($type);

        return $type;
    }

    public function delete(int $id): bool {
        return $this->repository->delete($id);
    }

    public function findById(int $id): ?TermType {
        return $this->repository->findById($id);
    }

    public function findBySlug(string $slug): ?TermType {
        return $this->repository->findBySlug($slug);
    }

    public function findAll(array $args = []): array {
        return $this->repository->findAll($args);
    }

    public function activate(int $id): ?TermType {
        return $this->update($id, ['status' => 'active']);
    }

    public function deactivate(int $id): ?TermType {
        return $this->update($id, ['status' => 'inactive']);
    }

    public function validate(array $data): array {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = __('Nome é obrigatório', 'amigopet');
        }

        if (isset($data['slug'])) {
            if (empty($data['slug'])) {
                $errors['slug'] = __('Slug é obrigatório', 'amigopet');
            } elseif (!preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
                $errors['slug'] = __('Slug inválido. Use apenas letras minúsculas, números e hífens', 'amigopet');
            }
        }

        if (isset($data['required']) && !is_bool($data['required'])) {
            $errors['required'] = __('Campo obrigatório deve ser um booleano', 'amigopet');
        }

        if (isset($data['status']) && !in_array($data['status'], ['active', 'inactive'])) {
            $errors['status'] = __('Status inválido', 'amigopet');
        }

        return $errors;
    }

    private function generateSlug(string $name): string {
        $slug = sanitize_title($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->repository->findBySlug($slug) !== null) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}