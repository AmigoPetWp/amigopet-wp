<?php
/**
 * Template para listagem de eventos no admin
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Eventos', 'amigopet-wp'); ?></h1>
    <a href="<?php echo add_query_arg('action', 'add'); ?>" class="page-title-action">
        <?php _e('Adicionar Novo', 'amigopet-wp'); ?>
    </a>
    
    <div class="apwp-events">
        <!-- Filtros -->
        <div class="apwp-filters">
            <select id="apwp-status-filter">
                <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                <option value="upcoming"><?php _e('Próximos', 'amigopet-wp'); ?></option>
                <option value="ongoing"><?php _e('Em andamento', 'amigopet-wp'); ?></option>
                <option value="past"><?php _e('Passados', 'amigopet-wp'); ?></option>
                <option value="canceled"><?php _e('Cancelados', 'amigopet-wp'); ?></option>
            </select>
            
            <input type="text" id="apwp-search" placeholder="<?php esc_attr_e('Buscar por título...', 'amigopet-wp'); ?>">
        </div>
        
        <!-- Tabela -->
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'amigopet-wp'); ?></th>
                    <th><?php _e('Título', 'amigopet-wp'); ?></th>
                    <th><?php _e('Data', 'amigopet-wp'); ?></th>
                    <th><?php _e('Horário', 'amigopet-wp'); ?></th>
                    <th><?php _e('Local', 'amigopet-wp'); ?></th>
                    <th><?php _e('Vagas', 'amigopet-wp'); ?></th>
                    <th><?php _e('Status', 'amigopet-wp'); ?></th>
                    <th><?php _e('Ações', 'amigopet-wp'); ?></th>
                </tr>
            </thead>
            <tbody id="apwp-events-list">
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo esc_html($event->id); ?></td>
                            <td><?php echo esc_html($event->title); ?></td>
                            <td><?php echo esc_html($event->formatted_date); ?></td>
                            <td><?php echo esc_html($event->formatted_time); ?></td>
                            <td><?php echo esc_html($event->location); ?></td>
                            <td>
                                <?php
                                printf(
                                    /* translators: %1$s: número de vagas preenchidas, %2$s: número total de vagas */
                                    __('%1$s/%2$s', 'amigopet-wp'),
                                    $event->registered_slots,
                                    $event->total_slots
                                );
                                ?>
                            </td>
                            <td>
                                <span class="apwp-status apwp-status-<?php echo esc_attr($event->status); ?>">
                                    <?php echo esc_html($event->status_label); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo add_query_arg(['action' => 'edit', 'id' => $event->id]); ?>" class="button button-small">
                                    <?php _e('Editar', 'amigopet-wp'); ?>
                                </a>
                                <button type="button" class="button button-small button-link-delete apwp-delete-event" data-id="<?php echo esc_attr($event->id); ?>">
                                    <?php _e('Excluir', 'amigopet-wp'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8"><?php _e('Nenhum evento encontrado.', 'amigopet-wp'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Paginação -->
        <div id="apwp-events-pagination" class="tablenav">
            <div class="tablenav-pages">
                <?php if ($total_pages > 1): ?>
                    <span class="displaying-num">
                        <?php
                        // translators: %s: número total de itens na lista
                        printf(
                            _n('%s item', '%s itens', $total_items, 'amigopet-wp'),
                            number_format_i18n($total_items)
                        );
                        ?>
                    </span>
                    
                    <span class="pagination-links">
                        <?php if ($current_page > 1): ?>
                            <a class="first-page button" href="#" data-page="1">
                                <span class="screen-reader-text"><?php _e('Primeira página', 'amigopet-wp'); ?></span>
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                            <a class="prev-page button" href="#" data-page="<?php echo $current_page - 1; ?>">
                                <span class="screen-reader-text"><?php _e('Página anterior', 'amigopet-wp'); ?></span>
                                <span aria-hidden="true">&lsaquo;</span>
                            </a>
                        <?php endif; ?>
                        
                        <span class="paging-input">
                            <label for="current-page-selector" class="screen-reader-text">
                                <?php _e('Página atual', 'amigopet-wp'); ?>
                            </label>
                            <input class="current-page" id="current-page-selector" type="text" name="paged"
                                value="<?php echo $current_page; ?>" size="1" aria-describedby="table-paging">
                            <span class="tablenav-paging-text">
                                <?php
                                // translators: %s: número total de páginas
                                printf(
                                    __('de %s', 'amigopet-wp'),
                                    '<span class="total-pages">' . number_format_i18n($total_pages) . '</span>'
                                );
                                ?>
                            </span>
                        </span>
                        
                        <?php if ($current_page < $total_pages): ?>
                            <a class="next-page button" href="#" data-page="<?php echo $current_page + 1; ?>">
                                <span class="screen-reader-text"><?php _e('Próxima página', 'amigopet-wp'); ?></span>
                                <span aria-hidden="true">&rsaquo;</span>
                            </a>
                            <a class="last-page button" href="#" data-page="<?php echo $total_pages; ?>">
                                <span class="screen-reader-text"><?php _e('Última página', 'amigopet-wp'); ?></span>
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Carrega a lista de eventos
    function loadEvents(page = 1) {
        var data = {
            action: 'apwp_load_events',
            _ajax_nonce: apwp.nonce,
            page: page,
            status: $('#apwp-status-filter').val(),
            search: $('#apwp-search').val()
        };
        
        $.post(apwp.ajax_url, data, function(response) {
            if (response.success) {
                $('#apwp-events-list').html(response.data.html);
                $('#apwp-events-pagination').html(response.data.pagination);
            } else {
                alert(response.data.message);
            }
        });
    }
    
    // Eventos
    $(document).on('click', '.pagination-links a', function(e) {
        e.preventDefault();
        loadEvents($(this).data('page'));
    });
    
    $('#apwp-status-filter').on('change', function() {
        loadEvents(1);
    });
    
    var searchTimer;
    $('#apwp-search').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadEvents(1);
        }, 500);
    });
    
    // Excluir evento
    $(document).on('click', '.apwp-delete-event', function() {
        if (confirm(apwp.i18n.confirm_delete)) {
            var $button = $(this);
            var data = {
                action: 'apwp_delete_event',
                _ajax_nonce: apwp.nonce,
                event_id: $button.data('id')
            };
            
            $.post(apwp.ajax_url, data, function(response) {
                if (response.success) {
                    $button.closest('tr').fadeOut(400, function() {
                        $(this).remove();
                    });
                } else {
                    alert(response.data.message);
                }
            });
        }
    });
});
</script>
