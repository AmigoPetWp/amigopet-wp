<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo $donation ? __('Editar Doação', 'amigopet-wp') : __('Adicionar Nova Doação', 'amigopet-wp'); ?></h1>
    
    <form method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('apwp_save_donation', 'apwp_donation_nonce'); ?>
        <input type="hidden" name="donation_id" value="<?php echo $donation ? $donation->ID : ''; ?>">
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="donor_name"><?php _e('Nome do Doador', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="text" id="donor_name" name="donor_name" class="regular-text" value="<?php echo $donation ? esc_attr(get_post_meta($donation->ID, 'donor_name', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="donor_email"><?php _e('Email do Doador', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="email" id="donor_email" name="donor_email" class="regular-text" value="<?php echo $donation ? esc_attr(get_post_meta($donation->ID, 'donor_email', true)) : ''; ?>">
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="donor_phone"><?php _e('Telefone do Doador', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="tel" id="donor_phone" name="donor_phone" class="regular-text" value="<?php echo $donation ? esc_attr(get_post_meta($donation->ID, 'donor_phone', true)) : ''; ?>">
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="donation_type"><?php _e('Tipo de Doação', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="donation_type" name="donation_type" required>
                        <option value=""><?php _e('Selecione...', 'amigopet-wp'); ?></option>
                        <option value="money" <?php selected($donation ? get_post_meta($donation->ID, 'donation_type', true) : '', 'money'); ?>><?php _e('Dinheiro', 'amigopet-wp'); ?></option>
                        <option value="food" <?php selected($donation ? get_post_meta($donation->ID, 'donation_type', true) : '', 'food'); ?>><?php _e('Ração', 'amigopet-wp'); ?></option>
                        <option value="medicine" <?php selected($donation ? get_post_meta($donation->ID, 'donation_type', true) : '', 'medicine'); ?>><?php _e('Medicamentos', 'amigopet-wp'); ?></option>
                        <option value="supplies" <?php selected($donation ? get_post_meta($donation->ID, 'donation_type', true) : '', 'supplies'); ?>><?php _e('Suprimentos', 'amigopet-wp'); ?></option>
                        <option value="other" <?php selected($donation ? get_post_meta($donation->ID, 'donation_type', true) : '', 'other'); ?>><?php _e('Outro', 'amigopet-wp'); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr class="donation-amount" style="display: none;">
                <th scope="row">
                    <label for="donation_amount"><?php _e('Valor da Doação', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="number" id="donation_amount" name="donation_amount" class="regular-text" step="0.01" min="0" value="<?php echo $donation ? esc_attr(get_post_meta($donation->ID, 'donation_amount', true)) : ''; ?>">
                </td>
            </tr>
            
            <tr class="donation-items" style="display: none;">
                <th scope="row">
                    <label for="donation_items"><?php _e('Itens Doados', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <textarea id="donation_items" name="donation_items" class="large-text" rows="5"><?php echo $donation ? esc_textarea(get_post_meta($donation->ID, 'donation_items', true)) : ''; ?></textarea>
                    <p class="description"><?php _e('Liste os itens doados, um por linha', 'amigopet-wp'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="donation_date"><?php _e('Data da Doação', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="date" id="donation_date" name="donation_date" value="<?php echo $donation ? esc_attr(get_post_meta($donation->ID, 'donation_date', true)) : date('Y-m-d'); ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="donation_status"><?php _e('Status', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="donation_status" name="donation_status" required>
                        <option value="pending" <?php selected($donation ? get_post_meta($donation->ID, 'donation_status', true) : '', 'pending'); ?>><?php _e('Pendente', 'amigopet-wp'); ?></option>
                        <option value="received" <?php selected($donation ? get_post_meta($donation->ID, 'donation_status', true) : '', 'received'); ?>><?php _e('Recebida', 'amigopet-wp'); ?></option>
                        <option value="cancelled" <?php selected($donation ? get_post_meta($donation->ID, 'donation_status', true) : '', 'cancelled'); ?>><?php _e('Cancelada', 'amigopet-wp'); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="donation_notes"><?php _e('Observações', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <textarea id="donation_notes" name="donation_notes" class="large-text" rows="5"><?php echo $donation ? esc_textarea(get_post_meta($donation->ID, 'donation_notes', true)) : ''; ?></textarea>
                </td>
            </tr>
        </table>
        
        <?php submit_button($donation ? __('Atualizar Doação', 'amigopet-wp') : __('Adicionar Doação', 'amigopet-wp')); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    function toggleDonationFields() {
        var type = $('#donation_type').val();
        if (type === 'money') {
            $('.donation-amount').show();
            $('.donation-items').hide();
        } else if (type !== '') {
            $('.donation-amount').hide();
            $('.donation-items').show();
        } else {
            $('.donation-amount, .donation-items').hide();
        }
    }
    
    $('#donation_type').on('change', toggleDonationFields);
    toggleDonationFields();
});
</script>
