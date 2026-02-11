<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template para formulário de doação no frontend
 */
?>

<div class="apwp-donation-form-wrapper">
    <h2><?php esc_html_e('Faça uma Doação', 'amigopet'); ?></h2>

    <div class="apwp-donation-intro">
        <p>
            <?php esc_html_e('Ajude-nos a cuidar dos animais! Você pode doar dinheiro, ração, medicamentos ou outros suprimentos.', 'amigopet'); ?>
        </p>
    </div>

    <!-- Abas de tipos de doação -->
    <div class="apwp-donation-tabs">
        <button type="button" class="apwp-tab-button active" data-tab="money">
            <?php esc_html_e('Dinheiro', 'amigopet'); ?>
        </button>
        <button type="button" class="apwp-tab-button" data-tab="food">
            <?php esc_html_e('Ração', 'amigopet'); ?>
        </button>
        <button type="button" class="apwp-tab-button" data-tab="medicine">
            <?php esc_html_e('Medicamentos', 'amigopet'); ?>
        </button>
        <button type="button" class="apwp-tab-button" data-tab="supplies">
            <?php esc_html_e('Suprimentos', 'amigopet'); ?>
        </button>
    </div>

    <!-- Formulário de doação em dinheiro -->
    <div id="apwp-money-form" class="apwp-donation-tab-content active">
        <form id="apwp-money-donation-form" class="apwp-form">
            <?php wp_nonce_field('apwp_submit_money_donation', '_wpnonce'); ?>
            <input type="hidden" name="type" value="money">

            <div class="apwp-form-row">
                <label for="money-amount"><?php esc_html_e('Valor da Doação', 'amigopet'); ?> <span
                        class="required">*</span></label>
                <div class="apwp-amount-buttons">
                    <button type="button" class="apwp-amount-preset" data-amount="10">R$ 10</button>
                    <button type="button" class="apwp-amount-preset" data-amount="20">R$ 20</button>
                    <button type="button" class="apwp-amount-preset" data-amount="50">R$ 50</button>
                    <button type="button" class="apwp-amount-preset" data-amount="100">R$ 100</button>
                </div>
                <input type="number" id="money-amount" name="amount" min="1" step="0.01" required>
            </div>

            <div class="apwp-form-row">
                <label for="money-recurrence"><?php esc_html_e('Frequência', 'amigopet'); ?></label>
                <select id="money-recurrence" name="recurrence">
                    <option value="once"><?php esc_html_e('Única vez', 'amigopet'); ?></option>
                    <option value="monthly"><?php esc_html_e('Mensal', 'amigopet'); ?></option>
                </select>
            </div>

            <?php require 'donation-form-common-fields.php'; ?>

            <div class="apwp-form-row">
                <button type="submit" class="button button-primary">
                    <?php esc_html_e('Doar Agora', 'amigopet'); ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Formulário de doação de ração -->
    <div id="apwp-food-form" class="apwp-donation-tab-content">
        <form id="apwp-food-donation-form" class="apwp-form">
            <?php wp_nonce_field('apwp_submit_food_donation', '_wpnonce'); ?>
            <input type="hidden" name="type" value="food">

            <div class="apwp-form-row">
                <label for="food-description"><?php esc_html_e('Descrição da Ração', 'amigopet'); ?> <span
                        class="required">*</span></label>
                <textarea id="food-description" name="description" rows="3" required
                    placeholder="<?php esc_attr_e('Ex: 10kg de ração premium para cães adultos', 'amigopet'); ?>"></textarea>
            </div>

            <?php require 'donation-form-common-fields.php'; ?>

            <div class="apwp-form-row">
                <button type="submit" class="button button-primary">
                    <?php esc_html_e('Confirmar Doação', 'amigopet'); ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Formulário de doação de medicamentos -->
    <div id="apwp-medicine-form" class="apwp-donation-tab-content">
        <form id="apwp-medicine-donation-form" class="apwp-form">
            <?php wp_nonce_field('apwp_submit_medicine_donation', '_wpnonce'); ?>
            <input type="hidden" name="type" value="medicine">

            <div class="apwp-form-row">
                <label for="medicine-description"><?php esc_html_e('Descrição dos Medicamentos', 'amigopet'); ?> <span
                        class="required">*</span></label>
                <textarea id="medicine-description" name="description" rows="3" required
                    placeholder="<?php esc_attr_e('Ex: 2 caixas de antibiótico, 1 pomada cicatrizante', 'amigopet'); ?>"></textarea>
            </div>

            <?php require 'donation-form-common-fields.php'; ?>

            <div class="apwp-form-row">
                <button type="submit" class="button button-primary">
                    <?php esc_html_e('Confirmar Doação', 'amigopet'); ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Formulário de doação de suprimentos -->
    <div id="apwp-supplies-form" class="apwp-donation-tab-content">
        <form id="apwp-supplies-donation-form" class="apwp-form">
            <?php wp_nonce_field('apwp_submit_supplies_donation', '_wpnonce'); ?>
            <input type="hidden" name="type" value="supplies">

            <div class="apwp-form-row">
                <label for="supplies-description"><?php esc_html_e('Descrição dos Suprimentos', 'amigopet'); ?> <span
                        class="required">*</span></label>
                <textarea id="supplies-description" name="description" rows="3" required
                    placeholder="<?php esc_attr_e('Ex: 3 cobertores, 2 camas para gatos, 1 caixa de areia', 'amigopet'); ?>"></textarea>
            </div>

            <?php require 'donation-form-common-fields.php'; ?>

            <div class="apwp-form-row">
                <button type="submit" class="button button-primary">
                    <?php esc_html_e('Confirmar Doação', 'amigopet'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        // Troca de abas
        $('.apwp-tab-button').on('click', function () {
            var tab = $(this).data('tab');

            $('.apwp-tab-button').removeClass('active');
            $(this).addClass('active');

            $('.apwp-donation-tab-content').removeClass('active');
            $('#apwp-' + tab + '-form').addClass('active');
        });

        // Botões de valor predefinido
        $('.apwp-amount-preset').on('click', function () {
            $('#money-amount').val($(this).data('amount'));
        });

        // Função genérica para submissão dos formulários
        function submitDonationForm($form) {
            var $submit = $form.find(':submit');
            $submit.prop('disabled', true);

            var data = $form.serialize();
            data += '&action=apwp_submit_donation';

            $.post(typeof apwp !== 'undefined' ? apwp.ajaxurl : '/wp-admin/admin-ajax.php', data, function (response) {
                if (response.success) {
                    if (response.data.redirect) {
                        window.location.href = response.data.redirect;
                    } else {
                        $form[0].reset();
                        alert(response.data.message);
                    }
                } else {
                    alert(response.data.message);
                }
                $submit.prop('disabled', false);
            }).fail(function () {
                alert('Erro ao enviar doação');
                $submit.prop('disabled', false);
            });
        }

        // Submissão dos formulários
        $('#apwp-money-donation-form').on('submit', function (e) {
            e.preventDefault();
            submitDonationForm($(this));
        });

        $('#apwp-food-donation-form').on('submit', function (e) {
            e.preventDefault();
            submitDonationForm($(this));
        });

        $('#apwp-medicine-donation-form').on('submit', function (e) {
            e.preventDefault();
            submitDonationForm($(this));
        });

        $('#apwp-supplies-donation-form').on('submit', function (e) {
            e.preventDefault();
            submitDonationForm($(this));
        });
    });
</script>