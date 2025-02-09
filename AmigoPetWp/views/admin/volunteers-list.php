<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Voluntários', 'amigopet-wp'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=apwp-volunteer-form'); ?>" class="page-title-action"><?php _e('Adicionar Novo', 'amigopet-wp'); ?></a>
    <hr class="wp-header-end">
    
    <?php
    // Mensagens de feedback
    if (isset($_GET['message'])) {
        $message = intval($_GET['message']);
        if ($message === 1) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Voluntário salvo com sucesso.', 'amigopet-wp') . '</p></div>';
        } elseif ($message === 2) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Voluntário excluído com sucesso.', 'amigopet-wp') . '</p></div>';
        }
    }
    
    // Busca os voluntários
    $volunteers_query = new WP_Query([
        'post_type' => 'apwp_volunteer',
        'posts_per_page' => -1,
        'orderby' => 'meta_value',
        'meta_key' => 'volunteer_name',
        'order' => 'ASC'
    ]);
    ?>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="column-image"><?php _e('Foto', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Nome', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Email', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Telefone', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Disponibilidade', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Habilidades', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Status', 'amigopet-wp'); ?></th>
                <th scope="col"><?php _e('Ações', 'amigopet-wp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($volunteers_query->have_posts()) : ?>
                <?php while ($volunteers_query->have_posts()) : $volunteers_query->the_post(); ?>
                    <?php
                    $volunteer_name = get_post_meta(get_the_ID(), 'volunteer_name', true);
                    $volunteer_email = get_post_meta(get_the_ID(), 'volunteer_email', true);
                    $volunteer_phone = get_post_meta(get_the_ID(), 'volunteer_phone', true);
                    $volunteer_availability = get_post_meta(get_the_ID(), 'volunteer_availability', true);
                    $volunteer_skills = get_post_meta(get_the_ID(), 'volunteer_skills', true);
                    $volunteer_status = get_post_meta(get_the_ID(), 'volunteer_status', true);
                    
                    $availability_labels = [
                        'morning' => __('Manhã', 'amigopet-wp'),
                        'afternoon' => __('Tarde', 'amigopet-wp'),
                        'evening' => __('Noite', 'amigopet-wp'),
                        'weekend' => __('Fim de Semana', 'amigopet-wp')
                    ];
                    
                    $skills_labels = [
                        'pet_care' => __('Cuidados com Pets', 'amigopet-wp'),
                        'veterinary' => __('Veterinária', 'amigopet-wp'),
                        'grooming' => __('Banho e Tosa', 'amigopet-wp'),
                        'driving' => __('Motorista', 'amigopet-wp'),
                        'photography' => __('Fotografia', 'amigopet-wp'),
                        'social_media' => __('Redes Sociais', 'amigopet-wp')
                    ];
                    ?>
                    <tr>
                        <td class="column-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php echo get_the_post_thumbnail(get_the_ID(), [50, 50]); ?>
                            <?php else : ?>
                                <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'assets/images/default-volunteer.png'; ?>" width="50" height="50" alt="">
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($volunteer_name); ?></td>
                        <td><?php echo esc_html($volunteer_email); ?></td>
                        <td><?php echo esc_html($volunteer_phone); ?></td>
                        <td>
                            <?php
                            if (is_array($volunteer_availability)) {
                                $availability_text = array_map(function($availability) use ($availability_labels) {
                                    return $availability_labels[$availability];
                                }, $volunteer_availability);
                                echo esc_html(implode(', ', $availability_text));
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (is_array($volunteer_skills)) {
                                $skills_text = array_map(function($skill) use ($skills_labels) {
                                    return $skills_labels[$skill];
                                }, $volunteer_skills);
                                echo esc_html(implode(', ', $skills_text));
                            }
                            ?>
                        </td>
                        <td>
                            <span class="volunteer-status status-<?php echo esc_attr($volunteer_status); ?>">
                                <?php echo $volunteer_status === 'active' ? __('Ativo', 'amigopet-wp') : __('Inativo', 'amigopet-wp'); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=apwp-volunteer-form&volunteer_id=' . get_the_ID()); ?>" class="button button-small">
                                <?php _e('Editar', 'amigopet-wp'); ?>
                            </a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=apwp_delete_volunteer&volunteer_id=' . get_the_ID()), 'delete_volunteer_' . get_the_ID()); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php _e('Tem certeza que deseja excluir este voluntário?', 'amigopet-wp'); ?>')">
                                <?php _e('Excluir', 'amigopet-wp'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8"><?php _e('Nenhum voluntário encontrado.', 'amigopet-wp'); ?></td>
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
    border-radius: 50%;
    object-fit: cover;
}

.volunteer-status {
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
</style>
