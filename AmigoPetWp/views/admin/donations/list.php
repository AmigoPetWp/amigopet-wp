<?php
/**
 * Template para listagem de doações no admin
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Doações', 'amigopet-wp'); ?></h1>
    <a href="<?php echo add_query_arg('action', 'add'); ?>" class="page-title-action">
        <?php _e('Adicionar Nova', 'amigopet-wp'); ?>
    </a>
    
    <div class="apwp-donations">
        <!-- Filtros -->
        <div class="apwp-filters">
            <select id="apwp-status-filter">
                <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                <option value="pending"><?php _e('Pendente', 'amigopet-wp'); ?></option>
                <option value="confirmed"><?php _e('Confirmada', 'amigopet-wp'); ?></option>
                <option value="canceled"><?php _e('Cancelada', 'amigopet-wp'); ?></option>
            </select>
            
            <select id="apwp-type-filter">
                <option value=""><?php _e('Todos os tipos', 'amigopet-wp'); ?></option>
                <option value="money"><?php _e('Dinheiro', 'amigopet-wp'); ?></option>
                <option value="food"><?php _e('Ração', 'amigopet-wp'); ?></option>
                <option value="medicine"><?php _e('Medicamentos', 'amigopet-wp'); ?></option>
                <option value="supplies"><?php _e('Suprimentos', 'amigopet-wp'); ?></option>
            </select>
            
            <input type="text" id="apwp-search" placeholder="<?php esc_attr_e('Buscar por doador...', 'amigopet-wp'); ?>">
        </div>
        
        <!-- Tabela -->
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'amigopet-wp'); ?></th>
                    <th><?php _e('Data', 'amigopet-wp'); ?></th>
                    <th><?php _e('Doador', 'amigopet-wp'); ?></th>
                    <th><?php _e('Tipo', 'amigopet-wp'); ?></th>
                    <th><?php _e('Valor/Quantidade', 'amigopet-wp'); ?></th>
                    <th><?php _e('Status', 'amigopet-wp'); ?></th>
                    <th><?php _e('Ações', 'amigopet-wp'); ?></th>
                </tr>
            </thead>
            <tbody id="apwp-donations-list">
                <?php if (!empty($donations)): ?>
                    <?php foreach ($donations as $donation): ?>
                        <tr>
                            <td><?php echo esc_html($donation->id); ?></td>
                            <td><?php echo esc_html($donation->formatted_date); ?></td>
                            <td>
                                <?php echo esc_html($donation->donor_name); ?>
                                <?php if ($donation->donor_email): ?>
                                    <br>
                                    <small><?php echo esc_html($donation->donor_email); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($donation->type_label); ?></td>
                            <td>
                                <?php if ($donation->type === 'money'): ?>
                                    <?php echo 'R$ ' . number_format($donation->amount, 2, ',', '.'); ?>
                                <?php else: ?>
                                    <?php echo esc_html($donation->description); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="apwp-status apwp-status-<?php echo esc_attr($donation->status); ?>">
                                    <?php echo esc_html($donation->status_label); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo add_query_arg(['action' => 'edit', 'id' => $donation->id]); ?>" class="button button-small">
                                    <?php _e('Editar', 'amigopet-wp'); ?>
                                </a>
                                <?php if ($donation->status === 'pending'): ?>
                                    <button type="button" class="button button-small apwp-confirm-donation" data-id="<?php echo esc_attr($donation->id); ?>">
                                        <?php _e('Confirmar', 'amigopet-wp'); ?>
                                    </button>
                                    <button type="button" class="button button-small button-link-delete apwp-cancel-donation" data-id="<?php echo esc_attr($donation->id); ?>">
                                        <?php _e('Cancelar', 'amigopet-wp'); ?>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7"><?php _e('Nenhuma doação encontrada.', 'amigopet-wp'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Paginação -->
        <div id="apwp-donations-pagination" class="tablenav">
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
    // Carrega a lista de doações
    function loadDonations(page = 1) {
        var data = {
            action: 'apwp_load_donations',
            _ajax_nonce: apwp.nonce,
            page: page,
            status: $('#apwp-status-filter').val(),
            type: $('#apwp-type-filter').val(),
            search: $('#apwp-search').val()
        };
        
        $.post(apwp.ajax_url, data, function(response) {
            if (response.success) {
                $('#apwp-donations-list').html(response.data.html);
                $('#apwp-donations-pagination').html(response.data.pagination);
            } else {
                alert(response.data.message);
            }
        });
    }
    
    // Eventos
    $(document).on('click', '.pagination-links a', function(e) {
        e.preventDefault();
        loadDonations($(this).data('page'));
    });
    
    $('#apwp-status-filter, #apwp-type-filter').on('change', function() {
        loadDonations(1);
    });
    
    var searchTimer;
    $('#apwp-search').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadDonations(1);
        }, 500);
    });
    
    // Confirmar doação
    $(document).on('click', '.apwp-confirm-donation', function() {
        if (confirm(apwp.i18n.confirm_donation)) {
            var $button = $(this);
            var data = {
                action: 'apwp_confirm_donation',
                _ajax_nonce: apwp.nonce,
                donation_id: $button.data('id')
            };
            
            $.post(apwp.ajax_url, data, function(response) {
                if (response.success) {
                    loadDonations($('#current-page-selector').val());
                } else {
                    alert(response.data.message);
                }
            });
        }
    });
    
    // Cancelar doação
    $(document).on('click', '.apwp-cancel-donation', function() {
        if (confirm(apwp.i18n.cancel_donation)) {
            var $button = $(this);
            var data = {
                action: 'apwp_cancel_donation',
                _ajax_nonce: apwp.nonce,
                donation_id: $button.data('id')
            };
            
            $.post(apwp.ajax_url, data, function(response) {
                if (response.success) {
                    loadDonations($('#current-page-selector').val());
                } else {
                    alert(response.data.message);
                }
            });
        }
    });
});
</script>
