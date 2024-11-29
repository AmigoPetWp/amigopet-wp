<?php
/**
 * Template para gerenciamento de raças
 *
 * @link       https://github.com/AmigoPetWp/amigopet-wp
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
    <!-- Formulário de adição/edição -->
    <div id="breed-form" style="display: none;">
        <h2 id="breed-form-title"><?php echo esc_html__('Adicionar Raça', 'amigopet-wp'); ?></h2>
        <form id="breed-form-data">
            <input type="hidden" id="breed-id" value="">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="breed-species"><?php echo esc_html__('Espécie', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <select id="breed-species" name="species_id" class="regular-text" required>
                            <option value=""><?php echo esc_html__('Selecione a Espécie', 'amigopet-wp'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="breed-name"><?php echo esc_html__('Nome', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="breed-name" name="name" class="regular-text" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="breed-description"><?php echo esc_html__('Descrição', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <textarea id="breed-description" name="description" class="large-text" rows="3"></textarea>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary"><?php echo esc_html__('Salvar Raça', 'amigopet-wp'); ?></button>
                <button type="button" class="button cancel-form"><?php echo esc_html__('Cancelar', 'amigopet-wp'); ?></button>
            </p>
        </form>
    </div>

    <!-- Lista de raças -->
    <div class="tablenav top">
        <div class="alignleft actions">
            <button type="button" id="add-breed" class="button">
                <?php echo esc_html__('Adicionar Nova Raça', 'amigopet-wp'); ?>
            </button>
        </div>
        <div class="alignleft actions">
            <select id="filter-species">
                <option value=""><?php echo esc_html__('Todas as Espécies', 'amigopet-wp'); ?></option>
            </select>
            <button type="button" id="filter-submit" class="button"><?php echo esc_html__('Filtrar', 'amigopet-wp'); ?></button>
        </div>
        <div class="alignleft actions">
            <select id="bulk-action-selector-top">
                <option value="-1"><?php echo esc_html__('Ações em Massa', 'amigopet-wp'); ?></option>
                <option value="delete"><?php echo esc_html__('Excluir', 'amigopet-wp'); ?></option>
            </select>
            <button type="button" id="doaction" class="button"><?php echo esc_html__('Aplicar', 'amigopet-wp'); ?></button>
        </div>
        <div class="tablenav-pages">
            <span class="displaying-num"></span>
            <span class="pagination-links"></span>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <td class="manage-column column-cb check-column">
                    <input type="checkbox" id="cb-select-all-1">
                </td>
                <th scope="col" class="manage-column column-title column-primary sortable asc">
                    <?php echo esc_html__('Nome', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-species">
                    <?php echo esc_html__('Espécie', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-description">
                    <?php echo esc_html__('Descrição', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-pets">
                    <?php echo esc_html__('Pets', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-date">
                    <?php echo esc_html__('Criado em', 'amigopet-wp'); ?>
                </th>
            </tr>
        </thead>

        <tbody id="the-list"></tbody>

        <tfoot>
            <tr>
                <td class="manage-column column-cb check-column">
                    <input type="checkbox" id="cb-select-all-2">
                </td>
                <th scope="col" class="manage-column column-title column-primary sortable asc">
                    <?php echo esc_html__('Nome', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-species">
                    <?php echo esc_html__('Espécie', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-description">
                    <?php echo esc_html__('Descrição', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-pets">
                    <?php echo esc_html__('Pets', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-date">
                    <?php echo esc_html__('Criado em', 'amigopet-wp'); ?>
                </th>
            </tr>
        </tfoot>
    </table>

    <div class="tablenav bottom">
        <div class="alignleft actions">
            <select id="bulk-action-selector-bottom">
                <option value="-1"><?php echo esc_html__('Ações em Massa', 'amigopet-wp'); ?></option>
                <option value="delete"><?php echo esc_html__('Excluir', 'amigopet-wp'); ?></option>
            </select>
            <button type="button" id="doaction2" class="button"><?php echo esc_html__('Aplicar', 'amigopet-wp'); ?></button>
        </div>
        <div class="tablenav-pages">
            <span class="displaying-num"></span>
            <span class="pagination-links"></span>
        </div>
    </div>
</div>
