<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * View da grid de pets
 *
 * @package AmigoPetWp
 */

// Extrai os atributos do shortcode
$species = isset($atts['species']) ? $atts['species'] : '';
$size = isset($atts['size']) ? $atts['size'] : '';
$limit = isset($atts['limit']) ? (int) $atts['limit'] : 10;
?>

<div class="apwp-pets-grid">
    <!-- Filtros -->
    <div class="apwp-pets-filters">
        <select id="apwp-species-filter">
            <option value=""><?php esc_html_e('Todas as espécies', 'amigopet'); ?></option>
            <?php
            $species_terms = get_terms([
                'taxonomy' => 'amigopetwp_pet_species',
                'hide_empty' => false
            ]);

            if (!is_wp_error($species_terms)) {
                foreach ($species_terms as $term) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($term->slug),
                        selected($species, $term->slug, false),
                        esc_html($term->name)
                    );
                }
            }
            ?>
        </select>

        <select id="apwp-size-filter">
            <option value=""><?php esc_html_e('Todos os tamanhos', 'amigopet'); ?></option>
            <?php
            $sizes = [
                'small' => esc_html__('Pequeno', 'amigopet'),
                'medium' => esc_html__('Médio', 'amigopet'),
                'large' => esc_html__('Grande', 'amigopet')
            ];

            foreach ($sizes as $value => $label) {
                printf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr($value),
                    selected($size, $value, false),
                    esc_html($label)
                );
            }
            ?>
        </select>
    </div>

    <!-- Grid de pets -->
    <div class="apwp-pets-container">
        <div id="apwp-pets-grid" class="apwp-grid"></div>
        <div id="apwp-pets-loading" class="apwp-loading" style="display: none;">
            <div class="apwp-spinner"></div>
        </div>
        <div id="apwp-pets-empty" class="apwp-empty" style="display: none;">
            <?php esc_html_e('Nenhum pet encontrado.', 'amigopet'); ?>
        </div>
    </div>

    <!-- Paginação -->
    <div id="apwp-pets-pagination" class="apwp-pagination"></div>
</div>

<script type="text/html" id="tmpl-apwp-pet-card">
    <div class="apwp-pet-card">
        <div class="apwp-pet-image">
            <# if (data.image) { #>
                <img src="{{ data.image }}" alt="{{ data.name }}">
            <# } else { #>
                <div class="apwp-pet-no-image">
                    <i class="dashicons dashicons-pets"></i>
                </div>
            <# } #>
        </div>
        
        <div class="apwp-pet-info">
            <h3>{{ data.name }}</h3>
            
            <div class="apwp-pet-meta">
                <span class="apwp-pet-species">
                    <i class="dashicons dashicons-category"></i>
                    {{ data.species }}
                </span>
                
                <span class="apwp-pet-breed">
                    <i class="dashicons dashicons-tag"></i>
                    {{ data.breed }}
                </span>
                
                <span class="apwp-pet-age">
                    <i class="dashicons dashicons-calendar"></i>
                    {{ data.age }}
                </span>
                
                <span class="apwp-pet-size">
                    <i class="dashicons dashicons-editor-expand"></i>
                    {{ data.size }}
                </span>
            </div>
            
            <div class="apwp-pet-description">
                {{ data.description }}
            </div>
            
            <div class="apwp-pet-actions">
                <a href="{{ data.url }}" class="button">
                    <?php esc_html_e('Ver Detalhes', 'amigopet'); ?>
                </a>
                
                <# if (data.can_adopt) { #>
                    <a href="{{ data.adoption_url }}" class="button button-primary">
                        <?php esc_html_e('Quero Adotar', 'amigopet'); ?>
                    </a>
                <# } #>
            </div>
        </div>
    </div>
</script>

<script>
    jQuery(document).ready(function ($) {
        const container = $('#apwp-pets-grid');
        const loading = $('#apwp-pets-loading');
        const empty = $('#apwp-pets-empty');
        const pagination = $('#apwp-pets-pagination');
        const template = wp.template('apwp-pet-card');

        let currentPage = 1;
        let totalPages = 0;

        // Função para carregar os pets
        function loadPets(page = 1) {
            // Mostra loading
            container.hide();
            empty.hide();
            loading.show();

            // Pega os filtros
            const species = $('#apwp-species-filter').val();
            const size = $('#apwp-size-filter').val();

            // Faz a requisição
            $.ajax({
                url: typeof apwp !== 'undefined' ? apwp.ajax_url : '/wp-admin/admin-ajax.php',
                type: 'GET',
                data: {
                    action: 'apwp_get_pets',
                    _ajax_nonce: typeof apwp !== 'undefined' ? apwp.nonce : '',
                    species: species,
                    size: size,
                    limit: <?php echo esc_js($limit); ?>,
                    page: page
                },
                success: function (response) {
                    if (response.success) {
                        const { pets, total, pages } = response.data;

                        // Atualiza as variáveis de paginação
                        currentPage = page;
                        totalPages = pages;

                        // Limpa o container
                        container.empty();

                        if (pets && pets.length > 0) {
                            // Adiciona os pets
                            pets.forEach(function (pet) {
                                container.append(template(pet));
                            });

                            // Atualiza a paginação
                            updatePagination();

                            // Mostra o container
                            loading.hide();
                            container.show();
                        } else {
                            // Mostra mensagem de vazio
                            loading.hide();
                            empty.show();
                        }
                    } else {
                        // Mostra erro
                        loading.hide();
                        empty.show().text(response.data);
                    }
                },
                error: function () {
                    // Mostra erro
                    loading.hide();
                    empty.show().text('Erro ao carregar pets');
                }
            });
        }

        // Função para atualizar a paginação
        function updatePagination() {
            pagination.empty();

            if (totalPages <= 1) {
                return;
            }

            // Adiciona link para primeira página
            if (currentPage > 1) {
                pagination.append(`
                <a href="#" class="apwp-page" data-page="1">
                    <i class="dashicons dashicons-controls-skipback"></i>
                </a>
            `);
            }

            // Adiciona link para página anterior
            if (currentPage > 1) {
                pagination.append(`
                <a href="#" class="apwp-page" data-page="${currentPage - 1}">
                    <i class="dashicons dashicons-controls-back"></i>
                </a>
            `);
            }

            // Adiciona links para as páginas
            for (let i = 1; i <= totalPages; i++) {
                if (
                    i === 1 || // Primeira página
                    i === totalPages || // Última página
                    (i >= currentPage - 2 && i <= currentPage + 2) // 2 páginas antes e depois
                ) {
                    pagination.append(`
                    <a href="#" class="apwp-page ${i === currentPage ? 'current' : ''}" data-page="${i}">
                        ${i}
                    </a>
                `);
                } else if (
                    i === currentPage - 3 || // Antes das páginas atuais
                    i === currentPage + 3 // Depois das páginas atuais
                ) {
                    pagination.append('<span class="apwp-dots">...</span>');
                }
            }

            // Adiciona link para próxima página
            if (currentPage < totalPages) {
                pagination.append(`
                <a href="#" class="apwp-page" data-page="${currentPage + 1}">
                    <i class="dashicons dashicons-controls-forward"></i>
                </a>
            `);
            }

            // Adiciona link para última página
            if (currentPage < totalPages) {
                pagination.append(`
                <a href="#" class="apwp-page" data-page="${totalPages}">
                    <i class="dashicons dashicons-controls-skipforward"></i>
                </a>
            `);
            }
        }

        // Event handlers
        $('#apwp-species-filter, #apwp-size-filter').on('change', function () {
            loadPets(1);
        });

        pagination.on('click', '.apwp-page', function (e) {
            e.preventDefault();
            const page = $(this).data('page');
            loadPets(page);

            // Scroll para o topo da grid
            $('html, body').animate({
                scrollTop: container.offset().top - 50
            }, 500);
        });

        // Carrega os pets iniciais
        loadPets(1);
    });
</script>