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
        <h2 id="breed-form-title"><?php echo esc_html__('Add Breed', 'amigopet-wp'); ?></h2>
        <form id="breed-form-data">
            <input type="hidden" id="breed-id" value="">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="breed-species"><?php echo esc_html__('Species', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <select id="breed-species" name="species_id" class="regular-text" required>
                            <option value=""><?php echo esc_html__('Select Species', 'amigopet-wp'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="breed-name"><?php echo esc_html__('Name', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="breed-name" name="name" class="regular-text" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="breed-description"><?php echo esc_html__('Description', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <textarea id="breed-description" name="description" class="large-text" rows="3"></textarea>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary"><?php echo esc_html__('Save Breed', 'amigopet-wp'); ?></button>
                <button type="button" class="button cancel-form"><?php echo esc_html__('Cancel', 'amigopet-wp'); ?></button>
            </p>
        </form>
    </div>

    <!-- Lista de raças -->
    <div class="tablenav top">
        <div class="alignleft actions">
            <button type="button" id="add-breed" class="button">
                <?php echo esc_html__('Add New Breed', 'amigopet-wp'); ?>
            </button>
        </div>
        <div class="alignleft actions">
            <select id="filter-species">
                <option value=""><?php echo esc_html__('All Species', 'amigopet-wp'); ?></option>
            </select>
            <button type="button" id="filter-submit" class="button"><?php echo esc_html__('Filter', 'amigopet-wp'); ?></button>
        </div>
        <div class="alignleft actions">
            <select id="bulk-action-selector-top">
                <option value="-1"><?php echo esc_html__('Bulk Actions', 'amigopet-wp'); ?></option>
                <option value="delete"><?php echo esc_html__('Delete', 'amigopet-wp'); ?></option>
            </select>
            <button type="button" id="doaction" class="button"><?php echo esc_html__('Apply', 'amigopet-wp'); ?></button>
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
                    <?php echo esc_html__('Name', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-species">
                    <?php echo esc_html__('Species', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-description">
                    <?php echo esc_html__('Description', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-pets">
                    <?php echo esc_html__('Pets', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-date">
                    <?php echo esc_html__('Created', 'amigopet-wp'); ?>
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
                    <?php echo esc_html__('Name', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-species">
                    <?php echo esc_html__('Species', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-description">
                    <?php echo esc_html__('Description', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-pets">
                    <?php echo esc_html__('Pets', 'amigopet-wp'); ?>
                </th>
                <th scope="col" class="manage-column column-date">
                    <?php echo esc_html__('Created', 'amigopet-wp'); ?>
                </th>
            </tr>
        </tfoot>
    </table>

    <div class="tablenav bottom">
        <div class="alignleft actions">
            <select id="bulk-action-selector-bottom">
                <option value="-1"><?php echo esc_html__('Bulk Actions', 'amigopet-wp'); ?></option>
                <option value="delete"><?php echo esc_html__('Delete', 'amigopet-wp'); ?></option>
            </select>
            <button type="button" id="doaction2" class="button"><?php echo esc_html__('Apply', 'amigopet-wp'); ?></button>
        </div>
        <div class="tablenav-pages">
            <span class="displaying-num"></span>
            <span class="pagination-links"></span>
        </div>
    </div>
</div>
