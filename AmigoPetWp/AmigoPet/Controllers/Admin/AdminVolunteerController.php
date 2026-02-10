<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Services\VolunteerService;

class AdminVolunteerController extends BaseAdminController
{
    private $volunteerService;

    public function __construct()
    {
        parent::__construct();
        $this->volunteerService = new VolunteerService($this->db->getVolunteerRepository());
    }

    protected function registerHooks(): void
    {
        // Menu e submenu
        add_action('admin_menu', [$this, 'addMenus']);

        // Ações para voluntários
        add_action('admin_post_apwp_save_volunteer', [$this, 'saveVolunteer']);
        add_action('admin_post_nopriv_apwp_save_volunteer', [$this, 'saveVolunteer']);
        add_action('admin_post_apwp_delete_volunteer', [$this, 'deleteVolunteer']);
        add_action('admin_post_nopriv_apwp_delete_volunteer', [$this, 'deleteVolunteer']);
    }

    public function addMenus(): void
    {
        add_submenu_page(
            'amigopet-wp',
            __('Voluntários', 'amigopet-wp'),
            __('Voluntários', 'amigopet-wp'),
            'manage_amigopet_volunteers',
            'amigopet-wp-volunteers',
            [$this, 'renderVolunteers']
        );
    }

    public function renderVolunteers(): void
    {
        $this->checkPermission('manage_amigopet_volunteers');
        $this->loadView('admin/volunteers/volunteers-list-combined');
    }

    public function renderVolunteerForm(): void
    {
        $this->checkPermission('manage_amigopet_volunteers');
        $this->loadView('admin/volunteers/volunteer-form-combined');
    }

    public function saveVolunteer(): void
    {
        $this->checkPermission('manage_amigopet_volunteers');
        $this->verifyNonce('apwp_save_volunteer');

        $id = isset($_POST['volunteer_id']) ? (int) $_POST['volunteer_id'] : 0;
        $data = [
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'status' => sanitize_text_field($_POST['status']),
            'organization_id' => get_current_user_id()
        ];

        try {
            if ($id) {
                $this->volunteerService->updateVolunteer($id, $data);
                $message = __('Voluntário atualizado com sucesso!', 'amigopet-wp');
            } else {
                $this->volunteerService->createVolunteer($data);
                $message = __('Voluntário cadastrado com sucesso!', 'amigopet-wp');
            }

            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-volunteers'),
                $message
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-volunteers'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function deleteVolunteer(): void
    {
        $this->checkPermission('manage_amigopet_volunteers');
        $this->verifyNonce('apwp_delete_volunteer');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            wp_die(__('ID do voluntário não fornecido', 'amigopet-wp'));
        }

        try {
            $this->volunteerService->deleteVolunteer($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-volunteers'),
                __('Voluntário excluído com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-volunteers'),
                $e->getMessage(),
                'error'
            );
        }
    }
}
