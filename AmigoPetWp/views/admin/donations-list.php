<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Doações', 'amigopet-wp'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=apwp-donation-form'); ?>" class="page-title-action"><?php _e('Adicionar Nova', 'amigopet-wp'); ?></a>
    <hr class="wp-header-end">
    
    <?php
    // Mensagens de feedback
    if (isset($_GET['message'])) {
        $message = intval($_GET['message']);
        if ($message === 1) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Doação salva com sucesso.', 'amigopet-wp') . '</p></div>';
        } elseif ($message === 2) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Doação excluída com sucesso.', 'amigopet-wp') . '</p></div>';
        }
    }
    
    // Busca as doações
    $donations_query = new WP_Query([
        'post_type' => 'apwp_donation',
        'posts_per_page' => -1,
        'orderby' => 'meta_value',
        'meta_key' => 'donation_date',
        'order' => 'DESC'
    ]);
    ?>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col"><?php _e('Doador', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Tipo', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Valor/Itens', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Data', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Status', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Contato', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Ações', 'amigopet-wp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($donations_query->have_posts()) : ?>
                <?php while ($donations_query->have_posts()) : $donations_query->the_post(); ?>
                    <?php
                    $donor_name = get_post_meta(get_the_ID(), 'donor_name', true);
                    $donor_email = get_post_meta(get_the_ID(), 'donor_email', true);
                    $donor_phone = get_post_meta(get_the_ID(), 'donor_phone', true);
                    $donation_type = get_post_meta(get_the_ID(), 'donation_type', true);
                    $donation_amount = get_post_meta(get_the_ID(), 'donation_amount', true);
                    $donation_items = get_post_meta(get_the_ID(), 'donation_items', true);
                    $donation_date = get_post_meta(get_the_ID(), 'donation_date', true);
                    $donation_status = get_post_meta(get_the_ID(), 'donation_status', true);
                    
                    $type_labels = [
                        'money' => __('Dinheiro', 'amigopet-wp'),
                        'food' => __('Ração', 'amigopet-wp'),
                        'medicine' => __('Medicamentos', 'amigopet-wp'),
                        'supplies' => __('Suprimentos', 'amigopet-wp'),
                        'other' => __('Outro', 'amigopet-wp')
                    ];
                    
                    $status_labels = [
                        'pending' => __('Pendente', 'amigopet-wp'),
                        'received' => __('Recebida', 'amigopet-wp'),
                        'cancelled' => __('Cancelada', 'amigopet-wp')
                    ];
                    ?>
                    <tr>
                        <td><?php echo esc_html($donor_name); ?></td>
                        <td><?php echo esc_html($type_labels[$donation_type]); ?></td>
                        <td>
                            <?php
                            if ($donation_type === 'money') {
                                echo 'R$ ' . number_format((float)$donation_amount, 2, ',', '.');
                            } else {
                                echo nl2br(esc_html($donation_items));
                            }
                            ?>
                        </td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($donation_date))); ?></td>
                        <td>
                            <span class="donation-status status-<?php echo esc_attr($donation_status); ?>">
                                <?php echo esc_html($status_labels[$donation_status]); ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $contact_info = [];
                            if ($donor_email) $contact_info[] = $donor_email;
                            if ($donor_phone) $contact_info[] = $donor_phone;
                            echo esc_html(implode(' / ', $contact_info));
                            ?>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=apwp-donation-form&donation_id=' . get_the_ID()); ?>" class="button button-small">
                                <?php _e('Editar', 'amigopet-wp'); ?>
                            </a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=apwp_delete_donation&donation_id=' . get_the_ID()), 'delete_donation_' . get_the_ID()); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php _e('Tem certeza que deseja excluir esta doação?', 'amigopet-wp'); ?>')">
                                <?php _e('Excluir', 'amigopet-wp'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7"><?php _e('Nenhuma doação encontrada.', 'amigopet-wp'); ?></td>
                </tr>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </tbody>
    </table>
</div>

<style>
.donation-status {
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

.status-received {
    background-color: #c6e1c6;
    color: #5b841b;
}

.status-cancelled {
    background-color: #f1adad;
    color: #dc3232;
}
</style>
