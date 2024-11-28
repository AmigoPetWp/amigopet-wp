<?php
/**
 * Widget para exibir pets
 */
class APWP_Pets_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'apwp_pets_widget',
            __('AmigoPet WP - Pets', 'amigopet-wp'),
            array('description' => __('Exibe uma grade de pets disponíveis para adoção', 'amigopet-wp'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $pet = new APWP_Pet();
        $pets = $pet->list(array(
            'status' => 'available',
            'limit' => isset($instance['limit']) ? intval($instance['limit']) : 6
        ));

        if (!empty($pets)) {
            echo '<div class="apwp-pets-grid">';
            foreach ($pets as $item) {
                ?>
                <div class="apwp-pet-item">
                    <?php if (!empty($item->photo_url)): ?>
                        <img src="<?php echo esc_url($item->photo_url); ?>" alt="<?php echo esc_attr($item->name); ?>">
                    <?php endif; ?>
                    <h3><?php echo esc_html($item->name); ?></h3>
                    <p><?php echo esc_html($item->breed); ?></p>
                    <button class="apwp-pet-details" data-pet-id="<?php echo esc_attr($item->id); ?>">
                        <?php _e('Ver Detalhes', 'amigopet-wp'); ?>
                    </button>
                </div>
                <?php
            }
            echo '</div>';
        } else {
            echo '<p>' . __('Nenhum pet disponível no momento.', 'amigopet-wp') . '</p>';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Pets para Adoção', 'amigopet-wp');
        $limit = !empty($instance['limit']) ? intval($instance['limit']) : 6;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php _e('Título:', 'amigopet-wp'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>">
                <?php _e('Número de pets:', 'amigopet-wp'); ?>
            </label>
            <input class="tiny-text" 
                   id="<?php echo esc_attr($this->get_field_id('limit')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('limit')); ?>" 
                   type="number" 
                   step="1" 
                   min="1" 
                   value="<?php echo esc_attr($limit); ?>" 
                   size="3">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['limit'] = (!empty($new_instance['limit'])) ? intval($new_instance['limit']) : 6;
        return $instance;
    }
}
