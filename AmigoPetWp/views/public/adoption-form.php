<?php
/**
 * View do formulário de adoção
 */

// Extrai os atributos do shortcode
$pet_id = (int) $atts['pet_id'];

// Se não tiver pet_id, mostra mensagem de erro
if (!$pet_id) {
    ?>
    <div class="apwp-error">
        <?php _e('Pet não encontrado.', 'amigopet-wp'); ?>
    </div>
    <?php
    return;
}

// Se o usuário não estiver logado, mostra mensagem para fazer login
if (!is_user_logged_in()) {
    ?>
    <div class="apwp-login-required">
        <p>
            <?php _e('Você precisa estar logado para adotar um pet.', 'amigopet-wp'); ?>
        </p>
        <p>
            <a href="<?php echo wp_login_url(get_permalink()); ?>" class="button">
                <?php _e('Fazer Login', 'amigopet-wp'); ?>
            </a>
            
            <a href="<?php echo wp_registration_url(); ?>" class="button">
                <?php _e('Criar Conta', 'amigopet-wp'); ?>
            </a>
        </p>
    </div>
    <?php
    return;
}

// Pega os dados do pet
$pet = get_post($pet_id);
if (!$pet || $pet->post_type !== 'apwp_pet') {
    ?>
    <div class="apwp-error">
        <?php _e('Pet não encontrado.', 'amigopet-wp'); ?>
    </div>
    <?php
    return;
}

// Pega os dados do usuário atual
$current_user = wp_get_current_user();
?>

<div class="apwp-adoption-form">
    <h2><?php _e('Formulário de Adoção', 'amigopet-wp'); ?></h2>
    
    <!-- Dados do pet -->
    <div class="apwp-pet-preview">
        <div class="apwp-pet-image">
            <?php if (has_post_thumbnail($pet)) : ?>
                <?php echo get_the_post_thumbnail($pet, 'medium'); ?>
            <?php else : ?>
                <div class="apwp-pet-no-image">
                    <i class="dashicons dashicons-pets"></i>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="apwp-pet-info">
            <h3><?php echo esc_html($pet->post_title); ?></h3>
            
            <div class="apwp-pet-meta">
                <?php
                // Espécie
                $species = wp_get_post_terms($pet->ID, 'apwp_species');
                if (!empty($species) && !is_wp_error($species)) {
                    ?>
                    <span class="apwp-pet-species">
                        <i class="dashicons dashicons-category"></i>
                        <?php echo esc_html($species[0]->name); ?>
                    </span>
                    <?php
                }
                
                // Raça
                $breed = wp_get_post_terms($pet->ID, 'apwp_breed');
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
                                _e('Pequeno', 'amigopet-wp');
                                break;
                            case 'medium':
                                _e('Médio', 'amigopet-wp');
                                break;
                            case 'large':
                                _e('Grande', 'amigopet-wp');
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
        <input type="hidden" name="pet_id" value="<?php echo $pet_id; ?>">
        
        <div class="apwp-form-section">
            <h3><?php _e('Seus Dados', 'amigopet-wp'); ?></h3>
            
            <div class="apwp-form-row">
                <div class="apwp-form-field">
                    <label for="name"><?php _e('Nome Completo', 'amigopet-wp'); ?></label>
                    <input type="text" id="name" name="name" value="<?php echo esc_attr($current_user->display_name); ?>" required>
                </div>
                
                <div class="apwp-form-field">
                    <label for="email"><?php _e('Email', 'amigopet-wp'); ?></label>
                    <input type="email" id="email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" required>
                </div>
            </div>
            
            <div class="apwp-form-row">
                <div class="apwp-form-field">
                    <label for="phone"><?php _e('Telefone', 'amigopet-wp'); ?></label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                
                <div class="apwp-form-field">
                    <label for="cpf"><?php _e('CPF', 'amigopet-wp'); ?></label>
                    <input type="text" id="cpf" name="cpf" required>
                </div>
            </div>
            
            <div class="apwp-form-row">
                <div class="apwp-form-field">
                    <label for="address"><?php _e('Endereço Completo', 'amigopet-wp'); ?></label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>
            </div>
        </div>
        
        <div class="apwp-form-section">
            <h3><?php _e('Informações Adicionais', 'amigopet-wp'); ?></h3>
            
            <div class="apwp-form-row">
                <div class="apwp-form-field">
                    <label for="reason"><?php _e('Por que você quer adotar este pet?', 'amigopet-wp'); ?></label>
                    <textarea id="reason" name="reason" rows="4" required></textarea>
                </div>
            </div>
            
            <div class="apwp-form-row">
                <div class="apwp-form-field">
                    <label>
                        <input type="checkbox" name="has_other_pets" value="1">
                        <?php _e('Você tem outros pets?', 'amigopet-wp'); ?>
                    </label>
                </div>
            </div>
            
            <div class="apwp-form-row">
                <div class="apwp-form-field">
                    <label for="home_type"><?php _e('Tipo de Moradia', 'amigopet-wp'); ?></label>
                    <select id="home_type" name="home_type" required>
                        <option value=""><?php _e('Selecione...', 'amigopet-wp'); ?></option>
                        <option value="house"><?php _e('Casa', 'amigopet-wp'); ?></option>
                        <option value="apartment"><?php _e('Apartamento', 'amigopet-wp'); ?></option>
                    </select>
                </div>
                
                <div class="apwp-form-field">
                    <label for="yard_size"><?php _e('Tamanho do Quintal', 'amigopet-wp'); ?></label>
                    <select id="yard_size" name="yard_size" required>
                        <option value=""><?php _e('Selecione...', 'amigopet-wp'); ?></option>
                        <option value="none"><?php _e('Sem quintal', 'amigopet-wp'); ?></option>
                        <option value="small"><?php _e('Pequeno', 'amigopet-wp'); ?></option>
                        <option value="medium"><?php _e('Médio', 'amigopet-wp'); ?></option>
                        <option value="large"><?php _e('Grande', 'amigopet-wp'); ?></option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="apwp-form-section">
            <h3><?php _e('Termos e Condições', 'amigopet-wp'); ?></h3>
            
            <div class="apwp-form-row">
                <div class="apwp-form-field">
                    <label>
                        <input type="checkbox" name="agree_terms" value="1" required>
                        <?php _e('Li e concordo com os termos de adoção', 'amigopet-wp'); ?>
                    </label>
                    <p class="description">
                        <a href="#" class="apwp-view-terms">
                            <?php _e('Clique aqui para ler os termos', 'amigopet-wp'); ?>
                        </a>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="apwp-form-actions">
            <button type="submit" class="button button-primary">
                <?php _e('Enviar Solicitação', 'amigopet-wp'); ?>
            </button>
            
            <?php if (get_option('amigopet_settings')['adoption_terms']): ?>
            <a href="#" class="button print-terms" onclick="window.open('<?php echo esc_url(add_query_arg(['action' => 'print_adoption_terms', 'pet_id' => $pet->ID], admin_url('admin-ajax.php'))); ?>', 'print_terms', 'width=800,height=600,scrollbars=yes'); return false;">
                <?php _e('Imprimir Termo de Adoção', 'amigopet-wp'); ?>
            </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    const form = $('#apwp-adoption-form');
    
    // Máscara para campos
    $('#phone').mask('(00) 00000-0000');
    $('#cpf').mask('000.000.000-00');
    
    // Submit do formulário
    form.on('submit', function(e) {
        e.preventDefault();
        
        const button = form.find('button[type="submit"]');
        const originalText = button.text();
        
        button.prop('disabled', true).text('<?php _e('Enviando...', 'amigopet-wp'); ?>');
        
        $.ajax({
            url: apwp.ajax_url,
            type: 'POST',
            data: {
                action: 'apwp_submit_adoption',
                _ajax_nonce: apwp.nonce,
                ...form.serializeArray().reduce((obj, item) => ({
                    ...obj,
                    [item.name]: item.value
                }), {})
            },
            success: function(response) {
                if (response.success) {
                    // Redireciona para página de sucesso
                    window.location.href = response.data.redirect_url;
                } else {
                    alert(response.data);
                    button.prop('disabled', false).text(originalText);
                }
            },
            error: function() {
                alert(apwp.i18n.error);
                button.prop('disabled', false).text(originalText);
            }
        });
    });
    
    // Modal dos termos
    $('.apwp-view-terms').on('click', function(e) {
        e.preventDefault();
        
        // TODO: Implementar modal dos termos
    });
});
</script>
