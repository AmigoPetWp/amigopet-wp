(function($) {
    'use strict';

    /**
     * Todo o código JavaScript para a área administrativa deve
     * estar contido dentro desta função.
     */
    $(function() {
        // Inicialização do menu de configurações
        $('.apwp-nav-tab').on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('tab');
            
            // Atualiza as tabs ativas
            $('.apwp-nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            // Mostra o conteúdo da tab selecionada
            $('.apwp-tab-content').hide();
            $('#' + target).show();
            
            // Atualiza a URL sem recarregar a página
            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=amigopet-wp&tab=' + target;
                window.history.pushState({path:newurl}, '', newurl);
            }
        });

        // Inicializa a primeira tab ou a tab da URL
        var currentTab = new URLSearchParams(window.location.search).get('tab');
        if (!currentTab) {
            currentTab = 'general';
        }
        $('.apwp-nav-tab[data-tab="' + currentTab + '"]').trigger('click');

        // Manipulação do upload de logo
        var mediaUploader;
        $('#upload_logo_button').on('click', function(e) {
            e.preventDefault();

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Escolher Logo',
                button: {
                    text: 'Usar esta imagem'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#logo_url').val(attachment.url);
                $('#logo_preview').attr('src', attachment.url).show();
            });

            mediaUploader.open();
        });

        // Remover logo
        $('#remove_logo_button').on('click', function(e) {
            e.preventDefault();
            $('#logo_url').val('');
            $('#logo_preview').attr('src', '').hide();
        });

        // Salvar configurações via AJAX
        $('#apwp-settings-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var submitButton = form.find('input[type="submit"]');
            
            submitButton.prop('disabled', true);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'save_apwp_settings',
                    nonce: $('#apwp_settings_nonce').val(),
                    formData: form.serialize()
                },
                success: function(response) {
                    if (response.success) {
                        alert('Configurações salvas com sucesso!');
                    } else {
                        alert('Erro ao salvar configurações: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('Erro ao salvar configurações. Por favor, tente novamente.');
                },
                complete: function() {
                    submitButton.prop('disabled', false);
                }
            });
        });

        // Inicialização de elementos do WordPress
        if ($.fn.wpColorPicker) {
            $('.color-picker').wpColorPicker();
        }

        // Tooltips
        $('.apwp-help-tip').on('mouseover', function() {
            var tip = $(this).data('tip');
            if (tip) {
                $(this).attr('title', tip);
            }
        });

    });

})(jQuery);
