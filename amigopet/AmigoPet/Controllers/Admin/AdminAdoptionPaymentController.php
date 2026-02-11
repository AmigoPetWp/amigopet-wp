<?php declare(strict_types=1);
namespace AmigoPetWp\Controllers\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Database;
use AmigoPetWp\Domain\Services\AdoptionPaymentService;
use AmigoPetWp\Domain\Services\AdoptionService;
use WP_Error;

class AdminAdoptionPaymentController extends BaseAdminController
{
    private $service;
    private $adoptionService;

    public function __construct()
    {
        parent::__construct();
        $database = Database::getInstance();
        $this->service = new AdoptionPaymentService(
            $database->getAdoptionPaymentRepository(),
            $database->getAdoptionRepository()
        );
        $this->adoptionService = new AdoptionService(
            $database->getAdoptionRepository()
        );
    }

    public function registerHooks(): void
    {
        add_action('admin_menu', [$this, 'addMenuItems']);
        add_action('wp_ajax_amigopet_list_adoption_payments', [$this, 'listPayments']);
        add_action('wp_ajax_amigopet_create_adoption_payment', [$this, 'createPayment']);
        add_action('wp_ajax_amigopet_update_adoption_payment', [$this, 'updatePayment']);
        add_action('wp_ajax_amigopet_delete_adoption_payment', [$this, 'deletePayment']);
        add_action('wp_ajax_amigopet_complete_adoption_payment', [$this, 'completePayment']);
        add_action('wp_ajax_amigopet_cancel_adoption_payment', [$this, 'cancelPayment']);
        add_action('wp_ajax_amigopet_refund_adoption_payment', [$this, 'refundPayment']);
    }

    public function addMenuItems(): void
    {
        add_submenu_page(
            'amigopet',
            esc_html__('Pagamentos de Adoção', 'amigopet'),
            esc_html__('Pagamentos de Adoção', 'amigopet'),
            'manage_options',
            'amigopet-adoption-payments',
            [$this, 'renderPage']
        );
    }

    public function renderPage(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Você não tem permissão para acessar esta página.', 'amigopet'));
        }

        $adoptionId = (int) ($_GET['adoption_id'] ?? 0);
        $adoption = $adoptionId ? $this->adoptionService->findById($adoptionId) : null;

        $this->loadView('admin/adoption-payments/index', [
            'adoption' => $adoption,
            'payments' => $adoption ? $this->service->findByAdoption($adoptionId) : [],
            'total' => $adoption ? $this->service->getTotalByAdoption($adoptionId) : 0
        ]);
    }

    public function listPayments(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        $args = [
            'adoption_id' => (int) ($_GET['adoption_id'] ?? 0),
            'status' => sanitize_text_field($_GET['status'] ?? ''),
            'payment_method' => sanitize_text_field($_GET['payment_method'] ?? ''),
            'start_date' => sanitize_text_field($_GET['start_date'] ?? ''),
            'end_date' => sanitize_text_field($_GET['end_date'] ?? ''),
            'min_amount' => isset($_GET['min_amount']) ? (float) $_GET['min_amount'] : null,
            'max_amount' => isset($_GET['max_amount']) ? (float) $_GET['max_amount'] : null
        ];

        $payments = $this->service->findAll($args);
        $data = array_map(function ($payment) {
            $data = $payment->toArray();
            $data['adoption'] = $this->adoptionService->findById($payment->getAdoptionId());
            return $data;
        }, $payments);

        wp_send_json_success(['data' => $data]);
    }

    public function createPayment(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_create_adoption_payment')) {
            wp_send_json_error(['message' => esc_html__('Nonce inválido', 'amigopet')]);
            return;
        }

        $data = [
            'adoption_id' => (int) ($_POST['adoption_id'] ?? 0),
            'amount' => (float) ($_POST['amount'] ?? 0),
            'payment_method' => sanitize_text_field($_POST['payment_method'] ?? ''),
            'transaction_id' => sanitize_text_field($_POST['transaction_id'] ?? ''),
            'notes' => sanitize_textarea_field($_POST['notes'] ?? ''),
            'status' => sanitize_text_field($_POST['status'] ?? 'pending')
        ];

        $errors = $this->service->validate($data);
        if (!empty($errors)) {
            wp_send_json_error(['errors' => $errors]);
            return;
        }

        try {
            $payment = $this->service->create($data);
            wp_send_json_success([
                'message' => esc_html__('Pagamento criado com sucesso', 'amigopet'),
                'data' => $payment->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function updatePayment(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_update_adoption_payment')) {
            wp_send_json_error(['message' => esc_html__('Nonce inválido', 'amigopet')]);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => esc_html__('ID inválido', 'amigopet')]);
            return;
        }

        $data = [
            'adoption_id' => (int) ($_POST['adoption_id'] ?? 0),
            'amount' => (float) ($_POST['amount'] ?? 0),
            'payment_method' => sanitize_text_field($_POST['payment_method'] ?? ''),
            'transaction_id' => sanitize_text_field($_POST['transaction_id'] ?? ''),
            'notes' => sanitize_textarea_field($_POST['notes'] ?? ''),
            'status' => sanitize_text_field($_POST['status'] ?? 'pending')
        ];

        $errors = $this->service->validate($data);
        if (!empty($errors)) {
            wp_send_json_error(['errors' => $errors]);
            return;
        }

        try {
            $payment = $this->service->update($id, $data);
            if (!$payment) {
                wp_send_json_error(['message' => esc_html__('Pagamento não encontrado', 'amigopet')]);
                return;
            }

            wp_send_json_success([
                'message' => esc_html__('Pagamento atualizado com sucesso', 'amigopet'),
                'data' => $payment->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function deletePayment(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_delete_adoption_payment')) {
            wp_send_json_error(['message' => esc_html__('Nonce inválido', 'amigopet')]);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => esc_html__('ID inválido', 'amigopet')]);
            return;
        }

        try {
            if ($this->service->delete($id)) {
                wp_send_json_success([
                    'message' => esc_html__('Pagamento excluído com sucesso', 'amigopet')
                ]);
            } else {
                wp_send_json_error([
                    'message' => esc_html__('Não foi possível excluir o pagamento', 'amigopet')
                ]);
            }
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function completePayment(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_complete_adoption_payment')) {
            wp_send_json_error(['message' => esc_html__('Nonce inválido', 'amigopet')]);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => esc_html__('ID inválido', 'amigopet')]);
            return;
        }

        try {
            $payment = $this->service->complete($id);
            if (!$payment) {
                wp_send_json_error(['message' => esc_html__('Pagamento não encontrado', 'amigopet')]);
                return;
            }

            wp_send_json_success([
                'message' => esc_html__('Pagamento concluído com sucesso', 'amigopet'),
                'data' => $payment->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function cancelPayment(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_cancel_adoption_payment')) {
            wp_send_json_error(['message' => esc_html__('Nonce inválido', 'amigopet')]);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => esc_html__('ID inválido', 'amigopet')]);
            return;
        }

        try {
            $payment = $this->service->cancel($id);
            if (!$payment) {
                wp_send_json_error(['message' => esc_html__('Pagamento não encontrado', 'amigopet')]);
                return;
            }

            wp_send_json_success([
                'message' => esc_html__('Pagamento cancelado com sucesso', 'amigopet'),
                'data' => $payment->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function refundPayment(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => esc_html__('Permissão negada', 'amigopet')]);
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'amigopet_refund_adoption_payment')) {
            wp_send_json_error(['message' => esc_html__('Nonce inválido', 'amigopet')]);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => esc_html__('ID inválido', 'amigopet')]);
            return;
        }

        try {
            $payment = $this->service->refund($id);
            if (!$payment) {
                wp_send_json_error(['message' => esc_html__('Pagamento não encontrado', 'amigopet')]);
                return;
            }

            wp_send_json_success([
                'message' => esc_html__('Pagamento reembolsado com sucesso', 'amigopet'),
                'data' => $payment->toArray()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
}