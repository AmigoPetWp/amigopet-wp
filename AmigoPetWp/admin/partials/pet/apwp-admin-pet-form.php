<?php
/**
 * Formulário de adição/edição de pets
 */
?>
<div class="wrap">
    <h2><?php echo isset($_GET['id']) ? 'Editar Pet' : 'Adicionar Novo Pet'; ?></h2>
    
    <form id="pet-form" class="apwp-form">
        <?php wp_nonce_field('apwp_pet_nonce', 'apwp_pet_nonce'); ?>
        <input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? esc_attr($_GET['id']) : ''; ?>">
        
        <div class="form-field">
            <label for="name">Nome <span class="required">*</span></label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-field">
            <label for="species_id">Espécie <span class="required">*</span></label>
            <select id="species_id" name="species_id" required>
                <option value="">Selecione uma espécie</option>
            </select>
        </div>

        <div class="form-field">
            <label for="breed_id">Raça</label>
            <select id="breed_id" name="breed_id">
                <option value="">Selecione uma raça</option>
            </select>
        </div>

        <div class="form-field inline">
            <div style="flex: 1;">
                <label for="age">Idade</label>
                <input type="text" id="age" name="age">
            </div>

            <div style="flex: 1;">
                <label for="gender">Gênero</label>
                <select id="gender" name="gender">
                    <option value="">Selecione o gênero</option>
                    <option value="male">Macho</option>
                    <option value="female">Fêmea</option>
                </select>
            </div>

            <div style="flex: 1;">
                <label for="size">Porte</label>
                <select id="size" name="size">
                    <option value="">Selecione o porte</option>
                    <option value="small">Pequeno</option>
                    <option value="medium">Médio</option>
                    <option value="large">Grande</option>
                </select>
            </div>
        </div>

        <div class="form-field">
            <label for="color">Cor</label>
            <input type="text" id="color" name="color">
        </div>

        <div class="form-field">
            <label for="description">Descrição</label>
            <textarea id="description" name="description" rows="5"></textarea>
        </div>

        <div class="form-field">
            <label for="photo">Foto</label>
            <input type="hidden" id="photo_url" name="photo_url">
            <div id="photo-preview"></div>
            <button type="button" class="button" id="upload-photo">Escolher Foto</button>
        </div>

        <div class="form-field">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="available">Disponível</option>
                <option value="adopted">Adotado</option>
                <option value="pending">Adoção Pendente</option>
                <option value="unavailable">Indisponível</option>
            </select>
        </div>

        <div class="submit">
            <button type="submit" class="button button-primary">Salvar</button>
            <a href="?page=amigopet-wp-pets" class="button">Cancelar</a>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Carrega as espécies
    $.get('/wp-json/amigopet-wp/v1/species', function(species) {
        var options = '<option value="">Selecione uma espécie</option>';
        species.forEach(function(species) {
            options += '<option value="' + species.id + '">' + species.name + '</option>';
        });
        $('#species_id').html(options);
    });

    // Carrega as raças quando uma espécie é selecionada
    $('#species_id').change(function() {
        var species_id = $(this).val();
        if (species_id) {
            $.get('/wp-json/amigopet-wp/v1/breeds?species_id=' + species_id, function(breeds) {
                var options = '<option value="">Selecione uma raça</option>';
                breeds.forEach(function(breed) {
                    options += '<option value="' + breed.id + '">' + breed.name + '</option>';
                });
                $('#breed_id').html(options);
            });
        } else {
            $('#breed_id').html('<option value="">Selecione uma raça</option>');
        }
    });

    // Upload de foto
    var mediaUploader;
    $('#upload-photo').click(function(e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media({
            title: 'Escolher Foto',
            button: {
                text: 'Usar esta foto'
            },
            multiple: false
        });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#photo_url').val(attachment.url);
            $('#photo-preview').html('<img src="' + attachment.url + '" style="max-width:200px;">');
        });
        mediaUploader.open();
    });

    // Submissão do formulário
    $('#pet-form').submit(function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        var method = $('#id').val() ? 'PUT' : 'POST';
        var url = '/wp-json/amigopet-wp/v1/pets';
        if ($('#id').val()) {
            url += '/' + $('#id').val();
        }
        $.ajax({
            url: url,
            method: method,
            data: data,
            success: function(response) {
                window.location.href = '?page=amigopet-wp-pets';
            },
            error: function(xhr) {
                alert('Erro ao salvar: ' + xhr.responseJSON.message);
            }
        });
    });

    // Carrega os dados do pet se estiver editando
    var id = $('#id').val();
    if (id) {
        $.get('/wp-json/amigopet-wp/v1/pets/' + id, function(pet) {
            $('#name').val(pet.name);
            $('#species_id').val(pet.species_id).trigger('change');
            $('#breed_id').val(pet.breed_id);
            $('#age').val(pet.age);
            $('#gender').val(pet.gender);
            $('#size').val(pet.size);
            $('#color').val(pet.color);
            $('#description').val(pet.description);
            $('#status').val(pet.status);
            if (pet.photo_url) {
                $('#photo_url').val(pet.photo_url);
                $('#photo-preview').html('<img src="' + pet.photo_url + '" style="max-width:200px;">');
            }
        });
    }
});
</script>
