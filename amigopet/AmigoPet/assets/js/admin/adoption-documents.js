jQuery(document).ready(function($) {
    // Cache de elementos
    const $form = $('#apwp-document-form');
    const $modal = $('#apwp-preview-modal');
    const $previewContent = $('#apwp-preview-content');
    
    // Event Listeners
    $('#apwp-new-document').on('click', handleNewDocument);
    $('.apwp-edit-document').on('click', handleEditDocument);
    $('#apwp-cancel-document').on('click', handleCancelDocument);
    $('.apwp-preview-document').on('click', handlePreviewDocument);
    $('.apwp-delete-document').on('click', handleDeleteDocument);
    $('.apwp-modal-close').on('click', closeModal);
    $('#add-required-field').on('click', () => addField('required'));
    $('#add-optional-field').on('click', () => addField('optional'));
    $form.on('submit', handleSubmitForm);

    // Handlers
    function handleNewDocument(e) {
        e.preventDefault();
        resetForm();
        showForm();
    }

    function handleEditDocument(e) {
        e.preventDefault();
        const id = $(this).data('id');
        loadDocument(id);
    }

    function handleCancelDocument(e) {
        e.preventDefault();
        hideForm();
        resetForm();
    }

    function handlePreviewDocument(e) {
        e.preventDefault();
        const id = $(this).data('id');
        loadPreview(id);
    }

    function handleDeleteDocument(e) {
        e.preventDefault();
        const id = $(this).data('id');
        if (confirm('Excluir este documento? Esta ação não pode ser desfeita.')) {
            deleteDocument(id);
        }
    }

    function handleSubmitForm(e) {
        e.preventDefault();
        saveDocument();
    }

    // API Calls
    function loadDocument(id) {
        startLoading();
        $.post(ajaxurl, {
            action: 'apwp_get_adoption_document',
            id: id,
            _ajax_nonce: apwpAdoptionDocuments.nonce
        })
        .done(function(response) {
            if (response.success) {
                fillForm(response.data);
                showForm();
            } else {
                showError(response.data.message);
            }
        })
        .fail(handleAjaxError)
        .always(stopLoading);
    }

    function loadPreview(id) {
        startLoading();
        $.post(ajaxurl, {
            action: 'apwp_preview_adoption_document',
            id: id,
            _ajax_nonce: apwpAdoptionDocuments.nonce
        })
        .done(function(response) {
            if (response.success) {
                showPreview(response.data.preview);
            } else {
                showError(response.data.message);
            }
        })
        .fail(handleAjaxError)
        .always(stopLoading);
    }

    function saveDocument() {
        startLoading();
        const data = getFormData();
        $.post(ajaxurl, {
            action: 'apwp_save_adoption_document',
            ...data,
            _ajax_nonce: apwpAdoptionDocuments.nonce
        })
        .done(function(response) {
            if (response.success) {
                showSuccess(response.data.message);
                reloadPage();
            } else {
                showError(response.data.message);
            }
        })
        .fail(handleAjaxError)
        .always(stopLoading);
    }

    function deleteDocument(id) {
        startLoading();
        $.post(ajaxurl, {
            action: 'apwp_delete_adoption_document',
            id: id,
            _ajax_nonce: apwpAdoptionDocuments.nonce
        })
        .done(function(response) {
            if (response.success) {
                showSuccess(response.data.message);
                reloadPage();
            } else {
                showError(response.data.message);
            }
        })
        .fail(handleAjaxError)
        .always(stopLoading);
    }

    // UI Helpers
    function showForm() {
        $form.slideDown();
    }

    function hideForm() {
        $form.slideUp();
    }

    function showPreview(content) {
        $previewContent.html(content);
        $modal.fadeIn();
    }

    function closeModal() {
        $modal.fadeOut();
    }

    function resetForm() {
        $form.find('input[name="id"]').val('');
        $form.find('input[name="title"]').val('');
        $form.find('textarea[name="description"]').val('');
        $form.find('select[name="template"]').val('');
        $form.find('select[name="status"]').val('draft');
        $('#required-fields').empty();
        $('#optional-fields').empty();
    }

    function fillForm(data) {
        $form.find('input[name="id"]').val(data.id);
        $form.find('input[name="title"]').val(data.title);
        $form.find('textarea[name="description"]').val(data.description);
        $form.find('select[name="template"]').val(data.template);
        $form.find('select[name="status"]').val(data.status);

        // Preenche campos obrigatórios
        $('#required-fields').empty();
        data.required_fields.forEach(field => {
            addField('required', field);
        });

        // Preenche campos opcionais
        $('#optional-fields').empty();
        data.optional_fields.forEach(field => {
            addField('optional', field);
        });
    }

    function addField(type, value = '') {
        const $container = type === 'required' ? $('#required-fields') : $('#optional-fields');
        const fieldHtml = `
            <div class="apwp-field-item">
                <input type="text" name="${type}_fields[]" value="${value}" class="regular-text" placeholder="Nome do campo">
                <button type="button" class="button remove-field">&times;</button>
            </div>
        `;
        const $field = $(fieldHtml);
        
        $field.find('.remove-field').on('click', function() {
            $field.remove();
        });

        $container.append($field);
    }

    function getFormData() {
        const requiredFields = [];
        $('input[name="required_fields[]"]').each(function() {
            const value = $(this).val().trim();
            if (value) requiredFields.push(value);
        });

        const optionalFields = [];
        $('input[name="optional_fields[]"]').each(function() {
            const value = $(this).val().trim();
            if (value) optionalFields.push(value);
        });

        return {
            id: $form.find('input[name="id"]').val(),
            title: $form.find('input[name="title"]').val(),
            description: $form.find('textarea[name="description"]').val(),
            template: $form.find('select[name="template"]').val(),
            status: $form.find('select[name="status"]').val(),
            required_fields: requiredFields,
            optional_fields: optionalFields
        };
    }

    function showSuccess(message) {
        const $message = $('<div class="notice notice-success is-dismissible"><p></p></div>')
            .find('p').text(message).end();
        $form.before($message);
        setTimeout(() => $message.fadeOut(() => $message.remove()), 3000);
    }

    function showError(message) {
        const $message = $('<div class="notice notice-error is-dismissible"><p></p></div>')
            .find('p').text(message).end();
        $form.before($message);
        setTimeout(() => $message.fadeOut(() => $message.remove()), 5000);
    }

    function startLoading() {
        $('body').addClass('apwp-loading');
    }

    function stopLoading() {
        $('body').removeClass('apwp-loading');
    }

    function reloadPage() {
        window.location.reload();
    }

    function handleAjaxError(xhr, status, error) {
        showError('Erro ao processar a requisição: ' + error);
    }

    // Keyboard Shortcuts
    $(document).keydown(function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    // Click fora do modal fecha ele
    $(window).click(function(e) {
        if ($(e.target).is($modal)) {
            closeModal();
        }
    });
});
