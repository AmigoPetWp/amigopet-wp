/**
 * Scripts base para o painel administrativo
 */
(function($) {
    'use strict';

    // Objeto principal do admin
    window.APWP_Admin = {
        /**
         * Inicializa os scripts do admin
         */
        init: function() {
            this.initTooltips();
            this.initConfirmDialogs();
            this.initTableFilters();
            this.initPagination();
        },

        /**
         * Inicializa tooltips
         */
        initTooltips: function() {
            $('[title]').tooltip();
        },

        /**
         * Inicializa diálogos de confirmação
         */
        initConfirmDialogs: function() {
            $(document).on('click', '[data-confirm]', function(e) {
                e.preventDefault();
                const message = $(this).data('confirm') || apwpAdmin.i18n.confirmDelete;
                if (confirm(message)) {
                    // Procede com a ação
                    const url = $(this).attr('href');
                    if (url && url !== '#') {
                        window.location.href = url;
                    }
                }
            });
        },

        /**
         * Inicializa filtros de tabela
         */
        initTableFilters: function() {
            // Busca em tempo real
            let searchTimeout;
            $('.apwp-admin-search input').on('keyup', function() {
                clearTimeout(searchTimeout);
                const input = $(this);
                searchTimeout = setTimeout(function() {
                    APWP_Admin.filterTable(input);
                }, 300);
            });

            // Filtros select
            $('.apwp-admin-select').on('change', function() {
                APWP_Admin.filterTable($(this));
            });

            // Filtros de data
            $('.apwp-admin-date').on('change', function() {
                APWP_Admin.filterTable($(this));
            });
        },

        /**
         * Filtra a tabela baseado nos valores dos filtros
         */
        filterTable: function(changedInput) {
            const tableId = changedInput.closest('.apwp-admin-content').find('table').attr('id');
            const searchValue = $('.apwp-admin-search input').val().toLowerCase();
            const filters = {};

            // Coleta valores dos filtros
            $('.apwp-admin-select').each(function() {
                const value = $(this).val();
                if (value) {
                    filters[$(this).attr('id')] = value;
                }
            });

            // Coleta datas
            const dateFrom = $('#date-from').val();
            const dateTo = $('#date-to').val();
            if (dateFrom) filters.dateFrom = dateFrom;
            if (dateTo) filters.dateTo = dateTo;

            // Faz a requisição AJAX
            $.ajax({
                url: apwpAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'apwp_filter_table',
                    nonce: apwpAdmin.nonce,
                    table: tableId,
                    search: searchValue,
                    filters: filters
                },
                beforeSend: function() {
                    APWP_Admin.showLoader();
                },
                success: function(response) {
                    if (response.success) {
                        // Atualiza a tabela com os novos dados
                        $('#' + tableId + ' tbody').html(response.data.html);
                        // Atualiza a paginação
                        if (response.data.pagination) {
                            $('.apwp-admin-pagination').html(response.data.pagination);
                        }
                    } else {
                        APWP_Admin.showError(response.data.message || apwpAdmin.i18n.error);
                    }
                },
                error: function() {
                    APWP_Admin.showError(apwpAdmin.i18n.error);
                },
                complete: function() {
                    APWP_Admin.hideLoader();
                }
            });
        },

        /**
         * Inicializa a paginação
         */
        initPagination: function() {
            $(document).on('click', '.apwp-admin-page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page) {
                    APWP_Admin.loadPage(page);
                }
            });
        },

        /**
         * Carrega uma página específica
         */
        loadPage: function(page) {
            const tableId = $('.apwp-admin-content table').attr('id');
            
            $.ajax({
                url: apwpAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'apwp_load_page',
                    nonce: apwpAdmin.nonce,
                    table: tableId,
                    page: page
                },
                beforeSend: function() {
                    APWP_Admin.showLoader();
                },
                success: function(response) {
                    if (response.success) {
                        $('#' + tableId + ' tbody').html(response.data.html);
                        $('.apwp-admin-pagination').html(response.data.pagination);
                    } else {
                        APWP_Admin.showError(response.data.message || apwpAdmin.i18n.error);
                    }
                },
                error: function() {
                    APWP_Admin.showError(apwpAdmin.i18n.error);
                },
                complete: function() {
                    APWP_Admin.hideLoader();
                }
            });
        },

        /**
         * Mostra o loader
         */
        showLoader: function() {
            if (!$('.apwp-admin-loader').length) {
                $('body').append('<div class="apwp-admin-loader"><div class="apwp-admin-spinner"></div></div>');
            }
            $('.apwp-admin-loader').fadeIn(200);
        },

        /**
         * Esconde o loader
         */
        hideLoader: function() {
            $('.apwp-admin-loader').fadeOut(200);
        },

        /**
         * Mostra uma mensagem de erro
         */
        showError: function(message) {
            const notice = $('<div class="notice notice-error is-dismissible"><p>' + message + '</p></div>');
            $('.wrap > h1').after(notice);
            setTimeout(function() {
                notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        },

        /**
         * Mostra uma mensagem de sucesso
         */
        showSuccess: function(message) {
            const notice = $('<div class="notice notice-success is-dismissible"><p>' + message + '</p></div>');
            $('.wrap > h1').after(notice);
            setTimeout(function() {
                notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    };

    // Inicializa quando o documento estiver pronto
    $(document).ready(function() {
        APWP_Admin.init();
    });

})(jQuery);
