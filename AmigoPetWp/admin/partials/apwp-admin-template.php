<?php
/**
 * Template base para páginas administrativas
 *
 * @link       https://github.com/wendel-passos/amigopet
 * @since      1.0.0
 *
 * @package    AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

/**
 * Renderiza o template base do admin
 *
 * @param string $title Título da página
 * @param string $content Conteúdo da página
 * @param array $actions Ações do cabeçalho (botões)
 * @param array $tabs Abas da página
 */
function apwp_render_admin_template($title, $content, $actions = array(), $tabs = array()) {
    ?>
    <div class="wrap">
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1><?php echo esc_html($title); ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp')); ?>">AmigoPet</a></li>
                                <li class="breadcrumb-item active"><?php echo esc_html($title); ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <?php if (isset($notification_message) && isset($notification_type)) : ?>
                        <div class="alert alert-<?php echo esc_attr($notification_type); ?> alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo esc_html($notification_message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Filters Card -->
                    <?php if (isset($show_filters) && $show_filters) : ?>
                    <div class="card card-outline card-primary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title"><?php esc_html_e('Filtros', 'amigopet-wp'); ?></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (isset($filters_template) && file_exists($filters_template)) : ?>
                                <?php include $filters_template; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Main Card -->
                    <div class="card">
                        <?php if (isset($card_title)) : ?>
                        <div class="card-header">
                            <h3 class="card-title"><?php echo esc_html($card_title); ?></h3>
                            <?php if (isset($card_tools_template) && file_exists($card_tools_template)) : ?>
                            <div class="card-tools">
                                <?php include $card_tools_template; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="card-body">
                            <?php
                            if (isset($content_template) && file_exists($content_template)) {
                                include $content_template;
                            }
                            ?>
                        </div>

                        <?php if (isset($card_footer_template) && file_exists($card_footer_template)) : ?>
                        <div class="card-footer">
                            <?php include $card_footer_template; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php
}

/**
 * Renderiza uma tabela do admin
 *
 * @param array $headers Cabeçalhos da tabela
 * @param array $rows Linhas da tabela
 * @param array $options Opções da tabela
 */
function apwp_render_admin_table($headers, $rows, $options = array()) {
    $default_options = array(
        'class' => '',
        'id' => '',
        'empty_message' => __('Nenhum item encontrado.', 'amigopet-wp')
    );
    
    $options = wp_parse_args($options, $default_options);
    ?>
    <table class="apwp-admin-table <?php echo esc_attr($options['class']); ?>"
           <?php if (!empty($options['id'])): ?>id="<?php echo esc_attr($options['id']); ?>"<?php endif; ?>>
        
        <thead>
            <tr>
                <?php foreach ($headers as $header): ?>
                    <th <?php if (!empty($header['class'])): ?>class="<?php echo esc_attr($header['class']); ?>"<?php endif; ?>
                        <?php if (!empty($header['style'])): ?>style="<?php echo esc_attr($header['style']); ?>"<?php endif; ?>>
                        <?php echo esc_html($header['text']); ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        
        <tbody>
            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="<?php echo count($headers); ?>" class="apwp-admin-table-empty">
                        <?php echo esc_html($options['empty_message']); ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                    <tr <?php if (!empty($row['class'])): ?>class="<?php echo esc_attr($row['class']); ?>"<?php endif; ?>>
                        <?php foreach ($row['columns'] as $column): ?>
                            <td <?php if (!empty($column['class'])): ?>class="<?php echo esc_attr($column['class']); ?>"<?php endif; ?>
                                <?php if (!empty($column['style'])): ?>style="<?php echo esc_attr($column['style']); ?>"<?php endif; ?>>
                                <?php echo $column['content']; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        
        <?php if (!empty($options['footer'])): ?>
            <tfoot>
                <tr>
                    <?php foreach ($options['footer'] as $footer): ?>
                        <td <?php if (!empty($footer['class'])): ?>class="<?php echo esc_attr($footer['class']); ?>"<?php endif; ?>
                            <?php if (!empty($footer['colspan'])): ?>colspan="<?php echo esc_attr($footer['colspan']); ?>"<?php endif; ?>>
                            <?php echo $footer['content']; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </tfoot>
        <?php endif; ?>
    </table>
    <?php
}

/**
 * Renderiza um card do admin
 *
 * @param string $title Título do card
 * @param string $content Conteúdo do card
 * @param array $options Opções do card
 */
function apwp_render_admin_card($title, $content, $options = array()) {
    $default_options = array(
        'class' => '',
        'id' => '',
        'icon' => '',
        'footer' => ''
    );
    
    $options = wp_parse_args($options, $default_options);
    ?>
    <div class="apwp-admin-card <?php echo esc_attr($options['class']); ?>"
         <?php if (!empty($options['id'])): ?>id="<?php echo esc_attr($options['id']); ?>"<?php endif; ?>>
        
        <div class="apwp-admin-card-header">
            <h2 class="apwp-admin-card-title">
                <?php if (!empty($options['icon'])): ?>
                    <span class="dashicons <?php echo esc_attr($options['icon']); ?>"></span>
                <?php endif; ?>
                <?php echo esc_html($title); ?>
            </h2>
        </div>
        
        <div class="apwp-admin-card-body">
            <?php echo $content; ?>
        </div>
        
        <?php if (!empty($options['footer'])): ?>
            <div class="apwp-admin-card-footer">
                <?php echo $options['footer']; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Renderiza um widget do admin
 *
 * @param string $title Título do widget
 * @param string $content Conteúdo do widget
 * @param array $options Opções do widget
 */
function apwp_render_admin_widget($title, $content, $options = array()) {
    $default_options = array(
        'class' => '',
        'id' => '',
        'icon' => ''
    );
    
    $options = wp_parse_args($options, $default_options);
    ?>
    <div class="apwp-admin-widget <?php echo esc_attr($options['class']); ?>"
         <?php if (!empty($options['id'])): ?>id="<?php echo esc_attr($options['id']); ?>"<?php endif; ?>>
        
        <div class="apwp-admin-widget-header">
            <h3 class="apwp-admin-widget-title">
                <?php if (!empty($options['icon'])): ?>
                    <span class="dashicons <?php echo esc_attr($options['icon']); ?>"></span>
                <?php endif; ?>
                <?php echo esc_html($title); ?>
            </h3>
        </div>
        
        <div class="apwp-admin-widget-body">
            <?php echo $content; ?>
        </div>
    </div>
    <?php
}
