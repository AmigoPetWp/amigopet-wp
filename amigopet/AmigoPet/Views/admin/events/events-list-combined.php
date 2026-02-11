<?php declare(strict_types=1);
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
            'singular' => esc_html__('Evento', 'amigopet'),
            'plural'   => esc_html__('Eventos', 'amigopet'),
            'ajax'     => true
        ]);
    }
    
    public function get_columns() {
        return [
            'cb'          => '<input type="checkbox" />',
            'image'       => esc_html__('Imagem', 'amigopet'),
            'title'       => esc_html__('Título', 'amigopet'),
            'type'        => esc_html__('Tipo', 'amigopet'),
            'date'        => esc_html__('Data', 'amigopet'),
            'location'    => esc_html__('Local', 'amigopet'),
            'organizer'   => esc_html__('Organizador', 'amigopet'),
            'status'      => esc_html__('Status', 'amigopet')
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
                        esc_html__('Editar', 'amigopet')
                    ),
                    'delete' => sprintf(
                        '<a href="%s" class="delete-event" data-id="%d">%s</a>',
                        wp_nonce_url(admin_url('admin-post.php?action=apwp_delete_event&id=' . $item->ID), 'delete_event_' . $item->ID),
                        $item->ID,
                        esc_html__('Excluir', 'amigopet')
                    )
                ];
                
                return sprintf(
                    '<strong><a href="%s">%s</a></strong> %s',
                    get_edit_post_link($item->ID),
                    esc_html(get_post_meta($item->ID, 'event_title', true)),
                    $this->row_actions($actions)
                );
                
            case 'type':
                $types = [
                    'adoption_fair' => esc_html__('Feira de Adoção', 'amigopet'),
                    'fundraising'   => esc_html__('Arrecadação de Fundos', 'amigopet'),
                    'vaccination'   => esc_html__('Campanha de Vacinação', 'amigopet'),
                    'other'         => esc_html__('Outro', 'amigopet')
                ];
                $type = get_post_meta($item->ID, 'event_type', true);
                return isset($types[$type]) ? $types[$type] : esc_html($type);
                
            case 'date':
                $date = get_post_meta($item->ID, 'event_date', true);
                $time = get_post_meta($item->ID, 'event_time', true);
                return sprintf(
                    '%s<br><small>%s</small>',
                    $date ? date_i18n(get_option('date_format'), strtotime($date)) : '-',
                    esc_html($time ?: '')
                );
                
            case 'location':
                return esc_html(get_post_meta($item->ID, 'event_location', true));
                
            case 'organizer':
                return esc_html(get_post_meta($item->ID, 'event_organizer', true));
                
            case 'status':
                $event_date = get_post_meta($item->ID, 'event_date', true);
                $today = gmdate('Y-m-d');
                
                if ($event_date && $event_date < $today) {
                    $status = 'past';
                    $status_text = esc_html__('Realizado', 'amigopet');
                } else {
                    $status = 'upcoming';
                    $status_text = esc_html__('Programado', 'amigopet');
                }
                
                return sprintf('<span class="event-status status-%s">%s</span>', esc_attr($status), esc_html($status_text));
                
            default:
                return '';
        }
    }

    protected function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="event_ids[]" value="%s" />',
            esc_attr($item->ID)
        );
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
            $today = gmdate('Y-m-d');
            $status_filter = sanitize_text_field($_REQUEST['event_status']);
            $args['meta_query'][] = [
                'key'     => 'event_date',
                'value'   => $today,
                'compare' => $status_filter === 'upcoming' ? '>=' : '<',
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
            'total_items' => (int) $query->found_posts,
            'per_page'    => $per_page,
            'total_pages' => ceil((int) $query->found_posts / $per_page)
        ]);
    }
    
    public function extra_tablenav($which) {
        if ($which !== 'top') return;
        ?>
        <div class="alignleft actions">
            <select name="event_type" id="filter-by-type">
                <option value=""><?php esc_html_e('Todos os tipos', 'amigopet'); ?></option>
                <?php
                $types = [
                    'adoption_fair' => esc_html__('Feira de Adoção', 'amigopet'),
                    'fundraising'   => esc_html__('Arrecadação de Fundos', 'amigopet'),
                    'vaccination'   => esc_html__('Campanha de Vacinação', 'amigopet'),
                    'other'         => esc_html__('Outro', 'amigopet')
                ];
                foreach ($types as $value => $label) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($value),
                        selected(isset($_REQUEST['event_type']) ? $_REQUEST['event_type'] : '', $value, false),
                        esc_html($label)
                    );
                }
                ?>
            </select>
            
            <select name="event_status" id="filter-by-status">
                <option value=""><?php esc_html_e('Todos os status', 'amigopet'); ?></option>
                <option value="upcoming" <?php selected(isset($_REQUEST['event_status']) ? $_REQUEST['event_status'] : '', 'upcoming'); ?>>
                    <?php esc_html_e('Programados', 'amigopet'); ?>
                </option>
                <option value="past" <?php selected(isset($_REQUEST['event_status']) ? $_REQUEST['event_status'] : '', 'past'); ?>>
                    <?php esc_html_e('Realizados', 'amigopet'); ?>
                </option>
            </select>
            <?php submit_button(esc_html__('Filtrar', 'amigopet'), '', 'filter_action', false); ?>
        </div>
        <?php
    }
}

// Cria uma instância da tabela
$apwp_events_table = new APWP_Events_List_Table();
$apwp_events_table->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('Eventos', 'amigopet'); ?></h1>
    <a href="<?php echo esc_url(add_query_arg('action', 'add')); ?>" class="page-title-action">
        <?php esc_html_e('Adicionar Novo', 'amigopet'); ?>
    </a>
    
    <?php
    // Mensagens de feedback
    if (isset($_GET['message'])) {
        $apwp_message = intval($_GET['message']);
        $apwp_messages = [
            1 => esc_html__('Evento salvo com sucesso.', 'amigopet'),
            2 => esc_html__('Evento excluído com sucesso.', 'amigopet')
        ];
        
        if (isset($apwp_messages[$apwp_message])) {
            /* translators: %s */
            printf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html($apwp_messages[$apwp_message]));
        }
    }
    ?>
    
    <form id="events-filter" method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page'] ?? ''); ?>" />
        <?php
        $apwp_events_table->search_box(esc_html__('Buscar Eventos', 'amigopet'), 'event');
        $apwp_events_table->display();
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
        if (!confirm('<?php echo esc_js(esc_html__('Tem certeza que deseja excluir este evento?', 'amigopet')); ?>')) {
            e.preventDefault();
        }
    });
    
    // Atualização em tempo real dos filtros
    $('#filter-by-type, #filter-by-status').on('change', function() {
        $('#events-filter').submit();
    });
});
</script>