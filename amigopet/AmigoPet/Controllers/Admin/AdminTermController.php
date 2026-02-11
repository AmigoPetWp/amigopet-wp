<?php declare(strict_types=1);
namespace AmigoPetWp\Controllers\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Services\TermService;

class AdminTermController extends BaseAdminController
{
    private $termService;

    public function __construct()
    {
        parent::__construct();
        $this->termService = new TermService($this->db->getTermRepository());
    }

    protected function registerHooks(): void
    {
        // Menu e submenu
        add_action('admin_menu', [$this, 'addMenus']);

        // Ações para termos
        add_action('admin_post_apwp_save_term', [$this, 'saveTerm']);
        add_action('admin_post_nopriv_apwp_save_term', [$this, 'saveTerm']);
        add_action('admin_post_apwp_delete_term', [$this, 'deleteTerm']);
        add_action('admin_post_nopriv_apwp_delete_term', [$this, 'deleteTerm']);
        add_action('admin_post_apwp_activate_term', [$this, 'activateTerm']);
        add_action('admin_post_nopriv_apwp_activate_term', [$this, 'activateTerm']);
        add_action('admin_post_apwp_deactivate_term', [$this, 'deactivateTerm']);
        add_action('admin_post_nopriv_apwp_deactivate_term', [$this, 'deactivateTerm']);

        // AJAX endpoints
        add_action('wp_ajax_apwp_get_term_users', [$this, 'getTermUsers']);
    }

    public function addMenus(): void
    {
        add_submenu_page(
            'amigopet',
            esc_html__('Termos', 'amigopet'),
            esc_html__('Termos', 'amigopet'),
            'manage_amigopet_terms',
            'amigopet-terms',
            [$this, 'renderTerms']
        );

        add_submenu_page(
            null,
            esc_html__('Adicionar/Editar Termo', 'amigopet'),
            esc_html__('Adicionar/Editar Termo', 'amigopet'),
            'manage_amigopet_terms',
            'apwp-term-form',
            [$this, 'renderTermForm']
        );
    }

    public function renderTerms(): void
    {
        $this->checkPermission('manage_amigopet_terms');
        $this->loadView('admin/terms/terms-list-combined');
    }

    public function renderTermForm(): void
    {
        $this->checkPermission('manage_amigopet_terms');
        $this->loadView('admin/terms/term-form-combined');
    }


    public function saveTerm(): void
    {
        $this->checkPermission('manage_amigopet_terms');
        check_admin_referer('apwp_save_term');

        $id = isset($_POST['term_id']) ? (int) $_POST['term_id'] : 0;
        $data = [
            'title' => sanitize_text_field($_POST['title']),
            'content' => wp_kses_post($_POST['content']),
            'type' => sanitize_text_field($_POST['type']),
            'is_required' => isset($_POST['is_required']),
            'is_active' => isset($_POST['is_active']),
            'organization_id' => get_current_user_id()
        ];

        try {
            if ($id) {
                $this->termService->updateTerm($id, $data);
                $message = esc_html__('Termo atualizado com sucesso!', 'amigopet');
            } else {
                $this->termService->createTerm($data);
                $message = esc_html__('Termo cadastrado com sucesso!', 'amigopet');
            }

            $this->redirect(
                admin_url('admin.php?page=amigopet-terms'),
                $message
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-terms'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function deleteTerm(): void
    {
        $this->checkPermission('manage_amigopet_terms');
        check_admin_referer('apwp_delete_term');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            wp_die(esc_html__('ID do termo não fornecido', 'amigopet'));
        }

        try {
            $this->termService->deleteTerm($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-terms'),
                esc_html__('Termo excluído com sucesso!', 'amigopet')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-terms'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function activateTerm(): void
    {
        $this->checkPermission('manage_amigopet_terms');
        check_admin_referer('apwp_activate_term');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            wp_die(esc_html__('ID do termo não fornecido', 'amigopet'));
        }

        try {
            $this->termService->activateTerm($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-terms'),
                esc_html__('Termo ativado com sucesso!', 'amigopet')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-terms'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function deactivateTerm(): void
    {
        $this->checkPermission('manage_amigopet_terms');
        check_admin_referer('apwp_deactivate_term');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            wp_die(esc_html__('ID do termo não fornecido', 'amigopet'));
        }

        try {
            $this->termService->deactivateTerm($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-terms'),
                esc_html__('Termo desativado com sucesso!', 'amigopet')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-terms'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function getTermUsers(): void
    {
        $this->checkPermission('manage_amigopet_terms');
        check_ajax_referer('apwp_nonce');

        $term_id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if (!$term_id) {
            wp_send_json_error(esc_html__('ID do termo não fornecido', 'amigopet'));
        }

        try {
            $term = $this->termService->findById($term_id);
            if (!$term) {
                throw new \InvalidArgumentException(esc_html__('Termo não encontrado', 'amigopet'));
            }

            $users = [];
            foreach ($term->getAcceptedBy() as $userId) {
                $user = get_user_by('id', $userId);
                if ($user) {
                    $users[] = [
                        'id' => $user->ID,
                        'name' => $user->display_name,
                        'email' => $user->user_email,
                        'date' => get_user_meta($user->ID, 'term_' . $term_id . '_accepted_date', true)
                    ];
                }
            }

            wp_send_json_success([
                'users' => $users
            ]);
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }
}