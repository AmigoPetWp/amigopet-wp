<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Termos', 'amigopet-wp'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=apwp-term-form'); ?>" class="page-title-action"><?php _e('Adicionar Novo', 'amigopet-wp'); ?></a>
    <hr class="wp-header-end">
    
    <?php
    // Mensagens de feedback
    if (isset($_GET['message'])) {
        $message = intval($_GET['message']);
        if ($message === 1) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Termo salvo com sucesso.', 'amigopet-wp') . '</p></div>';
        } elseif ($message === 2) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Termo excluído com sucesso.', 'amigopet-wp') . '</p></div>';
        }
    }
    
    // Busca os termos
    $terms_query = new WP_Query([
        'post_type' => 'apwp_term',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ]);
    ?>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col"><?php _e('Título', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Tipo', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Status', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Data de Criação', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Última Atualização', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Ações', 'amigopet-wp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($terms_query->have_posts()) : ?>
                <?php while ($terms_query->have_posts()) : $terms_query->the_post(); ?>
                    <?php
                    $term_type = get_post_meta(get_the_ID(), 'term_type', true);
                    $term_status = get_post_meta(get_the_ID(), 'term_status', true);
                    
                    $type_labels = [
                        'adoption' => __('Adoção', 'amigopet-wp'),
                        'volunteer' => __('Voluntariado', 'amigopet-wp'),
                        'donation' => __('Doação', 'amigopet-wp'),
                        'privacy' => __('Privacidade', 'amigopet-wp'),
                        'other' => __('Outro', 'amigopet-wp')
                    ];
                    
                    $status_labels = [
                        'active' => __('Ativo', 'amigopet-wp'),
                        'inactive' => __('Inativo', 'amigopet-wp'),
                        'draft' => __('Rascunho', 'amigopet-wp')
                    ];
                    ?>
                    <tr>
                        <td>
                            <strong>
                                <a href="<?php echo admin_url('admin.php?page=apwp-term-form&term_id=' . get_the_ID()); ?>" class="row-title">
                                    <?php echo esc_html(get_the_title()); ?>
                                </a>
                            </strong>
                        </td>
                        <td><?php echo esc_html($type_labels[$term_type] ?? $type_labels['other']); ?></td>
                        <td>
                            <span class="term-status status-<?php echo esc_attr($term_status); ?>">
                                <?php echo esc_html($status_labels[$term_status] ?? $status_labels['draft']); ?>
                            </span>
                        </td>
                        <td><?php echo get_the_date(); ?></td>
                        <td><?php echo get_the_modified_date(); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=apwp-term-form&term_id=' . get_the_ID()); ?>" class="button button-small">
                                <?php _e('Editar', 'amigopet-wp'); ?>
                            </a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=apwp_delete_term&term_id=' . get_the_ID()), 'delete_term_' . get_the_ID()); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php _e('Tem certeza que deseja excluir este termo?', 'amigopet-wp'); ?>')">
                                <?php _e('Excluir', 'amigopet-wp'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6"><?php _e('Nenhum termo encontrado.', 'amigopet-wp'); ?></td>
                </tr>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </tbody>
    </table>
</div>

<style>
.term-status {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}

.status-active {
    background-color: #c6e1c6;
    color: #5b841b;
}

.status-inactive {
    background-color: #f1adad;
    color: #dc3232;
}

.status-draft {
    background-color: #e5e5e5;
    color: #666;
}
</style>
