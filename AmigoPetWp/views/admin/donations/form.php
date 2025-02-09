<?php
/**
 * Template para formulário de doação no admin
 */
if (!defined('ABSPATH')) {
    exit;
}

$donation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $donation_id > 0;
$title = $is_edit ? __('Editar Doação', 'amigopet-wp') : __('Adicionar Nova Doação', 'amigopet-wp');
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
    <a href="<?php echo remove_query_arg(['action', 'id']); ?>" class="page-title-action">
        <?php _e('Voltar para Lista', 'amigopet-wp'); ?>
    </a>
    
    <div class="apwp-donation-form">
        <form id="apwp-donation-form" method="post">
            <?php wp_nonce_field('apwp_save_donation', '_wpnonce'); ?>
            <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit' : 'add'; ?>">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?php echo $donation_id; ?>">
            <?php endif; ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="donor_name"><?php _e('Nome do Doador', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="donor_name" name="donor_name" class="regular-text" required
                            value="<?php echo $is_edit ? esc_attr($donation->donor_name) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="donor_email"><?php _e('E-mail do Doador', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <input type="email" id="donor_email" name="donor_email" class="regular-text"
                            value="<?php echo $is_edit ? esc_attr($donation->donor_email) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="donor_phone"><?php _e('Telefone do Doador', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <input type="tel" id="donor_phone" name="donor_phone" class="regular-text"
                            value="<?php echo $is_edit ? esc_attr($donation->donor_phone) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="type"><?php _e('Tipo de Doação', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="type" name="type" required>
                            <option value="money" <?php selected($is_edit && $donation->type == 'money'); ?>>
                                <?php _e('Dinheiro', 'amigopet-wp'); ?>
                            </option>
                            <option value="food" <?php selected($is_edit && $donation->type == 'food'); ?>>
                                <?php _e('Ração', 'amigopet-wp'); ?>
                            </option>
                            <option value="medicine" <?php selected($is_edit && $donation->type == 'medicine'); ?>>
                                <?php _e('Medicamentos', 'amigopet-wp'); ?>
                            </option>
                            <option value="supplies" <?php selected($is_edit && $donation->type == 'supplies'); ?>>
                                <?php _e('Suprimentos', 'amigopet-wp'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr id="amount-row" style="display: none;">
                    <th scope="row">
                        <label for="amount"><?php _e('Valor', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="number" id="amount" name="amount" class="regular-text" step="0.01" min="0"
                            value="<?php echo $is_edit ? esc_attr($donation->amount) : ''; ?>">
                    </td>
                </tr>
                
                <tr id="description-row" style="display: none;">
                    <th scope="row">
                        <label for="description"><?php _e('Descrição', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <textarea id="description" name="description" class="large-text" rows="5"><?php
                            echo $is_edit ? esc_textarea($donation->description) : '';
                        ?></textarea>
                        <p class="description">
                            <?php _e('Descreva os itens doados, incluindo quantidade e especificações.', 'amigopet-wp'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="date"><?php _e('Data', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="date" id="date" name="date" required
                            value="<?php echo $is_edit ? esc_attr($donation->date) : date('Y-m-d'); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="status"><?php _e('Status', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="status" name="status" required>
                            <option value="pending" <?php selected($is_edit && $donation->status == 'pending'); ?>>
                                <?php _e('Pendente', 'amigopet-wp'); ?>
                            </option>
                            <option value="confirmed" <?php selected($is_edit && $donation->status == 'confirmed'); ?>>
                                <?php _e('Confirmada', 'amigopet-wp'); ?>
                            </option>
                            <option value="canceled" <?php selected($is_edit && $donation->status == 'canceled'); ?>>
                                <?php _e('Cancelada', 'amigopet-wp'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="notes"><?php _e('Observações', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <textarea id="notes" name="notes" class="large-text" rows="5"><?php
                            echo $is_edit ? esc_textarea($donation->notes) : '';
                        ?></textarea>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php echo $is_edit ? __('Atualizar', 'amigopet-wp') : __('Adicionar', 'amigopet-wp'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Toggle campos baseado no tipo de doação
    function toggleFields() {
        var type = $('#type').val();
        
        if (type === 'money') {
            $('#amount-row').show();
            $('#description-row').hide();
            $('#amount').prop('required', true);
            $('#description').prop('required', false);
        } else {
            $('#amount-row').hide();
            $('#description-row').show();
            $('#amount').prop('required', false);
            $('#description').prop('required', true);
        }
    }
    
    $('#type').on('change', toggleFields);
    toggleFields();
    
    // Validação do formulário
    $('#apwp-donation-form').on('submit', function(e) {
        var $form = $(this);
        var $submit = $form.find(':submit');
        
        $submit.prop('disabled', true);
        
        var data = $form.serialize();
        data += '&action=apwp_save_donation';
        
        $.post(apwp.ajax_url, data, function(response) {
            if (response.success) {
                window.location.href = response.data.redirect;
            } else {
                alert(response.data.message);
                $submit.prop('disabled', false);
            }
        }).fail(function() {
            alert(apwp.i18n.error_saving);
            $submit.prop('disabled', false);
        });
        
        e.preventDefault();
    });
});
</script>
