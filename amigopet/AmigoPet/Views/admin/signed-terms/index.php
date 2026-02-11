<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$signedTerms = $apwp_data['signedTerms'] ?? [];
$terms = $apwp_data['terms'] ?? [];
?>

<div class="wrap">
    <h1 class="wp-heading-inline"> esc_html_e('Termos Assinados', 'amigopet'); ?></h1>
    <hr class="wp-header-end">

     settings_errors(); ?>

    <div class="tablenav top">
        <div class="alignleft actions">
            <select id="filter-term">
                <option value=""> esc_html_e('Todos os termos', 'amigopet'); ?></option>
                 foreach ($terms as $term): ?>
                    <option value=" echo esc_attr($term->getId()); ?>">
                         echo esc_html($term->getTitle()); ?>
                    </option>
                 endforeach; ?>
            </select>
            <select id="filter-status">
                <option value=""> esc_html_e('Todos os status', 'amigopet'); ?></option>
                <option value="active"> esc_html_e('Ativo', 'amigopet'); ?></option>
                <option value="revoked"> esc_html_e('Revogado', 'amigopet'); ?></option>
            </select>
            <input type="text" id="filter-date-start" class="date-picker" placeholder=" esc_html_e('Data inicial', 'amigopet'); ?>">
            <input type="text" id="filter-date-end" class="date-picker" placeholder=" esc_html_e('Data final', 'amigopet'); ?>">
            <button class="button" id="filter-submit"> esc_html_e('Filtrar', 'amigopet'); ?></button>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-term"> esc_html_e('Termo', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-user"> esc_html_e('Usuário', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-adoption"> esc_html_e('Adoção', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-status"> esc_html_e('Status', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-signed-at"> esc_html_e('Assinado em', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-actions"> esc_html_e('Ações', 'amigopet'); ?></th>
            </tr>
        </thead>
        <tbody id="the-list">
             if (empty($signedTerms)): ?>
                <tr>
                    <td colspan="6"> esc_html_e('Nenhum termo assinado encontrado.', 'amigopet'); ?></td>
                </tr>
             else: ?>
                 foreach ($signedTerms as $signedTerm): ?>
                    <tr>
                        <td class="column-term">
                             echo esc_html($signedTerm->getTerm()->getTitle()); ?>
                        </td>
                        <td class="column-user">
                             
                            $user = get_userdata($signedTerm->getUserId());
                            echo esc_html($user ? $user->display_name : esc_html__('Usuário não encontrado', 'amigopet')); 
                            ?>
                        </td>
                        <td class="column-adoption">
                             if ($signedTerm->getAdoptionId()): ?>
                                <a href=" echo esc_url(admin_url('admin.php?page=amigopet-adoptions&action=edit&id=' . $signedTerm->getAdoptionId()); ?>">
                                     esc_html_e('Ver adoção', 'amigopet'); ?>
                                </a>
                             else: ?>
                                -
                             endif; ?>
                        </td>
                        <td class="column-status">
                             if ($signedTerm->getStatus() === 'active'): ?>
                                <span class="status-active"> esc_html_e('Ativo', 'amigopet'); ?></span>
                             else: ?>
                                <span class="status-revoked"> esc_html_e('Revogado', 'amigopet'); ?></span>
                             endif; ?>
                        </td>
                        <td class="column-signed-at">
                             echo esc_html($signedTerm->getSignedAt()->format('d/m/Y H:i:s')); ?>
                        </td>
                        <td class="column-actions">
                            <button type="button" class="button view-term" 
                                    data-id=" echo esc_attr($signedTerm->getId()); ?>">
                                 esc_html_e('Visualizar', 'amigopet'); ?>
                            </button>
                             if ($signedTerm->getStatus() === 'active'): ?>
                                 
                                $nonce = wp_create_nonce('amigopet_revoke_signed_term');
                                $revoke_url = add_query_arg([
                                    'action' => 'revoke',
                                    'id' => $signedTerm->getId(),
                                    '_wpnonce' => $nonce
                                ]);
                                ?>
                                <a href=" echo esc_url($revoke_url); ?>" 
                                   class="button revoke-term" 
                                   data-id=" echo esc_attr($signedTerm->getId()); ?>"
                                   onclick="return confirm(' esc_attresc_html_e('Tem certeza que deseja revogar este termo?', 'amigopet'); ?>');">
                                     esc_html_e('Revogar', 'amigopet'); ?>
                                </a>
                             endif; ?>
                        </td>
                    </tr>
                 endforeach; ?>
             endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para visualização do termo -->
<div id="term-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2> esc_html_e('Detalhes do Termo Assinado', 'amigopet'); ?></h2>
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
            tbody.append('<tr><td colspan="6"> esc_html_e('Nenhum termo assinado encontrado.', 'amigopet'); ?></td></tr>');
            return;
        }

        data.forEach(function(item) {
            var row = $('<tr></tr>');
            row.append('<td class="column-term">' + item.term.title + '</td>');
            row.append('<td class="column-user">' + item.user.display_name + '</td>');
            
            var adoptionCell = $('<td class="column-adoption"></td>');
            if (item.adoption_id) {
                adoptionCell.append('<a href="' + item.adoption_url + '"> esc_html_e('Ver adoção', 'amigopet'); ?></a>');
            } else {
                adoptionCell.append('-');
            }
            row.append(adoptionCell);

            var statusClass = item.status === 'active' ? 'status-active' : 'status-revoked';
            var statusText = item.status === 'active' ? ' esc_html_e('Ativo', 'amigopet'); ?>' : ' esc_html_e('Revogado', 'amigopet'); ?>';
            row.append('<td class="column-status"><span class="' + statusClass + '">' + statusText + '</span></td>');
            
            row.append('<td class="column-signed-at">' + item.signed_at + '</td>');

            var actions = $('<td class="column-actions"></td>');
            actions.append('<button type="button" class="button view-term" data-id="' + item.id + '"> esc_html_e('Visualizar', 'amigopet'); ?></button>');
            
            if (item.status === 'active') {
                actions.append('<a href="' + item.revoke_url + '" class="button revoke-term" data-id="' + item.id + '" onclick="return confirm(\' esc_attresc_html_e('Tem certeza que deseja revogar este termo?', 'amigopet'); ?>\');"> esc_html_e('Revogar', 'amigopet'); ?></a>');
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