<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template para formulário de termo no admin
 * Combina as melhores características de ambas implementações anteriores
 */


$term_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $term_id > 0;
$term = $is_edit ? get_post($term_id) : null;

if ($is_edit && (!$term || $term->post_type !== 'apwp_term')) {
    wp_die(esc_html__('Termo não encontrado', 'amigopet'));
}

$title = $is_edit ? esc_html__('Editar Termo', 'amigopet') : esc_html__('Adicionar Novo Termo', 'amigopet');

// Carrega dados do termo se estiver editando
$term_data = $is_edit ? [
    'type' => get_post_meta($term_id, 'term_type', true),
    'status' => get_post_meta($term_id, 'term_status', true),
    'version' => get_post_meta($term_id, 'term_version', true),
    'notes' => get_post_meta($term_id, 'term_notes', true)
] : [];
?>

<div class="wrap">
    <h1 class="wp-heading-inline"> echo esc_html($title); ?></h1>
    <a href=" echo esc_url(remove_query_arg(['action', 'id'])); ?>" class="page-title-action">
         esc_html_e('Voltar para Lista', 'amigopet'); ?>
    </a>

    <div class="apwp-term-form">
        <form id="apwp-term-form" method="post" action=" echo esc_url(admin_url('admin-post.php')); ?>">
             wp_nonce_field('apwp_save_term', 'apwp_term_nonce'); ?>
            <input type="hidden" name="action" value="apwp_save_term">
             if ($is_edit): ?>
                <input type="hidden" name="term_id" value=" echo esc_attr($term_id); ?>">
             endif; ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="term_title"> esc_html_e('Título', 'amigopet'); ?> <span
                                class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="term_title" name="term_title" class="regular-text" required
                            value=" echo $term ? esc_attr($term->post_title) : ''; ?>">
                        <p class="description">
                             esc_html_e('Digite um título descritivo para este termo.', 'amigopet'); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="term_type"> esc_html_e('Tipo', 'amigopet'); ?> <span
                                class="required">*</span></label>
                    </th>
                    <td>
                        <select id="term_type" name="term_type" required>
                            <option value=""> esc_html_e('Selecione um tipo', 'amigopet'); ?></option>
                            
                            $types = [
                                'adoption' => esc_html__('Adoção', 'amigopet'),
                                'volunteer' => esc_html__('Voluntariado', 'amigopet'),
                                'donation' => esc_html__('Doação', 'amigopet'),
                                'privacy' => esc_html__('Privacidade', 'amigopet'),
                                'service' => esc_html__('Serviço', 'amigopet')
                            ];

                            foreach ($types as $value => $label) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    esc_attr($value),
                                    selected($term_data['type'] ?? '', $value, false),
                                    esc_html($label)
                                );
                            }
                            ?>
                        </select>
                        <p class="description">
                             esc_html_e('Selecione o tipo de termo que você está criando.', 'amigopet'); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="term_content"> esc_html_e('Conteúdo', 'amigopet'); ?> <span
                                class="required">*</span></label>
                    </th>
                    <td>
                        
                        wp_editor(
                            $term ? $term->post_content : '',
                            'term_content',
                            [
                                'textarea_name' => 'term_content',
                                'textarea_rows' => 10,
                                'media_buttons' => true,
                                'teeny' => false,
                                'quicktags' => true,
                                'tinymce' => [
                                    'toolbar1' => 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,undo,redo',
                                    'toolbar2' => '',
                                    'toolbar3' => '',
                                    'toolbar4' => ''
                                ]
                            ]
                        );
                        ?>
                        <p class="description">
                             esc_html_e('Digite o conteúdo do termo. Use formatação para melhor legibilidade.', 'amigopet'); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="term_version"> esc_html_e('Versão', 'amigopet'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="term_version" name="term_version" class="regular-text"
                            value=" echo esc_attr($term_data['version'] ?? '1.0'); ?>" pattern="[0-9]+\.[0-9]+">
                        <p class="description">
                             esc_html_e('Versão do termo (ex: 1.0). Atualize este número quando fizer alterações significativas.', 'amigopet'); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="term_status"> esc_html_e('Status', 'amigopet'); ?> <span
                                class="required">*</span></label>
                    </th>
                    <td>
                        <select id="term_status" name="term_status" required>
                            
                            $statuses = [
                                'draft' => esc_html__('Rascunho', 'amigopet'),
                                'published' => esc_html__('Publicado', 'amigopet'),
                                'archived' => esc_html__('Arquivado', 'amigopet')
                            ];

                            foreach ($statuses as $value => $label) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    esc_attr($value),
                                    selected($term_data['status'] ?? 'draft', $value, false),
                                    esc_html($label)
                                );
                            }
                            ?>
                        </select>
                        <p class="description">
                             esc_html_e('Defina o status do termo. Apenas termos publicados serão exibidos no site.', 'amigopet'); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="term_notes"> esc_html_e('Notas de Revisão', 'amigopet'); ?></label>
                    </th>
                    <td>
                        <textarea id="term_notes" name="term_notes" class="large-text" rows="3">
                        echo esc_textarea($term_data['notes'] ?? '');
                        ?></textarea>
                        <p class="description">
                             esc_html_e('Adicione notas sobre as alterações feitas nesta versão do termo.', 'amigopet'); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" class="button button-primary">
                     echo esc_html($is_edit ? esc_html__('Atualizar Termo', 'amigopet') : esc_html__('Adicionar Termo', 'amigopet')); ?>
                </button>
                <button type="button" class="button preview-term">
                     esc_html_e('Pré-visualizar', 'amigopet'); ?>
                </button>
            </p>
        </form>
    </div>

    <!-- Modal de Pré-visualização -->
    <div id="term-preview-modal" class="term-preview-modal" style="display: none;">
        <div class="term-preview-content">
            <div class="term-preview-header">
                <h2> esc_html_e('Pré-visualização do Termo', 'amigopet'); ?></h2>
                <button class="close-preview">&times;</button>
            </div>
            <div class="term-preview-body"></div>
        </div>
    </div>
</div>

<style>
    .apwp-term-form .required {
        color: #dc3232;
    }

    .apwp-term-form .error {
        border-color: #dc3232;
    }

    .apwp-term-form .description {
        font-style: italic;
        color: #646970;
    }

    .term-preview-modal {
        display: none;
        position: fixed;
        z-index: 100000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .term-preview-content {
        position: relative;
        background-color: #fefefe;
        margin: 5% auto;
        padding: 0;
        width: 80%;
        max-width: 800px;
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .term-preview-header {
        padding: 15px 20px;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .term-preview-header h2 {
        margin: 0;
        font-size: 1.3em;
    }

    .close-preview {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
    }

    .close-preview:hover {
        color: #dc3232;
    }

    .term-preview-body {
        padding: 20px;
        max-height: 70vh;
        overflow-y: auto;
    }
</style>

<script>
    jQuery(document).ready(function ($) {
        // Validação do formulário
        $('#apwp-term-form').on('submit', function (e) {
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

        // Validação da versão
        $('#term_version').on('input', function () {
            var version = $(this).val();
            if (version && !version.match(/^\d+\.\d+$/)) {
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });

        // Pré-visualização do termo
        $('.preview-term').on('click', function () {
            var title = $('#term_title').val();
            var content = tinymce.get('term_content')
                ? tinymce.get('term_content').getContent()
                : $('#term_content').val();

            if (!title || !content) {
                alert(' echo esc_js(esc_html__('Preencha pelo menos o título e o conteúdo para pré-visualizar.', 'amigopet')); ?>');
                return;
            }

            // Helper function to escape HTML for plain text
            function escapeHtml(text) {
                var div = document.createElement('div');
                div.appendChild(document.createTextNode(text));
                return div.innerHTML;
            }

            var $modal = $('#term-preview-modal');
            var $body = $modal.find('.term-preview-body');

            // Escape title as it's plain text, content is expected to be HTML from TinyMCE
            $body.html('<h1>' + escapeHtml(title) + '</h1>' + content);
            $modal.show();
        });

        // Fechar modal de pré-visualização
        $('.close-preview, .term-preview-modal').on('click', function (e) {
            if (e.target === this) {
                $('#term-preview-modal').hide();
            }
        });

        // Atualizar versão ao mudar status
        $('#term_status').on('change', function () {
            if ($(this).val() === 'published') {
                var currentVersion = $('#term_version').val();
                if (currentVersion) {
                    var parts = currentVersion.split('.');
                    parts[1] = parseInt(parts[1]) + 1;
                    $('#term_version').val(parts.join('.'));
                }
            }
        });
    });
</script>