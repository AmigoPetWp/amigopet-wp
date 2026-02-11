<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

// Carrega a classe WP_List_Table se ainda não estiver disponível
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class APWP_Adoptions_List_Table extends WP_List_Table
{
    private $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        parent::__construct([
            'singular' => esc_html__('Adoção', 'amigopet'),
            'plural' => esc_html__('Adoções', 'amigopet'),
            'ajax' => false
        ]);
    }

    public function get_columns()
    {
        return [
            'cb' => '<input type="checkbox" />',
            'pet' => esc_html__('Pet', 'amigopet'),
            'adopter' => esc_html__('Adotante', 'amigopet'),
            'contact' => esc_html__('Contato', 'amigopet'),
            'status' => esc_html__('Status', 'amigopet'),
            'payment' => esc_html__('Pagamento', 'amigopet'),
            'reviewer' => esc_html__('Revisor', 'amigopet'),
            'created_at' => esc_html__('Data', 'amigopet')
        ];
    }

    public function get_sortable_columns()
    {
        return [
            'pet' => ['p.name', false],
            'adopter' => ['a.name', false],
            'status' => ['ad.status', false],
            'reviewer' => ['v.name', false],
            'created_at' => ['ad.created_at', true]
        ];
    }

    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'pet':
                $actions = [
                    'edit' => sprintf(
                        '<a href="%s">%s</a>',
                        add_query_arg(['action' => 'edit', 'id' => $item->id], menu_page_url('amigopet-adoptions', false)),
                        esc_html__('Editar', 'amigopet')
                    )
                ];

                // Adiciona ação de cancelar se a adoção não estiver concluída ou cancelada
                if (!in_array($item->status, ['completed', 'cancelled'])) {
                    $actions['cancel'] = sprintf(
                        '<a href="%s" onclick="return confirm(\'%s\');">%s</a>',
                        wp_nonce_url(add_query_arg(['action' => 'cancel', 'id' => $item->id], menu_page_url('amigopet-adoptions', false)), 'cancel_adoption_' . $item->id),
                        esc_html__('Tem certeza que deseja cancelar esta adoção?', 'amigopet'),
                        esc_html__('Cancelar', 'amigopet')
                    );
                }

                $pet_info = array(
                    esc_html($item->pet_name),
                    esc_html($item->species_name),
                    esc_html($item->breed_name),
                    /* translators: %d: age in years */
                    $item->age ? sprintf(esc_html__('%d anos', 'amigopet'), (int) $item->age) : '',
                    $item->size ? esc_html(ucfirst($item->size)) : '',
                    /* translators: %s: RGA number */
                    $item->rga ? sprintf(esc_html__('RGA: %s', 'amigopet'), esc_html($item->rga)) : '',
                    /* translators: %s: Microchip number */
                    $item->microchip_number ? sprintf(esc_html__('Microchip: %s', 'amigopet'), esc_html($item->microchip_number)) : ''
                );

                $pet_info = array_filter($pet_info);

                return sprintf(
                    '%1$s %2$s',
                    implode(' | ', $pet_info),
                    $this->row_actions($actions)
                );

            case 'adopter':
                return sprintf(
                    '%s<br><small>%s</small>',
                    esc_html($item->adopter_name),
                    esc_html($item->adopter_email)
                );

            case 'contact':
                return sprintf(
                    '%s<br>%s',
                    esc_html($item->adopter_phone),
                    esc_html($item->adopter_address)
                );

            case 'status':
                $status_labels = [
                    'pending' => esc_html__('Pendente', 'amigopet'),
                    'approved' => esc_html__('Aprovada', 'amigopet'),
                    'rejected' => esc_html__('Rejeitada', 'amigopet'),
                    'awaiting_payment' => esc_html__('Aguardando Pagamento', 'amigopet'),
                    'paid' => esc_html__('Pago', 'amigopet'),
                    'completed' => esc_html__('Concluída', 'amigopet'),
                    'cancelled' => esc_html__('Cancelada', 'amigopet')
                ];

                $status_classes = [
                    'pending' => 'status-pending',
                    'approved' => 'status-approved',
                    'rejected' => 'status-rejected',
                    'awaiting_payment' => 'status-awaiting',
                    'paid' => 'status-paid',
                    'completed' => 'status-completed',
                    'cancelled' => 'status-cancelled'
                ];

                return sprintf(
                    '<span class="adoption-status %s">%s</span>',
                    esc_attr($status_classes[$item->status] ?? ''),
                    esc_html($status_labels[$item->status] ?? $item->status)
                );

            case 'payment':
                if (empty($item->payment_id)) {
                    return '-';
                }

                $payment_method_labels = [
                    'cash' => esc_html__('Dinheiro', 'amigopet'),
                    'pix' => esc_html__('PIX', 'amigopet'),
                    'bank_transfer' => esc_html__('Transferência', 'amigopet')
                ];

                $payment_status_labels = [
                    'pending' => esc_html__('Pendente', 'amigopet'),
                    'paid' => esc_html__('Pago', 'amigopet'),
                    'refunded' => esc_html__('Reembolsado', 'amigopet')
                ];

                $payment_info = [
                    sprintf('R$ %.2f', (float) $item->payment_amount),
                    $payment_method_labels[$item->payment_method] ?? $item->payment_method,
                    $payment_status_labels[$item->payment_status] ?? $item->payment_status
                ];

                $actions = [];
                if ($item->payment_status === 'paid') {
                    $actions['refund'] = sprintf(
                        '<a href="%s" onclick="return confirm(\'%s\');">%s</a>',
                        wp_nonce_url(add_query_arg(['action' => 'refund', 'id' => $item->id], menu_page_url('amigopet-adoptions', false)), 'refund_payment_' . $item->id),
                        esc_html__('Tem certeza que deseja reembolsar este pagamento?', 'amigopet'),
                        esc_html__('Reembolsar', 'amigopet')
                    );
                }

                return sprintf(
                    '%s %s',
                    implode(' | ', array_map('esc_html', $payment_info)),
                    $this->row_actions($actions)
                );

            case 'reviewer':
                if (empty($item->reviewer_id)) {
                    return '-';
                }
                return sprintf(
                    '%s<br><small>%s</small>',
                    esc_html($item->reviewer_name),
                    esc_html($item->reviewer_role)
                );

            case 'created_at':
                return date_i18n(
                    get_option('date_format') . ' ' . get_option('time_format'),
                    strtotime($item->created_at)
                );

            default:
                return '';
        }
    }

    protected function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="adoption_ids[]" value="%s" />',
            esc_attr($item->id)
        );
    }

    public function prepare_items()
    {
        $wpdb = $this->wpdb;
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = [$columns, $hidden, $sortable];

        $per_page = 20;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        // Prepara a query base
        $table_name = preg_replace('/[^a-zA-Z0-9_]/', '', $this->wpdb->prefix . 'amigopet_adoptions');
        $pets_table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->wpdb->prefix . 'amigopet_pets');
        $species_table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->wpdb->prefix . 'amigopet_pet_species');
        $breeds_table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->wpdb->prefix . 'amigopet_pet_breeds');
        $adopters_table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->wpdb->prefix . 'amigopet_adopters');
        $volunteers_table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->wpdb->prefix . 'amigopet_volunteers');
        $payments_table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->wpdb->prefix . 'amigopet_adoption_payments');

        $search_term = '';
        $search = '';
        $status_filter = '';
        if (isset($_REQUEST['s']) && $_REQUEST['s'] !== '') {
            $search_term = sanitize_text_field(wp_unslash($_REQUEST['s']));
            $search = '%' . $wpdb->esc_like($search_term) . '%';
        }

        if (isset($_REQUEST['status']) && $_REQUEST['status'] !== '') {
            $status_filter = sanitize_text_field(wp_unslash($_REQUEST['status']));
        }

        $orderby = !empty($_REQUEST['orderby']) ? sanitize_text_field(wp_unslash($_REQUEST['orderby'])) : 'created_at';
        $order = !empty($_REQUEST['order']) ? strtoupper(sanitize_text_field(wp_unslash($_REQUEST['order']))) : 'DESC';

        $allowed_orderby = [
            'pet_name',
            'adopter_name',
            'status',
            'reviewer_name',
            'created_at'
        ];

        if (!in_array($orderby, $allowed_orderby)) {
            $orderby = 'ad.created_at';
        }

        // Whitelist for order
        if (!in_array($order, ['ASC', 'DESC'])) {
            $order = 'DESC';
        }

        if ($search !== '' && $status_filter !== '' && $order === 'ASC') {
            $this->items = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT 
                        ad.*, 
                        p.name as pet_name,
                        p.age,
                        p.size,
                        p.rga,
                        p.microchip_number,
                        s.name as species_name,
                        b.name as breed_name,
                        a.name as adopter_name,
                        a.email as adopter_email,
                        a.phone as adopter_phone,
                        a.address as adopter_address,
                        v.name as reviewer_name,
                        v.role as reviewer_role,
                        pay.id as payment_id,
                        pay.amount as payment_amount,
                        pay.payment_method,
                        pay.status as payment_status,
                        pay.paid_at
                    FROM %i ad
                    LEFT JOIN %i p ON ad.pet_id = p.id
                    LEFT JOIN %i s ON p.species_id = s.id
                    LEFT JOIN %i b ON p.breed_id = b.id
                    LEFT JOIN %i a ON ad.adopter_id = a.id
                    LEFT JOIN %i v ON ad.reviewer_id = v.id
                    LEFT JOIN %i pay ON ad.id = pay.adoption_id
                    WHERE (p.name LIKE %s OR a.name LIKE %s OR a.email LIKE %s) AND ad.status = %s
                    ORDER BY %i ASC
                    LIMIT %d OFFSET %d',
                    $table_name,
                    $pets_table,
                    $species_table,
                    $breeds_table,
                    $adopters_table,
                    $volunteers_table,
                    $payments_table,
                    $search,
                    $search,
                    $search,
                    $status_filter,
                    $orderby,
                    (int) $per_page,
                    (int) $offset
                )
            );
        } elseif ($search !== '' && $status_filter !== '' && $order === 'DESC') {
            $this->items = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT 
                        ad.*, 
                        p.name as pet_name,
                        p.age,
                        p.size,
                        p.rga,
                        p.microchip_number,
                        s.name as species_name,
                        b.name as breed_name,
                        a.name as adopter_name,
                        a.email as adopter_email,
                        a.phone as adopter_phone,
                        a.address as adopter_address,
                        v.name as reviewer_name,
                        v.role as reviewer_role,
                        pay.id as payment_id,
                        pay.amount as payment_amount,
                        pay.payment_method,
                        pay.status as payment_status,
                        pay.paid_at
                    FROM %i ad
                    LEFT JOIN %i p ON ad.pet_id = p.id
                    LEFT JOIN %i s ON p.species_id = s.id
                    LEFT JOIN %i b ON p.breed_id = b.id
                    LEFT JOIN %i a ON ad.adopter_id = a.id
                    LEFT JOIN %i v ON ad.reviewer_id = v.id
                    LEFT JOIN %i pay ON ad.id = pay.adoption_id
                    WHERE (p.name LIKE %s OR a.name LIKE %s OR a.email LIKE %s) AND ad.status = %s
                    ORDER BY %i DESC
                    LIMIT %d OFFSET %d',
                    $table_name,
                    $pets_table,
                    $species_table,
                    $breeds_table,
                    $adopters_table,
                    $volunteers_table,
                    $payments_table,
                    $search,
                    $search,
                    $search,
                    $status_filter,
                    $orderby,
                    (int) $per_page,
                    (int) $offset
                )
            );
        } elseif ($search !== '' && $order === 'ASC') {
            $this->items = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT 
                        ad.*, 
                        p.name as pet_name,
                        p.age,
                        p.size,
                        p.rga,
                        p.microchip_number,
                        s.name as species_name,
                        b.name as breed_name,
                        a.name as adopter_name,
                        a.email as adopter_email,
                        a.phone as adopter_phone,
                        a.address as adopter_address,
                        v.name as reviewer_name,
                        v.role as reviewer_role,
                        pay.id as payment_id,
                        pay.amount as payment_amount,
                        pay.payment_method,
                        pay.status as payment_status,
                        pay.paid_at
                    FROM %i ad
                    LEFT JOIN %i p ON ad.pet_id = p.id
                    LEFT JOIN %i s ON p.species_id = s.id
                    LEFT JOIN %i b ON p.breed_id = b.id
                    LEFT JOIN %i a ON ad.adopter_id = a.id
                    LEFT JOIN %i v ON ad.reviewer_id = v.id
                    LEFT JOIN %i pay ON ad.id = pay.adoption_id
                    WHERE (p.name LIKE %s OR a.name LIKE %s OR a.email LIKE %s)
                    ORDER BY %i ASC
                    LIMIT %d OFFSET %d',
                    $table_name,
                    $pets_table,
                    $species_table,
                    $breeds_table,
                    $adopters_table,
                    $volunteers_table,
                    $payments_table,
                    $search,
                    $search,
                    $search,
                    $orderby,
                    (int) $per_page,
                    (int) $offset
                )
            );
        } elseif ($search !== '' && $order === 'DESC') {
            $this->items = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT 
                        ad.*, 
                        p.name as pet_name,
                        p.age,
                        p.size,
                        p.rga,
                        p.microchip_number,
                        s.name as species_name,
                        b.name as breed_name,
                        a.name as adopter_name,
                        a.email as adopter_email,
                        a.phone as adopter_phone,
                        a.address as adopter_address,
                        v.name as reviewer_name,
                        v.role as reviewer_role,
                        pay.id as payment_id,
                        pay.amount as payment_amount,
                        pay.payment_method,
                        pay.status as payment_status,
                        pay.paid_at
                    FROM %i ad
                    LEFT JOIN %i p ON ad.pet_id = p.id
                    LEFT JOIN %i s ON p.species_id = s.id
                    LEFT JOIN %i b ON p.breed_id = b.id
                    LEFT JOIN %i a ON ad.adopter_id = a.id
                    LEFT JOIN %i v ON ad.reviewer_id = v.id
                    LEFT JOIN %i pay ON ad.id = pay.adoption_id
                    WHERE (p.name LIKE %s OR a.name LIKE %s OR a.email LIKE %s)
                    ORDER BY %i DESC
                    LIMIT %d OFFSET %d',
                    $table_name,
                    $pets_table,
                    $species_table,
                    $breeds_table,
                    $adopters_table,
                    $volunteers_table,
                    $payments_table,
                    $search,
                    $search,
                    $search,
                    $orderby,
                    (int) $per_page,
                    (int) $offset
                )
            );
        } elseif ($status_filter !== '' && $order === 'ASC') {
            $this->items = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT 
                        ad.*, 
                        p.name as pet_name,
                        p.age,
                        p.size,
                        p.rga,
                        p.microchip_number,
                        s.name as species_name,
                        b.name as breed_name,
                        a.name as adopter_name,
                        a.email as adopter_email,
                        a.phone as adopter_phone,
                        a.address as adopter_address,
                        v.name as reviewer_name,
                        v.role as reviewer_role,
                        pay.id as payment_id,
                        pay.amount as payment_amount,
                        pay.payment_method,
                        pay.status as payment_status,
                        pay.paid_at
                    FROM %i ad
                    LEFT JOIN %i p ON ad.pet_id = p.id
                    LEFT JOIN %i s ON p.species_id = s.id
                    LEFT JOIN %i b ON p.breed_id = b.id
                    LEFT JOIN %i a ON ad.adopter_id = a.id
                    LEFT JOIN %i v ON ad.reviewer_id = v.id
                    LEFT JOIN %i pay ON ad.id = pay.adoption_id
                    WHERE ad.status = %s
                    ORDER BY %i ASC
                    LIMIT %d OFFSET %d',
                    $table_name,
                    $pets_table,
                    $species_table,
                    $breeds_table,
                    $adopters_table,
                    $volunteers_table,
                    $payments_table,
                    $status_filter,
                    $orderby,
                    (int) $per_page,
                    (int) $offset
                )
            );
        } elseif ($status_filter !== '' && $order === 'DESC') {
            $this->items = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT 
                        ad.*, 
                        p.name as pet_name,
                        p.age,
                        p.size,
                        p.rga,
                        p.microchip_number,
                        s.name as species_name,
                        b.name as breed_name,
                        a.name as adopter_name,
                        a.email as adopter_email,
                        a.phone as adopter_phone,
                        a.address as adopter_address,
                        v.name as reviewer_name,
                        v.role as reviewer_role,
                        pay.id as payment_id,
                        pay.amount as payment_amount,
                        pay.payment_method,
                        pay.status as payment_status,
                        pay.paid_at
                    FROM %i ad
                    LEFT JOIN %i p ON ad.pet_id = p.id
                    LEFT JOIN %i s ON p.species_id = s.id
                    LEFT JOIN %i b ON p.breed_id = b.id
                    LEFT JOIN %i a ON ad.adopter_id = a.id
                    LEFT JOIN %i v ON ad.reviewer_id = v.id
                    LEFT JOIN %i pay ON ad.id = pay.adoption_id
                    WHERE ad.status = %s
                    ORDER BY %i DESC
                    LIMIT %d OFFSET %d',
                    $table_name,
                    $pets_table,
                    $species_table,
                    $breeds_table,
                    $adopters_table,
                    $volunteers_table,
                    $payments_table,
                    $status_filter,
                    $orderby,
                    (int) $per_page,
                    (int) $offset
                )
            );
        } elseif ($order === 'ASC') {
            $this->items = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT 
                        ad.*, 
                        p.name as pet_name,
                        p.age,
                        p.size,
                        p.rga,
                        p.microchip_number,
                        s.name as species_name,
                        b.name as breed_name,
                        a.name as adopter_name,
                        a.email as adopter_email,
                        a.phone as adopter_phone,
                        a.address as adopter_address,
                        v.name as reviewer_name,
                        v.role as reviewer_role,
                        pay.id as payment_id,
                        pay.amount as payment_amount,
                        pay.payment_method,
                        pay.status as payment_status,
                        pay.paid_at
                    FROM %i ad
                    LEFT JOIN %i p ON ad.pet_id = p.id
                    LEFT JOIN %i s ON p.species_id = s.id
                    LEFT JOIN %i b ON p.breed_id = b.id
                    LEFT JOIN %i a ON ad.adopter_id = a.id
                    LEFT JOIN %i v ON ad.reviewer_id = v.id
                    LEFT JOIN %i pay ON ad.id = pay.adoption_id
                    ORDER BY %i ASC
                    LIMIT %d OFFSET %d',
                    $table_name,
                    $pets_table,
                    $species_table,
                    $breeds_table,
                    $adopters_table,
                    $volunteers_table,
                    $payments_table,
                    $orderby,
                    (int) $per_page,
                    (int) $offset
                )
            );
        } else {
            $this->items = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT 
                        ad.*, 
                        p.name as pet_name,
                        p.age,
                        p.size,
                        p.rga,
                        p.microchip_number,
                        s.name as species_name,
                        b.name as breed_name,
                        a.name as adopter_name,
                        a.email as adopter_email,
                        a.phone as adopter_phone,
                        a.address as adopter_address,
                        v.name as reviewer_name,
                        v.role as reviewer_role,
                        pay.id as payment_id,
                        pay.amount as payment_amount,
                        pay.payment_method,
                        pay.status as payment_status,
                        pay.paid_at
                    FROM %i ad
                    LEFT JOIN %i p ON ad.pet_id = p.id
                    LEFT JOIN %i s ON p.species_id = s.id
                    LEFT JOIN %i b ON p.breed_id = b.id
                    LEFT JOIN %i a ON ad.adopter_id = a.id
                    LEFT JOIN %i v ON ad.reviewer_id = v.id
                    LEFT JOIN %i pay ON ad.id = pay.adoption_id
                    ORDER BY %i DESC
                    LIMIT %d OFFSET %d',
                    $table_name,
                    $pets_table,
                    $species_table,
                    $breeds_table,
                    $adopters_table,
                    $volunteers_table,
                    $payments_table,
                    $orderby,
                    (int) $per_page,
                    (int) $offset
                )
            );
        }

        $total_items = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT COUNT(id) FROM %i WHERE 1 = %d',
                $table_name,
                1
            )
        );

        // Configura a paginação
        $this->set_pagination_args([
            'total_items' => (int) $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil((int) $total_items / $per_page)
        ]);
    }

    public function extra_tablenav($which)
    {
        if ($which !== 'top')
            return;
        ?>
        <div class="alignleft actions">
            <select name="status" id="filter-by-status">
                <option value=""><?php esc_html_e('Todos os status', 'amigopet'); ?></option>
                <?php
                $statuses = [
                    'pending' => esc_html__('Pendente', 'amigopet'),
                    'approved' => esc_html__('Aprovada', 'amigopet'),
                    'rejected' => esc_html__('Rejeitada', 'amigopet'),
                    'awaiting_payment' => esc_html__('Aguardando Pagamento', 'amigopet'),
                    'paid' => esc_html__('Pago', 'amigopet'),
                    'completed' => esc_html__('Concluída', 'amigopet'),
                    'cancelled' => esc_html__('Cancelada', 'amigopet')
                ];

                foreach ($statuses as $value => $label) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($value),
                        selected(!empty($_REQUEST['status']) ? sanitize_text_field(wp_unslash($_REQUEST['status'])) : '', $value, false),
                        esc_html($label)
                    );
                }
                ?>
            </select>
            <?php submit_button(esc_html__('Filtrar', 'amigopet'), '', 'filter_action', false); ?>
        </div>
        <?php
    }
}

// Cria uma instância da tabela e configura os itens
$apwp_adoptions_table = new APWP_Adoptions_List_Table();
$apwp_adoptions_table->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('Adoções', 'amigopet'); ?></h1>
    <a href="<?php echo esc_url(add_query_arg('action', 'add')); ?>" class="page-title-action">
        <?php esc_html_e('Adicionar Nova', 'amigopet'); ?>
    </a>

    <?php
    // Mensagens de feedback
    if (isset($_GET['message'])) {
        $apwp_message = intval($_GET['message']);
        $apwp_messages = [
            1 => esc_html__('Adoção salva com sucesso.', 'amigopet'),
            2 => esc_html__('Adoção excluída com sucesso.', 'amigopet'),
            3 => esc_html__('Adoção aprovada com sucesso.', 'amigopet'),
            4 => esc_html__('Adoção rejeitada com sucesso.', 'amigopet')
        ];

        if (isset($apwp_messages[$apwp_message])) {
            /* translators: %s: success message */
            printf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html($apwp_messages[$apwp_message]));
        }
    }
    ?>

    <form id="adoptions-filter" method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr(isset($_REQUEST['page']) ? sanitize_text_field(wp_unslash($_REQUEST['page'])) : ''); ?>" />
        <?php
        $apwp_adoptions_table->search_box(esc_html__('Buscar Adoções', 'amigopet'), 'adoption');
        $apwp_adoptions_table->display();
        ?>
    </form>
</div>

<style>
    .status-pending {
        background-color: #f0f0f1;
        color: #646970;
    }

    .status-approved {
        background-color: #c6e1c6;
        color: #5b841b;
    }

    .status-rejected {
        background-color: #f1adad;
        color: #8f1919;
    }

    .status-cancelled {
        background-color: #e5e5e5;
        color: #646970;
    }

    .column-status span {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 12px;
        line-height: 1;
    }
</style>

<script>
    jQuery(document).ready(function ($) {
        // Confirmação de exclusão
        $('.delete-adoption').on('click', function (e) {
            if (!confirm('<?php echo esc_js(esc_html__('Tem certeza que deseja excluir esta adoção?', 'amigopet')); ?>')) {
                e.preventDefault();
            }
        });

        // Atualização em tempo real dos filtros
        $('#filter-by-status').on('change', function () {
            $('#adoptions-filter').submit();
        });
    });
</script>