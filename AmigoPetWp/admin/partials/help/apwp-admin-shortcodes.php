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
            <h2>Shortcodes Disponíveis</h2>
            <p>O AmigoPet WP oferece várias shortcodes para exibir o conteúdo em qualquer página ou post do seu site:</p>
            
            <div class="apwp-shortcodes-grid">
                <div class="apwp-shortcode-item">
                    <h3>[apwp_animals_grid]</h3>
                    <p><strong>Descrição:</strong> Exibe um grid responsivo com todos os pets disponíveis para adoção.</p>
                    <p><strong>Parâmetros:</strong></p>
                    <ul>
                        <li><code>species</code> - Filtra por espécie (ex: "cachorro", "gato")</li>
                        <li><code>breed</code> - Filtra por raça</li>
                        <li><code>age</code> - Filtra por idade</li>
                        <li><code>size</code> - Filtra por porte</li>
                        <li><code>gender</code> - Filtra por gênero</li>
                        <li><code>limit</code> - Número máximo de pets a exibir (padrão: 12)</li>
                        <li><code>order</code> - Ordenação ("ASC" ou "DESC")</li>
                    </ul>
                    <p><strong>Exemplo:</strong></p>
                    <code>[apwp_animals_grid species="cachorro" limit="6" order="DESC"]</code>
                </div>

                <div class="apwp-shortcode-item">
                    <h3>[apwp_adoption_form]</h3>
                    <p><strong>Descrição:</strong> Exibe o formulário de solicitação de adoção.</p>
                    <p><strong>Parâmetros:</strong></p>
                    <ul>
                        <li><code>pet_id</code> - ID do pet específico (opcional)</li>
                        <li><code>title</code> - Título personalizado do formulário</li>
                        <li><code>success_message</code> - Mensagem de sucesso personalizada</li>
                    </ul>
                    <p><strong>Exemplo:</strong></p>
                    <code>[apwp_adoption_form pet_id="123" title="Adote este Pet"]</code>
                </div>

                <div class="apwp-shortcode-item">
                    <h3>[apwp_featured_pets]</h3>
                    <p><strong>Descrição:</strong> Exibe um carrossel de pets em destaque.</p>
                    <p><strong>Parâmetros:</strong></p>
                    <ul>
                        <li><code>count</code> - Número de pets a exibir (padrão: 4)</li>
                        <li><code>autoplay</code> - Ativa rotação automática (true/false)</li>
                        <li><code>interval</code> - Intervalo da rotação em ms (padrão: 5000)</li>
                    </ul>
                    <p><strong>Exemplo:</strong></p>
                    <code>[apwp_featured_pets count="6" autoplay="true"]</code>
                </div>

                <div class="apwp-shortcode-item">
                    <h3>[apwp_pet_counter]</h3>
                    <p><strong>Descrição:</strong> Exibe contadores de pets por categoria.</p>
                    <p><strong>Parâmetros:</strong></p>
                    <ul>
                        <li><code>show</code> - Tipos de contadores ("all", "species", "adopted")</li>
                        <li><code>layout</code> - Estilo de exibição ("inline", "grid")</li>
                    </ul>
                    <p><strong>Exemplo:</strong></p>
                    <code>[apwp_pet_counter show="all" layout="grid"]</code>
                </div>

                <div class="apwp-shortcode-item">
                    <h3>[apwp_pet_search]</h3>
                    <p><strong>Descrição:</strong> Exibe um formulário de busca avançada de pets.</p>
                    <p><strong>Parâmetros:</strong></p>
                    <ul>
                        <li><code>fields</code> - Campos a exibir ("species,breed,age,size,gender")</li>
                        <li><code>button_text</code> - Texto do botão de busca</li>
                    </ul>
                    <p><strong>Exemplo:</strong></p>
                    <code>[apwp_pet_search fields="species,breed" button_text="Buscar Pet"]</code>
                </div>

                <div class="apwp-shortcode-item">
                    <h3>[apwp_single_pet]</h3>
                    <p><strong>Descrição:</strong> Exibe detalhes de um pet específico.</p>
                    <p><strong>Parâmetros:</strong></p>
                    <ul>
                        <li><code>id</code> - ID do pet (obrigatório)</li>
                        <li><code>show_form</code> - Exibe formulário de adoção (true/false)</li>
                        <li><code>gallery</code> - Exibe galeria de fotos (true/false)</li>
                    </ul>
                    <p><strong>Exemplo:</strong></p>
                    <code>[apwp_single_pet id="123" show_form="true" gallery="true"]</code>
                </div>
            </div>

            <div class="apwp-shortcode-tips">
                <h3>Dicas de Uso</h3>
                <ul>
                    <li>Você pode combinar múltiplos parâmetros em uma mesma shortcode</li>
                    <li>Use aspas duplas para valores dos parâmetros</li>
                    <li>Parâmetros são opcionais, exceto quando indicado como obrigatório</li>
                    <li>Shortcodes podem ser usadas em páginas, posts e widgets de texto</li>
                </ul>
            </div>
        </div>
    </div>
</div>
