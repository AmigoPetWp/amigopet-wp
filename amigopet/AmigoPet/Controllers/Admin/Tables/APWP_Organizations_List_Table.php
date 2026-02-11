<?php declare(strict_types=1);
namespace AmigoPetWp\Controllers\Admin\Tables;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Services\OrganizationService;

class APWP_Organizations_List_Table extends \WP_List_Table {
    private $organizationService;
    private $items_per_page = 10;
    private $total_items = 0;

    public function __construct() {
        parent::__construct([
            'singular' => __('Organização', 'amigopet'),
            'plural'   => __('Organizações', 'amigopet'),
            'ajax'     => false
        ]);

        $this->organizationService = new OrganizationService(
            amigopet_wp()->getDatabase()->getOrganizationRepository()
        );
    }

    public function prepare_items() {
        $this->_column_headers = [
            $this->get_columns(),
            $this->get_hidden_columns(),
            $this->get_sortable_columns()
        ];

        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $this->items_per_page;

        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
        $status = isset($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : '';

        if ($status) {
            $items = $this->organizationService->findByStatus($status);
        } else {
            $items = $this->organizationService->findAll();
        }

        if ($search) {
            $items = array_filter($items, function($item) use ($search) {
                return stripos($item->getName(), $search) !== false
                    || stripos($item->getEmail(), $search) !== false
                    || stripos($item->getPhone(), $search) !== false;
            });
        }

        $this->total_items = count($items);
        $this->items = array_slice($items, $offset, $this->items_per_page);

        $this->set_pagination_args([
            'total_items' => $this->total_items,
            'per_page'    => $this->items_per_page,
            'total_pages' => ceil($this->total_items / $this->items_per_page)
        ]);
    }

    public function get_columns() {
        return [
            'cb'        => '<input type="checkbox" />',
            'name'      => __('Nome', 'amigopet'),
            'email'     => __('Email', 'amigopet'),
            'phone'     => __('Telefone', 'amigopet'),
            'address'   => __('Endereço', 'amigopet'),
            'status'    => __('Status', 'amigopet'),
            'created_at' => __('Criado em', 'amigopet')
        ];
    }

    public function get_sortable_columns() {
        return [
            'name'       => ['name', true],
            'email'      => ['email', false],
            'status'     => ['status', false],
            'created_at' => ['created_at', false]
        ];
    }

    public function get_hidden_columns() {
        return [];
    }

    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="organization[]" value="%s" />',
            $item->getId()
        );
    }

    public function column_name($item) {
        $actions = [
            'edit' => sprintf(
                '<a href="%s">%s</a>',
                admin_url('admin.php?page=amigopet-organizations&action=edit&id=' . $item->getId()),
                __('Editar', 'amigopet')
            ),
            'delete' => sprintf(
                '<a href="%s" class="submitdelete" onclick="return confirm(\'%s\');">%s</a>',
                wp_nonce_url(
                    admin_url('admin.php?page=amigopet-organizations&action=delete&id=' . $item->getId()),
                    'delete_organization_' . $item->getId()
                ),
                __('Tem certeza que deseja excluir esta organização?', 'amigopet'),
                __('Excluir', 'amigopet')
            )
        ];

        return sprintf(
            '<strong><a href="%s">%s</a></strong> %s',
            admin_url('admin.php?page=amigopet-organizations&action=edit&id=' . $item->getId()),
            $item->getName(),
            $this->row_actions($actions)
        );
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'email':
                return $item->getEmail();
            case 'phone':
                return $item->getPhone();
            case 'address':
                return $item->getAddress();
            case 'status':
                return ucfirst($item->getStatus());
            case 'created_at':
                return $item->getCreatedAt()->format('d/m/Y H:i:s');
            default:
                return;
        }
    }

    public function get_bulk_actions() {
        return [
            'delete' => __('Excluir', 'amigopet'),
            'activate' => __('Ativar', 'amigopet'),
            'deactivate' => __('Desativar', 'amigopet')
        ];
    }

    public function process_bulk_action() {
        if ('delete' === $this->current_action()) {
            $nonce = esc_attr($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'bulk-' . $this->_args['plural'])) {
                wp_die('Nonce verification failed');
            }

            $organizations = isset($_REQUEST['organization']) ? (array) $_REQUEST['organization'] : [];
            foreach ($organizations as $id) {
                $this->organizationService->delete((int) $id);
            }
        }
    }
}