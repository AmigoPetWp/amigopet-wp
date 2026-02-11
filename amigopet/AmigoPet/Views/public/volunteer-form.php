<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template para formulário de voluntariado no frontend
 */

$areas = isset($areas) ? $areas : [];
$terms_content = isset($terms_content) ? $terms_content : '';
?>

<div class="apwp-volunteer-form-wrapper">
    <h2><?php esc_html_e('Seja um Voluntário', 'amigopet'); ?></h2>

    <div class="apwp-volunteer-intro">
        <p>
            <?php esc_html_e('Junte-se a nós e faça a diferença na vida dos animais! Preencha o formulário abaixo para se candidatar como voluntário.', 'amigopet'); ?>
        </p>
    </div>

    <form id="apwp-volunteer-form" class="apwp-form">
        <?php wp_nonce_field('apwp_submit_volunteer', '_wpnonce'); ?>

        <div class="apwp-form-row">
            <label for="name"><?php esc_html_e('Nome Completo', 'amigopet'); ?> <span class="required">*</span></label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="apwp-form-row">
            <label for="email"><?php esc_html_e('E-mail', 'amigopet'); ?> <span class="required">*</span></label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="apwp-form-row">
            <label for="phone"><?php esc_html_e('Telefone', 'amigopet'); ?> <span class="required">*</span></label>
            <input type="tel" id="phone" name="phone" required>
        </div>

        <div class="apwp-form-row">
            <label for="area_id"><?php esc_html_e('Área de Interesse', 'amigopet'); ?> <span
                    class="required">*</span></label>
            <select id="area_id" name="area_id" required>
                <option value=""><?php esc_html_e('Selecione uma área', 'amigopet'); ?></option>
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
            <label><?php esc_html_e('Disponibilidade', 'amigopet'); ?> <span class="required">*</span></label>
            <div class="apwp-checkbox-group">
                <label>
                    <input type="checkbox" name="availability[]" value="morning">
                    <?php esc_html_e('Manhã', 'amigopet'); ?>
                </label>
                <label>
                    <input type="checkbox" name="availability[]" value="afternoon">
                    <?php esc_html_e('Tarde', 'amigopet'); ?>
                </label>
                <label>
                    <input type="checkbox" name="availability[]" value="night">
                    <?php esc_html_e('Noite', 'amigopet'); ?>
                </label>
                <label>
                    <input type="checkbox" name="availability[]" value="weekend">
                    <?php esc_html_e('Fim de semana', 'amigopet'); ?>
                </label>
            </div>
        </div>

        <div class="apwp-form-row">
            <label for="experience"><?php esc_html_e('Experiência', 'amigopet'); ?></label>
            <textarea id="experience" name="experience" rows="5"
                placeholder="<?php esc_attr_e('Conte-nos sobre sua experiência com animais ou na área escolhida...', 'amigopet'); ?>"></textarea>
        </div>

        <div class="apwp-form-row">
            <label>
                <input type="checkbox" name="terms" required>
                <?php
                printf(
                    /* translators: %s: link para os termos */
                    esc_html__('Li e concordo com os %s', 'amigopet'),
                    '<a href="#" class="apwp-show-terms">' . esc_html__('termos do voluntariado', 'amigopet') . '</a>'
                );
                ?>
            </label>
        </div>

        <div class="apwp-form-row">
            <button type="submit" class="button button-primary">
                <?php esc_html_e('Enviar Candidatura', 'amigopet'); ?>
            </button>
        </div>
    </form>
</div>

<!-- Modal de Termos -->
<div id="apwp-terms-modal" class="apwp-modal" style="display: none;">
    <div class="apwp-modal-content">
        <span class="apwp-modal-close">&times;</span>
        <h3><?php esc_html_e('Termos do Voluntariado', 'amigopet'); ?></h3>
        <div class="apwp-terms-content">
            <?php echo wp_kses_post($terms_content); ?>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        // Validação de disponibilidade
        function validateAvailability() {
            var checked = $('input[name="availability[]"]:checked').length;
            if (checked === 0) {
                alert('Por favor, selecione pelo menos um horário de disponibilidade');
                return false;
            }
            return true;
        }

        // Modal de termos
        $('.apwp-show-terms').on('click', function (e) {
            e.preventDefault();
            $('#apwp-terms-modal').show();
        });

        $('.apwp-modal-close').on('click', function () {
            $(this).closest('.apwp-modal').hide();
        });

        $(window).on('click', function (e) {
            if ($(e.target).hasClass('apwp-modal')) {
                $('.apwp-modal').hide();
            }
        });

        // Envio do formulário
        $('#apwp-volunteer-form').on('submit', function (e) {
            e.preventDefault();

            if (!validateAvailability()) {
                return;
            }

            var $form = $(this);
            var $submit = $form.find(':submit');

            $submit.prop('disabled', true);

            var data = $form.serialize();
            data += '&action=apwp_submit_volunteer';

            $.post(typeof apwp !== 'undefined' ? apwp.ajax_url : '/wp-admin/admin-ajax.php', data, function (response) {
                if (response.success) {
                    $form[0].reset();
                    alert(response.data.message);
                } else {
                    alert(response.data.message);
                }
                $submit.prop('disabled', false);
            }).fail(function () {
                alert('Erro ao enviar candidatura');
                $submit.prop('disabled', false);
            });
        });
    });
</script>