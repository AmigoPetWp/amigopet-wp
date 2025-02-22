<?php
if (!defined('ABSPATH')) {
    exit;
}

// Verifica permissões
if (!current_user_can('manage_amigopet_donations')) {
    wp_die(__('Você não tem permissão para acessar esta página', 'amigopet-wp'));
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo $donation ? __('Editar Doação', 'amigopet-wp') : __('Nova Doação', 'amigopet-wp'); ?>
    </h1>
    <hr class="wp-header-end">

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="donation-form">
        <input type="hidden" name="action" value="apwp_save_donation">
        <?php wp_nonce_field('apwp_save_donation', 'apwp_donation_nonce'); ?>
        
        <?php if ($donation): ?>
            <input type="hidden" name="donation_id" value="<?php echo esc_attr($donation->getId()); ?>">
        <?php endif; ?>
        
        <input type="hidden" name="organization_id" value="<?php echo esc_attr(get_current_user_id()); ?>">

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="donor_name"><?php _e('Nome do Doador', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" 
                               name="donor_name" 
                               id="donor_name" 
                               class="regular-text" 
                               value="<?php echo $donation ? esc_attr($donation->getDonorName()) : ''; ?>" 
                               required>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="donor_email"><?php _e('Email do Doador', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="email" 
                               name="donor_email" 
                               id="donor_email" 
                               class="regular-text" 
                               value="<?php echo $donation ? esc_attr($donation->getDonorEmail()) : ''; ?>" 
                               required>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="donor_phone"><?php _e('Telefone do Doador', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <input type="tel" 
                               name="donor_phone" 
                               id="donor_phone" 
                               class="regular-text" 
                               value="<?php echo $donation ? esc_attr($donation->getDonorPhone()) : ''; ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="amount"><?php _e('Valor', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="number" 
                               name="amount" 
                               id="amount" 
                               class="regular-text" 
                               step="0.01" 
                               min="0.01"
                               value="<?php echo $donation ? esc_attr($donation->getAmount()) : ''; ?>" 
                               required>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="payment_method"><?php _e('Forma de Pagamento', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select name="payment_method" id="payment_method" required>
                            <option value=""><?php _e('Selecione...', 'amigopet-wp'); ?></option>
                            <?php foreach ($payment_methods as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($donation ? $donation->getPaymentMethod() : '', $value); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <?php if ($donation): ?>
                    <tr>
                        <th scope="row">
                            <label for="status"><?php _e('Status', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <select name="status" id="status" disabled>
                                <?php foreach ($statuses as $value => $label): ?>
                                    <option value="<?php echo esc_attr($value); ?>" <?php selected($donation->getPaymentStatus(), $value); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">
                                <?php _e('O status é atualizado automaticamente conforme o processamento do pagamento.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>

                    <?php if ($donation->getTransactionId()): ?>
                        <tr>
                            <th scope="row">
                                <label><?php _e('ID da Transação', 'amigopet-wp'); ?></label>
                            </th>
                            <td>
                                <code><?php echo esc_html($donation->getTransactionId()); ?></code>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>

                <tr>
                    <th scope="row">
                        <label for="description"><?php _e('Descrição', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <textarea name="description" 
                                  id="description" 
                                  class="large-text" 
                                  rows="5"><?php echo $donation ? esc_textarea($donation->getDescription()) : ''; ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button($donation ? __('Atualizar Doação', 'amigopet-wp') : __('Criar Doação', 'amigopet-wp')); ?>
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
            alert('<?php _e('O valor da doação deve ser maior que zero.', 'amigopet-wp'); ?>');
            return false;
        }
    });
});
