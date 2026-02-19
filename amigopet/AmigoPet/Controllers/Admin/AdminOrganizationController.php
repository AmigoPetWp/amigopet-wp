<?php declare(strict_types=1);
namespace AmigoPetWp\Controllers\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Services\OrganizationService;
use AmigoPetWp\Domain\Entities\Organization;

class AdminOrganizationController extends BaseAdminController
{
    private $organizationService;

    public function __construct()
    {
        parent::__construct();
        $this->organizationService = new OrganizationService($this->db->getOrganizationRepository());
    }

    protected function registerHooks(): void
    {
        // Menu e submenu
        add_action('admin_menu', [$this, 'addMenus']);

        // Ações para organizações
        add_action('admin_post_apwp_save_organization', [$this, 'saveOrganization']);
        add_action('admin_post_nopriv_apwp_save_organization', [$this, 'saveOrganization']);
        add_action('admin_post_apwp_activate_organization', [$this, 'activateOrganization']);
        add_action('admin_post_nopriv_apwp_activate_organization', [$this, 'activateOrganization']);
        add_action('admin_post_apwp_deactivate_organization', [$this, 'deactivateOrganization']);
        add_action('admin_post_nopriv_apwp_deactivate_organization', [$this, 'deactivateOrganization']);
    }

    public function addMenus(): void
    {
        add_submenu_page(
            'amigopetwp',
            esc_html__('Organizações', 'amigopet'),
            esc_html__('Organizações', 'amigopet'),
            'manage_amigopet_organizations',
            'amigopet-organizations',
            [$this, 'renderOrganizations']
        );
    }

    public function renderOrganizations(): void
    {
        $this->checkPermission('manage_amigopet_organizations');

        $list_table = new \AmigoPetWp\Admin\Tables\APWP_Organizations_List_Table();
        $list_table->prepare_items();

        $this->loadView('admin/organizations/organizations-list', [
            'list_table' => $list_table,
            'report' => $this->organizationService->getReport()
        ]);
    }

    public function renderOrganizationForm(): void
    {
        $this->checkPermission('manage_amigopet_organizations');

        $organization_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $organization = $organization_id ? $this->organizationService->findById($organization_id) : null;

        $this->loadView('admin/organizations/organization-form', [
            'organization' => $organization
        ]);
    }

    public function saveOrganization(): void
    {
        $this->checkPermission('manage_amigopet_organizations');
        check_admin_referer('apwp_save_organization');

        $id = isset($_POST['organization_id']) ? (int) $_POST['organization_id'] : 0;
        $data = [
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'address' => sanitize_text_field($_POST['address']),
            'website' => esc_url_raw($_POST['website'])
        ];

        try {
            if ($id) {
                $this->organizationService->updateOrganization(
                    $id,
                    $data['name'],
                    $data['email'],
                    $data['phone'],
                    $data['address'],
                    $data['website']
                );
                $message = esc_html__('Organização atualizada com sucesso!', 'amigopet');
            } else {
                $this->organizationService->createOrganization(
                    $data['name'],
                    $data['email'],
                    $data['phone'],
                    $data['address'],
                    $data['website']
                );
                $message = esc_html__('Organização cadastrada com sucesso!', 'amigopet');
            }

            $this->redirect(
                admin_url('admin.php?page=amigopet-organizations'),
                $message
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-organizations'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function activateOrganization(): void
    {
        $this->checkPermission('manage_amigopet_organizations');
        check_admin_referer('apwp_activate_organization');

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if (!$id) {
            wp_die(esc_html__('ID da organização não fornecido', 'amigopet'));
        }

        try {
            $this->organizationService->activateOrganization($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-organizations'),
                esc_html__('Organização ativada com sucesso!', 'amigopet')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-organizations'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function deactivateOrganization(): void
    {
        $this->checkPermission('manage_amigopet_organizations');
        check_admin_referer('apwp_deactivate_organization');

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if (!$id) {
            wp_die(esc_html__('ID da organização não fornecido', 'amigopet'));
        }

        try {
            $this->organizationService->deactivateOrganization($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-organizations'),
                esc_html__('Organização desativada com sucesso!', 'amigopet')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-organizations'),
                $e->getMessage(),
                'error'
            );
        }
    }
}