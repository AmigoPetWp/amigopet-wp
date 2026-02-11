<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

// Carrega a classe WP_List_Table se ainda não estiver disponível
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class APWP_Terms_List_Table extends WP_List_Table
{
    public function __construct()
    {
        parent::__construct([
            'singular' => esc_html__('Termo', 'amigopet'),
            'plural' => esc_html__('Termos', 'amigopet'),
            'ajax' => true
        ]);
    }

    public function get_columns()
    {
        return [
            'cb' => '<input type="checkbox" />',
            'title' => esc_html__('Título', 'amigopet'),
            'type' => esc_html__('Tipo', 'amigopet'),
            'content' => esc_html__('Conteúdo', 'amigopet'),
            'status' => esc_html__('Status', 'amigopet'),
            'last_modified' => esc_html__('Última Atualização', 'amigopet')
        ];
    }

    public function get_sortable_columns()
    {
        return [
            'title' => ['title', true],
            'type' => ['term_type', false],
            'status' => ['term_status', false],
            'last_modified' => ['post_modified', false]
        ];
    }

    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'content':
                $apwp_content = wp_strip_all_tags($item->post_content);
                return strlen($apwp_content) > 100
                    ? substr($apwp_content, 0, 100) . '...'
                    : $apwp_content;

            case 'last_modified':
                return get_the_modified_date('d/m/Y H:i:s', $item->ID);

            default:
                return '';
        }
    }

    protected function column_title($item)
    {
        $actions = [
            'edit' => sprintf(
                '<a href="%s">%s</a>',
                add_query_arg(['action' => 'edit', 'id' => $item->ID]),
                esc_html__('Editar', 'amigopet')
            ),
            'delete' => sprintf(
                '<a href="%s" class="delete-term" data-id="%d">%s</a>',
                wp_nonce_url(admin_url('admin-post.php?action=apwp_delete_term&id=' . $item->ID), 'delete_term_' . $item->ID),
                $item->ID,
                esc_html__('Excluir', 'amigopet')
            )
        ];

        return sprintf(
            '<strong><a href="%1$s">%2$s</a></strong> %3$s',
            add_query_arg(['action' => 'edit', 'id' => $item->ID]),
            esc_html($item->post_title),
            $this->row_actions($actions)
        );
    }

    protected function column_type($item)
    {
        $type = get_post_meta($item->ID, 'term_type', true);
        $types = [
            'adoption' => esc_html__('Adoção', 'amigopet'),
            'volunteer' => esc_html__('Voluntariado', 'amigopet'),
            'donation' => esc_html__('Doação', 'amigopet'),
            'privacy' => esc_html__('Privacidade', 'amigopet'),
            'service' => esc_html__('Serviço', 'amigopet')
        ];

        return isset($types[$type]) ? $types[$type] : esc_html($type);
    }

    protected function column_status($item)
    {
        $status = get_post_meta($item->ID, 'term_status', true);
        $statuses = [
            'published' => ['label' => esc_html__('Publicado', 'amigopet'), 'class' => 'published'],
            'draft' => ['label' => esc_html__('Rascunho', 'amigopet'), 'class' => 'draft'],
            'archived' => ['label' => esc_html__('Arquivado', 'amigopet'), 'class' => 'archived']
        ];

        return isset($statuses[$status])
            ? sprintf(
                '<span class="term-status status-%s">%s</span>',
                esc_attr($statuses[$status]['class']),
                esc_html($statuses[$status]['label'])
            )
            : esc_html($status);
    }

    protected function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="term_ids[]" value="%s" />',
            esc_attr($item->ID)
        );
    }

    protected function get_bulk_actions()
    {
        return [
            'delete' => esc_html__('Excluir', 'amigopet'),
            'publish' => esc_html__('Publicar', 'amigopet'),
            'archive' => esc_html__('Arquivar', 'amigopet')
        ];
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
            'post_type' => 'apwp_term',
            'posts_per_page' => $per_page,
            'paged' => $current_page,
            'orderby' => !empty($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'title',
            'order' => !empty($_REQUEST['order']) ? $_REQUEST['order'] : 'ASC'
        ];

        // Adiciona filtros se existirem
        if (!empty($_REQUEST['term_type'])) {
            $args['meta_query'][] = [
                'key' => 'term_type',
                'value' => sanitize_text_field($_REQUEST['term_type'])
            ];
        }

        if (!empty($_REQUEST['term_status'])) {
            $args['meta_query'][] = [
                'key' => 'term_status',
                'value' => sanitize_text_field($_REQUEST['term_status'])
            ];
        }

        if (!empty($_REQUEST['s'])) {
            $args['s'] = sanitize_text_field($_REQUEST['s']);
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
            <select name="term_type" id="filter-by-type">
                <option value=""><?php esc_html_e('Todos os tipos', 'amigopet'); ?></option>
                <?php
                $types = [
                    'adoption' => esc_html__('Adoção', 'amigopet'),
                    'volunteer' => esc_html__('Voluntariado', 'amigopet'),
                    'donation' => esc_html__('Doação', 'amigopet'),
                    'privacy' => esc_html__('Privacidade', 'amigopet'),
                    'service' => esc_html__('Serviço', 'amigopet')
                ];

                foreach ($types as $value => $label) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($value),
                        selected(isset($_REQUEST['term_type']) ? $_REQUEST['term_type'] : '', $value, false),
                        esc_html($label)
                    );
                }
                ?>
            </select>

            <select name="term_status" id="filter-by-status">
                <option value=""><?php esc_html_e('Todos os status', 'amigopet'); ?></option>
                <?php
                $statuses = [
                    'published' => esc_html__('Publicado', 'amigopet'),
                    'draft' => esc_html__('Rascunho', 'amigopet'),
                    'archived' => esc_html__('Arquivado', 'amigopet')
                ];

                foreach ($statuses as $value => $label) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($value),
                        selected(isset($_REQUEST['term_status']) ? $_REQUEST['term_status'] : '', $value, false),
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
$terms_table = new APWP_Terms_List_Table();
$terms_table->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('Termos', 'amigopet'); ?></h1>
    <a href="<?php echo esc_url(add_query_arg('action', 'add')); ?>" class="page-title-action">
        <?php esc_html_e('Adicionar Novo', 'amigopet'); ?>
    </a>

    <?php
    // Mensagens de feedback
    if (isset($_GET['message'])) {
        $apwp_message = intval($_GET['message']);
        $apwp_messages = [
            1 => esc_html__('Termo salvo com sucesso.', 'amigopet'),
            2 => esc_html__('Termo excluído com sucesso.', 'amigopet'),
            3 => esc_html__('Termos atualizados com sucesso.', 'amigopet')
        ];

        if (isset($apwp_messages[$apwp_message])) {
            /* translators: %s */
            printf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html($apwp_messages[$apwp_message]));
        }
    }
    ?>

    <form id="terms-filter" method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page'] ?? ''); ?>" />
        <?php
        $terms_table->search_box(esc_html__('Buscar Termos', 'amigopet'), 'term');
        $terms_table->display();
        ?>
    </form>
</div>

<style>
    .term-status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 12px;
        line-height: 1;
    }

    .status-published {
        background-color: #c6e1c6;
        color: #5b841b;
    }

    .status-draft {
        background-color: #f0f6fc;
        color: #1d2327;
    }

    .status-archived {
        background-color: #f1adad;
        color: #8b0000;
    }

    .column-title {
        width: 25%;
    }

    .column-type,
    .column-status {
        width: 15%;
    }

    .column-content {
        width: 25%;
    }

    .column-last_modified {
        width: 20%;
    }
</style>

<script>
    jQuery(document).ready(function ($) {
        // Confirmação de exclusão
        $('.delete-term').on('click', function (e) {
            if (!confirm('<?php echo esc_js(esc_html__('Tem certeza que deseja excluir este termo?', 'amigopet')); ?>')) {
                e.preventDefault();
            }
        });

        // Atualização em tempo real dos filtros
        $('#filter-by-type, #filter-by-status').on('change', function () {
            $('#terms-filter').submit();
        });

        // Confirmação de ações em lote
        $('form#terms-filter').on('submit', function (e) {
            var action = $('select[name="action"]').val() || $('select[name="action2"]').val();
            if (action === 'delete') {
                if (!confirm('<?php echo esc_js(esc_html__('Tem certeza que deseja excluir os termos selecionados?', 'amigopet')); ?>')) {
                    e.preventDefault();
                }
            }
        });
    });
</script>