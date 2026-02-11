<?php declare(strict_types=1);
namespace AmigoPetWp\Controllers\Admin;

if (!defined('ABSPATH')) {
    exit;
}

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
            'amigopet',
            esc_html__('Voluntários', 'amigopet'),
            esc_html__('Voluntários', 'amigopet'),
            'manage_amigopet_volunteers',
            'amigopet-volunteers',
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
        check_admin_referer('apwp_save_volunteer');

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
                $message = esc_html__('Voluntário atualizado com sucesso!', 'amigopet');
            } else {
                $this->volunteerService->createVolunteer($data);
                $message = esc_html__('Voluntário cadastrado com sucesso!', 'amigopet');
            }

            $this->redirect(
                admin_url('admin.php?page=amigopet-volunteers'),
                $message
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-volunteers'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function deleteVolunteer(): void
    {
        $this->checkPermission('manage_amigopet_volunteers');
        check_admin_referer('apwp_delete_volunteer');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            wp_die(esc_html__('ID do voluntário não fornecido', 'amigopet'));
        }

        try {
            $this->volunteerService->deleteVolunteer($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-volunteers'),
                esc_html__('Voluntário excluído com sucesso!', 'amigopet')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-volunteers'),
                $e->getMessage(),
                'error'
            );
        }
    }
}