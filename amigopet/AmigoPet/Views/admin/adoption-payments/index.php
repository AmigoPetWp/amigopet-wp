<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$adoption = $apwp_data['adoption'] ?? null;
$payments = $apwp_data['payments'] ?? [];
$total = $apwp_data['total'] ?? 0;
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
         
        if ($adoption) {
            printf(
                /* translators: %d */
                esc_html__('Pagamentos da Adoção #%d', 'amigopet'),
                $adoption->getId()
            );
        } else {
            esc_html_e('Pagamentos de Adoção', 'amigopet');
        }
        ?>
    </h1>
     if ($adoption): ?>
        <a href=" echo esc_url(admin_url('admin.php?page=amigopet-adoption-payments&action=new&adoption_id=' . $adoption->getId()); ?>" 
           class="page-title-action">
             esc_html_e('Adicionar Pagamento', 'amigopet'); ?>
        </a>
     endif; ?>
    <hr class="wp-header-end">

     settings_errors(); ?>

     if ($adoption): ?>
        <div class="adoption-info">
            <h3> esc_html_e('Informações da Adoção', 'amigopet'); ?></h3>
            <p>
                <strong> esc_html_e('Pet:', 'amigopet'); ?></strong>
                 echo esc_html($adoption->getPet()->getName()); ?>
            </p>
            <p>
                <strong> esc_html_e('Adotante:', 'amigopet'); ?></strong>
                 
                $user = get_userdata($adoption->getUserId());
                echo esc_html($user ? $user->display_name : esc_html__('Usuário não encontrado', 'amigopet')); 
                ?>
            </p>
            <p>
                <strong> esc_html_e('Status:', 'amigopet'); ?></strong>
                 echo esc_html(ucfirst($adoption->getStatus())); ?>
            </p>
            <p>
                <strong> esc_html_e('Data:', 'amigopet'); ?></strong>
                 echo esc_html($adoption->getCreatedAt()->format('d/m/Y H:i:s')); ?>
            </p>
            <p>
                <strong> esc_html_e('Total de Pagamentos:', 'amigopet'); ?></strong>
                 echo 'R$ ' . number_format($total, 2, ',', '.'); ?>
            </p>
        </div>
     endif; ?>

    <div class="tablenav top">
        <div class="alignleft actions">
             if (!$adoption): ?>
                <select id="filter-adoption">
                    <option value=""> esc_html_e('Todas as adoções', 'amigopet'); ?></option>
                     foreach ($adoptions as $item): ?>
                        <option value=" echo esc_attr($item->getId()); ?>">
                            // translators: %s: placeholder
                             echo esc_html(sprintf(sprintf(esc_html__('Adoção #%1%1$s - %2%2$s', 'amigopet'), $d, $s), $item->getId(), $item->getPet()->getName())); ?>
                        </option>
                     endforeach; ?>
                </select>
             endif; ?>
            <select id="filter-status">
                <option value=""> esc_html_e('Todos os status', 'amigopet'); ?></option>
                <option value="pending"> esc_html_e('Pendente', 'amigopet'); ?></option>
                <option value="completed"> esc_html_e('Concluído', 'amigopet'); ?></option>
                <option value="cancelled"> esc_html_e('Cancelado', 'amigopet'); ?></option>
                <option value="refunded"> esc_html_e('Reembolsado', 'amigopet'); ?></option>
            </select>
            <select id="filter-payment-method">
                <option value=""> esc_html_e('Todos os métodos', 'amigopet'); ?></option>
                <option value="cash"> esc_html_e('Dinheiro', 'amigopet'); ?></option>
                <option value="credit_card"> esc_html_e('Cartão de Crédito', 'amigopet'); ?></option>
                <option value="debit_card"> esc_html_e('Cartão de Débito', 'amigopet'); ?></option>
                <option value="bank_transfer"> esc_html_e('Transferência Bancária', 'amigopet'); ?></option>
                <option value="pix"> esc_html_e('PIX', 'amigopet'); ?></option>
            </select>
            <input type="text" id="filter-date-start" class="date-picker" placeholder=" esc_html_e('Data inicial', 'amigopet'); ?>">
            <input type="text" id="filter-date-end" class="date-picker" placeholder=" esc_html_e('Data final', 'amigopet'); ?>">
            <button class="button" id="filter-submit"> esc_html_e('Filtrar', 'amigopet'); ?></button>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                 if (!$adoption): ?>
                    <th scope="col" class="manage-column column-adoption"> esc_html_e('Adoção', 'amigopet'); ?></th>
                 endif; ?>
                <th scope="col" class="manage-column column-amount"> esc_html_e('Valor', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-payment-method"> esc_html_e('Método', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-transaction-id"> esc_html_e('Transação', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-status"> esc_html_e('Status', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-created-at"> esc_html_e('Data', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-actions"> esc_html_e('Ações', 'amigopet'); ?></th>
            </tr>
        </thead>
        <tbody id="the-list">
             if (empty($payments)): ?>
                <tr>
                    <td colspan=" echo $adoption ? '6' : '7'; ?>">
                         esc_html_e('Nenhum pagamento encontrado.', 'amigopet'); ?>
                    </td>
                </tr>
             else: ?>
                 foreach ($payments as $payment): ?>
                    <tr>
                         if (!$adoption): ?>
                            <td class="column-adoption">
                                <a href=" echo esc_url(admin_url('admin.php?page=amigopet-adoptions&action=edit&id=' . $payment->getAdoptionId()); ?>">
                                    /* translators: %d */
                                     echo sprintf(esc_html__('Adoção #%d', 'amigopet'), $payment->getAdoptionId()); ?>
                                </a>
                            </td>
                         endif; ?>
                        <td class="column-amount">
                             echo 'R$ ' . number_format($payment->getAmount(), 2, ',', '.'); ?>
                        </td>
                        <td class="column-payment-method">
                             echo esc_html(ucfirst($payment->getPaymentMethod())); ?>
                        </td>
                        <td class="column-transaction-id">
                             echo esc_html($payment->getTransactionId() ?: '-'); ?>
                        </td>
                        <td class="column-status">
                            <span class="status- echo esc_attr($payment->getStatus()); ?>">
                                 echo esc_html(ucfirst($payment->getStatus())); ?>
                            </span>
                        </td>
                        <td class="column-created-at">
                             echo esc_html($payment->getCreatedAt()->format('d/m/Y H:i:s')); ?>
                        </td>
                        <td class="column-actions">
                            <div class="row-actions">
                                <a href=" echo esc_url(admin_url('admin.php?page=amigopet-adoption-payments&action=edit&id=' . $payment->getId()); ?>" 
                                   class="button">
                                     esc_html_e('Editar', 'amigopet'); ?>
                                </a>
                                 if ($payment->getStatus() === 'pending'): ?>
                                     
                                    $complete_nonce = wp_create_nonce('amigopet_complete_adoption_payment');
                                    $complete_url = add_query_arg([
                                        'action' => 'complete',
                                        'id' => $payment->getId(),
                                        '_wpnonce' => $complete_nonce
                                    ]);
                                    ?>
                                    <a href=" echo esc_url($complete_url); ?>" 
                                       class="button complete-payment" 
                                       data-id=" echo esc_attr($payment->getId()); ?>"
                                       onclick="return confirm(' esc_attresc_html_e('Tem certeza que deseja concluir este pagamento?', 'amigopet'); ?>');">
                                         esc_html_e('Concluir', 'amigopet'); ?>
                                    </a>
                                     
                                    $cancel_nonce = wp_create_nonce('amigopet_cancel_adoption_payment');
                                    $cancel_url = add_query_arg([
                                        'action' => 'cancel',
                                        'id' => $payment->getId(),
                                        '_wpnonce' => $cancel_nonce
                                    ]);
                                    ?>
                                    <a href=" echo esc_url($cancel_url); ?>" 
                                       class="button cancel-payment" 
                                       data-id=" echo esc_attr($payment->getId()); ?>"
                                       onclick="return confirm(' esc_attresc_html_e('Tem certeza que deseja cancelar este pagamento?', 'amigopet'); ?>');">
                                         esc_html_e('Cancelar', 'amigopet'); ?>
                                    </a>
                                 elseif ($payment->getStatus() === 'completed'): ?>
                                     
                                    $refund_nonce = wp_create_nonce('amigopet_refund_adoption_payment');
                                    $refund_url = add_query_arg([
                                        'action' => 'refund',
                                        'id' => $payment->getId(),
                                        '_wpnonce' => $refund_nonce
                                    ]);
                                    ?>
                                    <a href=" echo esc_url($refund_url); ?>" 
                                       class="button refund-payment" 
                                       data-id=" echo esc_attr($payment->getId()); ?>"
                                       onclick="return confirm(' esc_attresc_html_e('Tem certeza que deseja reembolsar este pagamento?', 'amigopet'); ?>');">
                                         esc_html_e('Reembolsar', 'amigopet'); ?>
                                    </a>
                                 endif; ?>
                            </div>
                        </td>
                    </tr>
                 endforeach; ?>
             endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan=" echo $adoption ? '2' : '3'; ?>" class="total-label">
                    <strong> esc_html_e('Total:', 'amigopet'); ?></strong>
                </td>
                <td colspan="4" class="total-amount">
                    <strong> echo 'R$ ' . number_format($total, 2, ',', '.'); ?></strong>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    $('.date-picker').datepicker({
        dateFormat: 'dd/mm/yy'
    });

    $('#filter-submit').on('click', function() {
        var data = {
            action: 'amigopet_list_adoption_payments',
            adoption_id: $('#filter-adoption').val(),
            status: $('#filter-status').val(),
            payment_method: $('#filter-payment-method').val(),
            start_date: $('#filter-date-start').val(),
            end_date: $('#filter-date-end').val()
        };

        $.get(ajaxurl, data, function(response) {
            if (response.success) {
                updateTable(response.data);
            }
        });
    });

    function updateTable(data) {
        var tbody = $('#the-list');
        tbody.empty();

        if (data.length === 0) {
            tbody.append('<tr><td colspan="7"> esc_html_e('Nenhum pagamento encontrado.', 'amigopet'); ?></td></tr>');
            return;
        }

        var total = 0;
        data.forEach(function(item) {
            total += parseFloat(item.amount);
            var row = createPaymentRow(item);
            tbody.append(row);
        });

        $('.total-amount strong').text('R$ ' + total.toFixed(2).replace('.', ','));
    }

    function createPaymentRow(item) {
        var row = $('<tr></tr>');
        
         if (!$adoption): ?>
            row.append('<td class="column-adoption"><a href="' + item.adoption_url + '">' + 
                      ' esc_html_e('Adoção #', 'amigopet'); ?>' + item.adoption_id + '</a></td>');
         endif; ?>

        row.append('<td class="column-amount">R$ ' + parseFloat(item.amount).toFixed(2).replace('.', ',') + '</td>');
        row.append('<td class="column-payment-method">' + item.payment_method + '</td>');
        row.append('<td class="column-transaction-id">' + (item.transaction_id || '-') + '</td>');
        row.append('<td class="column-status"><span class="status-' + item.status + '">' + 
                  item.status.charAt(0).toUpperCase() + item.status.slice(1) + '</span></td>');
        row.append('<td class="column-created-at">' + item.created_at + '</td>');

        var actions = createActionButtons(item);
        row.append('<td class="column-actions">' + actions + '</td>');

        return row;
    }

    function createActionButtons(item) {
        var actions = '<div class="row-actions">';
        actions += '<a href="' + item.edit_url + '" class="button"> esc_html_e('Editar', 'amigopet'); ?></a>';

        if (item.status === 'pending') {
            actions += '<a href="' + item.complete_url + '" class="button complete-payment" ' +
                      'onclick="return confirm(\' esc_attresc_html_e('Tem certeza que deseja concluir este pagamento?', 'amigopet'); ?>\');">' +
                      ' esc_html_e('Concluir', 'amigopet'); ?></a>';
            actions += '<a href="' + item.cancel_url + '" class="button cancel-payment" ' +
                      'onclick="return confirm(\' esc_attresc_html_e('Tem certeza que deseja cancelar este pagamento?', 'amigopet'); ?>\');">' +
                      ' esc_html_e('Cancelar', 'amigopet'); ?></a>';
        } else if (item.status === 'completed') {
            actions += '<a href="' + item.refund_url + '" class="button refund-payment" ' +
                      'onclick="return confirm(\' esc_attresc_html_e('Tem certeza que deseja reembolsar este pagamento?', 'amigopet'); ?>\');">' +
                      ' esc_html_e('Reembolsar', 'amigopet'); ?></a>';
        }

        actions += '</div>';
        return actions;
    }
});
</script>

<style>
.adoption-info {
    background: #fff;
    border: 1px solid #ccd0d4;
    padding: 15px;
    margin-bottom: 20px;
}

.adoption-info h3 {
    margin-top: 0;
}

.status-pending {
    color: #ffba00;
}

.status-completed {
    color: #46b450;
}

.status-cancelled {
    color: #dc3232;
}

.status-refunded {
    color: #826eb4;
}

.column-actions .button {
    margin-right: 5px;
}

.date-picker {
    width: 100px;
}

.total-label {
    text-align: right;
}

.total-amount {
    font-size: 1.2em;
}

#filter-adoption,
#filter-status,
#filter-payment-method {
    margin-right: 5px;
}
</style>