<?php
if (!defined('ABSPATH')) {
    exit;
}

// Carrega a classe WP_List_Table se ainda não estiver disponível
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class APWP_Events_List_Table extends WP_List_Table {
    public function __construct() {
        parent::__construct([
            'singular' => __('Evento', 'amigopet-wp'),
            'plural'   => __('Eventos', 'amigopet-wp'),
            'ajax'     => true
        ]);
    }
    
    public function get_columns() {
        return [
            'cb'          => '<input type="checkbox" />',
            'image'       => __('Imagem', 'amigopet-wp'),
            'title'       => __('Título', 'amigopet-wp'),
            'type'        => __('Tipo', 'amigopet-wp'),
            'date'        => __('Data', 'amigopet-wp'),
            'location'    => __('Local', 'amigopet-wp'),
            'organizer'   => __('Organizador', 'amigopet-wp'),
            'status'      => __('Status', 'amigopet-wp')
        ];
    }
    
    public function get_sortable_columns() {
        return [
            'title'     => ['title', false],
            'type'      => ['type', false],
            'date'      => ['date', true],
            'organizer' => ['organizer', false]
        ];
    }
    
    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'image':
                return has_post_thumbnail($item->ID) 
                    ? get_the_post_thumbnail($item->ID, [50, 50]) 
                    : '<div class="event-placeholder-image"></div>';
                
            case 'title':
                $actions = [
                    'edit' => sprintf(
                        '<a href="%s">%s</a>',
                        add_query_arg(['action' => 'edit', 'id' => $item->ID]),
                        __('Editar', 'amigopet-wp')
                    ),
                    'delete' => sprintf(
                        '<a href="%s" class="delete-event" data-id="%d">%s</a>',
                        wp_nonce_url(admin_url('admin-post.php?action=apwp_delete_event&id=' . $item->ID), 'delete_event_' . $item->ID),
                        $item->ID,
                        __('Excluir', 'amigopet-wp')
                    )
                ];
                
                return sprintf(
                    '<strong><a href="%s">%s</a></strong> %s',
                    get_edit_post_link($item->ID),
                    get_post_meta($item->ID, 'event_title', true),
                    $this->row_actions($actions)
                );
                
            case 'type':
                $types = [
                    'adoption_fair' => __('Feira de Adoção', 'amigopet-wp'),
                    'fundraising'   => __('Arrecadação de Fundos', 'amigopet-wp'),
                    'vaccination'   => __('Campanha de Vacinação', 'amigopet-wp'),
                    'other'         => __('Outro', 'amigopet-wp')
                ];
                $type = get_post_meta($item->ID, 'event_type', true);
                return isset($types[$type]) ? $types[$type] : $type;
                
            case 'date':
                $date = get_post_meta($item->ID, 'event_date', true);
                $time = get_post_meta($item->ID, 'event_time', true);
                return sprintf(
                    '%s<br><small>%s</small>',
                    $date ? date_i18n(get_option('date_format'), strtotime($date)) : '-',
                    $time ?: ''
                );
                
            case 'location':
                return get_post_meta($item->ID, 'event_location', true);
                
            case 'organizer':
                return get_post_meta($item->ID, 'event_organizer', true);
                
            case 'status':
                $event_date = get_post_meta($item->ID, 'event_date', true);
                $today = date('Y-m-d');
                
                if ($event_date < $today) {
                    $status = 'past';
                    $status_text = __('Realizado', 'amigopet-wp');
                } else {
                    $status = 'upcoming';
                    $status_text = __('Programado', 'amigopet-wp');
                }
                
                return sprintf('<span class="event-status status-%s">%s</span>', $status, $status_text);
                
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
            'post_type'      => 'apwp_event',
            'posts_per_page' => $per_page,
            'paged'          => $current_page,
            'orderby'        => !empty($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'meta_value',
            'order'          => !empty($_REQUEST['order']) ? $_REQUEST['order'] : 'ASC',
            'meta_key'       => 'event_date',
            'meta_type'      => 'DATE'
        ];
        
        // Adiciona filtros se existirem
        if (!empty($_REQUEST['event_type'])) {
            $args['meta_query'][] = [
                'key'   => 'event_type',
                'value' => sanitize_text_field($_REQUEST['event_type'])
            ];
        }
        
        if (!empty($_REQUEST['event_status'])) {
            $today = date('Y-m-d');
            $args['meta_query'][] = [
                'key'     => 'event_date',
                'value'   => $today,
                'compare' => $_REQUEST['event_status'] === 'upcoming' ? '>=' : '<',
                'type'    => 'DATE'
            ];
        }
        
        if (!empty($_REQUEST['s'])) {
            $args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key'     => 'event_title',
                    'value'   => sanitize_text_field($_REQUEST['s']),
                    'compare' => 'LIKE'
                ],
                [
                    'key'     => 'event_location',
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
            <select name="event_type" id="filter-by-type">
                <option value=""><?php _e('Todos os tipos', 'amigopet-wp'); ?></option>
                <option value="adoption_fair" <?php selected(isset($_REQUEST['event_type']) ? $_REQUEST['event_type'] : '', 'adoption_fair'); ?>>
                    <?php _e('Feira de Adoção', 'amigopet-wp'); ?>
                </option>
                <option value="fundraising" <?php selected(isset($_REQUEST['event_type']) ? $_REQUEST['event_type'] : '', 'fundraising'); ?>>
                    <?php _e('Arrecadação de Fundos', 'amigopet-wp'); ?>
                </option>
                <option value="vaccination" <?php selected(isset($_REQUEST['event_type']) ? $_REQUEST['event_type'] : '', 'vaccination'); ?>>
                    <?php _e('Campanha de Vacinação', 'amigopet-wp'); ?>
                </option>
                <option value="other" <?php selected(isset($_REQUEST['event_type']) ? $_REQUEST['event_type'] : '', 'other'); ?>>
                    <?php _e('Outro', 'amigopet-wp'); ?>
                </option>
            </select>
            
            <select name="event_status" id="filter-by-status">
                <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                <option value="upcoming" <?php selected(isset($_REQUEST['event_status']) ? $_REQUEST['event_status'] : '', 'upcoming'); ?>>
                    <?php _e('Programados', 'amigopet-wp'); ?>
                </option>
                <option value="past" <?php selected(isset($_REQUEST['event_status']) ? $_REQUEST['event_status'] : '', 'past'); ?>>
                    <?php _e('Realizados', 'amigopet-wp'); ?>
                </option>
            </select>
            
            <?php submit_button(__('Filtrar', 'amigopet-wp'), '', 'filter_action', false); ?>
        </div>
        <?php
    }
}

// Cria uma instância da tabela
$events_table = new APWP_Events_List_Table();
$events_table->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Eventos', 'amigopet-wp'); ?></h1>
    <a href="<?php echo add_query_arg('action', 'add'); ?>" class="page-title-action">
        <?php _e('Adicionar Novo', 'amigopet-wp'); ?>
    </a>
    
    <?php
    // Mensagens de feedback
    if (isset($_GET['message'])) {
        $message = intval($_GET['message']);
        $messages = [
            1 => __('Evento salvo com sucesso.', 'amigopet-wp'),
            2 => __('Evento excluído com sucesso.', 'amigopet-wp')
        ];
        
        if (isset($messages[$message])) {
            printf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', $messages[$message]);
        }
    }
    ?>
    
    <form id="events-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
        <?php
        $events_table->search_box(__('Buscar Eventos', 'amigopet-wp'), 'event');
        $events_table->display();
        ?>
    </form>
</div>

<style>
.event-placeholder-image {
    width: 50px;
    height: 50px;
    background: #f0f0f1;
    border: 1px solid #ddd;
}

.event-status {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    line-height: 1;
}

.status-upcoming {
    background-color: #c6e1c6;
    color: #5b841b;
}

.status-past {
    background-color: #f0f0f1;
    color: #646970;
}

.column-image {
    width: 60px;
}

.column-type,
.column-status {
    width: 15%;
}

.column-date {
    width: 12%;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Confirmação de exclusão
    $('.delete-event').on('click', function(e) {
        if (!confirm('<?php _e('Tem certeza que deseja excluir este evento?', 'amigopet-wp'); ?>')) {
            e.preventDefault();
        }
    });
    
    // Atualização em tempo real dos filtros
    $('#filter-by-type, #filter-by-status').on('change', function() {
        $('#events-filter').submit();
    });
});
</script>
