<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Services\DonationService;

class AdminDonationController extends BaseAdminController
{
    private $donationService;

    public function __construct()
    {
        parent::__construct();
        $this->donationService = new DonationService($this->db->getDonationRepository());
    }

    protected function registerHooks(): void
    {
        // Menu e submenu
        add_action('admin_menu', [$this, 'addMenus']);

        // Ações para doações
        add_action('admin_post_apwp_save_donation', [$this, 'saveDonation']);
        add_action('admin_post_nopriv_apwp_save_donation', [$this, 'saveDonation']);
        add_action('admin_post_apwp_process_payment', [$this, 'processPayment']);
        add_action('admin_post_nopriv_apwp_process_payment', [$this, 'processPayment']);
        add_action('admin_post_apwp_refund_donation', [$this, 'refundDonation']);
        add_action('admin_post_nopriv_apwp_refund_donation', [$this, 'refundDonation']);
        add_action('admin_post_apwp_mark_failed', [$this, 'markAsFailed']);
        add_action('admin_post_nopriv_apwp_mark_failed', [$this, 'markAsFailed']);
    }

    public function addMenus(): void
    {
        add_submenu_page(
            'amigopet-wp',
            __('Doações', 'amigopet-wp'),
            __('Doações', 'amigopet-wp'),
            'manage_amigopet_donations',
            'amigopet-wp-donations',
            [$this, 'renderDonations']
        );
    }

    public function renderDonations(): void
    {
        $this->checkPermission('manage_amigopet_donations');
        $this->loadView('admin/donations/donations-list-combined');
    }

    public function renderDonationForm(): void
    {
        $this->checkPermission('manage_amigopet_donations');
        $this->loadView('admin/donations/donation-form-combined');
    }


    public function saveDonation(): void
    {
        $this->checkPermission('manage_amigopet_donations');
        $this->verifyNonce('apwp_save_donation');

        $id = isset($_POST['donation_id']) ? (int) $_POST['donation_id'] : 0;
        $data = [
            'donor_name' => sanitize_text_field($_POST['donor_name']),
            'donor_email' => sanitize_email($_POST['donor_email']),
            'donor_phone' => sanitize_text_field($_POST['donor_phone']),
            'amount' => (float) $_POST['amount'],
            'payment_method' => sanitize_text_field($_POST['payment_method']),
            'description' => wp_kses_post($_POST['description']),
            'organization_id' => get_current_user_id()
        ];

        try {
            if ($id) {
                $this->donationService->updateDonation($id, $data);
                $message = __('Doação atualizada com sucesso!', 'amigopet-wp');
            } else {
                $this->donationService->createDonation($data);
                $message = __('Doação cadastrada com sucesso!', 'amigopet-wp');
            }

            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-donations'),
                $message
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-donations'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function processPayment(): void
    {
        $this->checkPermission('manage_amigopet_donations');
        $this->verifyNonce('apwp_process_payment');

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $transaction_id = sanitize_text_field($_POST['transaction_id']);

        if (!$id || !$transaction_id) {
            wp_die(__('ID da doação e ID da transação são obrigatórios', 'amigopet-wp'));
        }

        try {
            $this->donationService->processPayment($id, $transaction_id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-donations'),
                __('Pagamento processado com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-donations'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function refundDonation(): void
    {
        $this->checkPermission('manage_amigopet_donations');
        $this->verifyNonce('apwp_refund_donation');

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if (!$id) {
            wp_die(__('ID da doação não fornecido', 'amigopet-wp'));
        }

        try {
            $this->donationService->refundDonation($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-donations'),
                __('Doação reembolsada com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-donations'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function markAsFailed(): void
    {
        $this->checkPermission('manage_amigopet_donations');
        $this->verifyNonce('apwp_mark_failed');

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if (!$id) {
            wp_die(__('ID da doação não fornecido', 'amigopet-wp'));
        }

        try {
            $this->donationService->markAsFailed($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-donations'),
                __('Doação marcada como falha com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-donations'),
                $e->getMessage(),
                'error'
            );
        }
    }
}
