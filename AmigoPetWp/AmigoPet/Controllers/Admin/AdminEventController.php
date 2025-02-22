<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Services\EventService;

class AdminEventController extends BaseAdminController {
    private $eventService;

    public function __construct() {
        parent::__construct();
        $this->eventService = new EventService($this->db->getEventRepository());
    }

    protected function registerHooks(): void {
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

    public function addMenus(): void {
        add_submenu_page(
            'amigopet-wp',
            __('Eventos', 'amigopet-wp'),
            __('Eventos', 'amigopet-wp'),
            'manage_amigopet_events',
            'amigopet-wp-events',
            [$this, 'renderEvents']
        );
    }

    public function renderEvents(): void {
        $this->checkPermission('manage_amigopet_events');

        $list_table = new \AmigoPetWp\Admin\Tables\APWP_Events_List_Table();
        $list_table->prepare_items();

        $this->loadView('admin/events/events-list', [
            'list_table' => $list_table
        ]);
    }

    public function renderEventForm(): void {
        $this->checkPermission('manage_amigopet_events');

        $event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $event = $event_id ? $this->eventService->findById($event_id) : null;

        $this->loadView('admin/events/event-form', [
            'event' => $event
        ]);
    }

    public function saveEvent(): void {
        $this->checkPermission('manage_amigopet_events');
        $this->verifyNonce('apwp_save_event');

        $id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
        $data = [
            'title' => sanitize_text_field($_POST['title']),
            'description' => wp_kses_post($_POST['description']),
            'date' => sanitize_text_field($_POST['date']),
            'location' => sanitize_text_field($_POST['location']),
            'max_participants' => !empty($_POST['max_participants']) ? (int)$_POST['max_participants'] : null,
            'organization_id' => get_current_user_id()
        ];

        try {
            if ($id) {
                $this->eventService->updateEvent($id, $data);
                $message = __('Evento atualizado com sucesso!', 'amigopet-wp');
            } else {
                $this->eventService->createEvent($data);
                $message = __('Evento cadastrado com sucesso!', 'amigopet-wp');
            }

            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-events'),
                $message
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-events'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function startEvent(): void {
        $this->checkPermission('manage_amigopet_events');
        $this->verifyNonce('apwp_start_event');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            wp_die(__('ID do evento não fornecido', 'amigopet-wp'));
        }

        try {
            $this->eventService->startEvent($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-events'),
                __('Evento iniciado com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-events'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function completeEvent(): void {
        $this->checkPermission('manage_amigopet_events');
        $this->verifyNonce('apwp_complete_event');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            wp_die(__('ID do evento não fornecido', 'amigopet-wp'));
        }

        try {
            $this->eventService->completeEvent($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-events'),
                __('Evento concluído com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-events'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function cancelEvent(): void {
        $this->checkPermission('manage_amigopet_events');
        $this->verifyNonce('apwp_cancel_event');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            wp_die(__('ID do evento não fornecido', 'amigopet-wp'));
        }

        try {
            $this->eventService->cancelEvent($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-events'),
                __('Evento cancelado com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-events'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function addParticipant(): void {
        $this->checkPermission('manage_amigopet_events');
        $this->verifyNonce('apwp_add_participant');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            wp_die(__('ID do evento não fornecido', 'amigopet-wp'));
        }

        try {
            $this->eventService->addParticipant($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-events'),
                __('Participante adicionado com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-events'),
                $e->getMessage(),
                'error'
            );
        }
    }

    public function removeParticipant(): void {
        $this->checkPermission('manage_amigopet_events');
        $this->verifyNonce('apwp_remove_participant');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            wp_die(__('ID do evento não fornecido', 'amigopet-wp'));
        }

        try {
            $this->eventService->removeParticipant($id);
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-events'),
                __('Participante removido com sucesso!', 'amigopet-wp')
            );
        } catch (\Exception $e) {
            $this->redirect(
                admin_url('admin.php?page=amigopet-wp-events'),
                $e->getMessage(),
                'error'
            );
        }
    }
}
