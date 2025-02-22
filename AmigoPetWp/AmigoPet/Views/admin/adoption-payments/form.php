<?php
if (!defined('ABSPATH')) {
    exit;
}

$payment = $data['payment'] ?? null;
$adoption = $data['adoption'] ?? null;
$action = $payment ? 'edit' : 'new';
$title = $action === 'edit' ? __('Editar Pagamento', 'amigopet-wp') : __('Novo Pagamento', 'amigopet-wp');
?>

<div class="wrap">
    <h1><?php echo esc_html($title); ?></h1>

    <?php settings_errors(); ?>

    <form method="post" action="">
        <?php wp_nonce_field('amigopet_save_adoption_payment'); ?>
        <input type="hidden" name="action" value="<?php echo esc_attr($action); ?>">
        <?php if ($payment): ?>
            <input type="hidden" name="id" value="<?php echo esc_attr($payment->getId()); ?>">
        <?php endif; ?>

        <?php if ($adoption): ?>
            <input type="hidden" name="adoption_id" value="<?php echo esc_attr($adoption->getId()); ?>">
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
            </div>
        <?php else: ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="adoption_id"><?php _e('Adoção', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <select name="adoption_id" id="adoption_id" class="regular-text" required>
                            <option value=""><?php _e('Selecione uma adoção', 'amigopet-wp'); ?></option>
                            <?php foreach ($adoptions as $item): ?>
                                <option value="<?php echo esc_attr($item->getId()); ?>" 
                                    <?php selected($payment ? $payment->getAdoptionId() : '', $item->getId()); ?>>
                                    <?php echo esc_html(sprintf(
                                        __('Adoção #%d - %s (%s)', 'amigopet-wp'),
                                        $item->getId(),
                                        $item->getPet()->getName(),
                                        get_userdata($item->getUserId())->display_name
                                    )); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
        <?php endif; ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="amount"><?php _e('Valor', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input name="amount" type="number" id="amount" 
                           value="<?php echo esc_attr($payment ? $payment->getAmount() : ''); ?>" 
                           class="regular-text" step="0.01" min="0" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="payment_method"><?php _e('Método de Pagamento', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select name="payment_method" id="payment_method" required>
                        <option value=""><?php _e('Selecione um método', 'amigopet-wp'); ?></option>
                        <option value="cash" <?php selected($payment ? $payment->getPaymentMethod() : '', 'cash'); ?>>
                            <?php _e('Dinheiro', 'amigopet-wp'); ?>
                        </option>
                        <option value="credit_card" <?php selected($payment ? $payment->getPaymentMethod() : '', 'credit_card'); ?>>
                            <?php _e('Cartão de Crédito', 'amigopet-wp'); ?>
                        </option>
                        <option value="debit_card" <?php selected($payment ? $payment->getPaymentMethod() : '', 'debit_card'); ?>>
                            <?php _e('Cartão de Débito', 'amigopet-wp'); ?>
                        </option>
                        <option value="bank_transfer" <?php selected($payment ? $payment->getPaymentMethod() : '', 'bank_transfer'); ?>>
                            <?php _e('Transferência Bancária', 'amigopet-wp'); ?>
                        </option>
                        <option value="pix" <?php selected($payment ? $payment->getPaymentMethod() : '', 'pix'); ?>>
                            <?php _e('PIX', 'amigopet-wp'); ?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="transaction_id"><?php _e('ID da Transação', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input name="transaction_id" type="text" id="transaction_id" 
                           value="<?php echo esc_attr($payment ? $payment->getTransactionId() : ''); ?>" 
                           class="regular-text">
                    <p class="description">
                        <?php _e('Opcional. Identificador único da transação (para pagamentos online).', 'amigopet-wp'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="notes"><?php _e('Observações', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <textarea name="notes" id="notes" class="large-text" rows="5"><?php 
                        echo esc_textarea($payment ? $payment->getNotes() : ''); 
                    ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="status"><?php _e('Status', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select name="status" id="status" required>
                        <option value="pending" <?php selected($payment ? $payment->getStatus() : 'pending', 'pending'); ?>>
                            <?php _e('Pendente', 'amigopet-wp'); ?>
                        </option>
                        <option value="completed" <?php selected($payment ? $payment->getStatus() : '', 'completed'); ?>>
                            <?php _e('Concluído', 'amigopet-wp'); ?>
                        </option>
                        <option value="cancelled" <?php selected($payment ? $payment->getStatus() : '', 'cancelled'); ?>>
                            <?php _e('Cancelado', 'amigopet-wp'); ?>
                        </option>
                        <option value="refunded" <?php selected($payment ? $payment->getStatus() : '', 'refunded'); ?>>
                            <?php _e('Reembolsado', 'amigopet-wp'); ?>
                        </option>
                    </select>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>

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
</style>
