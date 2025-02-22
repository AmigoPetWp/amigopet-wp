<?php
if (!defined('ABSPATH')) {
    exit;
}

// Carrega a classe WP_List_Table se ainda não estiver disponível
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class APWP_Donations_List_Table extends WP_List_Table {
    public function __construct() {
        parent::__construct([
            'singular' => __('Doação', 'amigopet-wp'),
            'plural'   => __('Doações', 'amigopet-wp'),
            'ajax'     => true
        ]);
    }
    
    public function get_columns() {
        return [
            'cb'          => '<input type="checkbox" />',
            'donor'       => __('Doador', 'amigopet-wp'),
            'type'        => __('Tipo', 'amigopet-wp'),
            'amount'      => __('Valor/Itens', 'amigopet-wp'),
            'date'        => __('Data', 'amigopet-wp'),
            'status'      => __('Status', 'amigopet-wp'),
            'contact'     => __('Contato', 'amigopet-wp')
        ];
    }
    
    public function get_sortable_columns() {
        return [
            'donor'  => ['donor_name', false],
            'type'   => ['donation_type', false],
            'amount' => ['donation_amount', false],
            'date'   => ['donation_date', true]
        ];
    }
    
    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'donor':
                $actions = [
                    'edit' => sprintf(
                        '<a href="%s">%s</a>',
                        add_query_arg(['action' => 'edit', 'id' => $item->ID]),
                        __('Editar', 'amigopet-wp')
                    ),
                    'delete' => sprintf(
                        '<a href="%s" class="delete-donation" data-id="%d">%s</a>',
                        wp_nonce_url(admin_url('admin-post.php?action=apwp_delete_donation&id=' . $item->ID), 'delete_donation_' . $item->ID),
                        $item->ID,
                        __('Excluir', 'amigopet-wp')
                    )
                ];
                
                return sprintf(
                    '<strong>%s</strong> %s',
                    get_post_meta($item->ID, 'donor_name', true),
                    $this->row_actions($actions)
                );
                
            case 'type':
                $types = [
                    'money'     => __('Dinheiro', 'amigopet-wp'),
                    'food'      => __('Ração', 'amigopet-wp'),
                    'medicine'  => __('Medicamentos', 'amigopet-wp'),
                    'supplies'  => __('Suprimentos', 'amigopet-wp'),
                    'other'     => __('Outro', 'amigopet-wp')
                ];
                $type = get_post_meta($item->ID, 'donation_type', true);
                return isset($types[$type]) ? $types[$type] : $type;
                
            case 'amount':
                $type = get_post_meta($item->ID, 'donation_type', true);
                $amount = get_post_meta($item->ID, 'donation_amount', true);
                
                if ($type === 'money') {
                    return 'R$ ' . number_format($amount, 2, ',', '.');
                } else {
                    return esc_html($amount);
                }
                
            case 'date':
                $date = get_post_meta($item->ID, 'donation_date', true);
                return $date ? date_i18n(get_option('date_format'), strtotime($date)) : '-';
                
            case 'status':
                $status = get_post_meta($item->ID, 'donation_status', true);
                $statuses = [
                    'pending'   => ['label' => __('Pendente', 'amigopet-wp'), 'class' => 'pending'],
                    'received'  => ['label' => __('Recebida', 'amigopet-wp'), 'class' => 'received'],
                    'cancelled' => ['label' => __('Cancelada', 'amigopet-wp'), 'class' => 'cancelled']
                ];
                
                return isset($statuses[$status]) 
                    ? sprintf('<span class="donation-status status-%s">%s</span>', 
                        $statuses[$status]['class'], 
                        $statuses[$status]['label']
                    )
                    : $status;
                
            case 'contact':
                $email = get_post_meta($item->ID, 'donor_email', true);
                $phone = get_post_meta($item->ID, 'donor_phone', true);
                
                $contact = [];
                if ($email) $contact[] = sprintf('<a href="mailto:%1$s">%1$s</a>', esc_attr($email));
                if ($phone) $contact[] = esc_html($phone);
                
                return implode('<br>', $contact);
                
            default:
                return print_r($item, true);
        }
    }
    
    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = [$columns, $hidden, $sortable];
        
        $per_page = 20;
        $current_page = $this->get_pagenum();
        
        // Prepara os argumentos da query
        $args = [
            'post_type'      => 'apwp_donation',
            'posts_per_page' => $per_page,
            'paged'          => $current_page,
            'orderby'        => !empty($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'meta_value',
            'order'          => !empty($_REQUEST['order']) ? $_REQUEST['order'] : 'DESC',
            'meta_key'       => 'donation_date'
        ];
        
        // Adiciona filtros se existirem
        if (!empty($_REQUEST['donation_type'])) {
            $args['meta_query'][] = [
                'key'   => 'donation_type',
                'value' => sanitize_text_field($_REQUEST['donation_type'])
            ];
        }
        
        if (!empty($_REQUEST['donation_status'])) {
            $args['meta_query'][] = [
                'key'   => 'donation_status',
                'value' => sanitize_text_field($_REQUEST['donation_status'])
            ];
        }
        
        if (!empty($_REQUEST['s'])) {
            $args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key'     => 'donor_name',
                    'value'   => sanitize_text_field($_REQUEST['s']),
                    'compare' => 'LIKE'
                ],
                [
                    'key'     => 'donor_email',
                    'value'   => sanitize_text_field($_REQUEST['s']),
                    'compare' => 'LIKE'
                ]
            ];
        }
        
        // Executa a query
        $query = new WP_Query($args);
        
        $this->items = $query->posts;
        
        // Configura a paginação
        $this->set_pagination_args([
            'total_items' => $query->found_posts,
            'per_page'    => $per_page,
            'total_pages' => ceil($query->found_posts / $per_page)
        ]);
    }
    
    public function extra_tablenav($which) {
        if ($which !== 'top') return;
        ?>
        <div class="alignleft actions">
            <select name="donation_type" id="filter-by-type">
                <option value=""><?php _e('Todos os tipos', 'amigopet-wp'); ?></option>
                <option value="money" <?php selected(isset($_REQUEST['donation_type']) ? $_REQUEST['donation_type'] : '', 'money'); ?>>
                    <?php _e('Dinheiro', 'amigopet-wp'); ?>
                </option>
                <option value="food" <?php selected(isset($_REQUEST['donation_type']) ? $_REQUEST['donation_type'] : '', 'food'); ?>>
                    <?php _e('Ração', 'amigopet-wp'); ?>
                </option>
                <option value="medicine" <?php selected(isset($_REQUEST['donation_type']) ? $_REQUEST['donation_type'] : '', 'medicine'); ?>>
                    <?php _e('Medicamentos', 'amigopet-wp'); ?>
                </option>
                <option value="supplies" <?php selected(isset($_REQUEST['donation_type']) ? $_REQUEST['donation_type'] : '', 'supplies'); ?>>
                    <?php _e('Suprimentos', 'amigopet-wp'); ?>
                </option>
                <option value="other" <?php selected(isset($_REQUEST['donation_type']) ? $_REQUEST['donation_type'] : '', 'other'); ?>>
                    <?php _e('Outro', 'amigopet-wp'); ?>
                </option>
            </select>
            
            <select name="donation_status" id="filter-by-status">
                <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                <option value="pending" <?php selected(isset($_REQUEST['donation_status']) ? $_REQUEST['donation_status'] : '', 'pending'); ?>>
                    <?php _e('Pendente', 'amigopet-wp'); ?>
                </option>
                <option value="received" <?php selected(isset($_REQUEST['donation_status']) ? $_REQUEST['donation_status'] : '', 'received'); ?>>
                    <?php _e('Recebida', 'amigopet-wp'); ?>
                </option>
                <option value="cancelled" <?php selected(isset($_REQUEST['donation_status']) ? $_REQUEST['donation_status'] : '', 'cancelled'); ?>>
                    <?php _e('Cancelada', 'amigopet-wp'); ?>
                </option>
            </select>
            
            <?php submit_button(__('Filtrar', 'amigopet-wp'), '', 'filter_action', false); ?>
        </div>
        <?php
    }
}

// Cria uma instância da tabela
$donations_table = new APWP_Donations_List_Table();
$donations_table->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Doações', 'amigopet-wp'); ?></h1>
    <a href="<?php echo add_query_arg('action', 'add'); ?>" class="page-title-action">
        <?php _e('Adicionar Nova', 'amigopet-wp'); ?>
    </a>
    
    <?php
    // Mensagens de feedback
    if (isset($_GET['message'])) {
        $message = intval($_GET['message']);
        $messages = [
            1 => __('Doação salva com sucesso.', 'amigopet-wp'),
            2 => __('Doação excluída com sucesso.', 'amigopet-wp')
        ];
        
        if (isset($messages[$message])) {
            printf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', $messages[$message]);
        }
    }
    ?>
    
    <form id="donations-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
        <?php
        $donations_table->search_box(__('Buscar Doações', 'amigopet-wp'), 'donation');
        $donations_table->display();
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
jQuery(document).ready(function($) {
    // Confirmação de exclusão
    $('.delete-donation').on('click', function(e) {
        if (!confirm('<?php _e('Tem certeza que deseja excluir esta doação?', 'amigopet-wp'); ?>')) {
            e.preventDefault();
        }
    });
    
    // Atualização em tempo real dos filtros
    $('#filter-by-type, #filter-by-status').on('change', function() {
        $('#donations-filter').submit();
    });
});
</script>
