<?php
/**
 * Template para formulário de doação no admin
 * Combina as melhores características de ambas implementações anteriores
 */
if (!defined('ABSPATH')) {
    exit;
}

$donation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $donation_id > 0;
$donation = $is_edit ? get_post($donation_id) : null;

if ($is_edit && (!$donation || $donation->post_type !== 'apwp_donation')) {
    wp_die(__('Doação não encontrada', 'amigopet-wp'));
}

$title = $is_edit ? __('Editar Doação', 'amigopet-wp') : __('Adicionar Nova Doação', 'amigopet-wp');

// Carrega dados da doação se estiver editando
$donation_data = $is_edit ? [
    'donor_name' => get_post_meta($donation_id, 'donor_name', true),
    'donor_email' => get_post_meta($donation_id, 'donor_email', true),
    'donor_phone' => get_post_meta($donation_id, 'donor_phone', true),
    'donation_type' => get_post_meta($donation_id, 'donation_type', true),
    'donation_amount' => get_post_meta($donation_id, 'donation_amount', true),
    'donation_date' => get_post_meta($donation_id, 'donation_date', true),
    'donation_status' => get_post_meta($donation_id, 'donation_status', true),
    'donation_notes' => get_post_meta($donation_id, 'donation_notes', true)
] : [];
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
    <a href="<?php echo remove_query_arg(['action', 'id']); ?>" class="page-title-action">
        <?php _e('Voltar para Lista', 'amigopet-wp'); ?>
    </a>
    
    <div class="apwp-donation-form">
        <form id="apwp-donation-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <?php wp_nonce_field('apwp_save_donation', 'apwp_donation_nonce'); ?>
            <input type="hidden" name="action" value="apwp_save_donation">
            <?php if ($is_edit): ?>
                <input type="hidden" name="donation_id" value="<?php echo $donation_id; ?>">
            <?php endif; ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="donor_name"><?php _e('Nome do Doador', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="donor_name" name="donor_name" class="regular-text" required
                               value="<?php echo esc_attr($donation_data['donor_name'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="donor_email"><?php _e('Email do Doador', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="email" id="donor_email" name="donor_email" class="regular-text" required
                               value="<?php echo esc_attr($donation_data['donor_email'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="donor_phone"><?php _e('Telefone do Doador', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="tel" id="donor_phone" name="donor_phone" class="regular-text" required
                               value="<?php echo esc_attr($donation_data['donor_phone'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="donation_type"><?php _e('Tipo de Doação', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="donation_type" name="donation_type" required>
                            <option value=""><?php _e('Selecione o tipo', 'amigopet-wp'); ?></option>
                            <option value="money" <?php selected($donation_data['donation_type'] ?? '', 'money'); ?>>
                                <?php _e('Dinheiro', 'amigopet-wp'); ?>
                            </option>
                            <option value="food" <?php selected($donation_data['donation_type'] ?? '', 'food'); ?>>
                                <?php _e('Ração', 'amigopet-wp'); ?>
                            </option>
                            <option value="medicine" <?php selected($donation_data['donation_type'] ?? '', 'medicine'); ?>>
                                <?php _e('Medicamentos', 'amigopet-wp'); ?>
                            </option>
                            <option value="supplies" <?php selected($donation_data['donation_type'] ?? '', 'supplies'); ?>>
                                <?php _e('Suprimentos', 'amigopet-wp'); ?>
                            </option>
                            <option value="other" <?php selected($donation_data['donation_type'] ?? '', 'other'); ?>>
                                <?php _e('Outro', 'amigopet-wp'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr id="donation_amount_row">
                    <th scope="row">
                        <label for="donation_amount"><?php _e('Valor/Quantidade', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="donation_amount" name="donation_amount" class="regular-text" required
                               value="<?php echo esc_attr($donation_data['donation_amount'] ?? ''); ?>">
                        <p class="description donation-amount-description"></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="donation_date"><?php _e('Data da Doação', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="date" id="donation_date" name="donation_date" required
                               value="<?php echo esc_attr($donation_data['donation_date'] ?? date('Y-m-d')); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="donation_status"><?php _e('Status', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="donation_status" name="donation_status" required>
                            <option value=""><?php _e('Selecione o status', 'amigopet-wp'); ?></option>
                            <option value="pending" <?php selected($donation_data['donation_status'] ?? '', 'pending'); ?>>
                                <?php _e('Pendente', 'amigopet-wp'); ?>
                            </option>
                            <option value="received" <?php selected($donation_data['donation_status'] ?? '', 'received'); ?>>
                                <?php _e('Recebida', 'amigopet-wp'); ?>
                            </option>
                            <option value="cancelled" <?php selected($donation_data['donation_status'] ?? '', 'cancelled'); ?>>
                                <?php _e('Cancelada', 'amigopet-wp'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="donation_notes"><?php _e('Observações', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <?php
                        wp_editor(
                            $donation_data['donation_notes'] ?? '',
                            'donation_notes',
                            [
                                'textarea_name' => 'donation_notes',
                                'textarea_rows' => 10,
                                'media_buttons' => false,
                                'teeny' => true,
                                'quicktags' => true
                            ]
                        );
                        ?>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php echo $is_edit ? __('Atualizar Doação', 'amigopet-wp') : __('Adicionar Doação', 'amigopet-wp'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<style>
.apwp-donation-form .required {
    color: #dc3232;
}

.apwp-donation-form .error {
    border-color: #dc3232;
}

.apwp-donation-form .description {
    font-style: italic;
    color: #646970;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Validação do formulário
    $('#apwp-donation-form').on('submit', function(e) {
        var $required = $(this).find('[required]');
        var valid = true;
        
        $required.each(function() {
            if (!$(this).val()) {
                valid = false;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert('<?php _e('Por favor, preencha todos os campos obrigatórios.', 'amigopet-wp'); ?>');
        }
    });
    
    // Máscara para telefone
    $('#donor_phone').on('input', function() {
        var phone = $(this).val().replace(/\D/g, '');
        if (phone.length > 0) {
            phone = phone.match(new RegExp('.{1,11}'));
            phone = phone[0].replace(/(\d{2})(\d{4,5})(\d{4})/, '($1) $2-$3');
            $(this).val(phone);
        }
    });
    
    // Atualiza o campo de valor/quantidade baseado no tipo de doação
    function updateAmountField() {
        var type = $('#donation_type').val();
        var $amountField = $('#donation_amount');
        var $description = $('.donation-amount-description');
        
        if (type === 'money') {
            $amountField.attr('type', 'number').attr('step', '0.01').attr('min', '0');
            $description.text('<?php _e('Valor em reais (R$)', 'amigopet-wp'); ?>');
            
            // Formata o valor como moeda
            var value = $amountField.val();
            if (value) {
                value = parseFloat(value.replace(/[^\d.,]/g, '').replace(',', '.'));
                if (!isNaN(value)) {
                    $amountField.val(value.toFixed(2));
                }
            }
        } else {
            $amountField.attr('type', 'text').removeAttr('step').removeAttr('min');
            $description.text('<?php _e('Descreva os itens e quantidades', 'amigopet-wp'); ?>');
        }
    }
    
    $('#donation_type').on('change', updateAmountField);
    updateAmountField(); // Executa na carga inicial
});
</script>
