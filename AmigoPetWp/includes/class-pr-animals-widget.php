<?php

/**
 * Widget para exibir a grid de animais.
 *
 * @link       https://github.com/wendelmax
 * @since      1.0.0
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 */

/**
 * Widget para exibir a grid de animais.
 *
 * Este widget permite que a grid de animais seja exibida em qualquer área de widgets
 * do tema, com opções de personalização como quantidade de animais, categorias, etc.
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 * @author     Jackson Sá
 */
class APWP_Animals_Widget extends WP_Widget {

    /**
     * Inicializa o widget.
     */
    public function __construct() {
        parent::__construct(
            'apwp_animals_widget', // Base ID
            __('AmigoPet WP - Grid de Animais', 'amigopet-wp'), // Nome
            array(
                'description' => __('Exibe uma grid de animais disponíveis para adoção', 'amigopet-wp'),
                'classname' => 'pr-animals-widget',
            )
        );
    }

    /**
     * Front-end do widget.
     *
     * @param array $args     Argumentos do widget.
     * @param array $instance Valores salvos.
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        // Parâmetros para a consulta de animais
        $query_args = array(
            'post_type' => 'apwp_animal',
            'posts_per_page' => !empty($instance['num_animals']) ? $instance['num_animals'] : 6,
            'orderby' => !empty($instance['orderby']) ? $instance['orderby'] : 'date',
            'order' => !empty($instance['order']) ? $instance['order'] : 'DESC',
        );

        // Adiciona filtro por espécie se especificado
        if (!empty($instance['species'])) {
            $query_args['tax_query'][] = array(
                'taxonomy' => 'apwp_species',
                'field' => 'term_id',
                'terms' => $instance['species'],
            );
        }

        // Adiciona filtro por status se especificado
        if (!empty($instance['status'])) {
            $query_args['meta_query'][] = array(
                'key' => '_apwp_status',
                'value' => $instance['status'],
            );
        }

        $animals = new WP_Query($query_args);

        if ($animals->have_posts()) {
            echo '<div class="pr-animals-grid">';
            while ($animals->have_posts()) {
                $animals->the_post();
                include AMIGOPET_WP_PLUGIN_DIR . 'public/partials/animal-card.php';
            }
            echo '</div>';

            if (!empty($instance['show_more_link']) && $instance['show_more_link'] === 'yes') {
                echo '<p class="pr-more-link"><a href="' . esc_url(get_post_type_archive_link('apwp_animal')) . '">' . 
                     esc_html__('Ver mais animais', 'amigopet-wp') . '</a></p>';
            }
        } else {
            echo '<p>' . esc_html__('Nenhum animal disponível no momento.', 'amigopet-wp') . '</p>';
        }

        wp_reset_postdata();

        echo $args['after_widget'];
    }

    /**
     * Back-end do widget.
     *
     * @param array $instance Valores salvos anteriormente.
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $num_animals = !empty($instance['num_animals']) ? $instance['num_animals'] : 6;
        $orderby = !empty($instance['orderby']) ? $instance['orderby'] : 'date';
        $order = !empty($instance['order']) ? $instance['order'] : 'DESC';
        $species = !empty($instance['species']) ? $instance['species'] : '';
        $status = !empty($instance['status']) ? $instance['status'] : '';
        $show_more_link = !empty($instance['show_more_link']) ? $instance['show_more_link'] : 'yes';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Título:', 'amigopet-wp'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('num_animals')); ?>">
                <?php esc_html_e('Número de animais:', 'amigopet-wp'); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('num_animals')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('num_animals')); ?>" type="number"
                   step="1" min="1" max="12" value="<?php echo esc_attr($num_animals); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('orderby')); ?>">
                <?php esc_html_e('Ordenar por:', 'amigopet-wp'); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('orderby')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('orderby')); ?>">
                <option value="date" <?php selected($orderby, 'date'); ?>>
                    <?php esc_html_e('Data', 'amigopet-wp'); ?>
                </option>
                <option value="title" <?php selected($orderby, 'title'); ?>>
                    <?php esc_html_e('Nome', 'amigopet-wp'); ?>
                </option>
                <option value="rand" <?php selected($orderby, 'rand'); ?>>
                    <?php esc_html_e('Aleatório', 'amigopet-wp'); ?>
                </option>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('order')); ?>">
                <?php esc_html_e('Ordem:', 'amigopet-wp'); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('order')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('order')); ?>">
                <option value="DESC" <?php selected($order, 'DESC'); ?>>
                    <?php esc_html_e('Decrescente', 'amigopet-wp'); ?>
                </option>
                <option value="ASC" <?php selected($order, 'ASC'); ?>>
                    <?php esc_html_e('Crescente', 'amigopet-wp'); ?>
                </option>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('species')); ?>">
                <?php esc_html_e('Espécie:', 'amigopet-wp'); ?>
            </label>
            <?php
            $species_terms = get_terms(array(
                'taxonomy' => 'apwp_species',
                'hide_empty' => false,
            ));
            if (!empty($species_terms) && !is_wp_error($species_terms)) : ?>
                <select class="widefat" id="<?php echo esc_attr($this->get_field_id('species')); ?>"
                        name="<?php echo esc_attr($this->get_field_name('species')); ?>">
                    <option value=""><?php esc_html_e('Todas', 'amigopet-wp'); ?></option>
                    <?php foreach ($species_terms as $term) : ?>
                        <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected($species, $term->term_id); ?>>
                            <?php echo esc_html($term->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('status')); ?>">
                <?php esc_html_e('Status:', 'amigopet-wp'); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('status')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('status')); ?>">
                <option value=""><?php esc_html_e('Todos', 'amigopet-wp'); ?></option>
                <option value="available" <?php selected($status, 'available'); ?>>
                    <?php esc_html_e('Disponível', 'amigopet-wp'); ?>
                </option>
                <option value="adopted" <?php selected($status, 'adopted'); ?>>
                    <?php esc_html_e('Adotado', 'amigopet-wp'); ?>
                </option>
            </select>
        </p>

        <p>
            <input class="checkbox" type="checkbox" id="<?php echo esc_attr($this->get_field_id('show_more_link')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('show_more_link')); ?>"
                   value="yes" <?php checked($show_more_link, 'yes'); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_more_link')); ?>">
                <?php esc_html_e('Mostrar link "Ver mais"', 'amigopet-wp'); ?>
            </label>
        </p>
        <?php
    }

    /**
     * Processa as opções do widget para salvar.
     *
     * @param array $new_instance Novos valores.
     * @param array $old_instance Valores antigos.
     *
     * @return array Valores atualizados.
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['num_animals'] = (!empty($new_instance['num_animals'])) ? absint($new_instance['num_animals']) : 6;
        $instance['orderby'] = (!empty($new_instance['orderby'])) ? $new_instance['orderby'] : 'date';
        $instance['order'] = (!empty($new_instance['order'])) ? $new_instance['order'] : 'DESC';
        $instance['species'] = (!empty($new_instance['species'])) ? absint($new_instance['species']) : '';
        $instance['status'] = (!empty($new_instance['status'])) ? $new_instance['status'] : '';
        $instance['show_more_link'] = (!empty($new_instance['show_more_link'])) ? 'yes' : 'no';

        return $instance;
    }
}
