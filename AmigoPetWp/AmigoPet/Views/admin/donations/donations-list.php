<?php
if (!defined('ABSPATH')) {
    exit;
}

// Verifica permissões
if (!current_user_can('manage_amigopet_donations')) {
    wp_die(__('Você não tem permissão para acessar esta página', 'amigopet-wp'));
}

$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
$messages = [
    'created' => __('Doação criada com sucesso', 'amigopet-wp'),
    'payment_processed' => __('Pagamento processado com sucesso', 'amigopet-wp'),
    'refunded' => __('Doação reembolsada com sucesso', 'amigopet-wp'),
    'marked_failed' => __('Doação marcada como falha', 'amigopet-wp')
];
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Doações', 'amigopet-wp'); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-donations&action=add')); ?>" class="page-title-action">
        <?php _e('Adicionar Nova', 'amigopet-wp'); ?>
    </a>
    <hr class="wp-header-end">

    <?php if ($message && isset($messages[$message])): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html($messages[$message]); ?></p>
        </div>
    <?php endif; ?>

    <div class="tablenav top">
        <div class="alignleft actions">
            <select name="filter_status" id="filter_status">
                <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                <?php foreach ($statuses as $value => $label): ?>
                    <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="filter_payment_method" id="filter_payment_method">
                <option value=""><?php _e('Todas as formas de pagamento', 'amigopet-wp'); ?></option>
                <?php foreach ($payment_methods as $value => $label): ?>
                    <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
            <button class="button" id="filter-donations"><?php _e('Filtrar', 'amigopet-wp'); ?></button>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-donor"><?php _e('Doador', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-amount"><?php _e('Valor', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-payment-method"><?php _e('Forma de Pagamento', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-status"><?php _e('Status', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-date"><?php _e('Data', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-actions"><?php _e('Ações', 'amigopet-wp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($donations)): ?>
                <tr>
                    <td colspan="6"><?php _e('Nenhuma doação encontrada.', 'amigopet-wp'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($donations as $donation): ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($donation->getDonorName()); ?></strong><br>
                            <small><?php echo esc_html($donation->getDonorEmail()); ?></small>
                            <?php if ($donation->getDonorPhone()): ?>
                                <br><small><?php echo esc_html($donation->getDonorPhone()); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo 'R$ ' . number_format($donation->getAmount(), 2, ',', '.'); ?>
                        </td>
                        <td>
                            <?php echo esc_html($payment_methods[$donation->getPaymentMethod()]); ?>
                        </td>
                        <td>
                            <span class="donation-status status-<?php echo esc_attr($donation->getPaymentStatus()); ?>">
                                <?php echo esc_html($statuses[$donation->getPaymentStatus()]); ?>
                            </span>
                        </td>
                        <td>
                            <?php echo esc_html($donation->getDate()->format('d/m/Y H:i')); ?>
                        </td>
                        <td class="actions">
                            <?php if ($donation->getPaymentStatus() === 'pending'): ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display: inline;">
                                    <input type="hidden" name="action" value="apwp_process_payment">
                                    <input type="hidden" name="donation_id" value="<?php echo esc_attr($donation->getId()); ?>">
                                    <?php wp_nonce_field('process_payment_' . $donation->getId()); ?>
                                    <button type="submit" class="button button-small">
                                        <?php _e('Processar Pagamento', 'amigopet-wp'); ?>
                                    </button>
                                </form>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display: inline;">
                                    <input type="hidden" name="action" value="apwp_mark_failed">
                                    <input type="hidden" name="donation_id" value="<?php echo esc_attr($donation->getId()); ?>">
                                    <?php wp_nonce_field('mark_failed_' . $donation->getId()); ?>
                                    <button type="submit" class="button button-small">
                                        <?php _e('Marcar como Falha', 'amigopet-wp'); ?>
                                    </button>
                                </form>
                            <?php elseif ($donation->getPaymentStatus() === 'completed'): ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display: inline;">
                                    <input type="hidden" name="action" value="apwp_refund_donation">
                                    <input type="hidden" name="donation_id" value="<?php echo esc_attr($donation->getId()); ?>">
                                    <?php wp_nonce_field('refund_donation_' . $donation->getId()); ?>
                                    <button type="submit" class="button button-small">
                                        <?php _e('Reembolsar', 'amigopet-wp'); ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
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
