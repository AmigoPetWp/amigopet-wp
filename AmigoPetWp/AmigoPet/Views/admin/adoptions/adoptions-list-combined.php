<?php
if (!defined('ABSPATH')) {
    exit;
}

// Carrega a classe WP_List_Table se ainda não estiver disponível
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class APWP_Adoptions_List_Table extends WP_List_Table {
    private $wpdb;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        
        parent::__construct([
            'singular' => __('Adoção', 'amigopet-wp'),
            'plural'   => __('Adoções', 'amigopet-wp'),
            'ajax'     => false
        ]);
    }
    
    public function get_columns() {
        return [
            'cb'           => '<input type="checkbox" />',
            'pet'          => __('Pet', 'amigopet-wp'),
            'adopter'      => __('Adotante', 'amigopet-wp'),
            'contact'      => __('Contato', 'amigopet-wp'),
            'status'       => __('Status', 'amigopet-wp'),
            'payment'      => __('Pagamento', 'amigopet-wp'),
            'reviewer'     => __('Revisor', 'amigopet-wp'),
            'created_at'   => __('Data', 'amigopet-wp')
        ];
    }
    
    public function get_sortable_columns() {
        return [
            'pet'        => ['p.name', false],
            'adopter'    => ['a.name', false],
            'status'     => ['ad.status', false],
            'reviewer'   => ['v.name', false],
            'created_at' => ['ad.created_at', true]
        ];
    }
    
    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'pet':
                $actions = [
                    'edit' => sprintf(
                        '<a href="%s">%s</a>',
                        add_query_arg(['action' => 'edit', 'id' => $item->id], menu_page_url('amigopet-wp-adoptions', false)),
                        __('Editar', 'amigopet-wp')
                    )
                ];

                // Adiciona ação de cancelar se a adoção não estiver concluída ou cancelada
                if (!in_array($item->status, ['completed', 'cancelled'])) {
                    $actions['cancel'] = sprintf(
                        '<a href="%s" onclick="return confirm(\'%s\');">%s</a>',
                        wp_nonce_url(add_query_arg(['action' => 'cancel', 'id' => $item->id], menu_page_url('amigopet-wp-adoptions', false)), 'cancel_adoption_' . $item->id),
                        __('Tem certeza que deseja cancelar esta adoção?', 'amigopet-wp'),
                        __('Cancelar', 'amigopet-wp')
                    );
                }
                
                $pet_info = array(
                    $item->pet_name,
                    $item->species_name,
                    $item->breed_name,
                    $item->age ? sprintf(__('%d anos', 'amigopet-wp'), $item->age) : '',
                    $item->size ? ucfirst($item->size) : '',
                    $item->rga ? sprintf('RGA: %s', $item->rga) : '',
                    $item->microchip_number ? sprintf('Microchip: %s', $item->microchip_number) : ''
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
                    $item->adopter_name,
                    $item->adopter_email
                );
            
            case 'contact':
                return sprintf(
                    '%s<br>%s',
                    $item->adopter_phone,
                    $item->adopter_address
                );
            
            case 'status':
                $status_labels = [
                    'pending'          => __('Pendente', 'amigopet-wp'),
                    'approved'         => __('Aprovada', 'amigopet-wp'),
                    'rejected'         => __('Rejeitada', 'amigopet-wp'),
                    'awaiting_payment' => __('Aguardando Pagamento', 'amigopet-wp'),
                    'paid'             => __('Pago', 'amigopet-wp'),
                    'completed'        => __('Concluída', 'amigopet-wp'),
                    'cancelled'        => __('Cancelada', 'amigopet-wp')
                ];

                $status_classes = [
                    'pending'          => 'status-pending',
                    'approved'         => 'status-approved',
                    'rejected'         => 'status-rejected',
                    'awaiting_payment' => 'status-awaiting',
                    'paid'             => 'status-paid',
                    'completed'        => 'status-completed',
                    'cancelled'        => 'status-cancelled'
                ];

                return sprintf(
                    '<span class="adoption-status %s">%s</span>',
                    $status_classes[$item->status] ?? '',
                    $status_labels[$item->status] ?? $item->status
                );
            
            case 'payment':
                if (!$item->payment_id) {
                    return '-';
                }

                $payment_method_labels = [
                    'cash'           => __('Dinheiro', 'amigopet-wp'),
                    'pix'            => __('PIX', 'amigopet-wp'),
                    'bank_transfer'  => __('Transferência', 'amigopet-wp')
                ];

                $payment_status_labels = [
                    'pending'  => __('Pendente', 'amigopet-wp'),
                    'paid'     => __('Pago', 'amigopet-wp'),
                    'refunded' => __('Reembolsado', 'amigopet-wp')
                ];

                $payment_info = [
                    sprintf('R$ %.2f', $item->payment_amount),
                    $payment_method_labels[$item->payment_method] ?? $item->payment_method,
                    $payment_status_labels[$item->payment_status] ?? $item->payment_status
                ];

                $actions = [];
                if ($item->payment_status === 'paid') {
                    $actions['refund'] = sprintf(
                        '<a href="%s" onclick="return confirm(\'%s\');">%s</a>',
                        wp_nonce_url(add_query_arg(['action' => 'refund', 'id' => $item->id], menu_page_url('amigopet-wp-adoptions', false)), 'refund_payment_' . $item->id),
                        __('Tem certeza que deseja reembolsar este pagamento?', 'amigopet-wp'),
                        __('Reembolsar', 'amigopet-wp')
                    );
                }

                return sprintf(
                    '%s %s',
                    implode(' | ', $payment_info),
                    $this->row_actions($actions)
                );

            case 'reviewer':
                if (!$item->reviewer_id) {
                    return '-';
                }
                return sprintf(
                    '%s<br><small>%s</small>',
                    $item->reviewer_name,
                    $item->reviewer_role
                );
            
            case 'created_at':
                return date_i18n(
                    get_option('date_format') . ' ' . get_option('time_format'),
                    strtotime($item->created_at)
                );
            
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
        $offset = ($current_page - 1) * $per_page;
        
        // Prepara a query base
        $sql = "SELECT 
            ad.*, 
            p.*,
            s.name as species_name,
            b.name as breed_name,
            a.*,
            v.name as reviewer_name,
            v.role as reviewer_role,
            pay.id as payment_id,
            pay.amount as payment_amount,
            pay.payment_method,
            pay.status as payment_status,
            pay.paid_at
        FROM {$this->wpdb->prefix}amigopet_adoptions ad
        LEFT JOIN {$this->wpdb->prefix}amigopet_pets p ON ad.pet_id = p.id
        LEFT JOIN {$this->wpdb->prefix}amigopet_pet_species s ON p.species_id = s.id
        LEFT JOIN {$this->wpdb->prefix}amigopet_pet_breeds b ON p.breed_id = b.id
        LEFT JOIN {$this->wpdb->prefix}amigopet_adopters a ON ad.adopter_id = a.id
        LEFT JOIN {$this->wpdb->prefix}amigopet_volunteers v ON ad.reviewer_id = v.id
        LEFT JOIN {$this->wpdb->prefix}amigopet_adoption_payments pay ON ad.id = pay.adoption_id";
        
        // Adiciona busca se houver
        $where = [];
        if (!empty($_REQUEST['s'])) {
            $search = '%' . $this->wpdb->esc_like($_REQUEST['s']) . '%';
            $where[] = $this->wpdb->prepare(
                '(p.name LIKE %s OR a.name LIKE %s OR a.email LIKE %s)',
                $search, $search, $search
            );
        }
        
        // Adiciona filtro por status se houver
        if (!empty($_REQUEST['status'])) {
            $where[] = $this->wpdb->prepare('ad.status = %s', $_REQUEST['status']);
        }
        
        // Adiciona cláusula WHERE se necessário
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        
        // Adiciona ordenação
        $orderby = !empty($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'ad.created_at';
        $order = !empty($_REQUEST['order']) ? $_REQUEST['order'] : 'DESC';
        $sql .= " ORDER BY {$orderby} {$order}";
        
        // Adiciona limite e offset
        $sql .= $this->wpdb->prepare(' LIMIT %d OFFSET %d', $per_page, $offset);
        
        // Executa a query
        $this->items = $this->wpdb->get_results($sql);
        
        // Conta o total de itens
        $total_items = $this->wpdb->get_var("SELECT COUNT(id) FROM {$this->wpdb->prefix}amigopet_adoptions");
        
        // Configura a paginação
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ]);
    }
    
    public function extra_tablenav($which) {
        if ($which !== 'top') return;
        ?>
        <div class="alignleft actions">
            <select name="status" id="filter-by-status">
                <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                <option value="pending" <?php selected(!empty($_REQUEST['status']) ? $_REQUEST['status'] : '', 'pending'); ?>>
                    <?php _e('Pendente', 'amigopet-wp'); ?>
                </option>
                <option value="approved" <?php selected(!empty($_REQUEST['status']) ? $_REQUEST['status'] : '', 'approved'); ?>>
                    <?php _e('Aprovada', 'amigopet-wp'); ?>
                </option>
                <option value="rejected" <?php selected(!empty($_REQUEST['status']) ? $_REQUEST['status'] : '', 'rejected'); ?>>
                    <?php _e('Rejeitada', 'amigopet-wp'); ?>
                </option>
                <option value="awaiting_payment" <?php selected(!empty($_REQUEST['status']) ? $_REQUEST['status'] : '', 'awaiting_payment'); ?>>
                    <?php _e('Aguardando Pagamento', 'amigopet-wp'); ?>
                </option>
                <option value="paid" <?php selected(!empty($_REQUEST['status']) ? $_REQUEST['status'] : '', 'paid'); ?>>
                    <?php _e('Pago', 'amigopet-wp'); ?>
                </option>
                <option value="completed" <?php selected(!empty($_REQUEST['status']) ? $_REQUEST['status'] : '', 'completed'); ?>>
                    <?php _e('Concluída', 'amigopet-wp'); ?>
                </option>
                <option value="cancelled" <?php selected(!empty($_REQUEST['status']) ? $_REQUEST['status'] : '', 'cancelled'); ?>>
                    <?php _e('Cancelada', 'amigopet-wp'); ?>
                </option>
                <option value="rejected" <?php selected(!empty($_REQUEST['status']) ? $_REQUEST['status'] : '', 'rejected'); ?>>
                    <?php _e('Rejeitada', 'amigopet-wp'); ?>
                </option>
                <option value="awaiting_payment" <?php selected(!empty($_REQUEST['status']) ? $_REQUEST['status'] : '', 'awaiting_payment'); ?>>
                    <?php _e('Aguardando Pagamento', 'amigopet-wp'); ?>
                </option>
                <option value="paid" <?php selected(!empty($_REQUEST['status']) ? $_REQUEST['status'] : '', 'paid'); ?>>
                    <?php _e('Pago', 'amigopet-wp'); ?>
                </option>
                <option value="completed" <?php selected(!empty($_REQUEST['status']) ? $_REQUEST['status'] : '', 'completed'); ?>>
                    <?php _e('Concluída', 'amigopet-wp'); ?>
                </option>
                <option value="cancelled" <?php selected(!empty($_REQUEST['status']) ? $_REQUEST['status'] : '', 'cancelled'); ?>>
                    <?php _e('Cancelada', 'amigopet-wp'); ?>
                </option>

            </select>
            
            <?php submit_button(__('Filtrar', 'amigopet-wp'), '', 'filter_action', false); ?>
        </div>
        <?php
    }
}

// Cria uma instância da tabela e configura os itens
$adoptions_table = new APWP_Adoptions_List_Table();
$adoptions_table->items = $adoptions ?? [];
$adoptions_table->set_pagination_args([
    'total_items' => $total_items ?? 0,
    'per_page'    => $per_page ?? 20,
    'total_pages' => $total_pages ?? 1
]);
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Adoções', 'amigopet-wp'); ?></h1>
    <a href="<?php echo add_query_arg('action', 'add'); ?>" class="page-title-action">
        <?php _e('Adicionar Nova', 'amigopet-wp'); ?>
    </a>
    
    <?php
    // Mensagens de feedback
    if (isset($_GET['message'])) {
        $message = intval($_GET['message']);
        $messages = [
            1 => __('Adoção salva com sucesso.', 'amigopet-wp'),
            2 => __('Adoção excluída com sucesso.', 'amigopet-wp'),
            3 => __('Adoção aprovada com sucesso.', 'amigopet-wp'),
            4 => __('Adoção rejeitada com sucesso.', 'amigopet-wp')
        ];
        
        if (isset($messages[$message])) {
            printf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', $messages[$message]);
        }
    }
    ?>
    
    <form id="adoptions-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
        <?php
        $adoptions_table->search_box(__('Buscar Adoções', 'amigopet-wp'), 'adoption');
        $adoptions_table->display();
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
jQuery(document).ready(function($) {
    // Confirmação de exclusão
    $('.delete-adoption').on('click', function(e) {
        if (!confirm('<?php _e('Tem certeza que deseja excluir esta adoção?', 'amigopet-wp'); ?>')) {
            e.preventDefault();
        }
    });
    
    // Atualização em tempo real dos filtros
    $('#filter-by-status').on('change', function() {
        $('#adoptions-filter').submit();
    });
});
</script>
