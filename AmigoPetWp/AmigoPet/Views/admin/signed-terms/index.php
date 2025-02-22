<?php
if (!defined('ABSPATH')) {
    exit;
}

$signedTerms = $data['signedTerms'] ?? [];
$terms = $data['terms'] ?? [];
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Termos Assinados', 'amigopet-wp'); ?></h1>
    <hr class="wp-header-end">

    <?php settings_errors(); ?>

    <div class="tablenav top">
        <div class="alignleft actions">
            <select id="filter-term">
                <option value=""><?php _e('Todos os termos', 'amigopet-wp'); ?></option>
                <?php foreach ($terms as $term): ?>
                    <option value="<?php echo esc_attr($term->getId()); ?>">
                        <?php echo esc_html($term->getTitle()); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select id="filter-status">
                <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                <option value="active"><?php _e('Ativo', 'amigopet-wp'); ?></option>
                <option value="revoked"><?php _e('Revogado', 'amigopet-wp'); ?></option>
            </select>
            <input type="text" id="filter-date-start" class="date-picker" placeholder="<?php _e('Data inicial', 'amigopet-wp'); ?>">
            <input type="text" id="filter-date-end" class="date-picker" placeholder="<?php _e('Data final', 'amigopet-wp'); ?>">
            <button class="button" id="filter-submit"><?php _e('Filtrar', 'amigopet-wp'); ?></button>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-term"><?php _e('Termo', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-user"><?php _e('Usuário', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-adoption"><?php _e('Adoção', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-status"><?php _e('Status', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-signed-at"><?php _e('Assinado em', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-actions"><?php _e('Ações', 'amigopet-wp'); ?></th>
            </tr>
        </thead>
        <tbody id="the-list">
            <?php if (empty($signedTerms)): ?>
                <tr>
                    <td colspan="6"><?php _e('Nenhum termo assinado encontrado.', 'amigopet-wp'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($signedTerms as $signedTerm): ?>
                    <tr>
                        <td class="column-term">
                            <?php echo esc_html($signedTerm->getTerm()->getTitle()); ?>
                        </td>
                        <td class="column-user">
                            <?php 
                            $user = get_userdata($signedTerm->getUserId());
                            echo esc_html($user ? $user->display_name : __('Usuário não encontrado', 'amigopet-wp')); 
                            ?>
                        </td>
                        <td class="column-adoption">
                            <?php if ($signedTerm->getAdoptionId()): ?>
                                <a href="<?php echo admin_url('admin.php?page=amigopet-adoptions&action=edit&id=' . $signedTerm->getAdoptionId()); ?>">
                                    <?php _e('Ver adoção', 'amigopet-wp'); ?>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="column-status">
                            <?php if ($signedTerm->getStatus() === 'active'): ?>
                                <span class="status-active"><?php _e('Ativo', 'amigopet-wp'); ?></span>
                            <?php else: ?>
                                <span class="status-revoked"><?php _e('Revogado', 'amigopet-wp'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="column-signed-at">
                            <?php echo esc_html($signedTerm->getSignedAt()->format('d/m/Y H:i:s')); ?>
                        </td>
                        <td class="column-actions">
                            <button type="button" class="button view-term" 
                                    data-id="<?php echo esc_attr($signedTerm->getId()); ?>">
                                <?php _e('Visualizar', 'amigopet-wp'); ?>
                            </button>
                            <?php if ($signedTerm->getStatus() === 'active'): ?>
                                <?php 
                                $nonce = wp_create_nonce('amigopet_revoke_signed_term');
                                $revoke_url = add_query_arg([
                                    'action' => 'revoke',
                                    'id' => $signedTerm->getId(),
                                    '_wpnonce' => $nonce
                                ]);
                                ?>
                                <a href="<?php echo esc_url($revoke_url); ?>" 
                                   class="button revoke-term" 
                                   data-id="<?php echo esc_attr($signedTerm->getId()); ?>"
                                   onclick="return confirm('<?php esc_attr_e('Tem certeza que deseja revogar este termo?', 'amigopet-wp'); ?>');">
                                    <?php _e('Revogar', 'amigopet-wp'); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para visualização do termo -->
<div id="term-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2><?php _e('Detalhes do Termo Assinado', 'amigopet-wp'); ?></h2>
        <div id="term-content"></div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.date-picker').datepicker({
        dateFormat: 'dd/mm/yy'
    });

    $('#filter-submit').on('click', function() {
        var data = {
            action: 'amigopet_list_signed_terms',
            term_id: $('#filter-term').val(),
            status: $('#filter-status').val(),
            start_date: $('#filter-date-start').val(),
            end_date: $('#filter-date-end').val()
        };

        $.get(ajaxurl, data, function(response) {
            if (response.success) {
                // Atualizar a tabela com os novos dados
                updateTable(response.data);
            }
        });
    });

    $('.view-term').on('click', function() {
        var id = $(this).data('id');
        var data = {
            action: 'amigopet_get_signed_term',
            id: id
        };

        $.get(ajaxurl, data, function(response) {
            if (response.success) {
                $('#term-content').html(response.data.content);
                $('#term-modal').show();
            }
        });
    });

    $('.close').on('click', function() {
        $('#term-modal').hide();
    });

    $(window).on('click', function(event) {
        if ($(event.target).is('#term-modal')) {
            $('#term-modal').hide();
        }
    });

    function updateTable(data) {
        var tbody = $('#the-list');
        tbody.empty();

        if (data.length === 0) {
            tbody.append('<tr><td colspan="6"><?php _e('Nenhum termo assinado encontrado.', 'amigopet-wp'); ?></td></tr>');
            return;
        }

        data.forEach(function(item) {
            var row = $('<tr></tr>');
            row.append('<td class="column-term">' + item.term.title + '</td>');
            row.append('<td class="column-user">' + item.user.display_name + '</td>');
            
            var adoptionCell = $('<td class="column-adoption"></td>');
            if (item.adoption_id) {
                adoptionCell.append('<a href="' + item.adoption_url + '"><?php _e('Ver adoção', 'amigopet-wp'); ?></a>');
            } else {
                adoptionCell.append('-');
            }
            row.append(adoptionCell);

            var statusClass = item.status === 'active' ? 'status-active' : 'status-revoked';
            var statusText = item.status === 'active' ? '<?php _e('Ativo', 'amigopet-wp'); ?>' : '<?php _e('Revogado', 'amigopet-wp'); ?>';
            row.append('<td class="column-status"><span class="' + statusClass + '">' + statusText + '</span></td>');
            
            row.append('<td class="column-signed-at">' + item.signed_at + '</td>');

            var actions = $('<td class="column-actions"></td>');
            actions.append('<button type="button" class="button view-term" data-id="' + item.id + '"><?php _e('Visualizar', 'amigopet-wp'); ?></button>');
            
            if (item.status === 'active') {
                actions.append('<a href="' + item.revoke_url + '" class="button revoke-term" data-id="' + item.id + '" onclick="return confirm(\'<?php esc_attr_e('Tem certeza que deseja revogar este termo?', 'amigopet-wp'); ?>\');"><?php _e('Revogar', 'amigopet-wp'); ?></a>');
            }
            
            row.append(actions);
            tbody.append(row);
        });
    }
});
</script>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 800px;
    position: relative;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.status-active {
    color: green;
}

.status-revoked {
    color: red;
}

.column-actions .button {
    margin-right: 5px;
}

.date-picker {
    width: 100px;
}
</style>
