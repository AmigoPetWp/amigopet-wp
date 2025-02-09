<?php
/**
 * Template para formulário de voluntário no admin
 */
if (!defined('ABSPATH')) {
    exit;
}

$volunteer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $volunteer_id > 0;
$title = $is_edit ? __('Editar Voluntário', 'amigopet-wp') : __('Adicionar Novo Voluntário', 'amigopet-wp');
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
    <a href="<?php echo remove_query_arg(['action', 'id']); ?>" class="page-title-action">
        <?php _e('Voltar para Lista', 'amigopet-wp'); ?>
    </a>
    
    <div class="apwp-volunteer-form">
        <form id="apwp-volunteer-form" method="post">
            <?php wp_nonce_field('apwp_save_volunteer', '_wpnonce'); ?>
            <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit' : 'add'; ?>">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?php echo $volunteer_id; ?>">
            <?php endif; ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="name"><?php _e('Nome', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="name" name="name" class="regular-text" required
                            value="<?php echo $is_edit ? esc_attr($volunteer->name) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="email"><?php _e('E-mail', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="email" id="email" name="email" class="regular-text" required
                            value="<?php echo $is_edit ? esc_attr($volunteer->email) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="phone"><?php _e('Telefone', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="tel" id="phone" name="phone" class="regular-text" required
                            value="<?php echo $is_edit ? esc_attr($volunteer->phone) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="area_id"><?php _e('Área de Atuação', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="area_id" name="area_id" required>
                            <option value=""><?php _e('Selecione uma área', 'amigopet-wp'); ?></option>
                            <?php foreach ($areas as $area): ?>
                                <option value="<?php echo esc_attr($area->id); ?>"
                                    <?php selected($is_edit && $volunteer->area_id == $area->id); ?>>
                                    <?php echo esc_html($area->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="availability"><?php _e('Disponibilidade', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="availability" name="availability[]" multiple required>
                            <option value="morning" <?php selected($is_edit && in_array('morning', $volunteer->availability)); ?>>
                                <?php _e('Manhã', 'amigopet-wp'); ?>
                            </option>
                            <option value="afternoon" <?php selected($is_edit && in_array('afternoon', $volunteer->availability)); ?>>
                                <?php _e('Tarde', 'amigopet-wp'); ?>
                            </option>
                            <option value="night" <?php selected($is_edit && in_array('night', $volunteer->availability)); ?>>
                                <?php _e('Noite', 'amigopet-wp'); ?>
                            </option>
                            <option value="weekend" <?php selected($is_edit && in_array('weekend', $volunteer->availability)); ?>>
                                <?php _e('Fim de semana', 'amigopet-wp'); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php _e('Pressione Ctrl (ou Cmd no Mac) para selecionar múltiplas opções', 'amigopet-wp'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="experience"><?php _e('Experiência', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <?php
                        wp_editor(
                            $is_edit ? $volunteer->experience : '',
                            'experience',
                            [
                                'textarea_name' => 'experience',
                                'textarea_rows' => 5,
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
                        <label for="status"><?php _e('Status', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="status" name="status" required>
                            <option value="active" <?php selected($is_edit && $volunteer->status == 'active'); ?>>
                                <?php _e('Ativo', 'amigopet-wp'); ?>
                            </option>
                            <option value="inactive" <?php selected($is_edit && $volunteer->status == 'inactive'); ?>>
                                <?php _e('Inativo', 'amigopet-wp'); ?>
                            </option>
                            <option value="pending" <?php selected($is_edit && $volunteer->status == 'pending'); ?>>
                                <?php _e('Pendente', 'amigopet-wp'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="notes"><?php _e('Observações', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <textarea id="notes" name="notes" class="large-text" rows="5"><?php
                            echo $is_edit ? esc_textarea($volunteer->notes) : '';
                        ?></textarea>
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
    // Validação do formulário
    $('#apwp-volunteer-form').on('submit', function(e) {
        var $form = $(this);
        var $submit = $form.find(':submit');
        
        $submit.prop('disabled', true);
        
        var data = $form.serialize();
        data += '&action=apwp_save_volunteer';
        
        $.post(apwp.ajax_url, data, function(response) {
            if (response.success) {
                window.location.href = response.data.redirect;
            } else {
                alert(response.data.message);
                $submit.prop('disabled', false);
            }
        }).fail(function() {
            alert(apwp.i18n.error_saving);
            $submit.prop('disabled', false);
        });
        
        e.preventDefault();
    });
});
</script>
