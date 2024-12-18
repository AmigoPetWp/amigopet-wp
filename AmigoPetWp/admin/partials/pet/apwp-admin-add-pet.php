<?php
/**
 * Formulário para adicionar/editar pet
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

$pet = new APWP_Pet();
$pet_data = null;
$is_edit = false;

// Se for edição, carrega os dados do pet
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $is_edit = true;
    $pet_data = $pet->get(intval($_GET['id']));
    if (!$pet_data) {
        wp_die(__('Pet não encontrado.', 'amigopet-wp'));
    }
}

?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo $is_edit ? __('Editar Pet', 'amigopet-wp') : __('Adicionar Novo Pet', 'amigopet-wp'); ?>
    </h1>
    
    <?php settings_errors('apwp_messages'); ?>

    <form method="post" action="<?php echo admin_url('admin.php?page=' . $this->plugin_name . '-pets'); ?>">
        <?php wp_nonce_field('apwp_pet_action', 'apwp_nonce'); ?>
        <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit' : 'add'; ?>">
        <?php if ($is_edit): ?>
        <input type="hidden" name="id" value="<?php echo $pet_data->id; ?>">
        <?php endif; ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="name"><?php _e('Nome', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="text" id="name" name="name" class="regular-text" 
                           value="<?php echo $is_edit ? esc_attr($pet_data->name) : ''; ?>" required>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="species"><?php _e('Espécie', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="species" name="species" required>
                        <option value=""><?php _e('Selecione uma espécie', 'amigopet-wp'); ?></option>
                        <?php
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'apwp_species';
                        $species = $wpdb->get_results("SELECT id, name FROM {$table_name} ORDER BY name ASC");
                        
                        foreach ($species as $specie) {
                            printf(
                                '<option value="%s" %s>%s</option>',
                                esc_attr($specie->id),
                                selected($is_edit && $pet_data->species == $specie->id, true, false),
                                esc_html($specie->name)
                            );
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="breed"><?php _e('Raça', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="breed" name="breed" required>
                        <option value=""><?php _e('Selecione uma raça', 'amigopet-wp'); ?></option>
                        <?php
                        if ($is_edit && $pet_data->species) {
                            $table_name = $wpdb->prefix . 'apwp_breeds';
                            $breeds = $wpdb->get_results($wpdb->prepare(
                                "SELECT id, name FROM {$table_name} WHERE species_id = %d ORDER BY name ASC",
                                $pet_data->species
                            ));
                            
                            foreach ($breeds as $breed) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    esc_attr($breed->id),
                                    selected($pet_data->breed == $breed->id, true, false),
                                    esc_html($breed->name)
                                );
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="age"><?php _e('Idade (anos)', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="number" id="age" name="age" min="0" step="1" class="small-text" 
                           value="<?php echo $is_edit ? esc_attr($pet_data->age) : ''; ?>">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="gender"><?php _e('Gênero', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="gender" name="gender" required>
                        <option value=""><?php _e('Selecione o gênero', 'amigopet-wp'); ?></option>
                        <option value="male" <?php selected($is_edit && $pet_data->gender === 'male'); ?>>
                            <?php _e('Macho', 'amigopet-wp'); ?>
                        </option>
                        <option value="female" <?php selected($is_edit && $pet_data->gender === 'female'); ?>>
                            <?php _e('Fêmea', 'amigopet-wp'); ?>
                        </option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="size"><?php _e('Porte', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="size" name="size" required>
                        <option value=""><?php _e('Selecione o porte', 'amigopet-wp'); ?></option>
                        <option value="small" <?php selected($is_edit && $pet_data->size === 'small'); ?>>
                            <?php _e('Pequeno', 'amigopet-wp'); ?>
                        </option>
                        <option value="medium" <?php selected($is_edit && $pet_data->size === 'medium'); ?>>
                            <?php _e('Médio', 'amigopet-wp'); ?>
                        </option>
                        <option value="large" <?php selected($is_edit && $pet_data->size === 'large'); ?>>
                            <?php _e('Grande', 'amigopet-wp'); ?>
                        </option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="weight"><?php _e('Peso (kg)', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="number" id="weight" name="weight" min="0" step="0.1" class="small-text" 
                           value="<?php echo $is_edit ? esc_attr($pet_data->weight) : ''; ?>">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="description"><?php _e('Descrição', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <textarea id="description" name="description" class="large-text" rows="5"><?php 
                        echo $is_edit ? esc_textarea($pet_data->description) : ''; 
                    ?></textarea>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="status"><?php _e('Status', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="status" name="status" required>
                        <option value="available" <?php selected($is_edit && $pet_data->status === 'available'); ?>>
                            <?php _e('Disponível', 'amigopet-wp'); ?>
                        </option>
                        <option value="pending" <?php selected($is_edit && $pet_data->status === 'pending'); ?>>
                            <?php _e('Pendente', 'amigopet-wp'); ?>
                        </option>
                        <option value="adopted" <?php selected($is_edit && $pet_data->status === 'adopted'); ?>>
                            <?php _e('Adotado', 'amigopet-wp'); ?>
                        </option>
                        <option value="unavailable" <?php selected($is_edit && $pet_data->status === 'unavailable'); ?>>
                            <?php _e('Indisponível', 'amigopet-wp'); ?>
                        </option>
                    </select>
                </td>
            </tr>

            <?php if ($is_edit && $pet_data->status === 'adopted'): ?>
            <tr>
                <th scope="row">
                    <label for="adopter_id"><?php _e('Adotante', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <?php
                    $adopter = get_userdata($pet_data->adopter_id);
                    echo $adopter ? esc_html($adopter->display_name) : __('Adotante não encontrado', 'amigopet-wp');
                    ?>
                </td>
            </tr>
            <?php endif; ?>
        </table>

        <?php submit_button($is_edit ? __('Atualizar Pet', 'amigopet-wp') : __('Adicionar Pet', 'amigopet-wp')); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Função para carregar raças baseadas na espécie selecionada
    function loadBreeds(speciesId) {
        if (!speciesId) {
            $('#breed').empty().append($('<option>', {
                value: '',
                text: '<?php _e("Selecione uma raça", "amigopet-wp"); ?>'
            }));
            return;
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'apwp_get_breeds',
                species: speciesId,
                nonce: '<?php echo wp_create_nonce("apwp_get_breeds"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    var breeds = response.data;
                    var select = $('#breed');
                    select.empty();
                    select.append($('<option>', {
                        value: '',
                        text: '<?php _e("Selecione uma raça", "amigopet-wp"); ?>'
                    }));
                    breeds.forEach(function(breed) {
                        select.append($('<option>', {
                            value: breed.id,
                            text: breed.name
                        }));
                    });
                    <?php if ($is_edit): ?>
                    select.val('<?php echo esc_js($pet_data->breed); ?>');
                    <?php endif; ?>
                }
            }
        });
    }

    // Carregar raças quando a espécie for alterada
    $('#species').on('change', function() {
        loadBreeds($(this).val());
    });

    // Carregar raças iniciais se estiver editando
    <?php if ($is_edit && $pet_data->species): ?>
    loadBreeds('<?php echo esc_js($pet_data->species); ?>');
    <?php endif; ?>
});
</script>
