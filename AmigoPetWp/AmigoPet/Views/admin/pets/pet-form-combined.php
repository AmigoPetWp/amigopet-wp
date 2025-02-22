<?php
/**
 * Template para formulário de pet no admin
 * Combina as melhores características de ambas implementações anteriores
 */
if (!defined('ABSPATH')) {
    exit;
}

$pet_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $pet_id > 0;
$pet = $is_edit ? get_post($pet_id) : null;

if ($is_edit && (!$pet || $pet->post_type !== 'apwp_pet')) {
    wp_die(__('Pet não encontrado', 'amigopet-wp'));
}

$title = $is_edit ? __('Editar Pet', 'amigopet-wp') : __('Adicionar Novo Pet', 'amigopet-wp');

// Carrega dados do pet se estiver editando
$pet_data = $is_edit ? [
    'name' => get_post_meta($pet_id, 'pet_name', true),
    'species_id' => get_post_meta($pet_id, 'species_id', true),
    'breed' => get_post_meta($pet_id, 'breed', true),
    'age' => get_post_meta($pet_id, 'age', true),
    'size' => get_post_meta($pet_id, 'size', true),
    'gender' => get_post_meta($pet_id, 'gender', true),
    'vaccinated' => get_post_meta($pet_id, 'vaccinated', true),
    'neutered' => get_post_meta($pet_id, 'neutered', true),
    'description' => get_post_meta($pet_id, 'description', true),
    'status' => get_post_meta($pet_id, 'status', true)
] : [];

// Carrega as espécies do banco de dados
global $wpdb;
$species = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}amigopet_pet_species");
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
    <a href="<?php echo remove_query_arg(['action', 'id']); ?>" class="page-title-action">
        <?php _e('Voltar para Lista', 'amigopet-wp'); ?>
    </a>
    
    <div class="apwp-pet-form">
        <form id="apwp-pet-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
            <?php wp_nonce_field('apwp_save_pet', 'apwp_pet_nonce'); ?>
            <input type="hidden" name="action" value="apwp_save_pet">
            <?php if ($is_edit): ?>
                <input type="hidden" name="pet_id" value="<?php echo $pet_id; ?>">
            <?php endif; ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="pet_name"><?php _e('Nome', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="pet_name" name="pet_name" class="regular-text" required
                            value="<?php echo esc_attr($pet_data['name'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="species_id"><?php _e('Espécie', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="species_id" name="species_id" required>
                            <option value=""><?php _e('Selecione uma espécie', 'amigopet-wp'); ?></option>
                            <?php foreach ($species as $specie): ?>
                                <option value="<?php echo esc_attr($specie->id); ?>"
                                    <?php selected($pet_data['species_id'] ?? '', $specie->id); ?>>
                                    <?php echo esc_html($specie->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="breed"><?php _e('Raça', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="breed" name="breed" class="regular-text"
                            value="<?php echo esc_attr($pet_data['breed'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="age"><?php _e('Idade Aproximada', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="number" id="age" name="age" class="small-text" min="0" required
                            value="<?php echo esc_attr($pet_data['age'] ?? ''); ?>">
                        <p class="description"><?php _e('Idade em anos. Para filhotes menores de 1 ano, use 0.', 'amigopet-wp'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="size"><?php _e('Porte', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="size" name="size" required>
                            <option value=""><?php _e('Selecione o porte', 'amigopet-wp'); ?></option>
                            <option value="small" <?php selected($pet_data['size'] ?? '', 'small'); ?>>
                                <?php _e('Pequeno', 'amigopet-wp'); ?>
                            </option>
                            <option value="medium" <?php selected($pet_data['size'] ?? '', 'medium'); ?>>
                                <?php _e('Médio', 'amigopet-wp'); ?>
                            </option>
                            <option value="large" <?php selected($pet_data['size'] ?? '', 'large'); ?>>
                                <?php _e('Grande', 'amigopet-wp'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="gender"><?php _e('Sexo', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="gender" name="gender" required>
                            <option value=""><?php _e('Selecione o sexo', 'amigopet-wp'); ?></option>
                            <option value="male" <?php selected($pet_data['gender'] ?? '', 'male'); ?>>
                                <?php _e('Macho', 'amigopet-wp'); ?>
                            </option>
                            <option value="female" <?php selected($pet_data['gender'] ?? '', 'female'); ?>>
                                <?php _e('Fêmea', 'amigopet-wp'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Características', 'amigopet-wp'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="vaccinated" value="1"
                                <?php checked($pet_data['vaccinated'] ?? '', '1'); ?>>
                            <?php _e('Vacinado', 'amigopet-wp'); ?>
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="neutered" value="1"
                                <?php checked($pet_data['neutered'] ?? '', '1'); ?>>
                            <?php _e('Castrado', 'amigopet-wp'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="description"><?php _e('Descrição', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <?php
                        wp_editor(
                            $pet_data['description'] ?? '',
                            'description',
                            [
                                'textarea_name' => 'description',
                                'textarea_rows' => 10,
                                'media_buttons' => true,
                                'teeny' => true,
                                'quicktags' => true
                            ]
                        );
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="status"><?php _e('Status', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="status" name="status" required>
                            <option value=""><?php _e('Selecione o status', 'amigopet-wp'); ?></option>
                            <option value="available" <?php selected($pet_data['status'] ?? '', 'available'); ?>>
                                <?php _e('Disponível', 'amigopet-wp'); ?>
                            </option>
                            <option value="adopted" <?php selected($pet_data['status'] ?? '', 'adopted'); ?>>
                                <?php _e('Adotado', 'amigopet-wp'); ?>
                            </option>
                            <option value="unavailable" <?php selected($pet_data['status'] ?? '', 'unavailable'); ?>>
                                <?php _e('Indisponível', 'amigopet-wp'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="pet_photos"><?php _e('Fotos', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <div id="pet-photos-container">
                            <?php if ($is_edit && has_post_thumbnail($pet_id)): ?>
                                <div class="current-thumbnail">
                                    <?php echo get_the_post_thumbnail($pet_id, 'thumbnail'); ?>
                                </div>
                            <?php endif; ?>
                            <input type="file" id="pet_photos" name="pet_photos[]" multiple accept="image/*">
                            <p class="description">
                                <?php _e('Selecione uma ou mais fotos do pet. A primeira foto será usada como foto principal.', 'amigopet-wp'); ?>
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php echo $is_edit ? __('Atualizar Pet', 'amigopet-wp') : __('Adicionar Pet', 'amigopet-wp'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Preview de imagens antes do upload
    $('#pet_photos').on('change', function() {
        var $container = $('#pet-photos-container');
        var $preview = $container.find('.photos-preview');
        
        if (!$preview.length) {
            $preview = $('<div class="photos-preview"></div>').appendTo($container);
        } else {
            $preview.empty();
        }
        
        var files = this.files;
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $preview.append(
                    $('<img>').attr({
                        src: e.target.result,
                        width: 150,
                        height: 150,
                        style: 'object-fit: cover; margin: 5px;'
                    })
                );
            }
            
            reader.readAsDataURL(file);
        }
    });
    
    // Validação do formulário
    $('#apwp-pet-form').on('submit', function(e) {
        var $required = $(this).find('[required]');
        var valid = true;
        
        $required.each(function() {
            if (!$(this).val()) {
                valid = false;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert('<?php _e('Por favor, preencha todos os campos obrigatórios.', 'amigopet-wp'); ?>');
        }
    });
});
</script>

<style>
.apwp-pet-form .error {
    border-color: #dc3232;
}

.apwp-pet-form .required {
    color: #dc3232;
}

.apwp-pet-form .photos-preview {
    margin-top: 10px;
}

.apwp-pet-form .current-thumbnail {
    margin-bottom: 10px;
}

.apwp-pet-form .current-thumbnail img {
    max-width: 150px;
    height: auto;
}
</style>
