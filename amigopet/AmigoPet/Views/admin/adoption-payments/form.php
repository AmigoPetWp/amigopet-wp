<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$payment = $apwp_data['payment'] ?? null;
$adoption = $apwp_data['adoption'] ?? null;
$action = $payment ? 'edit' : 'new';
$title = $action === 'edit' ? esc_html__('Editar Pagamento', 'amigopet') : esc_html__('Novo Pagamento', 'amigopet');
?>

<div class="wrap">
    <h1> echo esc_html($title); ?></h1>

     settings_errors(); ?>

    <form method="post" action="">
         wp_nonce_field('amigopet_save_adoption_payment'); ?>
        <input type="hidden" name="action" value=" echo esc_attr($action); ?>">
         if ($payment): ?>
            <input type="hidden" name="id" value=" echo esc_attr($payment->getId()); ?>">
         endif; ?>

         if ($adoption): ?>
            <input type="hidden" name="adoption_id" value=" echo esc_attr($adoption->getId()); ?>">
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
            </div>
         else: ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="adoption_id"> esc_html_e('Adoção', 'amigopet'); ?></label>
                    </th>
                    <td>
                        <select name="adoption_id" id="adoption_id" class="regular-text" required>
                            <option value=""> esc_html_e('Selecione uma adoção', 'amigopet'); ?></option>
                             foreach ($adoptions as $item): ?>
                                <option value=" echo esc_attr($item->getId()); ?>" 
                                     selected($payment ? $payment->getAdoptionId() : '', $item->getId()); ?>>
                                     echo esc_html(sprintf(
                                        /* translators: %1$d, %2$s, %3$s */
                                        sprintf(esc_html__('Adoção #%1%1$s - %2%2$s (%3%3$s)', 'amigopet'), $d, $s, $s),
                                        $item->getId(),
                                        $item->getPet()->getName(),
                                        get_userdata($item->getUserId())->display_name
                                    )); ?>
                                </option>
                             endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
         endif; ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="amount"> esc_html_e('Valor', 'amigopet'); ?></label>
                </th>
                <td>
                    <input name="amount" type="number" id="amount" 
                           value=" echo esc_attr($payment ? $payment->getAmount() : ''); ?>" 
                           class="regular-text" step="0.01" min="0" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="payment_method"> esc_html_e('Método de Pagamento', 'amigopet'); ?></label>
                </th>
                <td>
                    <select name="payment_method" id="payment_method" required>
                        <option value=""> esc_html_e('Selecione um método', 'amigopet'); ?></option>
                        <option value="cash"  selected($payment ? $payment->getPaymentMethod() : '', 'cash'); ?>>
                             esc_html_e('Dinheiro', 'amigopet'); ?>
                        </option>
                        <option value="credit_card"  selected($payment ? $payment->getPaymentMethod() : '', 'credit_card'); ?>>
                             esc_html_e('Cartão de Crédito', 'amigopet'); ?>
                        </option>
                        <option value="debit_card"  selected($payment ? $payment->getPaymentMethod() : '', 'debit_card'); ?>>
                             esc_html_e('Cartão de Débito', 'amigopet'); ?>
                        </option>
                        <option value="bank_transfer"  selected($payment ? $payment->getPaymentMethod() : '', 'bank_transfer'); ?>>
                             esc_html_e('Transferência Bancária', 'amigopet'); ?>
                        </option>
                        <option value="pix"  selected($payment ? $payment->getPaymentMethod() : '', 'pix'); ?>>
                             esc_html_e('PIX', 'amigopet'); ?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="transaction_id"> esc_html_e('ID da Transação', 'amigopet'); ?></label>
                </th>
                <td>
                    <input name="transaction_id" type="text" id="transaction_id" 
                           value=" echo esc_attr($payment ? $payment->getTransactionId() : ''); ?>" 
                           class="regular-text">
                    <p class="description">
                         esc_html_e('Opcional. Identificador único da transação (para pagamentos online).', 'amigopet'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="notes"> esc_html_e('Observações', 'amigopet'); ?></label>
                </th>
                <td>
                    <textarea name="notes" id="notes" class="large-text" rows="5"> 
                        echo esc_textarea($payment ? $payment->getNotes() : ''); 
                    ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="status"> esc_html_e('Status', 'amigopet'); ?></label>
                </th>
                <td>
                    <select name="status" id="status" required>
                        <option value="pending"  selected($payment ? $payment->getStatus() : 'pending', 'pending'); ?>>
                             esc_html_e('Pendente', 'amigopet'); ?>
                        </option>
                        <option value="completed"  selected($payment ? $payment->getStatus() : '', 'completed'); ?>>
                             esc_html_e('Concluído', 'amigopet'); ?>
                        </option>
                        <option value="cancelled"  selected($payment ? $payment->getStatus() : '', 'cancelled'); ?>>
                             esc_html_e('Cancelado', 'amigopet'); ?>
                        </option>
                        <option value="refunded"  selected($payment ? $payment->getStatus() : '', 'refunded'); ?>>
                             esc_html_e('Reembolsado', 'amigopet'); ?>
                        </option>
                    </select>
                </td>
            </tr>
        </table>

         submit_button(); ?>
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