jQuery(document).ready(function($) {
    'use strict';

    // Objeto principal do plugin
    var PR = {
        init: function() {
            if (!apwp_vars.is_user_logged_in) {
                return;
            }
            
            this.bindEvents();
            this.initFilters();
            this.initAdoptionForm();
        },

        // Vincula eventos aos elementos
        bindEvents: function() {
            $(document).on('click', '.apwp-animal-card', this.handleAnimalClick);
            $(document).on('submit', '.apwp-adoption-form', this.handleAdoptionSubmit);
            $(document).on('change', '.apwp-filter-select', this.handleFilterChange);
        },

        // Inicializa os filtros
        initFilters: function() {
            $('.apwp-filter-select').each(function() {
                $(this).on('change', function() {
                    PR.updateAnimalsGrid();
                });
            });
        },

        // Inicializa o formulário de adoção
        initAdoptionForm: function() {
            // Máscara para CPF
            $('.apwp-form-input[name="cpf"]').mask('000.000.000-00');
            
            // Máscara para telefone
            $('.apwp-form-input[name="phone"]').mask('(00) 00000-0000');
            
            // Máscara para CEP
            $('.apwp-form-input[name="cep"]').mask('00000-000');
        },

        // Atualiza o grid de animais com base nos filtros
        updateAnimalsGrid: function() {
            var filters = {};
            
            $('.apwp-filter-select').each(function() {
                var filter = $(this).attr('name');
                var value = $(this).val();
                if (value) {
                    filters[filter] = value;
                }
            });

            $.ajax({
                url: apwp_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'apwp_filter_animals',
                    nonce: apwp_vars.nonce,
                    filters: filters
                },
                beforeSend: function() {
                    PR.showLoader();
                },
                success: function(response) {
                    if (response.success) {
                        $('.apwp-animals-grid').html(response.data.html);
                    } else {
                        PR.showMessage(response.data.message, 'error');
                    }
                },
                error: function() {
                    PR.showMessage('Erro ao filtrar animais. Tente novamente.', 'error');
                },
                complete: function() {
                    PR.hideLoader();
                }
            });
        },

        // Manipula o clique no card do animal
        handleAnimalClick: function(e) {
            var animalId = $(this).data('animal-id');
            
            $.ajax({
                url: apwp_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'apwp_get_animal_details',
                    nonce: apwp_vars.nonce,
                    animal_id: animalId
                },
                beforeSend: function() {
                    PR.showLoader();
                },
                success: function(response) {
                    if (response.success) {
                        PR.openAnimalModal(response.data);
                    } else {
                        PR.showMessage(response.data.message, 'error');
                    }
                },
                error: function() {
                    PR.showMessage('Erro ao carregar detalhes do animal. Tente novamente.', 'error');
                },
                complete: function() {
                    PR.hideLoader();
                }
            });
        },

        // Manipula o envio do formulário de adoção
        handleAdoptionSubmit: function(e) {
            e.preventDefault();
            
            if (!apwp_vars.is_user_logged_in) {
                PR.showMessage('Você precisa estar logado para enviar o formulário de adoção.', 'error');
                return;
            }
            
            var form = $(this);
            var formData = new FormData(form[0]);
            formData.append('action', 'apwp_submit_adoption');
            formData.append('nonce', apwp_vars.nonce);

            $.ajax({
                url: apwp_vars.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    PR.showLoader();
                    form.find(':submit').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        PR.showMessage(response.data.message, 'success');
                        form[0].reset();
                    } else {
                        PR.showMessage(response.data.message, 'error');
                    }
                },
                error: function() {
                    PR.showMessage('Erro ao enviar formulário. Tente novamente.', 'error');
                },
                complete: function() {
                    PR.hideLoader();
                    form.find(':submit').prop('disabled', false);
                }
            });
        },

        // Abre o modal com detalhes do animal
        openAnimalModal: function(data) {
            var modal = $('<div>', {
                class: 'apwp-modal'
            }).appendTo('body');

            var modalContent = $('<div>', {
                class: 'apwp-modal-content',
                html: data.html
            }).appendTo(modal);

            var closeBtn = $('<button>', {
                class: 'apwp-modal-close',
                html: '×'
            }).appendTo(modalContent);

            closeBtn.on('click', function() {
                modal.remove();
            });

            $(document).on('keyup', function(e) {
                if (e.key === 'Escape') {
                    modal.remove();
                }
            });

            modal.on('click', function(e) {
                if ($(e.target).hasClass('apwp-modal')) {
                    modal.remove();
                }
            });
        },

        // Exibe mensagem de feedback
        showMessage: function(message, type) {
            var messageEl = $('<div>', {
                class: 'apwp-message apwp-message-' + type,
                text: message
            });

            $('.apwp-messages').html(messageEl);

            setTimeout(function() {
                messageEl.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },

        // Exibe loader
        showLoader: function() {
            if (!$('.apwp-loader').length) {
                $('<div>', {
                    class: 'apwp-loader'
                }).appendTo('body');
            }
        },

        // Oculta loader
        hideLoader: function() {
            $('.apwp-loader').remove();
        }
    };

    // Inicializa o plugin
    PR.init();
});
