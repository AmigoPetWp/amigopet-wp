<?php
/**
 * Template da tabela de adoções
 *
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Obtém a lista de adoções
$adoptions = APWP_Adoption_Model::get_adoptions(array(
    'pet' => $_GET['filter_pet'] ?? '',
    'adopter' => $_GET['filter_adopter'] ?? '',
    'status' => $_GET['filter_status'] ?? '',
    'date' => $_GET['filter_date'] ?? '',
    'page' => $_GET['paged'] ?? 1,
    'per_page' => 10
));
?>

<div class="table-responsive">
    <?php if (!empty($adoptions)) : ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><?php esc_html_e('ID', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Pet', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Adotante', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Data', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Status', 'amigopet-wp'); ?></th>
                    <th width="150"><?php esc_html_e('Ações', 'amigopet-wp'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($adoptions as $item) : ?>
                    <?php include APWP_PLUGIN_DIR . 'admin/partials/adoption/apwp-admin-adoption-row.php'; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <?php esc_html_e('Nenhuma adoção encontrada.', 'amigopet-wp'); ?>
        </div>
    <?php endif; ?>
</div>
