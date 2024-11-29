<?php
/**
 * Página de Shortcodes do AmigoPet WP
 *
 * @link       https://github.com/AmigoPetWp/amigopet-wp
 * @since      1.0.0
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/admin/partials
 */

// Se acessado diretamente, aborta
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="apwp-shortcodes-wrapper">
        <div class="apwp-help-section">
            <h2><?php _e('Shortcodes Disponíveis', 'amigopet-wp'); ?></h2>
            <p><?php _e('Use estes shortcodes para exibir o conteúdo do AmigoPet WP em qualquer página ou post:', 'amigopet-wp'); ?></p>
            
            <div class="apwp-shortcodes-grid">
                <!-- Grid de Pets -->
                <div class="apwp-shortcode-item">
                    <h3>[apwp_pets_grid]</h3>
                    <p><strong><?php _e('Descrição:', 'amigopet-wp'); ?></strong>
                    <?php _e('Exibe uma grade responsiva com todos os pets disponíveis para adoção.', 'amigopet-wp'); ?></p>
                    
                    <p><strong><?php _e('Parâmetros:', 'amigopet-wp'); ?></strong></p>
                    <ul>
                        <li><code>species</code> - <?php _e('Filtra por espécie (ex: "cachorro", "gato")', 'amigopet-wp'); ?></li>
                        <li><code>breed</code> - <?php _e('Filtra por raça', 'amigopet-wp'); ?></li>
                        <li><code>age</code> - <?php _e('Filtra por idade', 'amigopet-wp'); ?></li>
                        <li><code>size</code> - <?php _e('Filtra por porte', 'amigopet-wp'); ?></li>
                        <li><code>gender</code> - <?php _e('Filtra por gênero', 'amigopet-wp'); ?></li>
                        <li><code>limit</code> - <?php _e('Número máximo de pets a exibir (padrão: 12)', 'amigopet-wp'); ?></li>
                        <li><code>order</code> - <?php _e('Ordenação ("ASC" ou "DESC")', 'amigopet-wp'); ?></li>
                    </ul>
                    
                    <div class="shortcode-example">
                        <code>[apwp_pets_grid species="cachorro" limit="6" order="DESC"]</code>
                    </div>
                </div>

                <!-- Formulário de Adoção -->
                <div class="apwp-shortcode-item">
                    <h3>[apwp_adoption_form]</h3>
                    <p><strong><?php _e('Descrição:', 'amigopet-wp'); ?></strong>
                    <?php _e('Exibe o formulário de solicitação de adoção.', 'amigopet-wp'); ?></p>
                    
                    <p><strong><?php _e('Parâmetros:', 'amigopet-wp'); ?></strong></p>
                    <ul>
                        <li><code>pet_id</code> - <?php _e('ID do pet específico (opcional)', 'amigopet-wp'); ?></li>
                        <li><code>title</code> - <?php _e('Título personalizado do formulário', 'amigopet-wp'); ?></li>
                        <li><code>success_message</code> - <?php _e('Mensagem de sucesso personalizada', 'amigopet-wp'); ?></li>
                    </ul>
                    
                    <div class="shortcode-example">
                        <code>[apwp_adoption_form pet_id="123" title="Adote este Pet"]</code>
                    </div>
                </div>

                <!-- Pets em Destaque -->
                <div class="apwp-shortcode-item">
                    <h3>[apwp_featured_pets]</h3>
                    <p><strong><?php _e('Descrição:', 'amigopet-wp'); ?></strong>
                    <?php _e('Exibe um carrossel de pets em destaque.', 'amigopet-wp'); ?></p>
                    
                    <p><strong><?php _e('Parâmetros:', 'amigopet-wp'); ?></strong></p>
                    <ul>
                        <li><code>count</code> - <?php _e('Número de pets a exibir (padrão: 4)', 'amigopet-wp'); ?></li>
                        <li><code>autoplay</code> - <?php _e('Ativa rotação automática (true/false)', 'amigopet-wp'); ?></li>
                        <li><code>interval</code> - <?php _e('Intervalo da rotação em ms (padrão: 5000)', 'amigopet-wp'); ?></li>
                    </ul>
                    
                    <div class="shortcode-example">
                        <code>[apwp_featured_pets count="6" autoplay="true"]</code>
                    </div>
                </div>

                <!-- Contador de Pets -->
                <div class="apwp-shortcode-item">
                    <h3>[apwp_pet_counter]</h3>
                    <p><strong><?php _e('Descrição:', 'amigopet-wp'); ?></strong>
                    <?php _e('Exibe contadores de pets por categoria.', 'amigopet-wp'); ?></p>
                    
                    <p><strong><?php _e('Parâmetros:', 'amigopet-wp'); ?></strong></p>
                    <ul>
                        <li><code>show</code> - <?php _e('Tipos de contadores ("all", "species", "adopted")', 'amigopet-wp'); ?></li>
                        <li><code>layout</code> - <?php _e('Estilo de exibição ("inline", "grid")', 'amigopet-wp'); ?></li>
                    </ul>
                    
                    <div class="shortcode-example">
                        <code>[apwp_pet_counter show="all" layout="grid"]</code>
                    </div>
                </div>

                <!-- Busca de Pets -->
                <div class="apwp-shortcode-item">
                    <h3>[apwp_pet_search]</h3>
                    <p><strong><?php _e('Descrição:', 'amigopet-wp'); ?></strong>
                    <?php _e('Exibe um formulário de busca avançada de pets.', 'amigopet-wp'); ?></p>
                    
                    <p><strong><?php _e('Parâmetros:', 'amigopet-wp'); ?></strong></p>
                    <ul>
                        <li><code>fields</code> - <?php _e('Campos a exibir ("species,breed,age,size,gender")', 'amigopet-wp'); ?></li>
                        <li><code>button_text</code> - <?php _e('Texto do botão de busca', 'amigopet-wp'); ?></li>
                    </ul>
                    
                    <div class="shortcode-example">
                        <code>[apwp_pet_search fields="species,breed" button_text="Buscar Pet"]</code>
                    </div>
                </div>

                <!-- Pet Individual -->
                <div class="apwp-shortcode-item">
                    <h3>[apwp_single_pet]</h3>
                    <p><strong><?php _e('Descrição:', 'amigopet-wp'); ?></strong>
                    <?php _e('Exibe detalhes de um pet específico.', 'amigopet-wp'); ?></p>
                    
                    <p><strong><?php _e('Parâmetros:', 'amigopet-wp'); ?></strong></p>
                    <ul>
                        <li><code>id</code> - <?php _e('ID do pet (obrigatório)', 'amigopet-wp'); ?></li>
                        <li><code>show_form</code> - <?php _e('Exibir formulário de adoção (true/false)', 'amigopet-wp'); ?></li>
                        <li><code>gallery</code> - <?php _e('Exibir galeria de fotos (true/false)', 'amigopet-wp'); ?></li>
                    </ul>
                    
                    <div class="shortcode-example">
                        <code>[apwp_single_pet id="123" show_form="true" gallery="true"]</code>
                    </div>
                </div>
            </div>

            <div class="apwp-shortcode-tips">
                <h3><?php _e('Dicas de Uso', 'amigopet-wp'); ?></h3>
                <ul>
                    <li><?php _e('Você pode combinar múltiplos parâmetros em uma mesma shortcode', 'amigopet-wp'); ?></li>
                    <li><?php _e('Use aspas duplas para valores dos parâmetros', 'amigopet-wp'); ?></li>
                    <li><?php _e('Parâmetros são opcionais, exceto quando indicado como obrigatório', 'amigopet-wp'); ?></li>
                    <li><?php _e('Shortcodes podem ser usadas em páginas, posts e widgets de texto', 'amigopet-wp'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>
