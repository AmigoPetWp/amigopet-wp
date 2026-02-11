<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('ABSPATH')) exit;
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Versões de Termos</h1>
     if ($termId): ?>
        <a href="#" class="page-title-action" id="apwp-new-version">Nova Versão</a>
     endif; ?>
    <hr class="wp-header-end">

     if (!$termId): ?>
        <div class="notice notice-info">
            <p>Selecione um termo para gerenciar suas versões.</p>
        </div>

        <h2>Versões Pendentes de Revisão</h2>
         if (empty($pendingReview)): ?>
            <p>Não há versões pendentes de revisão.</p>
         else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Termo</th>
                        <th>Versão</th>
                        <th>Autor</th>
                        <th>Data Efetiva</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                     foreach ($pendingReview as $version): ?>
                        <tr>
                            <td> echo esc_html($version->getTermName()); ?></td>
                            <td> echo esc_html($version->getVersion()); ?></td>
                            <td> echo esc_html(get_userdata($version->getCreatedBy())->display_name); ?></td>
                            <td> echo esc_html($version->getEffectiveDate()->format('d/m/Y')); ?></td>
                            <td>
                                <a href="#" class="apwp-preview-version" data-id=" echo esc_attr($version->getId()); ?>">Visualizar</a>
                                 if (current_user_can('approve_terms')): ?>
                                    | <a href="#" class="apwp-approve-version" data-id=" echo esc_attr($version->getId()); ?>">Aprovar</a>
                                 endif; ?>
                            </td>
                        </tr>
                     endforeach; ?>
                </tbody>
            </table>
         endif; ?>

     else: ?>
        <div class="apwp-term-versions-container">
            <!-- Histórico de Versões -->
            <div class="apwp-version-history">
                <h2>Histórico de Versões</h2>
                 if (empty($versions)): ?>
                    <p>Não há versões para este termo.</p>
                 else: ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Versão</th>
                                <th>Status</th>
                                <th>Autor</th>
                                <th>Data Efetiva</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                             foreach ($versions as $version): ?>
                                <tr class=" echo $version->isActive() ? 'active-version' : ''; ?>">
                                    <td> echo esc_html($version->getVersion()); ?></td>
                                    <td> echo esc_html($version->getStatusName()); ?></td>
                                    <td> echo esc_html(get_userdata($version->getCreatedBy())->display_name); ?></td>
                                    <td> echo esc_html($version->getEffectiveDate()->format('d/m/Y')); ?></td>
                                    <td>
                                        <a href="#" class="apwp-preview-version" data-id=" echo esc_attr($version->getId()); ?>">Visualizar</a>
                                         if ($version->isDraft()): ?>
                                            | <a href="#" class="apwp-edit-version" data-id=" echo esc_attr($version->getId()); ?>">Editar</a>
                                            | <a href="#" class="apwp-send-to-review" data-id=" echo esc_attr($version->getId()); ?>">Enviar para Revisão</a>
                                             if (current_user_can('delete_terms')): ?>
                                                | <a href="#" class="apwp-delete-version" data-id=" echo esc_attr($version->getId()); ?>">Excluir</a>
                                             endif; ?>
                                         elseif ($version->isInReview() && current_user_can('approve_terms')): ?>
                                            | <a href="#" class="apwp-approve-version" data-id=" echo esc_attr($version->getId()); ?>">Aprovar</a>
                                         elseif ($version->isApproved() && current_user_can('activate_terms')): ?>
                                            | <a href="#" class="apwp-activate-version" data-id=" echo esc_attr($version->getId()); ?>">Ativar</a>
                                         endif; ?>
                                    </td>
                                </tr>
                             endforeach; ?>
                        </tbody>
                    </table>
                 endif; ?>
            </div>

            <!-- Formulário de Versão -->
            <div id="apwp-version-form" style="display: none;">
                <h2>Nova Versão</h2>
                <form method="post">
                    <input type="hidden" name="action" value="apwp_save_term_version">
                    <input type="hidden" name="term_id" value=" echo esc_attr($termId); ?>">
                    <input type="hidden" name="id" value="">
                     wp_nonce_field('apwp_term_versions'); ?>

                    <table class="form-table">
                        <tr>
                            <th><label for="version">Versão</label></th>
                            <td>
                                <input type="text" id="version" name="version" class="regular-text" required>
                                <p class="description">Use o formato X.Y.Z (ex: 1.0.0)</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="effective_date">Data Efetiva</label></th>
                            <td>
                                <input type="date" id="effective_date" name="effective_date" required>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="content">Conteúdo</label></th>
                            <td>
                                 wp_editor('', 'content', [
                                    'media_buttons' => false,
                                    'textarea_rows' => 15,
                                    'teeny' => true
                                ]); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="change_log">Log de Mudanças</label></th>
                            <td>
                                <textarea id="change_log" name="change_log" rows="5" class="large-text"></textarea>
                                <p class="description">Descreva as mudanças realizadas nesta versão</p>
                            </td>
                        </tr>
                    </table>

                    <p class="submit">
                        <button type="submit" class="button button-primary">Salvar Versão</button>
                        <button type="button" class="button" id="apwp-cancel-version">Cancelar</button>
                    </p>
                </form>
            </div>

            <!-- Modal de Prévia -->
            <div id="apwp-preview-modal" class="apwp-modal" style="display: none;">
                <div class="apwp-modal-content">
                    <span class="apwp-modal-close">&times;</span>
                    <h2>Prévia da Versão</h2>
                    <div id="apwp-preview-content"></div>
                </div>
            </div>
        </div>

        <style>
            .active-version {
                background-color: #e7f7e3;
            }
            .apwp-modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.4);
            }
            .apwp-modal-content {
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
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
            }
        </style>

        <script>
            jQuery(document).ready(function($) {
                // Novo/Editar
                $('#apwp-new-version').click(function(e) {
                    e.preventDefault();
                    resetForm();
                    $('#apwp-version-form').show();
                });

                $('.apwp-edit-version').click(function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    loadVersion(id);
                });

                $('#apwp-cancel-version').click(function(e) {
                    e.preventDefault();
                    resetForm();
                    $('#apwp-version-form').hide();
                });

                // Prévia
                $('.apwp-preview-version').click(function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    showPreview(id);
                });

                $('.apwp-modal-close').click(function() {
                    $('#apwp-preview-modal').hide();
                });

                // Ações
                $('.apwp-send-to-review').click(function(e) {
                    e.preventDefault();
                    if (confirm('Enviar esta versão para revisão?')) {
                        sendToReview($(this).data('id'));
                    }
                });

                $('.apwp-approve-version').click(function(e) {
                    e.preventDefault();
                    if (confirm('Aprovar esta versão?')) {
                        approveVersion($(this).data('id'));
                    }
                });

                $('.apwp-activate-version').click(function(e) {
                    e.preventDefault();
                    if (confirm('Ativar esta versão? A versão atual será desativada.')) {
                        activateVersion($(this).data('id'));
                    }
                });

                $('.apwp-delete-version').click(function(e) {
                    e.preventDefault();
                    if (confirm('Excluir esta versão? Esta ação não pode ser desfeita.')) {
                        deleteVersion($(this).data('id'));
                    }
                });

                // Funções auxiliares
                function resetForm() {
                    $('input[name="id"]').val('');
                    $('input[name="version"]').val('');
                    $('input[name="effective_date"]').val('');
                    tinymce.get('content').setContent('');
                    $('textarea[name="change_log"]').val('');
                }

                function loadVersion(id) {
                    $.post(ajaxurl, {
                        action: 'apwp_get_version',
                        id: id,
                        _ajax_nonce: apwpTermVersions.nonce
                    }, function(response) {
                        if (response.success) {
                            var data = response.data;
                            $('input[name="id"]').val(data.id);
                            $('input[name="version"]').val(data.version);
                            $('input[name="effective_date"]').val(data.effective_date);
                            tinymce.get('content').setContent(data.content);
                            $('textarea[name="change_log"]').val(data.change_log);
                            $('#apwp-version-form').show();
                        } else {
                            alert(response.data.message);
                        }
                    });
                }

                function showPreview(id) {
                    $.post(ajaxurl, {
                        action: 'apwp_get_version_preview',
                        id: id,
                        _ajax_nonce: apwpTermVersions.nonce
                    }, function(response) {
                        if (response.success) {
                            $('#apwp-preview-content').html(response.data.preview);
                            $('#apwp-preview-modal').show();
                        } else {
                            alert(response.data.message);
                        }
                    });
                }

                function sendToReview(id) {
                    $.post(ajaxurl, {
                        action: 'apwp_send_to_review',
                        id: id,
                        _ajax_nonce: apwpTermVersions.nonce
                    }, function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.data.message);
                        }
                    });
                }

                function approveVersion(id) {
                    $.post(ajaxurl, {
                        action: 'apwp_approve_version',
                        id: id,
                        _ajax_nonce: apwpTermVersions.nonce
                    }, function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.data.message);
                        }
                    });
                }

                function activateVersion(id) {
                    $.post(ajaxurl, {
                        action: 'apwp_activate_version',
                        id: id,
                        _ajax_nonce: apwpTermVersions.nonce
                    }, function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.data.message);
                        }
                    });
                }

                function deleteVersion(id) {
                    $.post(ajaxurl, {
                        action: 'apwp_delete_version',
                        id: id,
                        _ajax_nonce: apwpTermVersions.nonce
                    }, function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.data.message);
                        }
                    });
                }
            });
        </script>
     endif; ?>
</div>