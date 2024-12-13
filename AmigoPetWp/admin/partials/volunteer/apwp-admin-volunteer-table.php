<?php
/**
 * Template da tabela de voluntários
 *
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Obtém a lista de voluntários
$volunteers = APWP_Volunteer_Model::get_volunteers(array(
    'name' => $_GET['filter_name'] ?? '',
    'skills' => $_GET['filter_skills'] ?? '',
    'status' => $_GET['filter_status'] ?? '',
    'page' => $_GET['paged'] ?? 1,
    'per_page' => 10
));
?>

<div class="table-responsive">
    <?php if (!empty($volunteers)) : ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><?php esc_html_e('Nome', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Contato', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Habilidades', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Disponibilidade', 'amigopet-wp'); ?></th>
                    <th><?php esc_html_e('Status', 'amigopet-wp'); ?></th>
                    <th width="150"><?php esc_html_e('Ações', 'amigopet-wp'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($volunteers as $item) : ?>
                    <?php include APWP_PLUGIN_DIR . 'admin/partials/volunteer/apwp-admin-volunteer-row.php'; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <?php esc_html_e('Nenhum voluntário encontrado.', 'amigopet-wp'); ?>
        </div>
    <?php endif; ?>
</div>
