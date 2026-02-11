<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Campos comuns para os formulários de doação
 */
?>

<div class="apwp-form-section">
    <h3><?php esc_html_e('Seus Dados', 'amigopet'); ?></h3>

    <div class="apwp-form-row">
        <label for="donor-name"><?php esc_html_e('Nome Completo', 'amigopet'); ?> <span
                class="required">*</span></label>
        <input type="text" id="donor-name" name="donor_name" required
            value="<?php echo esc_attr(is_user_logged_in() ? wp_get_current_user()->display_name : ''); ?>">
    </div>

    <div class="apwp-form-row">
        <label for="donor-email"><?php esc_html_e('E-mail', 'amigopet'); ?> <span class="required">*</span></label>
        <input type="email" id="donor-email" name="donor_email" required
            value="<?php echo esc_attr(is_user_logged_in() ? wp_get_current_user()->user_email : ''); ?>">
    </div>

    <div class="apwp-form-row">
        <label for="donor-phone"><?php esc_html_e('Telefone', 'amigopet'); ?></label>
        <input type="tel" id="donor-phone" name="donor_phone">
    </div>

    <div class="apwp-form-row">
        <label for="donor-notes"><?php esc_html_e('Observações', 'amigopet'); ?></label>
        <textarea id="donor-notes" name="notes" rows="3"></textarea>
    </div>
</div>