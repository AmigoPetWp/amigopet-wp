/**
 * Scripts do frontend
 */
jQuery(document).ready(function($) {
    /**
     * Máscara para campos de formulário
     */
    function initMasks() {
        if (typeof $.fn.mask !== 'undefined') {
            $('.apwp-phone').mask('(00) 00000-0000');
            $('.apwp-cpf').mask('000.000.000-00');
            $('.apwp-cep').mask('00000-000');
            $('.apwp-money').mask('000.000.000.000.000,00', {
                reverse: true,
                placeholder: '0,00'
            });
        }
    }
    
    /**
     * Validação de formulários
     */
    function initFormValidation() {
        if (typeof $.fn.validate !== 'undefined') {
            // Tradução das mensagens
            $.extend($.validator.messages, {
                required: apwp.i18n.required,
                email: apwp.i18n.email,
                minlength: $.validator.format(apwp.i18n.minlength),
                maxlength: $.validator.format(apwp.i18n.maxlength),
                equalTo: apwp.i18n.equalTo
            });
            
            // Método para validar CPF
            $.validator.addMethod('cpf', function(value, element) {
                value = value.replace(/[^\d]+/g, '');
                
                if (value == '') return false;
                
                // Elimina CPFs inválidos conhecidos
                if (
                    value.length != 11 ||
                    value == "00000000000" ||
                    value == "11111111111" ||
                    value == "22222222222" ||
                    value == "33333333333" ||
                    value == "44444444444" ||
                    value == "55555555555" ||
                    value == "66666666666" ||
                    value == "77777777777" ||
                    value == "88888888888" ||
                    value == "99999999999"
                ) {
                    return false;
                }
                
                // Valida 1o digito
                let add = 0;
                for (let i = 0; i < 9; i++) {
                    add += parseInt(value.charAt(i)) * (10 - i);
                }
                let rev = 11 - (add % 11);
                if (rev == 10 || rev == 11) {
                    rev = 0;
                }
                if (rev != parseInt(value.charAt(9))) {
                    return false;
                }
                
                // Valida 2o digito
                add = 0;
                for (let i = 0; i < 10; i++) {
                    add += parseInt(value.charAt(i)) * (11 - i);
                }
                rev = 11 - (add % 11);
                if (rev == 10 || rev == 11) {
                    rev = 0;
                }
                if (rev != parseInt(value.charAt(10))) {
                    return false;
                }
                
                return true;
            }, apwp.i18n.cpf);
            
            // Método para validar CEP
            $.validator.addMethod('cep', function(value, element) {
                return this.optional(element) || /^[0-9]{5}-[0-9]{3}$/.test(value);
            }, apwp.i18n.cep);
            
            // Método para validar telefone
            $.validator.addMethod('phone', function(value, element) {
                return this.optional(element) || /^\([0-9]{2}\) [0-9]{4,5}-[0-9]{4}$/.test(value);
            }, apwp.i18n.phone);
            
            // Validação do formulário de adoção
            $('#apwp-adoption-form').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    phone: {
                        required: true,
                        phone: true
                    },
                    cpf: {
                        required: true,
                        cpf: true
                    },
                    address: {
                        required: true,
                        minlength: 10
                    },
                    reason: {
                        required: true,
                        minlength: 50
                    },
                    home_type: 'required',
                    yard_size: 'required',
                    agree_terms: 'required'
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    if (element.is(':checkbox')) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
            
            // Validação do formulário de doação
            $('#apwp-donation-form').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    phone: {
                        required: true,
                        phone: true
                    },
                    amount: {
                        required: true,
                        min: 1
                    },
                    payment_method: 'required',
                    agree_terms: 'required'
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    if (element.is(':checkbox')) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
            
            // Validação do formulário de voluntário
            $('#apwp-volunteer-form').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    phone: {
                        required: true,
                        phone: true
                    },
                    availability: 'required',
                    skills: {
                        required: true,
                        minlength: 50
                    },
                    agree_terms: 'required'
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    if (element.is(':checkbox')) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        }
    }
    
    /**
     * Modal de termos
     */
    function initTermsModal() {
        $('.apwp-view-terms').on('click', function(e) {
            e.preventDefault();
            
            const modal = $(`
                <div class="apwp-modal">
                    <div class="apwp-modal-overlay"></div>
                    <div class="apwp-modal-container">
                        <div class="apwp-modal-header">
                            <h3>${apwp.i18n.terms_title}</h3>
                            <button class="apwp-modal-close">
                                <span class="dashicons dashicons-no-alt"></span>
                            </button>
                        </div>
                        <div class="apwp-modal-content">
                            <div class="apwp-modal-loading">
                                <div class="apwp-spinner"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            
            $('body').append(modal).addClass('apwp-modal-open');
            
            // Carrega os termos
            $.ajax({
                url: apwp.ajax_url,
                type: 'GET',
                data: {
                    action: 'apwp_get_terms',
                    _ajax_nonce: apwp.nonce,
                    type: $(this).data('type')
                },
                success: function(response) {
                    if (response.success) {
                        modal.find('.apwp-modal-content').html(response.data);
                    } else {
                        modal.find('.apwp-modal-content').html(`
                            <div class="apwp-error">
                                ${response.data}
                            </div>
                        `);
                    }
                },
                error: function() {
                    modal.find('.apwp-modal-content').html(`
                        <div class="apwp-error">
                            ${apwp.i18n.error}
                        </div>
                    `);
                }
            });
            
            // Fecha o modal
            modal.on('click', '.apwp-modal-close, .apwp-modal-overlay', function() {
                modal.remove();
                $('body').removeClass('apwp-modal-open');
            });
        });
    }
    
    // Inicializa os scripts
    initMasks();
    initFormValidation();
    initTermsModal();
});
