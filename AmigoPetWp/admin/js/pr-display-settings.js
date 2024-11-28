jQuery(document).ready(function($) {
    'use strict';

    // Inicializa os color pickers
    $('.pr-color-picker').wpColorPicker({
        change: function(event, ui) {
            updatePreview();
        }
    });

    // Preview em tempo real
    function updatePreview() {
        var settings = getFormSettings();
        
        // Gera CSS dinâmico
        var dynamicCSS = generateDynamicCSS(settings);
        
        // Atualiza o preview
        $.ajax({
            url: prDisplaySettings.previewUrl,
            type: 'POST',
            data: {
                action: 'apwp_preview_grid',
                settings: settings,
                _ajax_nonce: prDisplaySettings.previewNonce
            },
            success: function(response) {
                if (response.success) {
                    // Atualiza o HTML do preview
                    $('#pr-grid-preview').html(response.data.html);
                    
                    // Atualiza o CSS
                    updateDynamicCSS(dynamicCSS);
                }
            }
        });
    }

    // Coleta as configurações do formulário
    function getFormSettings() {
        var settings = {
            grid_columns: $('[name="apwp_display_settings[grid_columns]"]').val(),
            card_style: $('[name="apwp_display_settings[card_style]"]').val(),
            show_status_icon: $('[name="apwp_display_settings[show_status_icon]"]').is(':checked'),
            status_icons: {},
            card_colors: {
                background: $('[name="apwp_display_settings[card_colors][background]"]').val(),
                text: $('[name="apwp_display_settings[card_colors][text]"]').val(),
                accent: $('[name="apwp_display_settings[card_colors][accent]"]').val()
            }
        };

        // Coleta configurações dos ícones de status
        $('.pr-status-icon-config').each(function() {
            var status = $(this).data('status');
            settings.status_icons[status] = {
                icon: $(this).find('.pr-icon-select').val(),
                color: $(this).find('.pr-color-picker').val()
            };
        });

        return settings;
    }

    // Gera CSS dinâmico baseado nas configurações
    function generateDynamicCSS(settings) {
        var css = `
            .pr-animal-card {
                background-color: ${settings.card_colors.background};
                color: ${settings.card_colors.text};
                border-radius: 8px;
                overflow: hidden;
                transition: all 0.3s ease;
            }

            .pr-animal-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }

            .pr-animal-card .pr-animal-title {
                color: ${settings.card_colors.text};
                font-size: ${settings.typography?.title_size || '18px'};
            }

            .pr-animal-card .pr-status-icon {
                font-size: 1.2em;
                margin-right: 5px;
            }
        `;

        // Adiciona CSS específico para cada status
        Object.keys(settings.status_icons).forEach(status => {
            const iconData = settings.status_icons[status];
            css += `
                .pr-animal-card.pr-status-${status} .pr-status-icon {
                    color: ${iconData.color};
                }
            `;
        });

        // CSS específico para cada estilo de card
        switch (settings.card_style) {
            case 'modern':
                css += `
                    .pr-animal-card {
                        border: none;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    }
                `;
                break;
            case 'classic':
                css += `
                    .pr-animal-card {
                        border: 1px solid #ddd;
                        box-shadow: none;
                    }
                `;
                break;
            case 'minimal':
                css += `
                    .pr-animal-card {
                        border: none;
                        box-shadow: none;
                        background: transparent;
                    }
                `;
                break;
        }

        return css;
    }

    // Atualiza o CSS dinâmico na página
    function updateDynamicCSS(css) {
        let styleTag = $('#pr-dynamic-style');
        if (!styleTag.length) {
            styleTag = $('<style id="pr-dynamic-style"></style>').appendTo('head');
        }
        styleTag.html(css);
    }

    // Event listeners
    $('select, input').on('change', updatePreview);
    $('.pr-icon-select').on('change', function() {
        var newIcon = $(this).val();
        $(this).siblings('.preview-icon')
            .removeClass()
            .addClass('fa fa-' + newIcon + ' preview-icon');
        updatePreview();
    });

    // Inicializa o preview
    updatePreview();
});
