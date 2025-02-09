<?php
/**
 * Template para listagem de adoções no admin
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Adoções', 'amigopet-wp'); ?></h1>
    
    <div class="apwp-adoptions">
        <!-- Filtros -->
        <div class="apwp-filters">
            <select id="apwp-status-filter">
                <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                <option value="pending"><?php _e('Pendente', 'amigopet-wp'); ?></option>
                <option value="approved"><?php _e('Aprovada', 'amigopet-wp'); ?></option>
                <option value="rejected"><?php _e('Rejeitada', 'amigopet-wp'); ?></option>
            </select>
            
            <input type="text" id="apwp-search" placeholder="<?php esc_attr_e('Buscar por nome ou email...', 'amigopet-wp'); ?>">
        </div>
        
        <!-- Tabela -->
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'amigopet-wp'); ?></th>
                    <th><?php _e('Pet', 'amigopet-wp'); ?></th>
                    <th><?php _e('Nome', 'amigopet-wp'); ?></th>
                    <th><?php _e('E-mail', 'amigopet-wp'); ?></th>
                    <th><?php _e('Data', 'amigopet-wp'); ?></th>
                    <th><?php _e('Status', 'amigopet-wp'); ?></th>
                    <th><?php _e('Ações', 'amigopet-wp'); ?></th>
                </tr>
            </thead>
            <tbody id="apwp-adoptions-list">
                <?php include __DIR__ . '/list-partial.php'; ?>
            </tbody>
        </table>
        
        <!-- Paginação -->
        <div id="apwp-adoptions-pagination" class="tablenav">
            <div class="tablenav-pages">
                <?php if ($total_pages > 1): ?>
                    <span class="displaying-num">
                        <?php
                        printf(
                            /* translators: %s: número total de itens na lista */
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
                                printf(
                                    /* translators: %s: número total de páginas */
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
    // Carrega a lista de adoções
    function loadAdoptions(page = 1) {
        const status = $('#apwp-status-filter').val();
        const search = $('#apwp-search').val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'apwp_load_adoptions',
                _ajax_nonce: apwp.nonce,
                page: page,
                status: status,
                search: search
            },
            beforeSend: function() {
                $('#apwp-adoptions-list').addClass('loading');
            },
            success: function(response) {
                if (response.success) {
                    $('#apwp-adoptions-list').html(response.data.html);
                    updatePagination(response.data.current_page, response.data.total_pages, response.data.total_items);
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert(apwp.i18n.error);
            },
            complete: function() {
                $('#apwp-adoptions-list').removeClass('loading');
            }
        });
    }
    
    // Atualiza a paginação
    function updatePagination(currentPage, totalPages, totalItems) {
        const pagination = $('#apwp-adoptions-pagination');
        
        // Atualiza o número de itens
        pagination.find('.displaying-num').text(
            totalItems === 1 ? 
            apwp.i18n.one_item : 
            apwp.i18n.x_items.replace('%s', totalItems)
        );
        
        // Atualiza os links de paginação
        pagination.find('.current-page').val(currentPage);
        pagination.find('.total-pages').text(totalPages);
        
        // Habilita/desabilita botões
        pagination.find('.first-page, .prev-page').toggleClass('disabled', currentPage <= 1);
        pagination.find('.last-page, .next-page').toggleClass('disabled', currentPage >= totalPages);
    }
    
    // Event handlers
    $('#apwp-status-filter').on('change', function() {
        loadAdoptions(1);
    });
    
    $('#apwp-search').on('input', $.debounce(500, function() {
        loadAdoptions(1);
    }));
    
    $('#apwp-adoptions-pagination').on('click', 'a:not(.disabled)', function(e) {
        e.preventDefault();
        loadAdoptions($(this).data('page'));
    });
    
    // Ações de aprovação/rejeição
    $('#apwp-adoptions-list').on('click', '.action-button', function() {
        const button = $(this);
        const action = button.data('action');
        const id = button.data('id');
        const originalText = button.data('original-text');
        
        if (confirm(apwp.i18n[action + '_confirm'])) {
            button.prop('disabled', true).text(apwp.i18n.processing);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'apwp_' + action + '_adoption',
                    _ajax_nonce: apwp.nonce,
                    adoption_id: id
                },
                success: function(response) {
                    if (response.success) {
                        loadAdoptions($('#current-page-selector').val());
                    } else {
                        alert(response.data.message);
                        button.prop('disabled', false).text(originalText);
                    }
                },
                error: function() {
                    alert(apwp.i18n.error);
                    button.prop('disabled', false).text(originalText);
                }
            });
        }
    });
});
</script>
