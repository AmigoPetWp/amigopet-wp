<?php
/**
 * Template para formulário de voluntário no admin
 * Combina as melhores características de ambas implementações anteriores
 */
if (!defined('ABSPATH')) {
    exit;
}

$volunteer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $volunteer_id > 0;
$volunteer = $is_edit ? get_post($volunteer_id) : null;

if ($is_edit && (!$volunteer || $volunteer->post_type !== 'apwp_volunteer')) {
    wp_die(__('Voluntário não encontrado', 'amigopet-wp'));
}

$title = $is_edit ? __('Editar Voluntário', 'amigopet-wp') : __('Adicionar Novo Voluntário', 'amigopet-wp');

// Carrega dados do voluntário se estiver editando
$volunteer_data = $is_edit ? [
    'name' => get_post_meta($volunteer_id, 'volunteer_name', true),
    'email' => get_post_meta($volunteer_id, 'volunteer_email', true),
    'phone' => get_post_meta($volunteer_id, 'volunteer_phone', true),
    'address' => get_post_meta($volunteer_id, 'volunteer_address', true),
    'availability_days' => get_post_meta($volunteer_id, 'volunteer_availability_days', true),
    'availability_periods' => get_post_meta($volunteer_id, 'volunteer_availability_periods', true),
    'skills' => get_post_meta($volunteer_id, 'volunteer_skills', true),
    'experience' => get_post_meta($volunteer_id, 'volunteer_experience', true),
    'status' => get_post_meta($volunteer_id, 'volunteer_status', true),
    'notes' => get_post_meta($volunteer_id, 'volunteer_notes', true)
] : [];
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
    <a href="<?php echo remove_query_arg(['action', 'id']); ?>" class="page-title-action">
        <?php _e('Voltar para Lista', 'amigopet-wp'); ?>
    </a>
    
    <div class="apwp-volunteer-form">
        <form id="apwp-volunteer-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
            <?php wp_nonce_field('apwp_save_volunteer', 'apwp_volunteer_nonce'); ?>
            <input type="hidden" name="action" value="apwp_save_volunteer">
            <?php if ($is_edit): ?>
                <input type="hidden" name="volunteer_id" value="<?php echo $volunteer_id; ?>">
            <?php endif; ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="volunteer_name"><?php _e('Nome Completo', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="volunteer_name" name="volunteer_name" class="regular-text" required
                               value="<?php echo esc_attr($volunteer_data['name'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_email"><?php _e('Email', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="email" id="volunteer_email" name="volunteer_email" class="regular-text" required
                               value="<?php echo esc_attr($volunteer_data['email'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_phone"><?php _e('Telefone', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="tel" id="volunteer_phone" name="volunteer_phone" class="regular-text" required
                               value="<?php echo esc_attr($volunteer_data['phone'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_address"><?php _e('Endereço', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <textarea id="volunteer_address" name="volunteer_address" class="large-text" rows="3" required><?php 
                            echo esc_textarea($volunteer_data['address'] ?? ''); 
                        ?></textarea>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_availability_days"><?php _e('Dias Disponíveis', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="volunteer_availability_days" name="volunteer_availability_days[]" multiple required>
                            <?php
                            $days = [
                                'monday'    => __('Segunda-feira', 'amigopet-wp'),
                                'tuesday'   => __('Terça-feira', 'amigopet-wp'),
                                'wednesday' => __('Quarta-feira', 'amigopet-wp'),
                                'thursday'  => __('Quinta-feira', 'amigopet-wp'),
                                'friday'    => __('Sexta-feira', 'amigopet-wp'),
                                'saturday'  => __('Sábado', 'amigopet-wp'),
                                'sunday'    => __('Domingo', 'amigopet-wp')
                            ];
                            
                            $selected_days = is_array($volunteer_data['availability_days'] ?? '') 
                                ? $volunteer_data['availability_days'] 
                                : [];
                            
                            foreach ($days as $value => $label) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    esc_attr($value),
                                    in_array($value, $selected_days) ? 'selected' : '',
                                    esc_html($label)
                                );
                            }
                            ?>
                        </select>
                        <p class="description">
                            <?php _e('Pressione Ctrl (ou Cmd no Mac) para selecionar múltiplos dias.', 'amigopet-wp'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_availability_periods"><?php _e('Períodos Disponíveis', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="volunteer_availability_periods" name="volunteer_availability_periods[]" multiple required>
                            <?php
                            $periods = [
                                'morning'   => __('Manhã', 'amigopet-wp'),
                                'afternoon' => __('Tarde', 'amigopet-wp'),
                                'evening'   => __('Noite', 'amigopet-wp')
                            ];
                            
                            $selected_periods = is_array($volunteer_data['availability_periods'] ?? '') 
                                ? $volunteer_data['availability_periods'] 
                                : [];
                            
                            foreach ($periods as $value => $label) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    esc_attr($value),
                                    in_array($value, $selected_periods) ? 'selected' : '',
                                    esc_html($label)
                                );
                            }
                            ?>
                        </select>
                        <p class="description">
                            <?php _e('Pressione Ctrl (ou Cmd no Mac) para selecionar múltiplos períodos.', 'amigopet-wp'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_skills"><?php _e('Habilidades', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="volunteer_skills" name="volunteer_skills[]" multiple required>
                            <?php
                            $skills = [
                                'veterinary'  => __('Veterinária', 'amigopet-wp'),
                                'grooming'    => __('Banho e Tosa', 'amigopet-wp'),
                                'transport'   => __('Transporte', 'amigopet-wp'),
                                'social'      => __('Mídias Sociais', 'amigopet-wp'),
                                'events'      => __('Eventos', 'amigopet-wp'),
                                'fundraising' => __('Captação de Recursos', 'amigopet-wp'),
                                'other'       => __('Outro', 'amigopet-wp')
                            ];
                            
                            $selected_skills = is_array($volunteer_data['skills'] ?? '') 
                                ? $volunteer_data['skills'] 
                                : [];
                            
                            foreach ($skills as $value => $label) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    esc_attr($value),
                                    in_array($value, $selected_skills) ? 'selected' : '',
                                    esc_html($label)
                                );
                            }
                            ?>
                        </select>
                        <p class="description">
                            <?php _e('Pressione Ctrl (ou Cmd no Mac) para selecionar múltiplas habilidades.', 'amigopet-wp'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_experience"><?php _e('Experiência', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <?php
                        wp_editor(
                            $volunteer_data['experience'] ?? '',
                            'volunteer_experience',
                            [
                                'textarea_name' => 'volunteer_experience',
                                'textarea_rows' => 5,
                                'media_buttons' => false,
                                'teeny' => true,
                                'quicktags' => true
                            ]
                        );
                        ?>
                        <p class="description">
                            <?php _e('Descreva sua experiência prévia com animais ou em outras atividades voluntárias.', 'amigopet-wp'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_photo"><?php _e('Foto', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <div id="volunteer-photo-container">
                            <?php if ($is_edit && has_post_thumbnail($volunteer_id)): ?>
                                <div class="current-thumbnail">
                                    <?php echo get_the_post_thumbnail($volunteer_id, 'thumbnail'); ?>
                                </div>
                            <?php endif; ?>
                            <input type="file" id="volunteer_photo" name="volunteer_photo" accept="image/*">
                            <p class="description">
                                <?php _e('Selecione uma foto sua. Tamanho recomendado: 300x300 pixels.', 'amigopet-wp'); ?>
                            </p>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_status"><?php _e('Status', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="volunteer_status" name="volunteer_status" required>
                            <option value=""><?php _e('Selecione o status', 'amigopet-wp'); ?></option>
                            <option value="active" <?php selected($volunteer_data['status'] ?? '', 'active'); ?>>
                                <?php _e('Ativo', 'amigopet-wp'); ?>
                            </option>
                            <option value="inactive" <?php selected($volunteer_data['status'] ?? '', 'inactive'); ?>>
                                <?php _e('Inativo', 'amigopet-wp'); ?>
                            </option>
                            <option value="pending" <?php selected($volunteer_data['status'] ?? '', 'pending'); ?>>
                                <?php _e('Pendente', 'amigopet-wp'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_notes"><?php _e('Observações', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <?php
                        wp_editor(
                            $volunteer_data['notes'] ?? '',
                            'volunteer_notes',
                            [
                                'textarea_name' => 'volunteer_notes',
                                'textarea_rows' => 5,
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
                    <?php echo $is_edit ? __('Atualizar Voluntário', 'amigopet-wp') : __('Adicionar Voluntário', 'amigopet-wp'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<style>
.apwp-volunteer-form .required {
    color: #dc3232;
}

.apwp-volunteer-form .error {
    border-color: #dc3232;
}

.apwp-volunteer-form .description {
    font-style: italic;
    color: #646970;
}

.apwp-volunteer-form .current-thumbnail {
    margin-bottom: 10px;
}

.apwp-volunteer-form .current-thumbnail img {
    max-width: 150px;
    height: auto;
    border: 1px solid #ddd;
    padding: 2px;
    border-radius: 50%;
}

#volunteer-photo-preview {
    margin-top: 10px;
}

#volunteer-photo-preview img {
    max-width: 150px;
    height: auto;
    border: 1px solid #ddd;
    padding: 2px;
    border-radius: 50%;
}

select[multiple] {
    min-width: 200px;
    min-height: 100px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Validação do formulário
    $('#apwp-volunteer-form').on('submit', function(e) {
        var $required = $(this).find('[required]');
        var valid = true;
        
        $required.each(function() {
            if (!$(this).val() || ($(this).is('select[multiple]') && !$(this).val()?.length)) {
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
    $('#volunteer_phone').on('input', function() {
        var phone = $(this).val().replace(/\D/g, '');
        if (phone.length > 0) {
            phone = phone.match(new RegExp('.{1,11}'));
            phone = phone[0].replace(/(\d{2})(\d{4,5})(\d{4})/, '($1) $2-$3');
            $(this).val(phone);
        }
    });
    
    // Preview da foto
    $('#volunteer_photo').on('change', function() {
        var $container = $('#volunteer-photo-container');
        var $preview = $container.find('#volunteer-photo-preview');
        
        if (!$preview.length) {
            $preview = $('<div id="volunteer-photo-preview"></div>').appendTo($container);
        } else {
            $preview.empty();
        }
        
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $preview.html('<img src="' + e.target.result + '">');
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>
