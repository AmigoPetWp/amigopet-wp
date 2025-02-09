<?php
if (!defined('ABSPATH')) {
    exit;
}

$term_id = isset($_GET['term_id']) ? intval($_GET['term_id']) : 0;
$term = $term_id ? get_post($term_id) : null;

// Se estiver editando, carrega os meta dados
if ($term_id) {
    $term_type = get_post_meta($term_id, 'term_type', true);
    $term_status = get_post_meta($term_id, 'term_status', true);
} else {
    $term_type = '';
    $term_status = 'draft';
}
?>

<div class="wrap">
    <h1><?php echo $term_id ? __('Editar Termo', 'amigopet-wp') : __('Adicionar Novo Termo', 'amigopet-wp'); ?></h1>
    
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <?php wp_nonce_field('apwp_save_term', 'apwp_term_nonce'); ?>
        <input type="hidden" name="action" value="apwp_save_term">
        <input type="hidden" name="term_id" value="<?php echo $term_id; ?>">
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="term_title"><?php _e('Título', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="text" 
                           id="term_title" 
                           name="term_title" 
                           value="<?php echo $term ? esc_attr($term->post_title) : ''; ?>" 
                           class="regular-text" 
                           required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="term_type"><?php _e('Tipo', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="term_type" name="term_type" required>
                        <option value=""><?php _e('Selecione um tipo', 'amigopet-wp'); ?></option>
                        <option value="adoption" <?php selected($term_type, 'adoption'); ?>><?php _e('Adoção', 'amigopet-wp'); ?></option>
                        <option value="volunteer" <?php selected($term_type, 'volunteer'); ?>><?php _e('Voluntariado', 'amigopet-wp'); ?></option>
                        <option value="donation" <?php selected($term_type, 'donation'); ?>><?php _e('Doação', 'amigopet-wp'); ?></option>
                        <option value="privacy" <?php selected($term_type, 'privacy'); ?>><?php _e('Privacidade', 'amigopet-wp'); ?></option>
                        <option value="other" <?php selected($term_type, 'other'); ?>><?php _e('Outro', 'amigopet-wp'); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="term_content"><?php _e('Conteúdo', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <?php
                    wp_editor(
                        $term ? $term->post_content : '',
                        'term_content',
                        [
                            'media_buttons' => true,
                            'textarea_rows' => 10,
                            'teeny' => false
                        ]
                    );
                    ?>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="term_status"><?php _e('Status', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="term_status" name="term_status" required>
                        <option value="draft" <?php selected($term_status, 'draft'); ?>><?php _e('Rascunho', 'amigopet-wp'); ?></option>
                        <option value="active" <?php selected($term_status, 'active'); ?>><?php _e('Ativo', 'amigopet-wp'); ?></option>
                        <option value="inactive" <?php selected($term_status, 'inactive'); ?>><?php _e('Inativo', 'amigopet-wp'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        
        <?php submit_button($term_id ? __('Atualizar Termo', 'amigopet-wp') : __('Adicionar Termo', 'amigopet-wp')); ?>
    </form>
</div>

<style>
.wp-editor-container {
    border: 1px solid #ddd;
}

#term_content {
    width: 100%;
    min-height: 300px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Adiciona validação do formulário
    $('form').on('submit', function(e) {
        var title = $('#term_title').val().trim();
        var type = $('#term_type').val();
        var content = tinyMCE.get('term_content') ? tinyMCE.get('term_content').getContent() : $('#term_content').val();
        
        if (!title) {
            alert('<?php _e('Por favor, insira um título para o termo.', 'amigopet-wp'); ?>');
            e.preventDefault();
            return false;
        }
        
        if (!type) {
            alert('<?php _e('Por favor, selecione um tipo para o termo.', 'amigopet-wp'); ?>');
            e.preventDefault();
            return false;
        }
        
        if (!content) {
            alert('<?php _e('Por favor, insira o conteúdo do termo.', 'amigopet-wp'); ?>');
            e.preventDefault();
            return false;
        }
    });
});
</script>
