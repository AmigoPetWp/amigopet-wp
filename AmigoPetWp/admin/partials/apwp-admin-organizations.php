<?php
/**
 * Template para a página de organizações do plugin
 *
 * @link       https://github.com/wendelmax/amigopet-wp
 * @since      1.0.0
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/admin/partials
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="apwp-organizations-wrapper">
        <!-- Botões de Ação -->
        <div class="apwp-action-buttons">
            <a href="#" class="page-title-action">Nova Organização</a>
        </div>

        <!-- Lista de Organizações -->
        <div class="apwp-list-section">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Logo</th>
                        <th>Nome</th>
                        <th>CNPJ</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Animais</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8">Nenhuma organização registrada.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
