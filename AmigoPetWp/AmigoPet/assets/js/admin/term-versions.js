jQuery(document).ready(function($) {
    // Cache de elementos
    const $form = $('#apwp-version-form');
    const $modal = $('#apwp-preview-modal');
    const $previewContent = $('#apwp-preview-content');
    
    // Estado
    let isEditing = false;
    let currentVersionId = null;

    // Inicialização
    initializeDatePicker();
    initializeEditor();

    // Event Listeners
    $('#apwp-new-version').on('click', handleNewVersion);
    $('.apwp-edit-version').on('click', handleEditVersion);
    $('#apwp-cancel-version').on('click', handleCancelVersion);
    $('.apwp-preview-version').on('click', handlePreviewVersion);
    $('.apwp-send-to-review').on('click', handleSendToReview);
    $('.apwp-approve-version').on('click', handleApproveVersion);
    $('.apwp-activate-version').on('click', handleActivateVersion);
    $('.apwp-delete-version').on('click', handleDeleteVersion);
    $('.apwp-modal-close').on('click', closeModal);
    $form.on('submit', handleSubmitForm);

    // Handlers
    function handleNewVersion(e) {
        e.preventDefault();
        isEditing = false;
        currentVersionId = null;
        resetForm();
        showForm();
    }

    function handleEditVersion(e) {
        e.preventDefault();
        const id = $(this).data('id');
        isEditing = true;
        currentVersionId = id;
        loadVersion(id);
    }

    function handleCancelVersion(e) {
        e.preventDefault();
        hideForm();
        resetForm();
    }

    function handlePreviewVersion(e) {
        e.preventDefault();
        const id = $(this).data('id');
        loadPreview(id);
    }

    function handleSendToReview(e) {
        e.preventDefault();
        const id = $(this).data('id');
        if (confirm('Enviar esta versão para revisão?')) {
            sendToReview(id);
        }
    }

    function handleApproveVersion(e) {
        e.preventDefault();
        const id = $(this).data('id');
        if (confirm('Aprovar esta versão?')) {
            approveVersion(id);
        }
    }

    function handleActivateVersion(e) {
        e.preventDefault();
        const id = $(this).data('id');
        if (confirm('Ativar esta versão? A versão atual será desativada.')) {
            activateVersion(id);
        }
    }

    function handleDeleteVersion(e) {
        e.preventDefault();
        const id = $(this).data('id');
        if (confirm('Excluir esta versão? Esta ação não pode ser desfeita.')) {
            deleteVersion(id);
        }
    }

    function handleSubmitForm(e) {
        e.preventDefault();
        saveVersion();
    }

    // API Calls
    function loadVersion(id) {
        startLoading();
        $.post(ajaxurl, {
            action: 'apwp_get_version',
            id: id,
            _ajax_nonce: apwpTermVersions.nonce
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
            action: 'apwp_get_version_preview',
            id: id,
            _ajax_nonce: apwpTermVersions.nonce
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

    function saveVersion() {
        startLoading();
        const data = getFormData();
        $.post(ajaxurl, {
            action: 'apwp_save_term_version',
            ...data,
            _ajax_nonce: apwpTermVersions.nonce
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

    function sendToReview(id) {
        startLoading();
        $.post(ajaxurl, {
            action: 'apwp_send_to_review',
            id: id,
            _ajax_nonce: apwpTermVersions.nonce
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

    function approveVersion(id) {
        startLoading();
        $.post(ajaxurl, {
            action: 'apwp_approve_version',
            id: id,
            _ajax_nonce: apwpTermVersions.nonce
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

    function activateVersion(id) {
        startLoading();
        $.post(ajaxurl, {
            action: 'apwp_activate_version',
            id: id,
            _ajax_nonce: apwpTermVersions.nonce
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

    function deleteVersion(id) {
        startLoading();
        $.post(ajaxurl, {
            action: 'apwp_delete_version',
            id: id,
            _ajax_nonce: apwpTermVersions.nonce
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
        $form.find('input[name="version"]').val('');
        $form.find('input[name="effective_date"]').val('');
        if (tinymce.get('content')) {
            tinymce.get('content').setContent('');
        }
        $form.find('textarea[name="change_log"]').val('');
    }

    function fillForm(data) {
        $form.find('input[name="id"]').val(data.id);
        $form.find('input[name="version"]').val(data.version);
        $form.find('input[name="effective_date"]').val(data.effective_date);
        if (tinymce.get('content')) {
            tinymce.get('content').setContent(data.content);
        }
        $form.find('textarea[name="change_log"]').val(data.change_log);
    }

    function getFormData() {
        return {
            id: currentVersionId,
            term_id: $form.find('input[name="term_id"]').val(),
            version: $form.find('input[name="version"]').val(),
            effective_date: $form.find('input[name="effective_date"]').val(),
            content: tinymce.get('content') ? tinymce.get('content').getContent() : '',
            change_log: $form.find('textarea[name="change_log"]').val()
        };
    }

    function showSuccess(message) {
        const $message = $('<div class="apwp-message success"></div>').text(message);
        $form.before($message);
        setTimeout(() => $message.fadeOut(() => $message.remove()), 3000);
    }

    function showError(message) {
        const $message = $('<div class="apwp-message error"></div>').text(message);
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

    // Inicializadores
    function initializeDatePicker() {
        if ($.fn.datepicker) {
            $('input[name="effective_date"]').datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: 0
            });
        }
    }

    function initializeEditor() {
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#content',
                height: 300,
                menubar: false,
                plugins: [
                    'lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic backcolor | \
                    alignleft aligncenter alignright alignjustify | \
                    bullist numlist outdent indent | removeformat | help'
            });
        }
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
