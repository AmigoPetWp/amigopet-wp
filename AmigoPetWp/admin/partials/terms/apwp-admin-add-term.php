<?php
/**
 * Template para adicionar um novo termo
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Processa o formulário se foi enviado
if (isset($_POST['submit_term'])) {
    check_admin_referer('add_term', 'term_nonce');
    
    $term_data = array(
        'title' => sanitize_text_field($_POST['title']),
        'content' => wp_kses_post($_POST['content']),
        'type_id' => intval($_POST['type_id']),
        'template_id' => intval($_POST['template_id']),
        'status' => 'active',
        'created_at' => current_time('mysql'),
        'created_by' => get_current_user_id()
    );

    global $wpdb;
    $result = $wpdb->insert(
        $wpdb->prefix . 'apwp_terms',
        $term_data,
        array('%s', '%s', '%d', '%d', '%s', '%s', '%d')
    );

    if ($result) {
        echo '<div class="notice notice-success"><p>' . __('Termo adicionado com sucesso!', 'amigopet-wp') . '</p></div>';
    } else {
        echo '<div class="notice notice-error"><p>' . __('Erro ao adicionar termo.', 'amigopet-wp') . '</p></div>';
    }
}

// Busca tipos de termos
global $wpdb;
$term_types = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}apwp_term_types WHERE status = 'active' ORDER BY name");
$term_templates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}apwp_term_templates WHERE status = 'active' ORDER BY name");
?>

<div class="wrap">
    <h1><?php _e('Adicionar Novo Termo', 'amigopet-wp'); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('add_term', 'term_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><label for="title"><?php _e('Título', 'amigopet-wp'); ?></label></th>
                <td><input type="text" name="title" id="title" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="type_id"><?php _e('Tipo de Termo', 'amigopet-wp'); ?></label></th>
                <td>
                    <select name="type_id" id="type_id" required>
                        <option value=""><?php _e('Selecione...', 'amigopet-wp'); ?></option>
                        <?php foreach ($term_types as $type): ?>
                            <option value="<?php echo esc_attr($type->id); ?>">
                                <?php echo esc_html($type->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="template_id"><?php _e('Template', 'amigopet-wp'); ?></label></th>
                <td>
                    <select name="template_id" id="template_id" required>
                        <option value=""><?php _e('Selecione...', 'amigopet-wp'); ?></option>
                        <?php foreach ($term_templates as $template): ?>
                            <option value="<?php echo esc_attr($template->id); ?>">
                                <?php echo esc_html($template->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="content"><?php _e('Conteúdo', 'amigopet-wp'); ?></label></th>
                <td>
                    <?php
                    wp_editor('', 'content', array(
                        'textarea_name' => 'content',
                        'textarea_rows' => 10,
                        'media_buttons' => true,
                        'teeny' => false,
                        'quicktags' => true
                    ));
                    ?>
                </td>
            </tr>
        </table>

        <?php submit_button(__('Adicionar Termo', 'amigopet-wp'), 'primary', 'submit_term'); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Atualiza o editor quando mudar o template
    $('#template_id').change(function() {
        var template_id = $(this).val();
        if (template_id) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'apwp_get_template_content',
                    template_id: template_id,
                    nonce: '<?php echo wp_create_nonce('apwp_get_template_content'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        wp.editor.remove('content');
                        wp.editor.initialize('content', {
                            tinymce: {
                                wpautop: true,
                                plugins: 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
                                toolbar1: 'formatselect bold italic underline strikethrough | bullist numlist | blockquote | alignleft aligncenter alignright | link unlink | wp_more | spellchecker'
                            },
                            quicktags: true,
                            mediaButtons: true
                        });
                        wp.editor.setContent('content', response.data);
                    }
                }
            });
        }
    });
});
</script>
