<?php
/**
 * Template para a página de termos
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Processa formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isset($_POST['apwp_nonce']) || !wp_verify_nonce($_POST['apwp_nonce'], 'apwp_term_action')) {
        wp_die(__('Nonce inválido', 'amigopet-wp'));
    }

    $term_data = array(
        'title' => sanitize_text_field($_POST['title']),
        'content' => wp_kses_post($_POST['content']),
        'type' => sanitize_text_field($_POST['type']),
        'status' => sanitize_text_field($_POST['status'])
    );

    global $wpdb;
    $table_name = $wpdb->prefix . 'apwp_terms';

    if ($_POST['action'] === 'add') {
        $wpdb->insert($table_name, $term_data);
        add_settings_error('apwp_messages', 'apwp_term_added', __('Termo adicionado com sucesso!', 'amigopet-wp'), 'success');
    } elseif ($_POST['action'] === 'edit' && isset($_POST['id'])) {
        $wpdb->update($table_name, $term_data, array('id' => intval($_POST['id'])));
        add_settings_error('apwp_messages', 'apwp_term_updated', __('Termo atualizado com sucesso!', 'amigopet-wp'), 'success');
    }
}

// Lista de termos
global $wpdb;
$terms = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}apwp_terms ORDER BY type, title");
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Termos', 'amigopet-wp'); ?></h1>
    <a href="#" class="page-title-action" id="add-new-term"><?php _e('Adicionar Novo', 'amigopet-wp'); ?></a>
    
    <?php settings_errors('apwp_messages'); ?>

    <!-- Lista de termos -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Título', 'amigopet-wp'); ?></th>
                <th><?php _e('Tipo', 'amigopet-wp'); ?></th>
                <th><?php _e('Status', 'amigopet-wp'); ?></th>
                <th><?php _e('Última Atualização', 'amigopet-wp'); ?></th>
                <th><?php _e('Ações', 'amigopet-wp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($terms)): ?>
            <tr>
                <td colspan="5"><?php _e('Nenhum termo encontrado.', 'amigopet-wp'); ?></td>
            </tr>
            <?php else: ?>
            <?php foreach ($terms as $term): ?>
            <tr>
                <td>
                    <strong>
                        <a href="#" class="edit-term" data-id="<?php echo esc_attr($term->id); ?>">
                            <?php echo esc_html($term->title); ?>
                        </a>
                    </strong>
                </td>
                <td><?php echo esc_html(ucfirst($term->type)); ?></td>
                <td>
                    <span class="status-<?php echo esc_attr($term->status); ?>">
                        <?php echo esc_html(ucfirst($term->status)); ?>
                    </span>
                </td>
                <td>
                    <?php echo esc_html(get_date_from_gmt($term->updated_at)); ?>
                </td>
                <td>
                    <a href="#" class="button button-small edit-term" data-id="<?php echo esc_attr($term->id); ?>">
                        <?php _e('Editar', 'amigopet-wp'); ?>
                    </a>
                    <a href="#" class="button button-small preview-term" data-id="<?php echo esc_attr($term->id); ?>">
                        <?php _e('Visualizar', 'amigopet-wp'); ?>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Termo -->
<div id="term-modal" class="apwp-modal">
    <div class="apwp-modal-content">
        <span class="apwp-modal-close">&times;</span>
        <h2 id="term-modal-title"><?php _e('Adicionar Termo', 'amigopet-wp'); ?></h2>
        
        <form method="post" id="term-form">
            <?php wp_nonce_field('apwp_term_action', 'apwp_nonce'); ?>
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" value="">
            
            <div class="form-field">
                <label for="title"><?php _e('Título', 'amigopet-wp'); ?></label>
                <input type="text" name="title" id="title" required>
            </div>
            
            <div class="form-field">
                <label for="type"><?php _e('Tipo', 'amigopet-wp'); ?></label>
                <select name="type" id="type" required>
                    <option value="adoption"><?php _e('Adoção', 'amigopet-wp'); ?></option>
                    <option value="foster"><?php _e('Apadrinhamento', 'amigopet-wp'); ?></option>
                    <option value="volunteer"><?php _e('Voluntariado', 'amigopet-wp'); ?></option>
                    <option value="donation"><?php _e('Doação', 'amigopet-wp'); ?></option>
                </select>
            </div>
            
            <div class="form-field">
                <label for="content"><?php _e('Conteúdo', 'amigopet-wp'); ?></label>
                <?php 
                wp_editor('', 'content', array(
                    'textarea_name' => 'content',
                    'media_buttons' => true,
                    'textarea_rows' => 10
                )); 
                ?>
            </div>
            
            <div class="form-field">
                <label for="status"><?php _e('Status', 'amigopet-wp'); ?></label>
                <select name="status" id="status" required>
                    <option value="active"><?php _e('Ativo', 'amigopet-wp'); ?></option>
                    <option value="inactive"><?php _e('Inativo', 'amigopet-wp'); ?></option>
                    <option value="draft"><?php _e('Rascunho', 'amigopet-wp'); ?></option>
                </select>
            </div>
            
            <div class="submit-button">
                <button type="submit" class="button button-primary"><?php _e('Salvar', 'amigopet-wp'); ?></button>
                <button type="button" class="button apwp-modal-close"><?php _e('Cancelar', 'amigopet-wp'); ?></button>
            </div>
        </form>
    </div>
</div>

<style>
.apwp-modal {
    display: none;
    position: fixed;
    z-index: 100000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.apwp-modal-content {
    position: relative;
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 800px;
    border-radius: 4px;
}

.apwp-modal-close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.apwp-modal-close:hover {
    color: black;
}

.form-field {
    margin-bottom: 15px;
}

.form-field label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.form-field input[type="text"],
.form-field select {
    width: 100%;
    max-width: 400px;
}

.submit-button {
    margin-top: 20px;
}

.status-active { color: #46b450; }
.status-inactive { color: #dc3232; }
.status-draft { color: #ffb900; }
</style>

<script>
jQuery(document).ready(function($) {
    // Abrir modal para adicionar
    $('#add-new-term').click(function(e) {
        e.preventDefault();
        $('#term-modal-title').text('<?php _e('Adicionar Termo', 'amigopet-wp'); ?>');
        $('#term-form')[0].reset();
        $('#term-form input[name="action"]').val('add');
        $('#term-form input[name="id"]').val('');
        tinyMCE.get('content').setContent('');
        $('#term-modal').show();
    });

    // Abrir modal para editar
    $('.edit-term').click(function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#term-modal-title').text('<?php _e('Editar Termo', 'amigopet-wp'); ?>');
        
        // Aqui você deve fazer uma chamada AJAX para buscar os dados do termo
        // e preencher o formulário
        
        $('#term-form input[name="action"]').val('edit');
        $('#term-form input[name="id"]').val(id);
        $('#term-modal').show();
    });

    // Fechar modal
    $('.apwp-modal-close').click(function() {
        $('#term-modal').hide();
    });

    // Fechar modal ao clicar fora
    $(window).click(function(e) {
        if ($(e.target).is('.apwp-modal')) {
            $('.apwp-modal').hide();
        }
    });
});
</script>
