<?php
/**
 * Template para formulário de voluntariado no frontend
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="apwp-volunteer-form-wrapper">
    <h2><?php _e('Seja um Voluntário', 'amigopet-wp'); ?></h2>
    
    <div class="apwp-volunteer-intro">
        <p>
            <?php _e('Junte-se a nós e faça a diferença na vida dos animais! Preencha o formulário abaixo para se candidatar como voluntário.', 'amigopet-wp'); ?>
        </p>
    </div>
    
    <form id="apwp-volunteer-form" class="apwp-form">
        <?php wp_nonce_field('apwp_submit_volunteer', '_wpnonce'); ?>
        
        <div class="apwp-form-row">
            <label for="name"><?php _e('Nome Completo', 'amigopet-wp'); ?> <span class="required">*</span></label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="apwp-form-row">
            <label for="email"><?php _e('E-mail', 'amigopet-wp'); ?> <span class="required">*</span></label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="apwp-form-row">
            <label for="phone"><?php _e('Telefone', 'amigopet-wp'); ?> <span class="required">*</span></label>
            <input type="tel" id="phone" name="phone" required>
        </div>
        
        <div class="apwp-form-row">
            <label for="area_id"><?php _e('Área de Interesse', 'amigopet-wp'); ?> <span class="required">*</span></label>
            <select id="area_id" name="area_id" required>
                <option value=""><?php _e('Selecione uma área', 'amigopet-wp'); ?></option>
                <?php foreach ($areas as $area): ?>
                    <option value="<?php echo esc_attr($area->id); ?>">
                        <?php echo esc_html($area->name); ?>
                        <?php if (!empty($area->description)): ?>
                            - <?php echo esc_html($area->description); ?>
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="apwp-form-row">
            <label><?php _e('Disponibilidade', 'amigopet-wp'); ?> <span class="required">*</span></label>
            <div class="apwp-checkbox-group">
                <label>
                    <input type="checkbox" name="availability[]" value="morning">
                    <?php _e('Manhã', 'amigopet-wp'); ?>
                </label>
                <label>
                    <input type="checkbox" name="availability[]" value="afternoon">
                    <?php _e('Tarde', 'amigopet-wp'); ?>
                </label>
                <label>
                    <input type="checkbox" name="availability[]" value="night">
                    <?php _e('Noite', 'amigopet-wp'); ?>
                </label>
                <label>
                    <input type="checkbox" name="availability[]" value="weekend">
                    <?php _e('Fim de semana', 'amigopet-wp'); ?>
                </label>
            </div>
        </div>
        
        <div class="apwp-form-row">
            <label for="experience"><?php _e('Experiência', 'amigopet-wp'); ?></label>
            <textarea id="experience" name="experience" rows="5" placeholder="<?php esc_attr_e('Conte-nos sobre sua experiência com animais ou na área escolhida...', 'amigopet-wp'); ?>"></textarea>
        </div>
        
        <div class="apwp-form-row">
            <label>
                <input type="checkbox" name="terms" required>
                <?php
                printf(
                    /* translators: %s: link para os termos */
                    __('Li e concordo com os %s', 'amigopet-wp'),
                    '<a href="#" class="apwp-show-terms">' . __('termos do voluntariado', 'amigopet-wp') . '</a>'
                );
                ?>
            </label>
        </div>
        
        <div class="apwp-form-row">
            <button type="submit" class="button button-primary">
                <?php _e('Enviar Candidatura', 'amigopet-wp'); ?>
            </button>
        </div>
    </form>
</div>

<!-- Modal de Termos -->
<div id="apwp-terms-modal" class="apwp-modal" style="display: none;">
    <div class="apwp-modal-content">
        <span class="apwp-modal-close">&times;</span>
        <h3><?php _e('Termos do Voluntariado', 'amigopet-wp'); ?></h3>
        <div class="apwp-terms-content">
            <?php echo wp_kses_post($terms_content); ?>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Validação de disponibilidade
    function validateAvailability() {
        var checked = $('input[name="availability[]"]:checked').length;
        if (checked === 0) {
            alert(apwp.i18n.select_availability);
            return false;
        }
        return true;
    }
    
    // Modal de termos
    $('.apwp-show-terms').on('click', function(e) {
        e.preventDefault();
        $('#apwp-terms-modal').show();
    });
    
    $('.apwp-modal-close').on('click', function() {
        $(this).closest('.apwp-modal').hide();
    });
    
    $(window).on('click', function(e) {
        if ($(e.target).hasClass('apwp-modal')) {
            $('.apwp-modal').hide();
        }
    });
    
    // Envio do formulário
    $('#apwp-volunteer-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateAvailability()) {
            return;
        }
        
        var $form = $(this);
        var $submit = $form.find(':submit');
        
        $submit.prop('disabled', true);
        
        var data = $form.serialize();
        data += '&action=apwp_submit_volunteer';
        
        $.post(apwp.ajax_url, data, function(response) {
            if (response.success) {
                $form[0].reset();
                alert(response.data.message);
            } else {
                alert(response.data.message);
            }
            $submit.prop('disabled', false);
        }).fail(function() {
            alert(apwp.i18n.error_submitting);
            $submit.prop('disabled', false);
        });
    });
});
</script>
