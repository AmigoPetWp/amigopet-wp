<?php
if (!defined('ABSPATH')) {
    exit;
}

$adoption = $data['adoption'] ?? null;
$payments = $data['payments'] ?? [];
$total = $data['total'] ?? 0;
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php 
        if ($adoption) {
            printf(
                __('Pagamentos da Adoção #%d', 'amigopet-wp'),
                $adoption->getId()
            );
        } else {
            _e('Pagamentos de Adoção', 'amigopet-wp');
        }
        ?>
    </h1>
    <?php if ($adoption): ?>
        <a href="<?php echo admin_url('admin.php?page=amigopet-adoption-payments&action=new&adoption_id=' . $adoption->getId()); ?>" 
           class="page-title-action">
            <?php _e('Adicionar Pagamento', 'amigopet-wp'); ?>
        </a>
    <?php endif; ?>
    <hr class="wp-header-end">

    <?php settings_errors(); ?>

    <?php if ($adoption): ?>
        <div class="adoption-info">
            <h3><?php _e('Informações da Adoção', 'amigopet-wp'); ?></h3>
            <p>
                <strong><?php _e('Pet:', 'amigopet-wp'); ?></strong>
                <?php echo esc_html($adoption->getPet()->getName()); ?>
            </p>
            <p>
                <strong><?php _e('Adotante:', 'amigopet-wp'); ?></strong>
                <?php 
                $user = get_userdata($adoption->getUserId());
                echo esc_html($user ? $user->display_name : __('Usuário não encontrado', 'amigopet-wp')); 
                ?>
            </p>
            <p>
                <strong><?php _e('Status:', 'amigopet-wp'); ?></strong>
                <?php echo esc_html(ucfirst($adoption->getStatus())); ?>
            </p>
            <p>
                <strong><?php _e('Data:', 'amigopet-wp'); ?></strong>
                <?php echo esc_html($adoption->getCreatedAt()->format('d/m/Y H:i:s')); ?>
            </p>
            <p>
                <strong><?php _e('Total de Pagamentos:', 'amigopet-wp'); ?></strong>
                <?php echo 'R$ ' . number_format($total, 2, ',', '.'); ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="tablenav top">
        <div class="alignleft actions">
            <?php if (!$adoption): ?>
                <select id="filter-adoption">
                    <option value=""><?php _e('Todas as adoções', 'amigopet-wp'); ?></option>
                    <?php foreach ($adoptions as $item): ?>
                        <option value="<?php echo esc_attr($item->getId()); ?>">
                            <?php echo esc_html(sprintf(__('Adoção #%d - %s', 'amigopet-wp'), $item->getId(), $item->getPet()->getName())); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <select id="filter-status">
                <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                <option value="pending"><?php _e('Pendente', 'amigopet-wp'); ?></option>
                <option value="completed"><?php _e('Concluído', 'amigopet-wp'); ?></option>
                <option value="cancelled"><?php _e('Cancelado', 'amigopet-wp'); ?></option>
                <option value="refunded"><?php _e('Reembolsado', 'amigopet-wp'); ?></option>
            </select>
            <select id="filter-payment-method">
                <option value=""><?php _e('Todos os métodos', 'amigopet-wp'); ?></option>
                <option value="cash"><?php _e('Dinheiro', 'amigopet-wp'); ?></option>
                <option value="credit_card"><?php _e('Cartão de Crédito', 'amigopet-wp'); ?></option>
                <option value="debit_card"><?php _e('Cartão de Débito', 'amigopet-wp'); ?></option>
                <option value="bank_transfer"><?php _e('Transferência Bancária', 'amigopet-wp'); ?></option>
                <option value="pix"><?php _e('PIX', 'amigopet-wp'); ?></option>
            </select>
            <input type="text" id="filter-date-start" class="date-picker" placeholder="<?php _e('Data inicial', 'amigopet-wp'); ?>">
            <input type="text" id="filter-date-end" class="date-picker" placeholder="<?php _e('Data final', 'amigopet-wp'); ?>">
            <button class="button" id="filter-submit"><?php _e('Filtrar', 'amigopet-wp'); ?></button>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <?php if (!$adoption): ?>
                    <th scope="col" class="manage-column column-adoption"><?php _e('Adoção', 'amigopet-wp'); ?></th>
                <?php endif; ?>
                <th scope="col" class="manage-column column-amount"><?php _e('Valor', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-payment-method"><?php _e('Método', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-transaction-id"><?php _e('Transação', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-status"><?php _e('Status', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-created-at"><?php _e('Data', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-actions"><?php _e('Ações', 'amigopet-wp'); ?></th>
            </tr>
        </thead>
        <tbody id="the-list">
            <?php if (empty($payments)): ?>
                <tr>
                    <td colspan="<?php echo $adoption ? '6' : '7'; ?>">
                        <?php _e('Nenhum pagamento encontrado.', 'amigopet-wp'); ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <?php if (!$adoption): ?>
                            <td class="column-adoption">
                                <a href="<?php echo admin_url('admin.php?page=amigopet-adoptions&action=edit&id=' . $payment->getAdoptionId()); ?>">
                                    <?php echo sprintf(__('Adoção #%d', 'amigopet-wp'), $payment->getAdoptionId()); ?>
                                </a>
                            </td>
                        <?php endif; ?>
                        <td class="column-amount">
                            <?php echo 'R$ ' . number_format($payment->getAmount(), 2, ',', '.'); ?>
                        </td>
                        <td class="column-payment-method">
                            <?php echo esc_html(ucfirst($payment->getPaymentMethod())); ?>
                        </td>
                        <td class="column-transaction-id">
                            <?php echo esc_html($payment->getTransactionId() ?: '-'); ?>
                        </td>
                        <td class="column-status">
                            <span class="status-<?php echo esc_attr($payment->getStatus()); ?>">
                                <?php echo esc_html(ucfirst($payment->getStatus())); ?>
                            </span>
                        </td>
                        <td class="column-created-at">
                            <?php echo esc_html($payment->getCreatedAt()->format('d/m/Y H:i:s')); ?>
                        </td>
                        <td class="column-actions">
                            <div class="row-actions">
                                <a href="<?php echo admin_url('admin.php?page=amigopet-adoption-payments&action=edit&id=' . $payment->getId()); ?>" 
                                   class="button">
                                    <?php _e('Editar', 'amigopet-wp'); ?>
                                </a>
                                <?php if ($payment->getStatus() === 'pending'): ?>
                                    <?php 
                                    $complete_nonce = wp_create_nonce('amigopet_complete_adoption_payment');
                                    $complete_url = add_query_arg([
                                        'action' => 'complete',
                                        'id' => $payment->getId(),
                                        '_wpnonce' => $complete_nonce
                                    ]);
                                    ?>
                                    <a href="<?php echo esc_url($complete_url); ?>" 
                                       class="button complete-payment" 
                                       data-id="<?php echo esc_attr($payment->getId()); ?>"
                                       onclick="return confirm('<?php esc_attr_e('Tem certeza que deseja concluir este pagamento?', 'amigopet-wp'); ?>');">
                                        <?php _e('Concluir', 'amigopet-wp'); ?>
                                    </a>
                                    <?php 
                                    $cancel_nonce = wp_create_nonce('amigopet_cancel_adoption_payment');
                                    $cancel_url = add_query_arg([
                                        'action' => 'cancel',
                                        'id' => $payment->getId(),
                                        '_wpnonce' => $cancel_nonce
                                    ]);
                                    ?>
                                    <a href="<?php echo esc_url($cancel_url); ?>" 
                                       class="button cancel-payment" 
                                       data-id="<?php echo esc_attr($payment->getId()); ?>"
                                       onclick="return confirm('<?php esc_attr_e('Tem certeza que deseja cancelar este pagamento?', 'amigopet-wp'); ?>');">
                                        <?php _e('Cancelar', 'amigopet-wp'); ?>
                                    </a>
                                <?php elseif ($payment->getStatus() === 'completed'): ?>
                                    <?php 
                                    $refund_nonce = wp_create_nonce('amigopet_refund_adoption_payment');
                                    $refund_url = add_query_arg([
                                        'action' => 'refund',
                                        'id' => $payment->getId(),
                                        '_wpnonce' => $refund_nonce
                                    ]);
                                    ?>
                                    <a href="<?php echo esc_url($refund_url); ?>" 
                                       class="button refund-payment" 
                                       data-id="<?php echo esc_attr($payment->getId()); ?>"
                                       onclick="return confirm('<?php esc_attr_e('Tem certeza que deseja reembolsar este pagamento?', 'amigopet-wp'); ?>');">
                                        <?php _e('Reembolsar', 'amigopet-wp'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="<?php echo $adoption ? '2' : '3'; ?>" class="total-label">
                    <strong><?php _e('Total:', 'amigopet-wp'); ?></strong>
                </td>
                <td colspan="4" class="total-amount">
                    <strong><?php echo 'R$ ' . number_format($total, 2, ',', '.'); ?></strong>
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
            tbody.append('<tr><td colspan="7"><?php _e('Nenhum pagamento encontrado.', 'amigopet-wp'); ?></td></tr>');
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
        
        <?php if (!$adoption): ?>
            row.append('<td class="column-adoption"><a href="' + item.adoption_url + '">' + 
                      '<?php _e('Adoção #', 'amigopet-wp'); ?>' + item.adoption_id + '</a></td>');
        <?php endif; ?>

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
        actions += '<a href="' + item.edit_url + '" class="button"><?php _e('Editar', 'amigopet-wp'); ?></a>';

        if (item.status === 'pending') {
            actions += '<a href="' + item.complete_url + '" class="button complete-payment" ' +
                      'onclick="return confirm(\'<?php esc_attr_e('Tem certeza que deseja concluir este pagamento?', 'amigopet-wp'); ?>\');">' +
                      '<?php _e('Concluir', 'amigopet-wp'); ?></a>';
            actions += '<a href="' + item.cancel_url + '" class="button cancel-payment" ' +
                      'onclick="return confirm(\'<?php esc_attr_e('Tem certeza que deseja cancelar este pagamento?', 'amigopet-wp'); ?>\');">' +
                      '<?php _e('Cancelar', 'amigopet-wp'); ?></a>';
        } else if (item.status === 'completed') {
            actions += '<a href="' + item.refund_url + '" class="button refund-payment" ' +
                      'onclick="return confirm(\'<?php esc_attr_e('Tem certeza que deseja reembolsar este pagamento?', 'amigopet-wp'); ?>\');">' +
                      '<?php _e('Reembolsar', 'amigopet-wp'); ?></a>';
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
