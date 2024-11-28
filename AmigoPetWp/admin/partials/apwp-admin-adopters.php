<?php
/**
 * Template para a página de adotantes do plugin
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
    
    <div class="apwp-adopters-wrapper">
        <!-- Botões de Ação -->
        <div class="apwp-action-buttons">
            <a href="#" class="page-title-action">Novo Adotante</a>
        </div>

        <!-- Lista de Adotantes -->
        <div class="apwp-list-section">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Adoções</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7">Nenhum adotante registrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
