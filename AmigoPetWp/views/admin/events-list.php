<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Eventos', 'amigopet-wp'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=apwp-event-form'); ?>" class="page-title-action"><?php _e('Adicionar Novo', 'amigopet-wp'); ?></a>
    <hr class="wp-header-end">
    
    <?php
    // Mensagens de feedback
    if (isset($_GET['message'])) {
        $message = intval($_GET['message']);
        if ($message === 1) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Evento salvo com sucesso.', 'amigopet-wp') . '</p></div>';
        } elseif ($message === 2) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Evento excluído com sucesso.', 'amigopet-wp') . '</p></div>';
        }
    }
    
    // Busca os eventos
    $events_query = new WP_Query([
        'post_type' => 'apwp_event',
        'posts_per_page' => -1,
        'orderby' => 'meta_value',
        'meta_key' => 'event_date',
        'order' => 'ASC',
        'meta_query' => [
            [
                'key' => 'event_date',
                'value' => date('Y-m-d'),
                'compare' => '>=',
                'type' => 'DATE'
            ]
        ]
    ]);
    ?>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="column-image"><?php _e('Imagem', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Título', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Tipo', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Data', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Local', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Organizador', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Ações', 'amigopet-wp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($events_query->have_posts()) : ?>
                <?php while ($events_query->have_posts()) : $events_query->the_post(); ?>
                    <?php
                    $event_title = get_post_meta(get_the_ID(), 'event_title', true);
                    $event_type = get_post_meta(get_the_ID(), 'event_type', true);
                    $event_date = get_post_meta(get_the_ID(), 'event_date', true);
                    $event_time = get_post_meta(get_the_ID(), 'event_time', true);
                    $event_location = get_post_meta(get_the_ID(), 'event_location', true);
                    $event_organizer = get_post_meta(get_the_ID(), 'event_organizer', true);
                    
                    $type_labels = [
                        'adoption_fair' => __('Feira de Adoção', 'amigopet-wp'),
                        'fundraising' => __('Arrecadação de Fundos', 'amigopet-wp'),
                        'vaccination' => __('Campanha de Vacinação', 'amigopet-wp'),
                        'other' => __('Outro', 'amigopet-wp')
                    ];
                    ?>
                    <tr>
                        <td class="column-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php echo get_the_post_thumbnail(get_the_ID(), [50, 50]); ?>
                            <?php else : ?>
                                <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'assets/images/default-event.png'; ?>" width="50" height="50" alt="">
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($event_title); ?></td>
                        <td><?php echo esc_html($type_labels[$event_type]); ?></td>
                        <td>
                            <?php 
                            echo esc_html(date_i18n(get_option('date_format'), strtotime($event_date)));
                            if ($event_time) {
                                echo ' ' . esc_html(date_i18n(get_option('time_format'), strtotime($event_time)));
                            }
                            ?>
                        </td>
                        <td><?php echo esc_html($event_location); ?></td>
                        <td><?php echo esc_html($event_organizer); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=apwp-event-form&event_id=' . get_the_ID()); ?>" class="button button-small">
                                <?php _e('Editar', 'amigopet-wp'); ?>
                            </a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=apwp_delete_event&event_id=' . get_the_ID()), 'delete_event_' . get_the_ID()); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php _e('Tem certeza que deseja excluir este evento?', 'amigopet-wp'); ?>')">
                                <?php _e('Excluir', 'amigopet-wp'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7"><?php _e('Nenhum evento encontrado.', 'amigopet-wp'); ?></td>
                </tr>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </tbody>
    </table>
</div>

<style>
.column-image {
    width: 60px;
}

.column-image img {
    border-radius: 4px;
    object-fit: cover;
}
</style>
