<?php
/**
 * Template para a página de tipos de termos
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

// Processa o formulário
if (isset($_POST['action']) && $_POST['action'] === 'add_term_type') {
    if (check_admin_referer('apwp_add_term_type', 'apwp_nonce')) {
        $term_type->name = sanitize_text_field($_POST['name']);
        $term_type->slug = sanitize_title($_POST['name']);
        $term_type->description = sanitize_textarea_field($_POST['description']);
        $term_type->roles = isset($_POST['roles']) ? array_map('sanitize_text_field', $_POST['roles']) : array();
        $term_type->status = 'active';
        
        if ($term_type->save()) {
            add_settings_error(
                'apwp_messages',
                'apwp_term_type_added',
                __('Tipo de termo adicionado com sucesso.', 'amigopet-wp'),
                'success'
            );
        }
    }
}

// Lista todos os tipos de termos
$term_types = $term_type->list();

// Obtém todas as roles do WordPress
$wp_roles = wp_roles();
$available_roles = $wp_roles->get_names();
?>

<div class="wrap">
    <h1><?php _e('Tipos de Termos', 'amigopet-wp'); ?></h1>
    
    <?php settings_errors('apwp_messages'); ?>

    <div class="apwp-term-types-container">
        <!-- Formulário para adicionar novo tipo -->
        <div class="apwp-term-type-form">
            <h2><?php _e('Adicionar Novo Tipo de Termo', 'amigopet-wp'); ?></h2>
            
            <form method="post" action="">
                <?php wp_nonce_field('apwp_add_term_type', 'apwp_nonce'); ?>
                <input type="hidden" name="action" value="add_term_type">
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="name"><?php _e('Nome', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="name" id="name" class="regular-text" required>
                            <p class="description"><?php _e('Nome do tipo de termo (ex: Adoção, Apadrinhamento)', 'amigopet-wp'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="description"><?php _e('Descrição', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <textarea name="description" id="description" class="large-text" rows="4"></textarea>
                            <p class="description"><?php _e('Descrição do tipo de termo e sua finalidade', 'amigopet-wp'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <?php _e('Funções Permitidas', 'amigopet-wp'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <?php _e('Funções que podem assinar este tipo de termo', 'amigopet-wp'); ?>
                                </legend>
                                <?php foreach ($available_roles as $role => $label) : ?>
                                    <label>
                                        <input type="checkbox" name="roles[]" value="<?php echo esc_attr($role); ?>">
                                        <?php echo esc_html($label); ?>
                                    </label><br>
                                <?php endforeach; ?>
                            </fieldset>
                            <p class="description">
                                <?php _e('Selecione quais funções podem assinar este tipo de termo', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('Adicionar Tipo de Termo', 'amigopet-wp')); ?>
            </form>
        </div>

        <!-- Lista de tipos existentes -->
        <div class="apwp-term-types-list">
            <h2><?php _e('Tipos de Termos Existentes', 'amigopet-wp'); ?></h2>
            
            <?php if (!empty($term_types)) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Nome', 'amigopet-wp'); ?></th>
                            <th><?php _e('Descrição', 'amigopet-wp'); ?></th>
                            <th><?php _e('Funções', 'amigopet-wp'); ?></th>
                            <th><?php _e('Templates', 'amigopet-wp'); ?></th>
                            <th><?php _e('Ações', 'amigopet-wp'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($term_types as $type) : 
                            $templates = $term_template->list(array('type_id' => $type['id']));
                            $roles = maybe_unserialize($type['roles']);
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($type['name']); ?></strong>
                                </td>
                                <td><?php echo esc_html($type['description']); ?></td>
                                <td>
                                    <?php
                                    if (!empty($roles)) {
                                        $role_labels = array();
                                        foreach ($roles as $role) {
                                            if (isset($available_roles[$role])) {
                                                $role_labels[] = $available_roles[$role];
                                            }
                                        }
                                        echo esc_html(implode(', ', $role_labels));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo count($templates); ?>
                                    <a href="<?php echo admin_url('admin.php?page=amigopet-wp-term-templates&type=' . $type['id']); ?>" class="button button-small">
                                        <?php _e('Gerenciar Templates', 'amigopet-wp'); ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="row-actions">
                                        <span class="edit">
                                            <a href="#" data-id="<?php echo esc_attr($type['id']); ?>">
                                                <?php _e('Editar', 'amigopet-wp'); ?>
                                            </a> |
                                        </span>
                                        <span class="delete">
                                            <a href="#" class="delete-term-type" data-id="<?php echo esc_attr($type['id']); ?>">
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
                <p><?php _e('Nenhum tipo de termo cadastrado.', 'amigopet-wp'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.apwp-term-types-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-top: 20px;
}

.apwp-term-type-form,
.apwp-term-types-list {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.apwp-term-type-form h2,
.apwp-term-types-list h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

@media screen and (max-width: 782px) {
    .apwp-term-types-container {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.delete-term-type').on('click', function(e) {
        e.preventDefault();
        if (confirm('<?php _e('Tem certeza que deseja excluir este tipo de termo?', 'amigopet-wp'); ?>')) {
            // Implementar exclusão via AJAX
        }
    });
});
</script>
