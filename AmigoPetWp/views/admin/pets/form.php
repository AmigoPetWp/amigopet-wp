<?php
/**
 * Template para formulário de pet no admin
 */
if (!defined('ABSPATH')) {
    exit;
}

$pet_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $pet_id > 0;
$title = $is_edit ? __('Editar Pet', 'amigopet-wp') : __('Adicionar Novo Pet', 'amigopet-wp');
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
    <a href="<?php echo remove_query_arg(['action', 'id']); ?>" class="page-title-action">
        <?php _e('Voltar para Lista', 'amigopet-wp'); ?>
    </a>
    
    <div class="apwp-pet-form">
        <form id="apwp-pet-form" method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('apwp_save_pet', '_wpnonce'); ?>
            <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit' : 'add'; ?>">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?php echo $pet_id; ?>">
            <?php endif; ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="name"><?php _e('Nome', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="name" name="name" class="regular-text" required
                            value="<?php echo $is_edit ? esc_attr($pet->name) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="species_id"><?php _e('Espécie', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="species_id" name="species_id" required>
                            <option value=""><?php _e('Selecione uma espécie', 'amigopet-wp'); ?></option>
                            <?php foreach ($species as $specie): ?>
                                <option value="<?php echo esc_attr($specie->id); ?>"
                                    <?php selected($is_edit && $pet->species_id == $specie->id); ?>>
                                    <?php echo esc_html($specie->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="breed_id"><?php _e('Raça', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <select id="breed_id" name="breed_id">
                            <option value=""><?php _e('Selecione uma raça', 'amigopet-wp'); ?></option>
                            <?php if ($is_edit && !empty($breeds)): ?>
                                <?php foreach ($breeds as $breed): ?>
                                    <option value="<?php echo esc_attr($breed->id); ?>"
                                        <?php selected($pet->breed_id == $breed->id); ?>>
                                        <?php echo esc_html($breed->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="age"><?php _e('Idade', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="age" name="age" class="regular-text"
                            value="<?php echo $is_edit ? esc_attr($pet->age) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="description"><?php _e('Descrição', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <?php
                        wp_editor(
                            $is_edit ? $pet->description : '',
                            'description',
                            [
                                'textarea_name' => 'description',
                                'textarea_rows' => 10,
                                'media_buttons' => false,
                                'teeny' => true,
                                'quicktags' => false
                            ]
                        );
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="photo"><?php _e('Foto', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <?php if ($is_edit && $pet->photo_url): ?>
                            <div class="apwp-current-photo">
                                <img src="<?php echo esc_url($pet->photo_url); ?>" alt="<?php echo esc_attr($pet->name); ?>" width="150">
                                <br>
                                <label>
                                    <input type="checkbox" name="remove_photo" value="1">
                                    <?php _e('Remover foto atual', 'amigopet-wp'); ?>
                                </label>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="photo" name="photo" accept="image/*">
                        <p class="description">
                            <?php _e('Tamanho máximo: 2MB. Formatos aceitos: JPG, PNG, GIF', 'amigopet-wp'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="status"><?php _e('Status', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="status" name="status" required>
                            <option value="available" <?php selected($is_edit && $pet->status == 'available'); ?>>
                                <?php _e('Disponível', 'amigopet-wp'); ?>
                            </option>
                            <option value="adopted" <?php selected($is_edit && $pet->status == 'adopted'); ?>>
                                <?php _e('Adotado', 'amigopet-wp'); ?>
                            </option>
                            <option value="unavailable" <?php selected($is_edit && $pet->status == 'unavailable'); ?>>
                                <?php _e('Indisponível', 'amigopet-wp'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php echo $is_edit ? __('Atualizar', 'amigopet-wp') : __('Adicionar', 'amigopet-wp'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Carrega as raças quando a espécie é alterada
    $('#species_id').on('change', function() {
        var species_id = $(this).val();
        var $breed_select = $('#breed_id');
        
        $breed_select.prop('disabled', true)
            .html('<option value=""><?php _e('Carregando...', 'amigopet-wp'); ?></option>');
        
        if (!species_id) {
            $breed_select.prop('disabled', false)
                .html('<option value=""><?php _e('Selecione uma raça', 'amigopet-wp'); ?></option>');
            return;
        }
        
        var data = {
            action: 'apwp_get_breeds',
            _ajax_nonce: apwp.nonce,
            species_id: species_id
        };
        
        $.post(apwp.ajax_url, data, function(response) {
            if (response.success) {
                var options = '<option value=""><?php _e('Selecione uma raça', 'amigopet-wp'); ?></option>';
                $.each(response.data, function(index, breed) {
                    options += '<option value="' + breed.id + '">' + breed.name + '</option>';
                });
                $breed_select.html(options);
            } else {
                alert(response.data.message);
            }
            $breed_select.prop('disabled', false);
        });
    });
    
    // Validação do formulário
    $('#apwp-pet-form').on('submit', function(e) {
        var $form = $(this);
        var $submit = $form.find(':submit');
        
        $submit.prop('disabled', true);
        
        var formData = new FormData(this);
        formData.append('action', 'apwp_save_pet');
        
        $.ajax({
            url: apwp.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    window.location.href = response.data.redirect;
                } else {
                    alert(response.data.message);
                    $submit.prop('disabled', false);
                }
            },
            error: function() {
                alert(apwp.i18n.error_saving);
                $submit.prop('disabled', false);
            }
        });
        
        e.preventDefault();
    });
});
</script>
