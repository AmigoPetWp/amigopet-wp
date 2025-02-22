<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Services\OrganizationService;
use AmigoPetWp\Domain\Entities\Organization;

class AdminOrganizationController extends BaseAdminController {
    private $organizationService;

    public function __construct() {
        parent::__construct();
        $this->organizationService = new OrganizationService($this->db->getOrganizationRepository());
    }

    protected function registerHooks(): void {
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

    public function addMenus(): void {
        add_submenu_page(
            'amigopet-wp',
            __('Organizações', 'amigopet-wp'),
            __('Organizações', 'amigopet-wp'),
            'manage_amigopet_organizations',
            'amigopet-wp-organizations',
            [$this, 'renderOrganizations']
        );
    }

    public function renderOrganizations(): void {
        $this->checkPermission('manage_amigopet_organizations');

        $list_table = new \AmigoPetWp\Admin\Tables\APWP_Organizations_List_Table();
        $list_table->prepare_items();

        $this->loadView('admin/organizations/organizations-list', [
            'list_table' => $list_table,
            'report' => $this->organizationService->getReport()
        ]);
    }

    public function renderOrganizationForm(): void {
        $this->checkPermission('manage_amigopet_organizations');

        $organization_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $organization = $organization_id ? $this->organizationService->findById($organization_id) : null;

        $this->loadView('admin/organizations/organization-form', [
            'organization' => $organization
        ]);
    }

    public function saveOrganization(): void {
        $this->checkPermission('manage_amigopet_organizations');
        $this->verifyNonce('apwp_save_organization');

        $id = isset($_POST['organization_id']) ? (int)$_POST['organization_id'] : 0;
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
                $message = __('Organização atualizada com sucesso!', 'amigopet-wp');
            } else {
                $this->organizationService->createOrganization(
                    $data['name'],
                    $data['email'],
                    $data['phone'],
                    $data['address'],
                    $data['website']
                );
                $message = __('Organização cadastrada com sucesso!', 'amigopet-wp');
            }

            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-organizations'),
                $message
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-organizations'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function activateOrganization(): void {
        $this->checkPermission('manage_amigopet_organizations');
        $this->verifyNonce('apwp_activate_organization');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            wp_die(__('ID da organização não fornecido', 'amigopet-wp'));
        }

        try {
            $this->organizationService->activateOrganization($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-organizations'),
                __('Organização ativada com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-organizations'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function deactivateOrganization(): void {
        $this->checkPermission('manage_amigopet_organizations');
        $this->verifyNonce('apwp_deactivate_organization');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            wp_die(__('ID da organização não fornecido', 'amigopet-wp'));
        }

        try {
            $this->organizationService->deactivateOrganization($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-organizations'),
                __('Organização desativada com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-organizations'),
                $e->getMessage(),
                'error'
            );
        }
    }
}
