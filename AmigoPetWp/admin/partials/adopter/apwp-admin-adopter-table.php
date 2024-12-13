<?php
/**
 * Template da tabela de adotantes
 *
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Obtém a lista de adotantes
$adopters = APWP_Adopter_Model::get_adopters(array(
    'name' => $_GET['filter_name'] ?? '',
    'document' => $_GET['filter_document'] ?? '',
    'email' => $_GET['filter_email'] ?? '',
    'city' => $_GET['filter_city'] ?? '',
    'page' => $_GET['paged'] ?? 1,
    'per_page' => 10
));
?>

<div class="table-responsive">
    <?php if (!empty($adopters)) : ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><?php esc_html_e('Nome', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Contato', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Endereço', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Adoções', 'amigopet-wp'); ?></th>
                    <th width="150"><?php esc_html_e('Ações', 'amigopet-wp'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($adopters as $item) : ?>
                    <?php include APWP_PLUGIN_DIR . 'admin/partials/adopter/apwp-admin-adopter-row.php'; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <?php esc_html_e('Nenhum adotante encontrado.', 'amigopet-wp'); ?>
        </div>
    <?php endif; ?>
</div>
