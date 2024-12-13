<?php
/**
 * Template de tabela para a listagem de organizações
 *
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Obtém os parâmetros de filtro da URL
$args = array(
    'search' => sanitize_text_field($_GET['filter_name'] ?? ''),
    'city' => sanitize_text_field($_GET['filter_city'] ?? ''),
    'limit' => 10,
    'offset' => (get_query_var('paged', 1) - 1) * 10
);

// Instancia a classe de organizações
$organization = new APWP_Organization();

// Obtém a lista de organizações
$organizations = $organization->list($args);
?>

<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th><?php esc_html_e('ID', 'amigopet-wp'); ?></th>
                <th><?php esc_html_e('Nome', 'amigopet-wp'); ?></th>
                <th><?php esc_html_e('Email', 'amigopet-wp'); ?></th>
                <th><?php esc_html_e('Telefone', 'amigopet-wp'); ?></th>
                <th><?php esc_html_e('Cidade', 'amigopet-wp'); ?></th>
                <th><?php esc_html_e('Ações', 'amigopet-wp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($organizations)) : ?>
                <tr>
                    <td colspan="6" class="text-center">
                        <?php esc_html_e('Nenhuma organização encontrada.', 'amigopet-wp'); ?>
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($organizations as $organization) : ?>
                    <tr>
                        <td><?php echo esc_html($organization->id); ?></td>
                        <td><?php echo esc_html($organization->name); ?></td>
                        <td><?php echo esc_html($organization->email); ?></td>
                        <td><?php echo esc_html($organization->phone); ?></td>
                        <td><?php echo esc_html($organization->city); ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="<?php echo esc_url(admin_url("admin.php?page=amigopet-wp-organizations-edit&id={$organization->id}")); ?>" 
                                   class="btn btn-sm btn-info" title="<?php esc_attr_e('Editar', 'amigopet-wp'); ?>">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger delete-organization" 
                                        data-id="<?php echo esc_attr($organization->id); ?>"
                                        data-name="<?php echo esc_attr($organization->name); ?>"
                                        title="<?php esc_attr_e('Excluir', 'amigopet-wp'); ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Adiciona o script de confirmação de exclusão
wp_add_inline_script('apwp-admin', '
    jQuery(document).ready(function($) {
        $(".delete-organization").click(function() {
            var id = $(this).data("id");
            var name = $(this).data("name");
            
            if (confirm(apwpAdmin.i18n.confirmDelete.replace("%s", name))) {
                $.post(ajaxurl, {
                    action: "delete_organization",
                    id: id,
                    _wpnonce: apwpAdmin.nonce
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                });
            }
        });
    });
');
?>
