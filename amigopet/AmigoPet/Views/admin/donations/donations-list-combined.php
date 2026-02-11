<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

// Carrega a classe WP_List_Table se ainda não estiver disponível
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class APWP_Donations_List_Table extends WP_List_Table
{
    public function __construct()
    {
        parent::__construct([
            'singular' => esc_html__('Doação', 'amigopet'),
            'plural' => esc_html__('Doações', 'amigopet'),
            'ajax' => true
        ]);
    }

    public function get_columns()
    {
        return [
            'cb' => '<input type="checkbox" />',
            'donor' => esc_html__('Doador', 'amigopet'),
            'type' => esc_html__('Tipo', 'amigopet'),
            'amount' => esc_html__('Valor/Itens', 'amigopet'),
            'date' => esc_html__('Data', 'amigopet'),
            'status' => esc_html__('Status', 'amigopet'),
            'contact' => esc_html__('Contato', 'amigopet')
        ];
    }

    public function get_sortable_columns()
    {
        return [
            'donor' => ['donor_name', false],
            'type' => ['donation_type', false],
            'amount' => ['donation_amount', false],
            'date' => ['donation_date', true]
        ];
    }

    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'donor':
                $actions = [
                    'edit' => sprintf(
                        '<a href="%s">%s</a>',
                        add_query_arg(['action' => 'edit', 'id' => $item->ID]),
                        esc_html__('Editar', 'amigopet')
                    ),
                    'delete' => sprintf(
                        '<a href="%s" class="delete-donation" data-id="%d">%s</a>',
                        wp_nonce_url(admin_url('admin-post.php?action=apwp_delete_donation&id=' . $item->ID), 'delete_donation_' . $item->ID),
                        $item->ID,
                        esc_html__('Excluir', 'amigopet')
                    )
                ];

                return sprintf(
                    '<strong>%s</strong> %s',
                    esc_html(get_post_meta($item->ID, 'donor_name', true)),
                    $this->row_actions($actions)
                );

            case 'type':
                $types = [
                    'money' => esc_html__('Dinheiro', 'amigopet'),
                    'food' => esc_html__('Ração', 'amigopet'),
                    'medicine' => esc_html__('Medicamentos', 'amigopet'),
                    'supplies' => esc_html__('Suprimentos', 'amigopet'),
                    'other' => esc_html__('Outro', 'amigopet')
                ];
                $type = get_post_meta($item->ID, 'donation_type', true);
                return isset($types[$type]) ? $types[$type] : esc_html($type);

            case 'amount':
                $type = get_post_meta($item->ID, 'donation_type', true);
                $amount = get_post_meta($item->ID, 'donation_amount', true);

                if ($type === 'money') {
                    return 'R$ ' . number_format((float) $amount, 2, ',', '.');
                } else {
                    return esc_html($amount);
                }

            case 'date':
                $date = get_post_meta($item->ID, 'donation_date', true);
                return $date ? date_i18n(get_option('date_format'), strtotime($date)) : '-';

            case 'status':
                $status = get_post_meta($item->ID, 'donation_status', true);
                $statuses = [
                    'pending' => ['label' => esc_html__('Pendente', 'amigopet'), 'class' => 'pending'],
                    'received' => ['label' => esc_html__('Recebida', 'amigopet'), 'class' => 'received'],
                    'cancelled' => ['label' => esc_html__('Cancelada', 'amigopet'), 'class' => 'cancelled']
                ];

                return isset($statuses[$status])
                    ? sprintf(
                        '<span class="donation-status status-%s">%s</span>',
                        esc_attr($statuses[$status]['class']),
                        esc_html($statuses[$status]['label'])
                    )
                    : esc_html($status);

            case 'contact':
                $email = get_post_meta($item->ID, 'donor_email', true);
                $phone = get_post_meta($item->ID, 'donor_phone', true);

                $contact = [];
                if ($email) {
                    $contact[] = sprintf('<a href="mailto:%1$s">%1$s</a>', esc_attr($email));
                }
                if ($phone) {
                    $contact[] = esc_html($phone);
                }

                return implode('<br>', $contact);

            default:
                return '';
        }
    }

    protected function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="donation_ids[]" value="%s" />',
            esc_attr($item->ID)
        );
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = [$columns, $hidden, $sortable];

        $per_page = 20;
        $current_page = $this->get_pagenum();

        // Prepara os argumentos da query
        $args = [
            'post_type' => 'apwp_donation',
            'posts_per_page' => $per_page,
            'paged' => $current_page,
            'orderby' => !empty($_REQUEST['orderby']) ? sanitize_sql_orderby(wp_unslash($_REQUEST['orderby'])) : 'meta_value',
            'order' => !empty($_REQUEST['order']) ? strtoupper(sanitize_text_field(wp_unslash($_REQUEST['order']))) : 'DESC',
            'meta_key' => 'donation_date'
        ];

        // Adiciona filtros se existirem
        if (!empty($_REQUEST['donation_type'])) {
            $args['meta_query'][] = [
                'key' => 'donation_type',
                'value' => sanitize_text_field(wp_unslash($_REQUEST['donation_type']))
            ];
        }

        if (!empty($_REQUEST['donation_status'])) {
            $args['meta_query'][] = [
                'key' => 'donation_status',
                'value' => sanitize_text_field(wp_unslash($_REQUEST['donation_status']))
            ];
        }

        if (!empty($_REQUEST['s'])) {
            $search_term = sanitize_text_field(wp_unslash($_REQUEST['s']));
            $args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key' => 'donor_name',
                    'value' => $search_term,
                    'compare' => 'LIKE'
                ],
                [
                    'key' => 'donor_email',
                    'value' => $search_term,
                    'compare' => 'LIKE'
                ]
            ];
        }

        // Executa a query
        $query = new WP_Query($args);

        $this->items = $query->posts;

        // Configura a paginação
        $this->set_pagination_args([
            'total_items' => (int) $query->found_posts,
            'per_page' => $per_page,
            'total_pages' => ceil((int) $query->found_posts / $per_page)
        ]);
    }

    public function extra_tablenav($which)
    {
        if ($which !== 'top')
            return;
        ?>
        <div class="alignleft actions">
            <select name="donation_type" id="filter-by-type">
                <option value=""><?php esc_html_e('Todos os tipos', 'amigopet'); ?></option>
                <?php
                $types = [
                    'money' => esc_html__('Dinheiro', 'amigopet'),
                    'food' => esc_html__('Ração', 'amigopet'),
                    'medicine' => esc_html__('Medicamentos', 'amigopet'),
                    'supplies' => esc_html__('Suprimentos', 'amigopet'),
                    'other' => esc_html__('Outro', 'amigopet')
                ];
                foreach ($types as $value => $label) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($value),
                        selected(isset($_REQUEST['donation_type']) ? $_REQUEST['donation_type'] : '', $value, false),
                        esc_html($label)
                    );
                }
                ?>
            </select>

            <select name="donation_status" id="filter-by-status">
                <option value=""><?php esc_html_e('Todos os status', 'amigopet'); ?></option>
                <?php
                $statuses = [
                    'pending' => esc_html__('Pendente', 'amigopet'),
                    'received' => esc_html__('Recebida', 'amigopet'),
                    'cancelled' => esc_html__('Cancelada', 'amigopet')
                ];
                foreach ($statuses as $value => $label) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($value),
                        selected(isset($_REQUEST['donation_status']) ? $_REQUEST['donation_status'] : '', $value, false),
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

// Cria uma instância da tabela
$apwp_donations_table = new APWP_Donations_List_Table();
$apwp_donations_table->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('Doações', 'amigopet'); ?></h1>
    <a href="<?php echo esc_url(add_query_arg('action', 'add')); ?>" class="page-title-action">
        <?php esc_html_e('Adicionar Nova', 'amigopet'); ?>
    </a>

    <?php
    // Mensagens de feedback
    if (isset($_GET['message'])) {
        $apwp_message = intval($_GET['message']);
        $apwp_messages = [
            1 => esc_html__('Doação salva com sucesso.', 'amigopet'),
            2 => esc_html__('Doação excluída com sucesso.', 'amigopet')
        ];

        if (isset($apwp_messages[$apwp_message])) {
            /* translators: %s: success message */
            printf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html($apwp_messages[$apwp_message]));
        }
    }
    ?>

    <form id="donations-filter" method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page'] ?? ''); ?>" />
        <?php
        $apwp_donations_table->search_box(esc_html__('Buscar Doações', 'amigopet'), 'donation');
        $apwp_donations_table->display();
        ?>
    </form>
</div>

<style>
    .donation-status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 12px;
        line-height: 1;
    }

    .status-pending {
        background-color: #f0f6fc;
        color: #1d2327;
    }

    .status-received {
        background-color: #c6e1c6;
        color: #5b841b;
    }

    .status-cancelled {
        background-color: #f1adad;
        color: #8b0000;
    }

    .column-type,
    .column-status {
        width: 15%;
    }

    .column-amount,
    .column-date {
        width: 12%;
    }

    .column-contact {
        width: 20%;
    }
</style>

<script>
    jQuery(document).ready(function ($) {
        // Confirmação de exclusão
        $('.delete-donation').on('click', function (e) {
            if (!confirm('<?php esc_html_e('Tem certeza que deseja excluir esta doação?', 'amigopet'); ?>')) {
                e.preventDefault();
            }
        });

        // Atualização em tempo real dos filtros
        $('#filter-by-type, #filter-by-status').on('change', function () {
            $('#donations-filter').submit();
        });
    });
</script>