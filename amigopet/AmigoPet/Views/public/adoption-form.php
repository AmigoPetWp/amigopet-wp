<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * View do formulário de adoção
 */

// Extrai os atributos do shortcode
$pet_id = isset($atts['pet_id']) ? (int) $atts['pet_id'] : 0;

// Se não tiver pet_id, mostra mensagem de erro
if (!$pet_id) {
    ?>
    <div class="apwp-error">
        <?php esc_html_e('Pet não encontrado.', 'amigopet'); ?>
    </div>
    <?php
    return;
}

// Se o usuário não estiver logado, mostra mensagem para fazer login
if (!is_user_logged_in()) {
    ?>
    <div class="apwp-login-required">
        <p>
            <?php esc_html_e('Você precisa estar logado para adotar um pet.', 'amigopet'); ?>
        </p>
        <p>
            <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="button">
                <?php esc_html_e('Fazer Login', 'amigopet'); ?>
            </a>

            <a href="<?php echo esc_url(wp_registration_url()); ?>" class="button">
                <?php esc_html_e('Criar Conta', 'amigopet'); ?>
            </a>
        </p>
    </div>
    <?php
    return;
}

// Pega os dados do pet
$pet = get_post($pet_id);
if (!$pet || $pet->post_type !== 'pet') {
    ?>
    <div class="apwp-error">
        <?php esc_html_e('Pet não encontrado.', 'amigopet'); ?>
    </div>
    <?php
    return;
}

// Pega os dados do usuário atual
$current_user = wp_get_current_user();
?>

<div class="apwp-adoption-form">
    <h2>
        <?php esc_html_e('Formulário de Adoção', 'amigopet'); ?>
    </h2>

    <!-- Dados do pet -->
    <div class="apwp-pet-preview">
        <div class="apwp-pet-image">
            <?php if (has_post_thumbnail($pet)): ?>
                <?php echo get_the_post_thumbnail($pet, 'medium'); ?>
            <?php else: ?>
                <div class="apwp-pet-no-image">
                    <i class="dashicons dashicons-pets"></i>
                </div>
            <?php endif; ?>
        </div>

        <div class="apwp-pet-info">
            <h3>
                <?php echo esc_html($pet->post_title); ?>
            </h3>

            <div class="apwp-pet-meta">
                <?php
                // Espécie
                $species = wp_get_post_terms($pet->ID, 'pet_species');
                if (!empty($species) && !is_wp_error($species)) {
                    ?>
                    <span class="apwp-pet-species">
                        <i class="dashicons dashicons-category"></i>
                        <?php echo esc_html($species[0]->name); ?>
                    </span>
                    <?php
                }

                // Raça
                $breed = wp_get_post_terms($pet->ID, 'pet_breed');
                if (!empty($breed) && !is_wp_error($breed)) {
                    ?>
                    <span class="apwp-pet-breed">
                        <i class="dashicons dashicons-tag"></i>
                        <?php echo esc_html($breed[0]->name); ?>
                    </span>
                    <?php
                }

                // Idade
                $age = get_post_meta($pet->ID, 'age', true);
                if ($age) {
                    ?>
                    <span class="apwp-pet-age">
                        <i class="dashicons dashicons-calendar"></i>
                        <?php echo esc_html($age); ?>
                    </span>
                    <?php
                }

                // Tamanho
                $size = get_post_meta($pet->ID, 'size', true);
                if ($size) {
                    ?>
                    <span class="apwp-pet-size">
                        <i class="dashicons dashicons-editor-expand"></i>
                        <?php
                        switch ($size) {
                            case 'small':
                                esc_html_e('Pequeno', 'amigopet');
                                break;
                            case 'medium':
                                esc_html_e('Médio', 'amigopet');
                                break;
                            case 'large':
                                esc_html_e('Grande', 'amigopet');
                                break;
                        }
                        ?>
                    </span>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Formulário -->
    <form id="apwp-adoption-form">
        <input type="hidden" name="pet_id" value="<?php echo esc_attr($pet_id); ?>">

        <div class="apwp-form-section">
            <h3>
                <?php esc_html_e('Seus Dados', 'amigopet'); ?>
            </h3>

            <div class="apwp-form-row">
                <div class="apwp-form-field">
                    <label for="name">
                        <?php esc_html_e('Nome Completo', 'amigopet'); ?>
                    </label>
                    <input type="text" id="name" name="name"
                        value="<?php echo esc_attr($current_user->display_name); ?>" required>
                </div>

                <div class="apwp-form-field">
                    <label for="email">
                        <?php esc_html_e('Email', 'amigopet'); ?>
                    </label>
                    <input type="email" id="email" name="email"
                        value="<?php echo esc_attr($current_user->user_email); ?>" required>
                </div>
            </div>

            <div class="apwp-form-row">
                <div class="apwp-form-field">
                    <label for="phone">
                        <?php esc_html_e('Telefone', 'amigopet'); ?>
                    </label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="apwp-form-field">
                    <label for="cpf">
                        <?php esc_html_e('CPF', 'amigopet'); ?>
                    </label>
                    <input type="text" id="cpf" name="cpf" required>
                </div>
            </div>

            <div class="apwp-form-row">
                <div class="apwp-form-field">
                    <label for="address">
                        <?php esc_html_e('Endereço Completo', 'amigopet'); ?>
                    </label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>
            </div>
        </div>

        <div class="apwp-form-section">
            <h3>
                <?php esc_html_e('Informações Adicionais', 'amigopet'); ?>
            </h3>

            <div class="apwp-form-row">
                <div class="apwp-form-field">
                    <label for="reason">
                        <?php esc_html_e('Por que você quer adotar este pet?', 'amigopet'); ?>
                    </label>
                    <textarea id="reason" name="reason" rows="4" required></textarea>
                </div>
            </div>

            <div class="apwp-form-row">
                <div class="apwp-form-field">
                    <label>
                        <input type="checkbox" name="has_other_pets" value="1">
                        <?php esc_html_e('Você tem outros pets?', 'amigopet'); ?>
                    </label>
                </div>
            </div>

            <div class="apwp-form-row">
                <div class="apwp-form-field">
                    <label for="home_type">
                        <?php esc_html_e('Tipo de Moradia', 'amigopet'); ?>
                    </label>
                    <select id="home_type" name="home_type" required>
                        <option value="">
                            <?php esc_html_e('Selecione...', 'amigopet'); ?>
                        </option>
                        <option value="house">
                            <?php esc_html_e('Casa', 'amigopet'); ?>
                        </option>
                        <option value="apartment">
                            <?php esc_html_e('Apartamento', 'amigopet'); ?>
                        </option>
                    </select>
                </div>

                <div class="apwp-form-field">
                    <label for="yard_size">
                        <?php esc_html_e('Tamanho do Quintal', 'amigopet'); ?>
                    </label>
                    <select id="yard_size" name="yard_size" required>
                        <option value="">
                            <?php esc_html_e('Selecione...', 'amigopet'); ?>
                        </option>
                        <option value="none">
                            <?php esc_html_e('Sem quintal', 'amigopet'); ?>
                        </option>
                        <option value="small">
                            <?php esc_html_e('Pequeno', 'amigopet'); ?>
                        </option>
                        <option value="medium">
                            <?php esc_html_e('Médio', 'amigopet'); ?>
                        </option>
                        <option value="large">
                            <?php esc_html_e('Grande', 'amigopet'); ?>
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <div class="apwp-form-section">
            <h3>
                <?php esc_html_e('Termos e Condições', 'amigopet'); ?>
            </h3>

            <div class="apwp-form-row">
                <div class="apwp-form-field">
                    <label>
                        <input type="checkbox" name="agree_terms" value="1" required>
                        <?php esc_html_e('Li e concordo com os termos de adoção', 'amigopet'); ?>
                    </label>
                    <p class="description">
                        <a href="#" class="apwp-view-terms">
                            <?php esc_html_e('Clique aqui para ler os termos', 'amigopet'); ?>
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <div class="apwp-form-actions">
            <button type="submit" class="button button-primary">
                <?php esc_html_e('Enviar Solicitação', 'amigopet'); ?>
            </button>

            <?php if (get_option('apwp_adoption_workflow')['require_terms_acceptance']): ?>
                <a href="#" class="button print-terms"
                    onclick="window.open('<?php echo esc_url(add_query_arg(['action' => 'print_adoption_terms', 'pet_id' => $pet->ID], admin_url('admin-ajax.php'))); ?>', 'print_terms', 'width=800,height=600,scrollbars=yes'); return false;">
                    <?php esc_html_e('Imprimir Termo de Adoção', 'amigopet'); ?>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
    jQuery(document).ready(function ($) {
        const form = $('#apwp-adoption-form');

        // Máscara para campos
        if ($.fn.mask) {
            $('#phone').mask('(00) 00000-0000');
            $('#cpf').mask('000.000.000-00');
        }

        // Submit do formulário
        form.on('submit', function (e) {
            e.preventDefault();

            const button = form.find('button[type="submit"]');
            const originalText = button.text();

            button.prop('disabled', true).text('<?php esc_html_e('Enviando...', 'amigopet'); ?>');

            $.ajax({
                url: typeof amigopetPublic !== 'undefined' ? amigopetPublic.ajaxurl : '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'apwp_submit_adoption',
                    _ajax_nonce: typeof amigopetPublic !== 'undefined' ? amigopetPublic.nonce : '',
                    ...form.serializeArray().reduce((obj, item) => ({
                        ...obj,
                        [item.name]: item.value
                    }), {})
                },
                success: function (response) {
                    if (response.success) {
                        // Redireciona para página de sucesso
                        window.location.href = response.data.redirect_url;
                    } else {
                        alert(response.data);
                        button.prop('disabled', false).text(originalText);
                    }
                },
                error: function () {
                    alert('Erro ao enviar solicitação');
                    button.prop('disabled', false).text(originalText);
                }
            });
        });

        // Modal dos termos
        $('.apwp-view-terms').on('click', function (e) {
            e.preventDefault();

            // TODO: Implementar modal dos termos
        });
    });
</script>