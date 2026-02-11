<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Services;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Repositories\AdoptionPaymentRepository;
use AmigoPetWp\Domain\Database\Repositories\AdoptionRepository;
use AmigoPetWp\Domain\Entities\AdoptionPayment;

class AdoptionPaymentService {
    private $repository;
    private $adoptionRepository;

    public function __construct(
        AdoptionPaymentRepository $repository,
        AdoptionRepository $adoptionRepository
    ) {
        $this->repository = $repository;
        $this->adoptionRepository = $adoptionRepository;
    }

    public function create(array $data): AdoptionPayment {
        $payment = new AdoptionPayment(
            $data['adoption_id'],
            $data['amount'],
            $data['payment_method'],
            $data['transaction_id'] ?? null,
            $data['status'] ?? 'pending',
            $data['notes'] ?? null
        );

        $id = $this->repository->save($payment);
        $payment->setId($id);

        return $payment;
    }

    public function update(int $id, array $data): ?AdoptionPayment {
        $payment = $this->repository->findById($id);
        
        if (!$payment) {
            return null;
        }

        if (isset($data['adoption_id'])) {
            $payment->setAdoptionId($data['adoption_id']);
        }

        if (isset($data['amount'])) {
            $payment->setAmount($data['amount']);
        }

        if (isset($data['payment_method'])) {
            $payment->setPaymentMethod($data['payment_method']);
        }

        if (isset($data['transaction_id'])) {
            $payment->setTransactionId($data['transaction_id']);
        }

        if (isset($data['status'])) {
            $payment->setStatus($data['status']);
        }

        if (isset($data['notes'])) {
            $payment->setNotes($data['notes']);
        }

        $this->repository->save($payment);

        return $payment;
    }

    public function delete(int $id): bool {
        return $this->repository->delete($id);
    }

    public function findById(int $id): ?AdoptionPayment {
        return $this->repository->findById($id);
    }

    public function findByAdoption(int $adoptionId): array {
        return $this->repository->findByAdoption($adoptionId);
    }

    public function findAll(array $args = []): array {
        return $this->repository->findAll($args);
    }

    public function complete(int $id): ?AdoptionPayment {
        return $this->update($id, ['status' => 'completed']);
    }

    public function cancel(int $id): ?AdoptionPayment {
        return $this->update($id, ['status' => 'cancelled']);
    }

    public function refund(int $id): ?AdoptionPayment {
        return $this->update($id, ['status' => 'refunded']);
    }

    public function getTotalByAdoption(int $adoptionId): float {
        return $this->repository->sumByAdoption($adoptionId);
    }

    public function validate(array $data): array {
        $errors = [];

        if (empty($data['adoption_id'])) {
            $errors['adoption_id'] = __('Adoção é obrigatória', 'amigopet');
        } elseif (!$this->adoptionRepository->findById($data['adoption_id'])) {
            $errors['adoption_id'] = __('Adoção não encontrada', 'amigopet');
        }

        if (!isset($data['amount'])) {
            $errors['amount'] = __('Valor é obrigatório', 'amigopet');
        } elseif (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            $errors['amount'] = __('Valor inválido', 'amigopet');
        }

        if (empty($data['payment_method'])) {
            $errors['payment_method'] = __('Método de pagamento é obrigatório', 'amigopet');
        } elseif (!in_array($data['payment_method'], ['cash', 'credit_card', 'debit_card', 'bank_transfer', 'pix'])) {
            $errors['payment_method'] = __('Método de pagamento inválido', 'amigopet');
        }

        if (isset($data['status']) && !in_array($data['status'], ['pending', 'completed', 'cancelled', 'refunded'])) {
            $errors['status'] = __('Status inválido', 'amigopet');
        }

        return $errors;
    }
}