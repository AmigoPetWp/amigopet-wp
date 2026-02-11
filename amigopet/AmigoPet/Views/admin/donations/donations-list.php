<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

// Verifica permissões
if (!current_user_can('manage_amigopet_donations')) {
    wp_die(esc_html__('Você não tem permissão para acessar esta página', 'amigopet'));
}

$apwp_message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
$apwp_messages = [
    'created' => esc_html__('Doação criada com sucesso', 'amigopet'),
    'payment_processed' => esc_html__('Pagamento processado com sucesso', 'amigopet'),
    'refunded' => esc_html__('Doação reembolsada com sucesso', 'amigopet'),
    'marked_failed' => esc_html__('Doação marcada como falha', 'amigopet')
];
?>

<div class="wrap">
    <h1 class="wp-heading-inline"> esc_html_e('Doações', 'amigopet'); ?></h1>
    <a href=" echo esc_url(admin_url('admin.php?page=amigopet-donations&action=add')); ?>" class="page-title-action">
         esc_html_e('Adicionar Nova', 'amigopet'); ?>
    </a>
    <hr class="wp-header-end">

     if ($apwp_message && isset($apwp_messages[$apwp_message])): ?>
        <div class="notice notice-success is-dismissible">
            <p> echo esc_html($apwp_messages[$apwp_message]); ?></p>
        </div>
     endif; ?>

    <div class="tablenav top">
        <div class="alignleft actions">
            <select name="filter_status" id="filter_status">
                <option value=""> esc_html_e('Todos os status', 'amigopet'); ?></option>
                 foreach ($statuses as $value => $label): ?>
                    <option value=" echo esc_attr($value); ?>"> echo esc_html($label); ?></option>
                 endforeach; ?>
            </select>
            <select name="filter_payment_method" id="filter_payment_method">
                <option value=""> esc_html_e('Todas as formas de pagamento', 'amigopet'); ?></option>
                 foreach ($payment_methods as $value => $label): ?>
                    <option value=" echo esc_attr($value); ?>"> echo esc_html($label); ?></option>
                 endforeach; ?>
            </select>
            <button class="button" id="filter-donations"> esc_html_e('Filtrar', 'amigopet'); ?></button>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-donor"> esc_html_e('Doador', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-amount"> esc_html_e('Valor', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-payment-method"> esc_html_e('Forma de Pagamento', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-status"> esc_html_e('Status', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-date"> esc_html_e('Data', 'amigopet'); ?></th>
                <th scope="col" class="manage-column column-actions"> esc_html_e('Ações', 'amigopet'); ?></th>
            </tr>
        </thead>
        <tbody>
             if (empty($donations)): ?>
                <tr>
                    <td colspan="6"> esc_html_e('Nenhuma doação encontrada.', 'amigopet'); ?></td>
                </tr>
             else: ?>
                 foreach ($donations as $donation): ?>
                    <tr>
                        <td>
                            <strong> echo esc_html($donation->getDonorName()); ?></strong><br>
                            <small> echo esc_html($donation->getDonorEmail()); ?></small>
                             if ($donation->getDonorPhone()): ?>
                                <br><small> echo esc_html($donation->getDonorPhone()); ?></small>
                             endif; ?>
                        </td>
                        <td>
                             echo 'R$ ' . number_format($donation->getAmount(), 2, ',', '.'); ?>
                        </td>
                        <td>
                             echo esc_html($payment_methods[$donation->getPaymentMethod()]); ?>
                        </td>
                        <td>
                            <span class="donation-status status- echo esc_attr($donation->getPaymentStatus()); ?>">
                                 echo esc_html($statuses[$donation->getPaymentStatus()]); ?>
                            </span>
                        </td>
                        <td>
                             echo esc_html($donation->getDate()->format('d/m/Y H:i')); ?>
                        </td>
                        <td class="actions">
                             if ($donation->getPaymentStatus() === 'pending'): ?>
                                <form method="post" action=" echo esc_url(admin_url('admin-post.php')); ?>" style="display: inline;">
                                    <input type="hidden" name="action" value="apwp_process_payment">
                                    <input type="hidden" name="donation_id" value=" echo esc_attr($donation->getId()); ?>">
                                     wp_nonce_field('process_payment_' . $donation->getId()); ?>
                                    <button type="submit" class="button button-small">
                                         esc_html_e('Processar Pagamento', 'amigopet'); ?>
                                    </button>
                                </form>
                                <form method="post" action=" echo esc_url(admin_url('admin-post.php')); ?>" style="display: inline;">
                                    <input type="hidden" name="action" value="apwp_mark_failed">
                                    <input type="hidden" name="donation_id" value=" echo esc_attr($donation->getId()); ?>">
                                     wp_nonce_field('mark_failed_' . $donation->getId()); ?>
                                    <button type="submit" class="button button-small">
                                         esc_html_e('Marcar como Falha', 'amigopet'); ?>
                                    </button>
                                </form>
                             elseif ($donation->getPaymentStatus() === 'completed'): ?>
                                <form method="post" action=" echo esc_url(admin_url('admin-post.php')); ?>" style="display: inline;">
                                    <input type="hidden" name="action" value="apwp_refund_donation">
                                    <input type="hidden" name="donation_id" value=" echo esc_attr($donation->getId()); ?>">
                                     wp_nonce_field('refund_donation_' . $donation->getId()); ?>
                                    <button type="submit" class="button button-small">
                                         esc_html_e('Reembolsar', 'amigopet'); ?>
                                    </button>
                                </form>
                             endif; ?>
                        </td>
                    </tr>
                 endforeach; ?>
             endif; ?>
        </tbody>
    </table>
</div>

<style>
.donation-status {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}

.status-pending {
    background-color: #f0f0f1;
    color: #50575e;
}

.status-completed {
    background-color: #dff0d8;
    color: #3c763d;
}

.status-failed {
    background-color: #f2dede;
    color: #a94442;
}

.status-refunded {
    background-color: #fcf8e3;
    color: #8a6d3b;
}

.actions form {
    margin: 2px 0;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('#filter-donations').on('click', function(e) {
        e.preventDefault();
        
        var status = $('#filter_status').val();
        var paymentMethod = $('#filter_payment_method').val();
        
        var url = new URL(window.location.href);
        if (status) url.searchParams.set('status', status);
        else url.searchParams.delete('status');
        
        if (paymentMethod) url.searchParams.set('payment_method', paymentMethod);
        else url.searchParams.delete('payment_method');
        
        window.location.href = url.toString();
    });
});
</script>