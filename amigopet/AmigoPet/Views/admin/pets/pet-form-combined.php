<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template para formulário de pet no admin
 * Combina as melhores características de ambas implementações anteriores
 */


$pet_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $pet_id > 0;
$pet = $is_edit ? get_post($pet_id) : null;

if ($is_edit && (!$pet || $pet->post_type !== 'amigopetwp_pet')) {
    wp_die(esc_html__('Pet não encontrado', 'amigopet'));
}

$title = $is_edit ? esc_html__('Editar Pet', 'amigopet') : esc_html__('Adicionar Novo Pet', 'amigopet');

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
    <h1 class="wp-heading-inline"> echo esc_html($title); ?></h1>
    <a href=" echo esc_url(remove_query_arg(['action', 'id'])); ?>" class="page-title-action">
         esc_html_e('Voltar para Lista', 'amigopet'); ?>
    </a>

    <div class="apwp-pet-form">
        <form id="apwp-pet-form" method="post" action=" echo esc_url(admin_url('admin-post.php')); ?>"
            enctype="multipart/form-data">
             wp_nonce_field('apwp_save_pet', 'apwp_pet_nonce'); ?>
            <input type="hidden" name="action" value="apwp_save_pet">
             if ($is_edit): ?>
                <input type="hidden" name="pet_id" value=" echo esc_attr($pet_id); ?>">
             endif; ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="pet_name"> esc_html_e('Nome', 'amigopet'); ?> <span
                                class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="pet_name" name="pet_name" class="regular-text" required
                            value=" echo esc_attr($pet_data['name'] ?? ''); ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="species_id"> esc_html_e('Espécie', 'amigopet'); ?> <span
                                class="required">*</span></label>
                    </th>
                    <td>
                        <select id="species_id" name="species_id" required>
                            <option value=""> esc_html_e('Selecione uma espécie', 'amigopet'); ?></option>
                             foreach ($species as $specie): ?>
                                <option value=" echo esc_attr($specie->id); ?>"  selected($pet_data['species_id'] ?? '', $specie->id); ?>>
                                     echo esc_html($specie->name); ?>
                                </option>
                             endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="breed"> esc_html_e('Raça', 'amigopet'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="breed" name="breed" class="regular-text"
                            value=" echo esc_attr($pet_data['breed'] ?? ''); ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="age"> esc_html_e('Idade Aproximada', 'amigopet'); ?> <span
                                class="required">*</span></label>
                    </th>
                    <td>
                        <input type="number" id="age" name="age" class="small-text" min="0" required
                            value=" echo esc_attr($pet_data['age'] ?? ''); ?>">
                        <p class="description">
                             esc_html_e('Idade em anos. Para filhotes menores de 1 ano, use 0.', 'amigopet'); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="size"> esc_html_e('Porte', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="size" name="size" required>
                            <option value=""> esc_html_e('Selecione o porte', 'amigopet'); ?></option>
                            <option value="small"  selected($pet_data['size'] ?? '', 'small'); ?>>
                                 esc_html_e('Pequeno', 'amigopet'); ?>
                            </option>
                            <option value="medium"  selected($pet_data['size'] ?? '', 'medium'); ?>>
                                 esc_html_e('Médio', 'amigopet'); ?>
                            </option>
                            <option value="large"  selected($pet_data['size'] ?? '', 'large'); ?>>
                                 esc_html_e('Grande', 'amigopet'); ?>
                            </option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="gender"> esc_html_e('Sexo', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="gender" name="gender" required>
                            <option value=""> esc_html_e('Selecione o sexo', 'amigopet'); ?></option>
                            <option value="male"  selected($pet_data['gender'] ?? '', 'male'); ?>>
                                 esc_html_e('Macho', 'amigopet'); ?>
                            </option>
                            <option value="female"  selected($pet_data['gender'] ?? '', 'female'); ?>>
                                 esc_html_e('Fêmea', 'amigopet'); ?>
                            </option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row"> esc_html_e('Características', 'amigopet'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="vaccinated" value="1"  checked($pet_data['vaccinated'] ?? '', '1'); ?>>
                             esc_html_e('Vacinado', 'amigopet'); ?>
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="neutered" value="1"  checked($pet_data['neutered'] ?? '', '1'); ?>>
                             esc_html_e('Castrado', 'amigopet'); ?>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="description"> esc_html_e('Descrição', 'amigopet'); ?></label>
                    </th>
                    <td>
                        
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
                        <label for="status"> esc_html_e('Status', 'amigopet'); ?> <span
                                class="required">*</span></label>
                    </th>
                    <td>
                        <select id="status" name="status" required>
                            <option value=""> esc_html_e('Selecione o status', 'amigopet'); ?></option>
                            <option value="available"  selected($pet_data['status'] ?? '', 'available'); ?>>
                                 esc_html_e('Disponível', 'amigopet'); ?>
                            </option>
                            <option value="adopted"  selected($pet_data['status'] ?? '', 'adopted'); ?>>
                                 esc_html_e('Adotado', 'amigopet'); ?>
                            </option>
                            <option value="unavailable"  selected($pet_data['status'] ?? '', 'unavailable'); ?>>
                                 esc_html_e('Indisponível', 'amigopet'); ?>
                            </option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="pet_photos"> esc_html_e('Fotos', 'amigopet'); ?></label>
                    </th>
                    <td>
                        <div id="pet-photos-container">
                             if ($is_edit && has_post_thumbnail($pet_id)): ?>
                                <div class="current-thumbnail">
                                     echo get_the_post_thumbnail($pet_id, 'thumbnail'); ?>
                                </div>
                             endif; ?>
                            <input type="file" id="pet_photos" name="pet_photos[]" multiple accept="image/*">
                            <p class="description">
                                 esc_html_e('Selecione uma ou mais fotos do pet. A primeira foto será usada como foto principal.', 'amigopet'); ?>
                            </p>
                        </div>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" class="button button-primary">
                     echo esc_html($is_edit ? esc_html__('Atualizar Pet', 'amigopet') : esc_html__('Adicionar Pet', 'amigopet')); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        // Preview de imagens antes do upload
        $('#pet_photos').on('change', function () {
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

                reader.onload = function (e) {
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
        $('#apwp-pet-form').on('submit', function (e) {
            var $required = $(this).find('[required]');
            var valid = true;

            $required.each(function () {
                if (!$(this).val()) {
                    valid = false;
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });

            if (!valid) {
                e.preventDefault();
                alert(' echo esc_js(esc_html__('Por favor, preencha todos os campos obrigatórios.', 'amigopet')); ?>');
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