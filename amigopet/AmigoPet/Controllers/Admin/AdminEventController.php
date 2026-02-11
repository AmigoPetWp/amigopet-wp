<?php declare(strict_types=1);
namespace AmigoPetWp\Controllers\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Services\EventService;

class AdminEventController extends BaseAdminController
{
    private $eventService;

    public function __construct()
    {
        parent::__construct();
        $this->eventService = new EventService($this->db->getEventRepository());
    }

    protected function registerHooks(): void
    {
        // Menu e submenu
        add_action('admin_menu', [$this, 'addMenus']);

        // Ações para eventos
        add_action('admin_post_apwp_save_event', [$this, 'saveEvent']);
        add_action('admin_post_nopriv_apwp_save_event', [$this, 'saveEvent']);
        add_action('admin_post_apwp_start_event', [$this, 'startEvent']);
        add_action('admin_post_nopriv_apwp_start_event', [$this, 'startEvent']);
        add_action('admin_post_apwp_complete_event', [$this, 'completeEvent']);
        add_action('admin_post_nopriv_apwp_complete_event', [$this, 'completeEvent']);
        add_action('admin_post_apwp_cancel_event', [$this, 'cancelEvent']);
        add_action('admin_post_nopriv_apwp_cancel_event', [$this, 'cancelEvent']);
        add_action('admin_post_apwp_add_participant', [$this, 'addParticipant']);
        add_action('admin_post_nopriv_apwp_add_participant', [$this, 'addParticipant']);
        add_action('admin_post_apwp_remove_participant', [$this, 'removeParticipant']);
        add_action('admin_post_nopriv_apwp_remove_participant', [$this, 'removeParticipant']);
    }

    public function addMenus(): void
    {
        add_submenu_page(
            'amigopet',
            esc_html__('Eventos', 'amigopet'),
            esc_html__('Eventos', 'amigopet'),
            'manage_amigopet_events',
            'amigopet-events',
            [$this, 'renderEvents']
        );
    }

    public function renderEvents(): void
    {
        $this->checkPermission('manage_amigopet_events');
        $this->loadView('admin/events/events-list-combined');
    }

    public function renderEventForm(): void
    {
        $this->checkPermission('manage_amigopet_events');
        $this->loadView('admin/events/event-form-combined');
    }


    public function saveEvent(): void
    {
        $this->checkPermission('manage_amigopet_events');
        check_admin_referer('apwp_save_event');

        $id = isset($_POST['event_id']) ? (int) $_POST['event_id'] : 0;
        $data = [
            'title' => sanitize_text_field($_POST['title']),
            'description' => wp_kses_post($_POST['description']),
            'date' => sanitize_text_field($_POST['date']),
            'location' => sanitize_text_field($_POST['location']),
            'max_participants' => !empty($_POST['max_participants']) ? (int) $_POST['max_participants'] : null,
            'organization_id' => get_current_user_id()
        ];

        try {
            if ($id) {
                $this->eventService->updateEvent($id, $data);
                $message = esc_html__('Evento atualizado com sucesso!', 'amigopet');
            } else {
                $this->eventService->createEvent($data);
                $message = esc_html__('Evento cadastrado com sucesso!', 'amigopet');
            }

            $this->redirect(
                admin_url('admin.php?page=amigopet-events'),
                $message
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-events'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function startEvent(): void
    {
        $this->checkPermission('manage_amigopet_events');
        check_admin_referer('apwp_start_event');

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if (!$id) {
            wp_die(esc_html__('ID do evento não fornecido', 'amigopet'));
        }

        try {
            $this->eventService->startEvent($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-events'),
                esc_html__('Evento iniciado com sucesso!', 'amigopet')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-events'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function completeEvent(): void
    {
        $this->checkPermission('manage_amigopet_events');
        check_admin_referer('apwp_complete_event');

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if (!$id) {
            wp_die(esc_html__('ID do evento não fornecido', 'amigopet'));
        }

        try {
            $this->eventService->completeEvent($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-events'),
                esc_html__('Evento concluído com sucesso!', 'amigopet')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-events'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function cancelEvent(): void
    {
        $this->checkPermission('manage_amigopet_events');
        check_admin_referer('apwp_cancel_event');

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if (!$id) {
            wp_die(esc_html__('ID do evento não fornecido', 'amigopet'));
        }

        try {
            $this->eventService->cancelEvent($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-events'),
                esc_html__('Evento cancelado com sucesso!', 'amigopet')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-events'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function addParticipant(): void
    {
        $this->checkPermission('manage_amigopet_events');
        check_admin_referer('apwp_add_participant');

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if (!$id) {
            wp_die(esc_html__('ID do evento não fornecido', 'amigopet'));
        }

        try {
            $this->eventService->addParticipant($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-events'),
                esc_html__('Participante adicionado com sucesso!', 'amigopet')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-events'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function removeParticipant(): void
    {
        $this->checkPermission('manage_amigopet_events');
        check_admin_referer('apwp_remove_participant');

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if (!$id) {
            wp_die(esc_html__('ID do evento não fornecido', 'amigopet'));
        }

        try {
            $this->eventService->removeParticipant($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-events'),
                esc_html__('Participante removido com sucesso!', 'amigopet')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-events'),
                $e->getMessage(),
                'error'
            );
        }
    }
}