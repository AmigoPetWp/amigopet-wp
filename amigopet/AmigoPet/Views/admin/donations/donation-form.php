<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

// Verifica permissões
if (!current_user_can('manage_amigopet_donations')) {
    wp_die(esc_html__('Você não tem permissão para acessar esta página', 'amigopet'));
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
         echo $donation ? esc_html__('Editar Doação', 'amigopet') : esc_html__('Nova Doação', 'amigopet'); ?>
    </h1>
    <hr class="wp-header-end">

    <form method="post" action=" echo esc_url(admin_url('admin-post.php')); ?>" class="donation-form">
        <input type="hidden" name="action" value="apwp_save_donation">
         wp_nonce_field('apwp_save_donation', 'apwp_donation_nonce'); ?>
        
         if ($donation): ?>
            <input type="hidden" name="donation_id" value=" echo esc_attr($donation->getId()); ?>">
         endif; ?>
        
        <input type="hidden" name="organization_id" value=" echo esc_attr(get_current_user_id()); ?>">

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="donor_name"> esc_html_e('Nome do Doador', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" 
                               name="donor_name" 
                               id="donor_name" 
                               class="regular-text" 
                               value=" echo $donation ? esc_attr($donation->getDonorName()) : ''; ?>" 
                               required>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="donor_email"> esc_html_e('Email do Doador', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="email" 
                               name="donor_email" 
                               id="donor_email" 
                               class="regular-text" 
                               value=" echo $donation ? esc_attr($donation->getDonorEmail()) : ''; ?>" 
                               required>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="donor_phone"> esc_html_e('Telefone do Doador', 'amigopet'); ?></label>
                    </th>
                    <td>
                        <input type="tel" 
                               name="donor_phone" 
                               id="donor_phone" 
                               class="regular-text" 
                               value=" echo $donation ? esc_attr($donation->getDonorPhone()) : ''; ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="amount"> esc_html_e('Valor', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="number" 
                               name="amount" 
                               id="amount" 
                               class="regular-text" 
                               step="0.01" 
                               min="0.01"
                               value=" echo $donation ? esc_attr($donation->getAmount()) : ''; ?>" 
                               required>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="payment_method"> esc_html_e('Forma de Pagamento', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select name="payment_method" id="payment_method" required>
                            <option value=""> esc_html_e('Selecione...', 'amigopet'); ?></option>
                             foreach ($payment_methods as $value => $label): ?>
                                <option value=" echo esc_attr($value); ?>"  selected($donation ? $donation->getPaymentMethod() : '', $value); ?>>
                                     echo esc_html($label); ?>
                                </option>
                             endforeach; ?>
                        </select>
                    </td>
                </tr>

                 if ($donation): ?>
                    <tr>
                        <th scope="row">
                            <label for="status"> esc_html_e('Status', 'amigopet'); ?></label>
                        </th>
                        <td>
                            <select name="status" id="status" disabled>
                                 foreach ($statuses as $value => $label): ?>
                                    <option value=" echo esc_attr($value); ?>"  selected($donation->getPaymentStatus(), $value); ?>>
                                         echo esc_html($label); ?>
                                    </option>
                                 endforeach; ?>
                            </select>
                            <p class="description">
                                 esc_html_e('O status é atualizado automaticamente conforme o processamento do pagamento.', 'amigopet'); ?>
                            </p>
                        </td>
                    </tr>

                     if ($donation->getTransactionId()): ?>
                        <tr>
                            <th scope="row">
                                <label> esc_html_e('ID da Transação', 'amigopet'); ?></label>
                            </th>
                            <td>
                                <code> echo esc_html($donation->getTransactionId()); ?></code>
                            </td>
                        </tr>
                     endif; ?>
                 endif; ?>

                <tr>
                    <th scope="row">
                        <label for="description"> esc_html_e('Descrição', 'amigopet'); ?></label>
                    </th>
                    <td>
                        <textarea name="description" 
                                  id="description" 
                                  class="large-text" 
                                  rows="5"> echo $donation ? esc_textarea($donation->getDescription()) : ''; ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>

         submit_button($donation ? esc_html__('Atualizar Doação', 'amigopet') : esc_html__('Criar Doação', 'amigopet')); ?>
    </form>
</div>

<style>
.donation-form .required {
    color: #dc3232;
}

.donation-form input[type="number"] {
    width: 150px;
}

.donation-form select {
    min-width: 200px;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('#donor_phone').mask('(00) 00000-0000');
    
    $('.donation-form').on('submit', function(e) {
        var amount = parseFloat($('#amount').val());
        if (amount <= 0) {
            e.preventDefault();
            alert(' esc_html_e('O valor da doação deve ser maior que zero.', 'amigopet'); ?>');
            return false;
        }
    });
});