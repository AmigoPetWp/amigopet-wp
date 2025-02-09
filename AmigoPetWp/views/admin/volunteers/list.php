<?php
/**
 * Template para listagem de voluntários no admin
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Voluntários', 'amigopet-wp'); ?></h1>
    <a href="<?php echo add_query_arg('action', 'add'); ?>" class="page-title-action">
        <?php _e('Adicionar Novo', 'amigopet-wp'); ?>
    </a>
    
    <div class="apwp-volunteers">
        <!-- Filtros -->
        <div class="apwp-filters">
            <select id="apwp-status-filter">
                <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                <option value="active"><?php _e('Ativos', 'amigopet-wp'); ?></option>
                <option value="inactive"><?php _e('Inativos', 'amigopet-wp'); ?></option>
                <option value="pending"><?php _e('Pendentes', 'amigopet-wp'); ?></option>
            </select>
            
            <select id="apwp-area-filter">
                <option value=""><?php _e('Todas as áreas', 'amigopet-wp'); ?></option>
                <?php foreach ($areas as $area): ?>
                    <option value="<?php echo esc_attr($area->id); ?>">
                        <?php echo esc_html($area->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" id="apwp-search" placeholder="<?php esc_attr_e('Buscar por nome...', 'amigopet-wp'); ?>">
        </div>
        
        <!-- Tabela -->
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'amigopet-wp'); ?></th>
                    <th><?php _e('Nome', 'amigopet-wp'); ?></th>
                    <th><?php _e('E-mail', 'amigopet-wp'); ?></th>
                    <th><?php _e('Telefone', 'amigopet-wp'); ?></th>
                    <th><?php _e('Área', 'amigopet-wp'); ?></th>
                    <th><?php _e('Disponibilidade', 'amigopet-wp'); ?></th>
                    <th><?php _e('Status', 'amigopet-wp'); ?></th>
                    <th><?php _e('Ações', 'amigopet-wp'); ?></th>
                </tr>
            </thead>
            <tbody id="apwp-volunteers-list">
                <?php if (!empty($volunteers)): ?>
                    <?php foreach ($volunteers as $volunteer): ?>
                        <tr>
                            <td><?php echo esc_html($volunteer->id); ?></td>
                            <td><?php echo esc_html($volunteer->name); ?></td>
                            <td><?php echo esc_html($volunteer->email); ?></td>
                            <td><?php echo esc_html($volunteer->phone); ?></td>
                            <td><?php echo esc_html($volunteer->area_name); ?></td>
                            <td><?php echo esc_html($volunteer->availability); ?></td>
                            <td>
                                <span class="apwp-status apwp-status-<?php echo esc_attr($volunteer->status); ?>">
                                    <?php echo esc_html($volunteer->status_label); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo add_query_arg(['action' => 'edit', 'id' => $volunteer->id]); ?>" class="button button-small">
                                    <?php _e('Editar', 'amigopet-wp'); ?>
                                </a>
                                <button type="button" class="button button-small button-link-delete apwp-delete-volunteer" data-id="<?php echo esc_attr($volunteer->id); ?>">
                                    <?php _e('Excluir', 'amigopet-wp'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8"><?php _e('Nenhum voluntário encontrado.', 'amigopet-wp'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Paginação -->
        <div id="apwp-volunteers-pagination" class="tablenav">
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
    // Carrega a lista de voluntários
    function loadVolunteers(page = 1) {
        var data = {
            action: 'apwp_load_volunteers',
            _ajax_nonce: apwp.nonce,
            page: page,
            status: $('#apwp-status-filter').val(),
            area: $('#apwp-area-filter').val(),
            search: $('#apwp-search').val()
        };
        
        $.post(apwp.ajax_url, data, function(response) {
            if (response.success) {
                $('#apwp-volunteers-list').html(response.data.html);
                $('#apwp-volunteers-pagination').html(response.data.pagination);
            } else {
                alert(response.data.message);
            }
        });
    }
    
    // Eventos
    $(document).on('click', '.pagination-links a', function(e) {
        e.preventDefault();
        loadVolunteers($(this).data('page'));
    });
    
    $('#apwp-status-filter, #apwp-area-filter').on('change', function() {
        loadVolunteers(1);
    });
    
    var searchTimer;
    $('#apwp-search').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadVolunteers(1);
        }, 500);
    });
    
    // Excluir voluntário
    $(document).on('click', '.apwp-delete-volunteer', function() {
        if (confirm(apwp.i18n.confirm_delete)) {
            var $button = $(this);
            var data = {
                action: 'apwp_delete_volunteer',
                _ajax_nonce: apwp.nonce,
                volunteer_id: $button.data('id')
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
