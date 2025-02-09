<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Adoções', 'amigopet-wp'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=apwp-adoption-form'); ?>" class="page-title-action"><?php _e('Adicionar Nova', 'amigopet-wp'); ?></a>
    <hr class="wp-header-end">
    
    <?php
    // Mensagens de feedback
    if (isset($_GET['message'])) {
        $message = intval($_GET['message']);
        if ($message === 1) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Adoção salva com sucesso.', 'amigopet-wp') . '</p></div>';
        } elseif ($message === 2) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Adoção excluída com sucesso.', 'amigopet-wp') . '</p></div>';
        }
    }
    
    // Busca as adoções
    $adoptions_query = new WP_Query([
        'post_type' => 'apwp_adoption',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC'
    ]);
    ?>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col"><?php _e('Pet', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Adotante', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Data', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Status', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Ações', 'amigopet-wp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($adoptions_query->have_posts()) : ?>
                <?php while ($adoptions_query->have_posts()) : $adoptions_query->the_post(); ?>
                    <?php
                    $pet_id = get_post_meta(get_the_ID(), 'pet_id', true);
                    $pet_name = get_post_meta($pet_id, 'pet_name', true);
                    $adopter_name = get_post_meta(get_the_ID(), 'adopter_name', true);
                    $adoption_date = get_post_meta(get_the_ID(), 'adoption_date', true);
                    $adoption_status = get_post_meta(get_the_ID(), 'adoption_status', true);
                    ?>
                    <tr>
                        <td><?php echo esc_html($pet_name); ?></td>
                        <td><?php echo esc_html($adopter_name); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($adoption_date))); ?></td>
                        <td>
                            <?php
                            $status_labels = [
                                'pending' => __('Pendente', 'amigopet-wp'),
                                'approved' => __('Aprovada', 'amigopet-wp'),
                                'rejected' => __('Rejeitada', 'amigopet-wp'),
                                'completed' => __('Concluída', 'amigopet-wp')
                            ];
                            $status_classes = [
                                'pending' => 'status-pending',
                                'approved' => 'status-approved',
                                'rejected' => 'status-rejected',
                                'completed' => 'status-completed'
                            ];
                            ?>
                            <span class="adoption-status <?php echo esc_attr($status_classes[$adoption_status]); ?>">
                                <?php echo esc_html($status_labels[$adoption_status]); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=apwp-adoption-form&adoption_id=' . get_the_ID()); ?>" class="button button-small">
                                <?php _e('Editar', 'amigopet-wp'); ?>
                            </a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=apwp_delete_adoption&adoption_id=' . get_the_ID()), 'delete_adoption_' . get_the_ID()); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php _e('Tem certeza que deseja excluir esta adoção?', 'amigopet-wp'); ?>')">
                                <?php _e('Excluir', 'amigopet-wp'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5"><?php _e('Nenhuma adoção encontrada.', 'amigopet-wp'); ?></td>
                </tr>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </tbody>
    </table>
</div>

<style>
.adoption-status {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}

.status-pending {
    background-color: #f0f0f0;
    color: #666;
}

.status-approved {
    background-color: #c6e1c6;
    color: #5b841b;
}

.status-rejected {
    background-color: #f1adad;
    color: #dc3232;
}

.status-completed {
    background-color: #c8d7e1;
    color: #2271b1;
}
</style>
