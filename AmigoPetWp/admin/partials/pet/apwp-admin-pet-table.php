<?php
/**
 * Template da tabela de pets
 *
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Obtém a lista de pets
$pets = APWP_Pet_Model::get_pets(array(
    'name' => $_GET['filter_name'] ?? '',
    'species' => $_GET['filter_species'] ?? '',
    'status' => $_GET['filter_status'] ?? '',
    'page' => $_GET['paged'] ?? 1,
    'per_page' => 10
));
?>

<div class="table-responsive">
    <?php if (!empty($pets)) : ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="60"><?php esc_html_e('Foto', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Nome', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Espécie', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Idade', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Status', 'amigopet-wp'); ?></th>
                    <th width="150"><?php esc_html_e('Ações', 'amigopet-wp'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pets as $pet) : ?>
                    <?php include APWP_PLUGIN_DIR . 'admin/partials/pet/apwp-admin-pet-row.php'; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <?php esc_html_e('Nenhum pet encontrado.', 'amigopet-wp'); ?>
        </div>
    <?php endif; ?>
</div>
