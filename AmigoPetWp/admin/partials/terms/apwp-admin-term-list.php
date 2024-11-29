<?php
/**
 * Template para listar termos e contratos
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Obtém os termos do banco de dados
global $wpdb;
$table_name = $wpdb->prefix . 'apwp_terms';
$terms = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

// Mensagens de feedback
if (isset($_GET['message'])) {
    $message = '';
    switch ($_GET['message']) {
        case '1':
            $message = __('Termo adicionado com sucesso.', 'amigopet-wp');
            break;
        case '2':
            $message = __('Termo atualizado com sucesso.', 'amigopet-wp');
            break;
        case '3':
            $message = __('Termo excluído com sucesso.', 'amigopet-wp');
            break;
    }
    if ($message) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($message) . '</p></div>';
    }
}
?>

<div class="apwp-terms-list">
    <?php if (!empty($terms)) : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col"><?php _e('Nome', 'amigopet-wp'); ?></th>
                    <th scope="col"><?php _e('Tipo', 'amigopet-wp'); ?></th>
                    <th scope="col"><?php _e('Status', 'amigopet-wp'); ?></th>
                    <th scope="col"><?php _e('Assinaturas', 'amigopet-wp'); ?></th>
                    <th scope="col"><?php _e('Última Atualização', 'amigopet-wp'); ?></th>
                    <th scope="col"><?php _e('Ações', 'amigopet-wp'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($terms as $term) : ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($term->name); ?></strong>
                        </td>
                        <td><?php echo esc_html($term->type); ?></td>
                        <td>
                            <span class="apwp-status-badge status-<?php echo esc_attr($term->status); ?>">
                                <?php echo esc_html(ucfirst($term->status)); ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $signatures = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(*) FROM {$wpdb->prefix}apwp_term_signatures WHERE term_id = %d",
                                $term->id
                            ));
                            echo esc_html($signatures);
                            ?>
                        </td>
                        <td>
                            <?php echo esc_html(wp_date(
                                get_option('date_format') . ' ' . get_option('time_format'),
                                strtotime($term->updated_at)
                            )); ?>
                        </td>
                        <td class="actions">
                            <a href="<?php echo add_query_arg(['action' => 'edit', 'id' => $term->id]); ?>" 
                               class="button button-small">
                                <span class="dashicons dashicons-edit"></span>
                                <?php _e('Editar', 'amigopet-wp'); ?>
                            </a>
                            <a href="<?php echo add_query_arg(['action' => 'preview', 'id' => $term->id]); ?>" 
                               class="button button-small">
                                <span class="dashicons dashicons-visibility"></span>
                                <?php _e('Visualizar', 'amigopet-wp'); ?>
                            </a>
                            <?php if ($term->status === 'draft') : ?>
                                <a href="<?php echo wp_nonce_url(
                                    add_query_arg(['action' => 'delete', 'id' => $term->id]),
                                    'delete_term_' . $term->id
                                ); ?>" 
                                   class="button button-small delete-term" 
                                   onclick="return confirm('<?php esc_attr_e('Tem certeza que deseja excluir este termo?', 'amigopet-wp'); ?>')">
                                    <span class="dashicons dashicons-trash"></span>
                                    <?php _e('Excluir', 'amigopet-wp'); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="apwp-no-items">
            <p><?php _e('Nenhum termo ou contrato encontrado.', 'amigopet-wp'); ?></p>
            <a href="<?php echo add_query_arg('tab', 'add'); ?>" class="button button-primary">
                <span class="dashicons dashicons-plus"></span>
                <?php _e('Adicionar Novo Termo', 'amigopet-wp'); ?>
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    // Confirmação de exclusão
    $('.delete-term').on('click', function(e) {
        if (!confirm($(this).data('confirm'))) {
            e.preventDefault();
        }
    });
});
</script>
