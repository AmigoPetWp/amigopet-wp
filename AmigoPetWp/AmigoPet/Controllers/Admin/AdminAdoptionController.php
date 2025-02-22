<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Services\AdoptionService;

class AdminAdoptionController extends BaseAdminController {
    private $adoptionService;

    public function __construct() {
        parent::__construct();
        $this->adoptionService = new AdoptionService($this->db->getAdoptionRepository());
    }

    protected function registerHooks(): void {
        // Menu e submenu
        add_action('admin_menu', [$this, 'addMenus']);

        // Ações para adoções
        add_action('admin_post_apwp_save_adoption', [$this, 'saveAdoption']);
        add_action('admin_post_nopriv_apwp_save_adoption', [$this, 'saveAdoption']);
        add_action('admin_post_apwp_delete_adoption', [$this, 'deleteAdoption']);
        add_action('admin_post_nopriv_apwp_delete_adoption', [$this, 'deleteAdoption']);
        add_action('admin_post_apwp_cancel_adoption', [$this, 'cancelAdoption']);
        add_action('admin_post_nopriv_apwp_cancel_adoption', [$this, 'cancelAdoption']);

        // AJAX endpoints
        add_action('wp_ajax_apwp_load_adoptions', [$this, 'loadAdoptions']);
        add_action('wp_ajax_apwp_approve_adoption', [$this, 'approveAdoption']);
        add_action('wp_ajax_apwp_reject_adoption', [$this, 'rejectAdoption']);
    }

    public function addMenus(): void {
        add_submenu_page(
            'amigopet-wp',
            __('Adoções', 'amigopet-wp'),
            __('Adoções', 'amigopet-wp'),
            'manage_amigopet_adoptions',
            'amigopet-wp-adoptions',
            [$this, 'renderAdoptions']
        );
    }

    public function renderAdoptions(): void {
        $this->checkPermission('manage_amigopet_adoptions');

        $list_table = new \AmigoPetWp\Admin\Tables\APWP_Adoptions_List_Table();
        $list_table->prepare_items();

        $this->loadView('admin/adoptions/adoptions-list', [
            'list_table' => $list_table
        ]);
    }

    public function loadAdoptions(): void {
        $this->checkPermission('manage_amigopet_adoptions');
        check_ajax_referer('apwp_nonce');

        $list_table = new \AmigoPetWp\Admin\Tables\APWP_Adoptions_List_Table();
        $list_table->prepare_items();

        ob_start();
        $list_table->display();
        $content = ob_get_clean();

        wp_send_json_success([
            'content' => $content
        ]);
    }

    public function approveAdoption(): void {
        $this->checkPermission('manage_amigopet_adoptions');
        check_ajax_referer('apwp_nonce');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            wp_send_json_error(__('ID da adoção não fornecido', 'amigopet-wp'));
        }

        try {
            $this->adoptionService->approveAdoption($id);
            wp_send_json_success(__('Adoção aprovada com sucesso!', 'amigopet-wp'));
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    public function rejectAdoption(): void {
        $this->checkPermission('manage_amigopet_adoptions');
        check_ajax_referer('apwp_nonce');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            wp_send_json_error(__('ID da adoção não fornecido', 'amigopet-wp'));
        }

        try {
            $this->adoptionService->rejectAdoption($id);
            wp_send_json_success(__('Adoção rejeitada com sucesso!', 'amigopet-wp'));
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    public function saveAdoption(): void {
        $this->checkPermission('manage_amigopet_adoptions');
        $this->verifyNonce('apwp_save_adoption');

        $id = isset($_POST['adoption_id']) ? (int)$_POST['adoption_id'] : 0;
        $data = [
            'pet_id' => (int)$_POST['pet_id'],
            'adopter_id' => (int)$_POST['adopter_id'],
            'status' => sanitize_text_field($_POST['status']),
            'notes' => wp_kses_post($_POST['notes']),
            'organization_id' => get_current_user_id()
        ];

        try {
            if ($id) {
                $this->adoptionService->updateAdoption($id, $data);
                $message = __('Adoção atualizada com sucesso!', 'amigopet-wp');
            } else {
                $this->adoptionService->createAdoption($data);
                $message = __('Adoção cadastrada com sucesso!', 'amigopet-wp');
            }

            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-adoptions'),
                $message
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-adoptions'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function deleteAdoption(): void {
        $this->checkPermission('manage_amigopet_adoptions');
        $this->verifyNonce('apwp_delete_adoption');

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            wp_die(__('ID da adoção não fornecido', 'amigopet-wp'));
        }

        try {
            $this->adoptionService->deleteAdoption($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-adoptions'),
                __('Adoção excluída com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-adoptions'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function cancelAdoption(): void {
        $this->checkPermission('manage_amigopet_adoptions');
        $this->verifyNonce('apwp_cancel_adoption');

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            wp_die(__('ID da adoção não fornecido', 'amigopet-wp'));
        }

        try {
            $this->adoptionService->cancelAdoption($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-adoptions'),
                __('Adoção cancelada com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-adoptions'),
                $e->getMessage(),
                'error'
            );
        }
    }
}
