<?php
/**
 * Template para a página de templates de termos
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Inicializa as classes
$term_type = new APWP_Term_Type();
$term_template = new APWP_Term_Template();

// Obtém o tipo de termo selecionado
$type_id = isset($_GET['type']) ? intval($_GET['type']) : 0;
$type = $term_type->get($type_id);

if (!$type) {
    wp_die(__('Tipo de termo não encontrado.', 'amigopet-wp'));
}

// Processa o formulário
if (isset($_POST['action']) && $_POST['action'] === 'add_template') {
    if (check_admin_referer('apwp_add_template', 'apwp_nonce')) {
        $term_template->type_id = $type_id;
        $term_template->title = sanitize_text_field($_POST['title']);
        $term_template->content = wp_kses_post($_POST['content']);
        $term_template->status = 'active';
        
        if ($term_template->save()) {
            add_settings_error(
                'apwp_messages',
                'apwp_template_added',
                __('Template adicionado com sucesso.', 'amigopet-wp'),
                'success'
            );
        }
    }
}

// Lista todos os templates deste tipo
$templates = $term_template->list(array('type_id' => $type_id));

// Obtém os shortcodes disponíveis
$available_shortcodes = APWP_Term_Template::get_available_shortcodes();
?>

<div class="wrap">
    <h1>
        <?php printf(__('Templates de Termo: %s', 'amigopet-wp'), esc_html($type['name'])); ?>
        <a href="<?php echo admin_url('admin.php?page=amigopet-wp-term-types'); ?>" class="page-title-action">
            <?php _e('Voltar para Tipos de Termos', 'amigopet-wp'); ?>
        </a>
    </h1>
    
    <?php settings_errors('apwp_messages'); ?>

    <div class="apwp-templates-container">
        <!-- Formulário para adicionar novo template -->
        <div class="apwp-template-form">
            <h2><?php _e('Adicionar Novo Template', 'amigopet-wp'); ?></h2>
            
            <form method="post" action="">
                <?php wp_nonce_field('apwp_add_template', 'apwp_nonce'); ?>
                <input type="hidden" name="action" value="add_template">
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="title"><?php _e('Título', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="title" id="title" class="regular-text" required>
                            <p class="description">
                                <?php _e('Título do template (ex: Termo de Adoção - Versão 1)', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="content"><?php _e('Conteúdo', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <?php
                            wp_editor('', 'content', array(
                                'textarea_name' => 'content',
                                'textarea_rows' => 20,
                                'media_buttons' => false
                            ));
                            ?>
                            <p class="description">
                                <?php _e('Use os shortcodes disponíveis para inserir dados dinâmicos no template.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('Adicionar Template', 'amigopet-wp')); ?>
            </form>
        </div>

        <!-- Shortcodes disponíveis -->
        <div class="apwp-shortcodes-help">
            <h2><?php _e('Shortcodes Disponíveis', 'amigopet-wp'); ?></h2>
            
            <div class="apwp-shortcodes-list">
                <?php foreach ($available_shortcodes as $category => $shortcodes) : ?>
                    <div class="shortcode-category">
                        <h3><?php echo esc_html($category); ?></h3>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th><?php _e('Shortcode', 'amigopet-wp'); ?></th>
                                    <th><?php _e('Descrição', 'amigopet-wp'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($shortcodes as $code => $description) : ?>
                                    <tr>
                                        <td>
                                            <code><?php echo esc_html($code); ?></code>
                                            <button type="button" class="button button-small copy-shortcode" data-shortcode="<?php echo esc_attr($code); ?>">
                                                <?php _e('Copiar', 'amigopet-wp'); ?>
                                            </button>
                                        </td>
                                        <td><?php echo esc_html($description); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Lista de templates existentes -->
        <div class="apwp-templates-list">
            <h2><?php _e('Templates Existentes', 'amigopet-wp'); ?></h2>
            
            <?php if (!empty($templates)) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Título', 'amigopet-wp'); ?></th>
                            <th><?php _e('Status', 'amigopet-wp'); ?></th>
                            <th><?php _e('Criado em', 'amigopet-wp'); ?></th>
                            <th><?php _e('Ações', 'amigopet-wp'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($templates as $template) : ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($template['title']); ?></strong>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo esc_attr($template['status']); ?>">
                                        <?php echo esc_html($template['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo date_i18n(get_option('date_format'), strtotime($template['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="row-actions">
                                        <span class="edit">
                                            <a href="#" data-id="<?php echo esc_attr($template['id']); ?>">
                                                <?php _e('Editar', 'amigopet-wp'); ?>
                                            </a> |
                                        </span>
                                        <span class="preview">
                                            <a href="#" class="preview-template" data-id="<?php echo esc_attr($template['id']); ?>">
                                                <?php _e('Visualizar', 'amigopet-wp'); ?>
                                            </a> |
                                        </span>
                                        <span class="delete">
                                            <a href="#" class="delete-template" data-id="<?php echo esc_attr($template['id']); ?>">
                                                <?php _e('Excluir', 'amigopet-wp'); ?>
                                            </a>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php _e('Nenhum template cadastrado para este tipo de termo.', 'amigopet-wp'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.apwp-templates-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
    margin-top: 20px;
}

.apwp-template-form,
.apwp-shortcodes-help,
.apwp-templates-list {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.apwp-templates-list {
    grid-column: 1 / -1;
}

.apwp-template-form h2,
.apwp-shortcodes-help h2,
.apwp-templates-list h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.shortcode-category {
    margin-bottom: 20px;
}

.shortcode-category h3 {
    margin: 0 0 10px 0;
    color: #1d2327;
}

.copy-shortcode {
    margin-left: 10px !important;
}

@media screen and (max-width: 782px) {
    .apwp-templates-container {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Copiar shortcode
    $('.copy-shortcode').on('click', function() {
        const shortcode = $(this).data('shortcode');
        const textarea = document.createElement('textarea');
        textarea.value = shortcode;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        
        const $button = $(this);
        $button.text('<?php _e('Copiado!', 'amigopet-wp'); ?>');
        setTimeout(() => {
            $button.text('<?php _e('Copiar', 'amigopet-wp'); ?>');
        }, 2000);
    });

    // Excluir template
    $('.delete-template').on('click', function(e) {
        e.preventDefault();
        if (confirm('<?php _e('Tem certeza que deseja excluir este template?', 'amigopet-wp'); ?>')) {
            // Implementar exclusão via AJAX
        }
    });

    // Visualizar template
    $('.preview-template').on('click', function(e) {
        e.preventDefault();
        // Implementar preview em modal
    });
});
</script>
