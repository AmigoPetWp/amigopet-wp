<?php
/**
 * Campos comuns para todos os formulários de doação
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="apwp-form-row">
    <label for="donor-name"><?php _e('Nome', 'amigopet-wp'); ?> <span class="required">*</span></label>
    <input type="text" id="donor-name" name="donor_name" required>
</div>

<div class="apwp-form-row">
    <label for="donor-email"><?php _e('E-mail', 'amigopet-wp'); ?></label>
    <input type="email" id="donor-email" name="donor_email">
    <p class="description">
        <?php _e('Opcional. Será usado apenas para enviar o comprovante de doação.', 'amigopet-wp'); ?>
    </p>
</div>

<div class="apwp-form-row">
    <label for="donor-phone"><?php _e('Telefone', 'amigopet-wp'); ?></label>
    <input type="tel" id="donor-phone" name="donor_phone">
    <p class="description">
        <?php _e('Opcional. Será usado apenas em caso de necessidade de contato.', 'amigopet-wp'); ?>
    </p>
</div>

<div class="apwp-form-row">
    <label for="notes"><?php _e('Observações', 'amigopet-wp'); ?></label>
    <textarea id="notes" name="notes" rows="3"></textarea>
</div>

<div class="apwp-form-row">
    <label>
        <input type="checkbox" name="anonymous" value="1">
        <?php _e('Fazer doação anônima', 'amigopet-wp'); ?>
    </label>
</div>
