<?php
/**
 * Página de Ajuda do AmigoPet WP
 *
 * @link       https://github.com/
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

    <div class="apwp-help-wrapper">
        <!-- Seção de Introdução -->
        <div class="apwp-help-section">
            <h2>Bem-vindo ao AmigoPet WP</h2>
            <p>O AmigoPet WP é um plugin WordPress desenvolvido para ajudar ONGs e protetores de animais a gerenciar seus pets disponíveis para adoção. Este guia irá ajudá-lo a entender todas as funcionalidades do plugin.</p>
        </div>

        <!-- Guia Rápido -->
        <div class="apwp-help-section">
            <h2>Guia Rápido</h2>
            <div class="apwp-help-grid">
                <div class="apwp-help-card">
                    <h3>Dashboard</h3>
                    <p>Visualize estatísticas gerais, pets recentes e atividades do sistema.</p>
                </div>
                <div class="apwp-help-card">
                    <h3>Gerenciar Pets</h3>
                    <p>Adicione, edite e gerencie todos os pets disponíveis para adoção.</p>
                </div>
                <div class="apwp-help-card">
                    <h3>Espécies e Raças</h3>
                    <p>Configure as espécies e raças disponíveis para cadastro.</p>
                </div>
                <div class="apwp-help-card">
                    <h3>Adotantes</h3>
                    <p>Gerencie os cadastros de pessoas interessadas em adotar.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="apwp-help-section">
            <h2>Perguntas Frequentes</h2>
            <div class="apwp-help-faq">
                <div class="apwp-faq-item">
                    <h3>Como adicionar um novo pet?</h3>
                    <p>1. Acesse o menu "Adicionar Pet"<br>
                       2. Preencha todos os dados do animal<br>
                       3. Adicione fotos do pet<br>
                       4. Clique em "Publicar"</p>
                </div>
                <div class="apwp-faq-item">
                    <h3>Como gerenciar adoções?</h3>
                    <p>1. Acesse o menu "Adoções"<br>
                       2. Visualize todas as solicitações pendentes<br>
                       3. Aprove ou recuse cada solicitação<br>
                       4. Acompanhe o status das adoções</p>
                </div>
                <div class="apwp-faq-item">
                    <h3>Como personalizar o formulário de adoção?</h3>
                    <p>1. Acesse as configurações do plugin<br>
                       2. Vá até a aba "Formulário de Adoção"<br>
                       3. Adicione ou remova campos conforme necessário<br>
                       4. Salve as alterações</p>
                </div>
                <div class="apwp-faq-item">
                    <h3>Como gerar relatórios?</h3>
                    <p>1. Acesse o menu "Relatórios"<br>
                       2. Selecione o tipo de relatório desejado<br>
                       3. Configure os filtros necessários<br>
                       4. Clique em "Gerar Relatório"</p>
                </div>
            </div>
        </div>

        <!-- Suporte -->
        <div class="apwp-help-section">
            <h2>Suporte</h2>
            <p>Se você precisar de ajuda adicional:</p>
            <ul>
                <li>GitHub: <a href="https://github.com/jacksonsalopek/amigopet-wp" target="_blank">AmigoPet WP no GitHub</a></li>
                <li>Para ver a documentação completa das shortcodes disponíveis, acesse a seção "Shortcodes" no menu.</li>
            </ul>
        </div>
    </div>
</div>
