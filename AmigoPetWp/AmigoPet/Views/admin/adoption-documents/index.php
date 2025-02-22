<?php
if (!defined('ABSPATH')) exit;
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Documentos de Adoção</h1>
    <a href="#" class="page-title-action" id="apwp-new-document">Adicionar Novo</a>
    <hr class="wp-header-end">

    <!-- Lista de Documentos -->
    <div class="apwp-documents-list">
        <?php if (empty($documents)): ?>
            <div class="notice notice-info">
                <p>Nenhum documento cadastrado.</p>
            </div>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Template</th>
                        <th>Status</th>
                        <th>Campos Obrigatórios</th>
                        <th>Última Atualização</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $document): ?>
                        <tr>
                            <td><?php echo esc_html($document->getTitle()); ?></td>
                            <td><?php echo esc_html($document->getTemplate()); ?></td>
                            <td>
                                <span class="apwp-status-badge status-<?php echo esc_attr($document->getStatus()); ?>">
                                    <?php echo esc_html($document->getStatusName()); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html(implode(', ', $document->getRequiredFields())); ?></td>
                            <td><?php echo esc_html($document->getUpdatedAt()->format('d/m/Y H:i')); ?></td>
                            <td>
                                <div class="row-actions">
                                    <span class="edit">
                                        <a href="#" class="apwp-edit-document" data-id="<?php echo esc_attr($document->getId()); ?>">
                                            Editar
                                        </a> |
                                    </span>
                                    <span class="preview">
                                        <a href="#" class="apwp-preview-document" data-id="<?php echo esc_attr($document->getId()); ?>">
                                            Prévia
                                        </a> |
                                    </span>
                                    <span class="download">
                                        <a href="<?php echo esc_url(add_query_arg([
                                            'action' => 'apwp_download_adoption_document',
                                            'id' => $document->getId(),
                                            '_wpnonce' => wp_create_nonce('apwp_adoption_documents')
                                        ], admin_url('admin-ajax.php'))); ?>" target="_blank">
                                            Download
                                        </a> |
                                    </span>
                                    <span class="delete">
                                        <a href="#" class="apwp-delete-document" data-id="<?php echo esc_attr($document->getId()); ?>">
                                            Excluir
                                        </a>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Formulário de Documento -->
    <div id="apwp-document-form" style="display: none;">
        <div class="apwp-form-container">
            <h2>Documento de Adoção</h2>
            <form method="post">
                <input type="hidden" name="action" value="apwp_save_adoption_document">
                <input type="hidden" name="id" value="">
                <?php wp_nonce_field('apwp_adoption_documents'); ?>

                <table class="form-table">
                    <tr>
                        <th><label for="title">Título</label></th>
                        <td>
                            <input type="text" id="title" name="title" class="regular-text" required>
                            <p class="description">Nome do documento para identificação</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="description">Descrição</label></th>
                        <td>
                            <textarea id="description" name="description" rows="3" class="large-text"></textarea>
                            <p class="description">Breve descrição do documento e seu propósito</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="template">Template</label></th>
                        <td>
                            <select id="template" name="template" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($templates as $key => $name): ?>
                                    <option value="<?php echo esc_attr($key); ?>">
                                        <?php echo esc_html($name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">Modelo base para o documento</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="status">Status</label></th>
                        <td>
                            <select id="status" name="status" required>
                                <option value="draft">Rascunho</option>
                                <option value="active">Ativo</option>
                                <option value="inactive">Inativo</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Campos Obrigatórios</label></th>
                        <td>
                            <div class="apwp-fields-container">
                                <div class="apwp-field-list" id="required-fields">
                                    <!-- Campos serão adicionados via JavaScript -->
                                </div>
                                <button type="button" class="button" id="add-required-field">
                                    Adicionar Campo
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Campos Opcionais</label></th>
                        <td>
                            <div class="apwp-fields-container">
                                <div class="apwp-field-list" id="optional-fields">
                                    <!-- Campos serão adicionados via JavaScript -->
                                </div>
                                <button type="button" class="button" id="add-optional-field">
                                    Adicionar Campo
                                </button>
                            </div>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary">Salvar Documento</button>
                    <button type="button" class="button" id="apwp-cancel-document">Cancelar</button>
                </p>
            </form>
        </div>
    </div>

    <!-- Modal de Prévia -->
    <div id="apwp-preview-modal" class="apwp-modal" style="display: none;">
        <div class="apwp-modal-content">
            <span class="apwp-modal-close">&times;</span>
            <h2>Prévia do Documento</h2>
            <div id="apwp-preview-content"></div>
        </div>
    </div>
</div>

<style>
/* Status Badges */
.apwp-status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 500;
}

.status-draft {
    background-color: #f0f0f1;
    color: #646970;
}

.status-active {
    background-color: #edfaef;
    color: #00a32a;
}

.status-inactive {
    background-color: #fcf0f1;
    color: #d63638;
}

/* Form Container */
.apwp-form-container {
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 20px;
    margin: 20px 0;
}

/* Fields Container */
.apwp-fields-container {
    margin-bottom: 10px;
}

.apwp-field-list {
    margin-bottom: 10px;
}

.apwp-field-item {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}

.apwp-field-item input {
    margin-right: 10px;
}

.apwp-field-item .button {
    padding: 0 8px;
}

/* Modal */
.apwp-modal {
    display: none;
    position: fixed;
    z-index: 100000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.apwp-modal-content {
    position: relative;
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 800px;
    max-height: 80vh;
    overflow-y: auto;
}

.apwp-modal-close {
    position: absolute;
    right: 20px;
    top: 10px;
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.apwp-modal-close:hover {
    color: #666;
}

/* Responsive */
@media screen and (max-width: 782px) {
    .apwp-form-container {
        margin: 10px 0;
        padding: 10px;
    }

    .apwp-modal-content {
        width: 95%;
        margin: 10% auto;
    }
}
</style>
