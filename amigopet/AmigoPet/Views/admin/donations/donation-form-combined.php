<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template para formulário de doação no admin
 * Combina as melhores características de ambas implementações anteriores
 */


$donation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $donation_id > 0;
$donation = $is_edit ? get_post($donation_id) : null;

if ($is_edit && (!$donation || $donation->post_type !== 'apwp_donation')) {
    wp_die(esc_html__('Doação não encontrada', 'amigopet'));
}

$title = $is_edit ? esc_html__('Editar Doação', 'amigopet') : esc_html__('Adicionar Nova Doação', 'amigopet');

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
    <h1 class="wp-heading-inline"> echo esc_html($title); ?></h1>
    <a href=" echo esc_url(remove_query_arg(['action', 'id']); ?>" class="page-title-action">
         esc_html_e('Voltar para Lista', 'amigopet'); ?>
    </a>
    
    <div class="apwp-donation-form">
        <form id="apwp-donation-form" method="post" action=" echo esc_url(admin_url('admin-post.php'); ?>">
             wp_nonce_field('apwp_save_donation', 'apwp_donation_nonce'); ?>
            <input type="hidden" name="action" value="apwp_save_donation">
             if ($is_edit): ?>
                <input type="hidden" name="donation_id" value=" echo esc_attr($donation_id); ?>">
             endif; ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="donor_name"> esc_html_e('Nome do Doador', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="donor_name" name="donor_name" class="regular-text" required
                               value=" echo esc_attr($donation_data['donor_name'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="donor_email"> esc_html_e('Email do Doador', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="email" id="donor_email" name="donor_email" class="regular-text" required
                               value=" echo esc_attr($donation_data['donor_email'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="donor_phone"> esc_html_e('Telefone do Doador', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="tel" id="donor_phone" name="donor_phone" class="regular-text" required
                               value=" echo esc_attr($donation_data['donor_phone'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="donation_type"> esc_html_e('Tipo de Doação', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="donation_type" name="donation_type" required>
                            <option value=""> esc_html_e('Selecione o tipo', 'amigopet'); ?></option>
                            <option value="money"  selected($donation_data['donation_type'] ?? '', 'money'); ?>>
                                 esc_html_e('Dinheiro', 'amigopet'); ?>
                            </option>
                            <option value="food"  selected($donation_data['donation_type'] ?? '', 'food'); ?>>
                                 esc_html_e('Ração', 'amigopet'); ?>
                            </option>
                            <option value="medicine"  selected($donation_data['donation_type'] ?? '', 'medicine'); ?>>
                                 esc_html_e('Medicamentos', 'amigopet'); ?>
                            </option>
                            <option value="supplies"  selected($donation_data['donation_type'] ?? '', 'supplies'); ?>>
                                 esc_html_e('Suprimentos', 'amigopet'); ?>
                            </option>
                            <option value="other"  selected($donation_data['donation_type'] ?? '', 'other'); ?>>
                                 esc_html_e('Outro', 'amigopet'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr id="donation_amount_row">
                    <th scope="row">
                        <label for="donation_amount"> esc_html_e('Valor/Quantidade', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="donation_amount" name="donation_amount" class="regular-text" required
                               value=" echo esc_attr($donation_data['donation_amount'] ?? ''); ?>">
                        <p class="description donation-amount-description"></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="donation_date"> esc_html_e('Data da Doação', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="date" id="donation_date" name="donation_date" required
                               value=" echo esc_attr($donation_data['donation_date'] ?? gmdate('Y-m-d')); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="donation_status"> esc_html_e('Status', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="donation_status" name="donation_status" required>
                            <option value=""> esc_html_e('Selecione o status', 'amigopet'); ?></option>
                            <option value="pending"  selected($donation_data['donation_status'] ?? '', 'pending'); ?>>
                                 esc_html_e('Pendente', 'amigopet'); ?>
                            </option>
                            <option value="received"  selected($donation_data['donation_status'] ?? '', 'received'); ?>>
                                 esc_html_e('Recebida', 'amigopet'); ?>
                            </option>
                            <option value="cancelled"  selected($donation_data['donation_status'] ?? '', 'cancelled'); ?>>
                                 esc_html_e('Cancelada', 'amigopet'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="donation_notes"> esc_html_e('Observações', 'amigopet'); ?></label>
                    </th>
                    <td>
                        
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
                     echo $is_edit ? esc_html__('Atualizar Doação', 'amigopet') : esc_html__('Adicionar Doação', 'amigopet'); ?>
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
            alert(' esc_html_e('Por favor, preencha todos os campos obrigatórios.', 'amigopet'); ?>');
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
            $description.text(' esc_html_e('Valor em reais (R$)', 'amigopet'); ?>');
            
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
            $description.text(' esc_html_e('Descreva os itens e quantidades', 'amigopet'); ?>');
        }
    }
    
    $('#donation_type').on('change', updateAmountField);
    updateAmountField(); // Executa na carga inicial
});
</script>