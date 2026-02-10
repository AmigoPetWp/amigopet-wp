<?php
namespace AmigoPetWp\Controllers\Admin;

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
            'amigopet-wp',
            __('Termos', 'amigopet-wp'),
            __('Termos', 'amigopet-wp'),
            'manage_amigopet_terms',
            'amigopet-wp-terms',
            [$this, 'renderTerms']
        );

        add_submenu_page(
            null,
            __('Adicionar/Editar Termo', 'amigopet-wp'),
            __('Adicionar/Editar Termo', 'amigopet-wp'),
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
        $this->verifyNonce('apwp_save_term');

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
                $message = __('Termo atualizado com sucesso!', 'amigopet-wp');
            } else {
                $this->termService->createTerm($data);
                $message = __('Termo cadastrado com sucesso!', 'amigopet-wp');
            }

            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-terms'),
                $message
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-terms'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function deleteTerm(): void
    {
        $this->checkPermission('manage_amigopet_terms');
        $this->verifyNonce('apwp_delete_term');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            wp_die(__('ID do termo não fornecido', 'amigopet-wp'));
        }

        try {
            $this->termService->deleteTerm($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-terms'),
                __('Termo excluído com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-terms'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function activateTerm(): void
    {
        $this->checkPermission('manage_amigopet_terms');
        $this->verifyNonce('apwp_activate_term');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            wp_die(__('ID do termo não fornecido', 'amigopet-wp'));
        }

        try {
            $this->termService->activateTerm($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-terms'),
                __('Termo ativado com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-terms'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function deactivateTerm(): void
    {
        $this->checkPermission('manage_amigopet_terms');
        $this->verifyNonce('apwp_deactivate_term');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            wp_die(__('ID do termo não fornecido', 'amigopet-wp'));
        }

        try {
            $this->termService->deactivateTerm($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-terms'),
                __('Termo desativado com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-terms'),
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
            wp_send_json_error(__('ID do termo não fornecido', 'amigopet-wp'));
        }

        try {
            $term = $this->termService->findById($term_id);
            if (!$term) {
                throw new \InvalidArgumentException(__('Termo não encontrado', 'amigopet-wp'));
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
