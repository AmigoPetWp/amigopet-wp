<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\AdoptionPayment;

/**
 * Repositório para gerenciar pagamentos de adoções
 * 
 * @package AmigoPetWp\Domain\Database\Repositories
 */
class AdoptionPaymentRepository extends AbstractRepository {
    /**
     * {@inheritDoc}
     */
    protected function getTableName(): string {
        return 'apwp_adoption_payments';
    }

    /**
     * {@inheritDoc}
     */
    protected function createEntity(array $data): AdoptionPayment {
        $payment = new AdoptionPayment(
            (int)$data['adoption_id'],
            (float)$data['amount'],
            $data['payment_method'],
            $data['payer_name'] ?? null,
            $data['payer_email'] ?? null,
            $data['payer_document'] ?? null
        );

        if (isset($data['id'])) {
            $payment->setId((int)$data['id']);
        }

        if ($data['payment_status'] !== AdoptionPayment::STATUS_PENDING) {
            $payment->setStatus($data['payment_status']);
        }

        if (isset($data['transaction_id'])) {
            $payment->setTransactionId($data['transaction_id']);
        }

        if (isset($data['payment_date'])) {
            $payment->setPaymentDate(new \DateTime($data['payment_date']));
        }

        if (isset($data['refund_date'])) {
            $payment->setRefundDate(new \DateTime($data['refund_date']));
        }

        if (isset($data['refund_reason'])) {
            $payment->setRefundReason($data['refund_reason']);
        }

        if (isset($data['gateway_response'])) {
            $payment->setGatewayResponse(json_decode($data['gateway_response'], true));
        }

        if (isset($data['notes'])) {
            $payment->setNotes($data['notes']);
        }

        if (isset($data['created_at'])) {
            $payment->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at'])) {
            $payment->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $payment;
    }

    /**
     * {@inheritDoc}
     */
    protected function toDatabase($entity): array {
        if (!$entity instanceof AdoptionPayment) {
            throw new \InvalidArgumentException('Entity must be an instance of AdoptionPayment');
        }

        return [
            'adoption_id' => $entity->getAdoptionId(),
            'amount' => $entity->getAmount(),
            'payment_method' => $entity->getPaymentMethod(),
            'payment_status' => $entity->getStatus(),
            'transaction_id' => $entity->getTransactionId(),
            'payer_name' => $entity->getPayerName(),
            'payer_email' => $entity->getPayerEmail(),
            'payer_document' => $entity->getPayerDocument(),
            'payment_date' => $entity->getPaymentDate()?->format('Y-m-d H:i:s'),
            'refund_date' => $entity->getRefundDate()?->format('Y-m-d H:i:s'),
            'refund_reason' => $entity->getRefundReason(),
            'gateway_response' => $entity->getGatewayResponse() ? json_encode($entity->getGatewayResponse()) : null,
            'notes' => $entity->getNotes(),
            'created_at' => $entity->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()?->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Encontra pagamentos por adoção
     *
     * @param int $adoptionId ID da adoção
     * @return array Lista de pagamentos
     */
    public function findByAdoption(int $adoptionId): array {
        return $this->findAll([
            'adoption_id' => $adoptionId,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    /**
     * Encontra pagamentos por status
     *
     * @param string $status Status do pagamento
     * @return array Lista de pagamentos
     */
    public function findByStatus(string $status): array {
        return $this->findAll([
            'payment_status' => $status,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    /**
     * Encontra pagamentos por método de pagamento
     *
     * @param string $method Método de pagamento
     * @return array Lista de pagamentos
     */
    public function findByPaymentMethod(string $method): array {
        return $this->findAll([
            'payment_method' => $method,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    /**
     * Calcula o total de pagamentos confirmados de uma adoção
     *
     * @param int $adoptionId ID da adoção
     * @return float Total dos pagamentos
     */
    public function sumByAdoption(int $adoptionId): float {
        return (float) $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT SUM(amount) FROM {$this->table} WHERE adoption_id = %d AND payment_status = 'completed'",
                $adoptionId
            )
        ) ?: 0.0;
    }

    /**
     * Encontra pagamentos por período
     *
     * @param string $startDate Data inicial
     * @param string $endDate Data final
     * @return array Lista de pagamentos
     */
    public function findByPeriod(string $startDate, string $endDate): array {
        return $this->findAll([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    /**
     * Encontra pagamentos por faixa de valor
     *
     * @param float $minAmount Valor mínimo
     * @param float $maxAmount Valor máximo
     * @return array Lista de pagamentos
     */
    public function findByAmountRange(float $minAmount, float $maxAmount): array {
        return $this->findAll([
            'min_amount' => $minAmount,
            'max_amount' => $maxAmount,
            'orderby' => 'amount',
            'order' => 'DESC'
        ]);
    }

    /**
     * Encontra o último pagamento de uma adoção
     *
     * @param int $adoptionId ID da adoção
     * @return AdoptionPayment|null Último pagamento ou null se não houver
     */
    public function findLastPaymentByAdoption(int $adoptionId): ?AdoptionPayment {
        $payments = $this->findByAdoption($adoptionId);
        return !empty($payments) ? $payments[0] : null;
    }

    /**
     * Encontra pagamentos por status e método de pagamento
     *
     * @param string $status Status do pagamento
     * @param string $method Método de pagamento
     * @return array Lista de pagamentos
     */
    public function findByStatusAndMethod(string $status, string $method): array {
        return $this->findAll([
            'payment_status' => $status,
            'payment_method' => $method,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    /**
     * Encontra pagamentos por período e status
     *
     * @param string $startDate Data inicial
     * @param string $endDate Data final
     * @param string $status Status do pagamento
     * @return array Lista de pagamentos
     */
    public function findByPeriodAndStatus(string $startDate, string $endDate, string $status): array {
        return $this->findAll([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'payment_status' => $status,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    /**
     * Encontra pagamentos por nome do pagador
     *
     * @param string $payerName Nome do pagador
     * @return array Lista de pagamentos
     */
    public function findByPayerName(string $payerName): array {
        return $this->findAll([
            'payer_name' => $payerName,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    /**
     * Encontra pagamentos por email do pagador
     *
     * @param string $payerEmail Email do pagador
     * @return array Lista de pagamentos
     */
    public function findByPayerEmail(string $payerEmail): array {
        return $this->findAll([
            'payer_email' => $payerEmail,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    /**
     * Encontra pagamentos por documento do pagador
     *
     * @param string $payerDocument Documento do pagador
     * @return array Lista de pagamentos
     */
    public function findByPayerDocument(string $payerDocument): array {
        return $this->findAll([
            'payer_document' => $payerDocument,
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    /**
     * Encontra pagamentos por ID da transação
     *
     * @param string $transactionId ID da transação
     * @return AdoptionPayment|null Pagamento encontrado ou null
     */
    public function findByTransactionId(string $transactionId): ?AdoptionPayment {
        $payments = $this->findAll([
            'transaction_id' => $transactionId,
            'limit' => 1
        ]);
        return !empty($payments) ? $payments[0] : null;
    }

    /**
     * Encontra pagamentos que foram reembolsados em um determinado período
     *
     * @param string $startDate Data inicial
     * @param string $endDate Data final
     * @return array Lista de pagamentos
     */
    public function findRefundedByPeriod(string $startDate, string $endDate): array {
        return $this->findAll([
            'refund_start_date' => $startDate,
            'refund_end_date' => $endDate,
            'payment_status' => AdoptionPayment::STATUS_REFUNDED,
            'orderby' => 'refund_date',
            'order' => 'DESC'
        ]);
    }

    /**
     * Calcula o total de pagamentos por período e status
     *
     * @param string $startDate Data inicial (Y-m-d)
     * @param string $endDate Data final (Y-m-d)
     * @param string|null $status Status do pagamento (opcional)
     * @param string|null $paymentMethod Método de pagamento (opcional)
     * @return float Total dos pagamentos
     */
    public function sumByPeriod(
        string $startDate,
        string $endDate,
        ?string $status = 'completed',
        ?string $paymentMethod = null
    ): float {
        $where = ['payment_date BETWEEN %s AND %s'];
        $params = [$startDate . ' 00:00:00', $endDate . ' 23:59:59'];

        if ($status) {
            $where[] = 'payment_status = %s';
            $params[] = $status;
        }

        if ($paymentMethod) {
            $where[] = 'payment_method = %s';
            $params[] = $paymentMethod;
        }

        $sql = $this->wpdb->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM {$this->table} WHERE " . implode(' AND ', $where),
            $params
        );

        return (float) $this->wpdb->get_var($sql);
    }

    /**
     * Gera um relatório de pagamentos por período
     *
     * @param string $startDate Data inicial (Y-m-d)
     * @param string $endDate Data final (Y-m-d)
     * @return array Relatório com totais por status e método de pagamento
     */
    public function getPaymentReport(string $startDate, string $endDate): array {
        $report = [
            'total_period' => $this->sumByPeriod($startDate, $endDate),
            'by_status' => [],
            'by_method' => [],
            'refunds' => [
                'total' => $this->sumByPeriod($startDate, $endDate, AdoptionPayment::STATUS_REFUNDED),
                'count' => count($this->findRefundedByPeriod($startDate, $endDate))
            ]
        ];

        // Totais por status
        foreach (AdoptionPayment::getAvailablePaymentStatus() as $status => $label) {
            $report['by_status'][$status] = [
                'total' => $this->sumByPeriod($startDate, $endDate, $status),
                'count' => count($this->findByStatusAndPeriod($status, $startDate, $endDate))
            ];
        }

        // Totais por método de pagamento
        foreach (AdoptionPayment::getAvailablePaymentMethods() as $method => $label) {
            $report['by_method'][$method] = [
                'total' => $this->sumByPeriod($startDate, $endDate, null, $method),
                'count' => count($this->findByMethodAndPeriod($method, $startDate, $endDate))
            ];
        }

        return $report;
    }

    /**
     * Encontra pagamentos por status e período
     *
     * @param string $status Status do pagamento
     * @param string $startDate Data inicial
     * @param string $endDate Data final
     * @return array Lista de pagamentos
     */
    public function findByStatusAndPeriod(string $status, string $startDate, string $endDate): array {
        return $this->findAll([
            'payment_status' => $status,
            'start_date' => $startDate . ' 00:00:00',
            'end_date' => $endDate . ' 23:59:59',
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }

    /**
     * Encontra pagamentos por método e período
     *
     * @param string $method Método de pagamento
     * @param string $startDate Data inicial
     * @param string $endDate Data final
     * @return array Lista de pagamentos
     */
    public function findByMethodAndPeriod(string $method, string $startDate, string $endDate): array {
        return $this->findAll([
            'payment_method' => $method,
            'start_date' => $startDate . ' 00:00:00',
            'end_date' => $endDate . ' 23:59:59',
            'orderby' => 'created_at',
            'order' => 'DESC'
        ]);
    }


}
