<?php
/**
 * Template para página de ajuda no admin
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php _e('Ajuda do AmigoPet', 'amigopet-wp'); ?></h1>

    <?php if (!empty($data['organization'])): ?>
    <div class="notice notice-info">
        <p>
            <strong><?php _e('Organização:', 'amigopet-wp'); ?></strong> <?php echo esc_html($data['organization']['name'] ?? ''); ?><br>
            <strong><?php _e('Email:', 'amigopet-wp'); ?></strong> <?php echo esc_html($data['organization']['email'] ?? ''); ?><br>
            <strong><?php _e('Telefone:', 'amigopet-wp'); ?></strong> <?php echo esc_html($data['organization']['phone'] ?? ''); ?><br>
            <strong><?php _e('Versão do Plugin:', 'amigopet-wp'); ?></strong> <?php echo esc_html($data['version'] ?? '1.0.0'); ?>
        </p>
    </div>
    <?php endif; ?>

    <?php if (!empty($data['stats'])): ?>
    <div class="notice">
        <p>
            <strong><?php _e('Estatísticas Atuais:', 'amigopet-wp'); ?></strong><br>
            <?php _e('Pets:', 'amigopet-wp'); ?> <?php echo esc_html($data['stats']['pets'] ?? 0); ?> |
            <?php _e('Adoções:', 'amigopet-wp'); ?> <?php echo esc_html($data['stats']['adoptions'] ?? 0); ?> |
            <?php _e('Eventos:', 'amigopet-wp'); ?> <?php echo esc_html($data['stats']['events'] ?? 0); ?> |
            <?php _e('Doações:', 'amigopet-wp'); ?> <?php echo esc_html($data['stats']['donations'] ?? 0); ?>
        </p>
    </div>
    <?php endif; ?>
    
    <div class="apwp-help-wrapper">
        <!-- Menu de navegação -->
        <div class="apwp-help-nav">
            <ul>
                <li>
                    <a href="#getting-started" class="active">
                        <?php _e('Primeiros Passos', 'amigopet-wp'); ?>
                    </a>
                </li>
                <li>
                    <a href="#shortcodes">
                        <?php _e('Shortcodes', 'amigopet-wp'); ?>
                    </a>
                </li>
                <li>
                    <a href="#pets">
                        <?php _e('Gerenciando Pets', 'amigopet-wp'); ?>
                    </a>
                </li>
                <li>
                    <a href="#adoptions">
                        <?php _e('Processo de Adoção', 'amigopet-wp'); ?>
                    </a>
                </li>
                <li>
                    <a href="#volunteers">
                        <?php _e('Gerenciando Voluntários', 'amigopet-wp'); ?>
                    </a>
                </li>
                <li>
                    <a href="#donations">
                        <?php _e('Sistema de Doações', 'amigopet-wp'); ?>
                    </a>
                </li>
                <li>
                    <a href="#events">
                        <?php _e('Eventos', 'amigopet-wp'); ?>
                    </a>
                </li>
                <li>
                    <a href="#reports">
                        <?php _e('Relatórios', 'amigopet-wp'); ?>
                    </a>
                </li>
                <li>
                    <a href="#settings">
                        <?php _e('Configurações', 'amigopet-wp'); ?>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Conteúdo -->
        <div class="apwp-help-content">
            <!-- Primeiros Passos -->
            <section id="getting-started" class="apwp-help-section">
                <h2><?php _e('Primeiros Passos', 'amigopet-wp'); ?></h2>
                
                <h3><?php _e('Configuração Inicial', 'amigopet-wp'); ?></h3>
                <ol>
                    <li><?php _e('Acesse o menu "Configurações" e preencha os dados da sua organização', 'amigopet-wp'); ?></li>
                    <li><?php _e('Configure as informações de contato que aparecerão nos formulários', 'amigopet-wp'); ?></li>
                    <li><?php _e('Personalize os modelos de email para comunicação com adotantes e doadores', 'amigopet-wp'); ?></li>
                </ol>

                <h3><?php _e('Cadastro de Pets', 'amigopet-wp'); ?></h3>
                <ol>
                    <li><?php _e('Acesse o menu "Pets" para adicionar novos animais', 'amigopet-wp'); ?></li>
                    <li><?php _e('Preencha todas as informações do pet: nome, espécie, idade, etc', 'amigopet-wp'); ?></li>
                    <li><?php _e('Adicione fotos de qualidade para aumentar as chances de adoção', 'amigopet-wp'); ?></li>
                    <li><?php _e('Mantenha o status do pet atualizado (disponível, adotado, etc)', 'amigopet-wp'); ?></li>
                </ol>

                <h3><?php _e('Gerenciamento de Adoções', 'amigopet-wp'); ?></h3>
                <ol>
                    <li><?php _e('Acompanhe as solicitações de adoção no menu "Adoções"', 'amigopet-wp'); ?></li>
                    <li><?php _e('Avalie cada solicitação cuidadosamente', 'amigopet-wp'); ?></li>
                    <li><?php _e('Use os termos de adoção para formalizar o processo', 'amigopet-wp'); ?></li>
                    <li><?php _e('Mantenha contato com os adotantes após a adoção', 'amigopet-wp'); ?></li>
                </ol>
                
                <h3><?php _e('Instalação', 'amigopet-wp'); ?></h3>
                <ol>
                    <li><?php _e('Instale e ative o plugin através do WordPress', 'amigopet-wp'); ?></li>
                    <li><?php _e('Acesse o menu "AmigoPet" no painel administrativo', 'amigopet-wp'); ?></li>
                    <li><?php _e('Configure as informações básicas da sua organização em "Configurações"', 'amigopet-wp'); ?></li>
                    <li><?php _e('Comece cadastrando seus pets disponíveis para adoção', 'amigopet-wp'); ?></li>
                </ol>
                
                <h3><?php _e('Requisitos', 'amigopet-wp'); ?></h3>
                <ul>
                    <li><?php _e('WordPress 5.0 ou superior', 'amigopet-wp'); ?></li>
                    <li><?php _e('PHP 7.4 ou superior', 'amigopet-wp'); ?></li>
                    <li><?php _e('MySQL 5.6 ou superior', 'amigopet-wp'); ?></li>
                </ul>
            </section>
            
            <!-- Shortcodes -->
            <section id="shortcodes" class="apwp-help-section">
                <h2><?php _e('Shortcodes', 'amigopet-wp'); ?></h2>
                
                <div class="notice notice-info">
                    <p><?php _e('Use estes shortcodes para exibir conteúdo do AmigoPet em qualquer página ou post do seu site.', 'amigopet-wp'); ?></p>
                </div>
                
                <div class="apwp-shortcode-list">
                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_pets]</h3>
                        <p><?php _e('Exibe a grade de pets disponíveis para adoção.', 'amigopet-wp'); ?></p>
                        <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                        <ul>
                            <li><code>limit</code> - <?php _e('Número máximo de pets para exibir (ex: limit="12")', 'amigopet-wp'); ?></li>
                            <li><code>species</code> - <?php _e('Filtrar por espécie (ex: species="dog")', 'amigopet-wp'); ?></li>
                            <li><code>columns</code> - <?php _e('Número de colunas na grade (ex: columns="3")', 'amigopet-wp'); ?></li>
                        </ul>
                        <p><strong><?php _e('Exemplo:', 'amigopet-wp'); ?></strong> <code>[amigopet_pets limit="6" species="cat" columns="2"]</code></p>
                    </div>

                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_adoption_form]</h3>
                        <p><?php _e('Exibe o formulário de solicitação de adoção.', 'amigopet-wp'); ?></p>
                        <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                        <ul>
                            <li><code>pet_id</code> - <?php _e('ID do pet específico (opcional)', 'amigopet-wp'); ?></li>
                            <li><code>title</code> - <?php _e('Título personalizado do formulário', 'amigopet-wp'); ?></li>
                        </ul>
                        <p><strong><?php _e('Exemplo:', 'amigopet-wp'); ?></strong> <code>[amigopet_adoption_form pet_id="123" title="Adote este Pet"]</code></p>
                    </div>

                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_donation_form]</h3>
                        <p><?php _e('Exibe o formulário de doação.', 'amigopet-wp'); ?></p>
                        <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                        <ul>
                            <li><code>type</code> - <?php _e('Tipo de doação (money, supplies, volunteer)', 'amigopet-wp'); ?></li>
                            <li><code>title</code> - <?php _e('Título personalizado do formulário', 'amigopet-wp'); ?></li>
                        </ul>
                        <p><strong><?php _e('Exemplo:', 'amigopet-wp'); ?></strong> <code>[amigopet_donation_form type="money" title="Faça uma Doação"]</code></p>
                    </div>

                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_events]</h3>
                        <p><?php _e('Exibe a lista de eventos próximos.', 'amigopet-wp'); ?></p>
                        <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                        <ul>
                            <li><code>limit</code> - <?php _e('Número de eventos para exibir', 'amigopet-wp'); ?></li>
                            <li><code>view</code> - <?php _e('Tipo de visualização (list, calendar)', 'amigopet-wp'); ?></li>
                        </ul>
                        <p><strong><?php _e('Exemplo:', 'amigopet-wp'); ?></strong> <code>[amigopet_events limit="5" view="calendar"]</code></p>
                    </div>
                        <ul>
                            <li><code>limit</code>: <?php _e('Número de pets por página (padrão: 12)', 'amigopet-wp'); ?></li>
                            <li><code>species</code>: <?php _e('Filtrar por espécie (ex: "cachorro", "gato")', 'amigopet-wp'); ?></li>
                            <li><code>size</code>: <?php _e('Filtrar por tamanho (pequeno, medio, grande)', 'amigopet-wp'); ?></li>
                            <li><code>age</code>: <?php _e('Filtrar por idade (filhote, adulto, idoso)', 'amigopet-wp'); ?></li>
                        </ul>
                        <p><strong><?php _e('Exemplo:', 'amigopet-wp'); ?></strong></p>
                        <code>[amigopet_pets limit="6" species="cachorro" size="pequeno"]</code>
                    </div>
                    
                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_adoption_form]</h3>
                        <p><?php _e('Exibe o formulário de adoção para um pet específico.', 'amigopet-wp'); ?></p>
                        <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                        <ul>
                            <li><code>pet_id</code>: <?php _e('ID do pet (obrigatório)', 'amigopet-wp'); ?></li>
                        </ul>
                        <p><strong><?php _e('Exemplo:', 'amigopet-wp'); ?></strong></p>
                        <code>[amigopet_adoption_form pet_id="123"]</code>
                    </div>
                    
                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_events]</h3>
                        <p><?php _e('Exibe a lista de eventos programados.', 'amigopet-wp'); ?></p>
                        <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                        <ul>
                            <li><code>limit</code>: <?php _e('Número de eventos por página (padrão: 10)', 'amigopet-wp'); ?></li>
                            <li><code>type</code>: <?php _e('Filtrar por tipo de evento', 'amigopet-wp'); ?></li>
                            <li><code>view</code>: <?php _e('Tipo de visualização (grid, list, calendar)', 'amigopet-wp'); ?></li>
                        </ul>
                        <p><strong><?php _e('Exemplo:', 'amigopet-wp'); ?></strong></p>
                        <code>[amigopet_events limit="4" type="feira" view="grid"]</code>
                    </div>
                    
                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_volunteer_form]</h3>
                        <p><?php _e('Exibe o formulário de cadastro de voluntários.', 'amigopet-wp'); ?></p>
                        <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                        <ul>
                            <li><code>redirect</code>: <?php _e('URL para redirecionamento após envio', 'amigopet-wp'); ?></li>
                        </ul>
                        <p><strong><?php _e('Exemplo:', 'amigopet-wp'); ?></strong></p>
                        <code>[amigopet_volunteer_form redirect="/obrigado"]</code>
                    </div>
                    
                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_donation_form]</h3>
                        <p><?php _e('Exibe o formulário de doação.', 'amigopet-wp'); ?></p>
                        <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                        <ul>
                            <li><code>type</code>: <?php _e('Tipo de doação (money, food, supplies)', 'amigopet-wp'); ?></li>
                            <li><code>amount</code>: <?php _e('Valor predefinido para doação em dinheiro', 'amigopet-wp'); ?></li>
                            <li><code>recurring</code>: <?php _e('Permitir doação recorrente (true/false)', 'amigopet-wp'); ?></li>
                        </ul>
                        <p><strong><?php _e('Exemplo:', 'amigopet-wp'); ?></strong></p>
                        <code>[amigopet_donation_form type="money" amount="50" recurring="true"]</code>
                    </div>
                    
                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_stats]</h3>
                        <p><?php _e('Exibe estatísticas da organização.', 'amigopet-wp'); ?></p>
                        <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                        <ul>
                            <li><code>show</code>: <?php _e('Estatísticas a exibir (pets, adoptions, donations, volunteers)', 'amigopet-wp'); ?></li>
                            <li><code>period</code>: <?php _e('Período das estatísticas (all, year, month)', 'amigopet-wp'); ?></li>
                        </ul>
                        <p><strong><?php _e('Exemplo:', 'amigopet-wp'); ?></strong></p>
                        <code>[amigopet_stats show="pets,adoptions" period="year"]</code>
                    </div>
                </div>
            </section>
            
            <!-- Placeholders para Termos -->
            <section id="terms-placeholders" class="apwp-help-section">
                <h2><?php _e('Placeholders para Termos', 'amigopet-wp'); ?></h2>
                <p><?php _e('Use os seguintes placeholders nos seus modelos de termos para inserir automaticamente informações:', 'amigopet-wp'); ?></p>
                
                <div class="apwp-placeholder-list">
                    <!-- Organização -->
                    <div class="apwp-placeholder-group">
                        <h3><?php _e('Dados da Organização', 'amigopet-wp'); ?></h3>
                        <div class="apwp-placeholder-item">
                            <code>{org_name}</code>
                            <span><?php _e('Nome da organização', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{org_cnpj}</code>
                            <span><?php _e('CNPJ da organização', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{org_address}</code>
                            <span><?php _e('Endereço completo da organização', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{org_phone}</code>
                            <span><?php _e('Telefone da organização', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{org_email}</code>
                            <span><?php _e('E-mail da organização', 'amigopet-wp'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Adotante -->
                    <div class="apwp-placeholder-group">
                        <h3><?php _e('Dados do Adotante', 'amigopet-wp'); ?></h3>
                        <div class="apwp-placeholder-item">
                            <code>{adopter_name}</code>
                            <span><?php _e('Nome completo do adotante', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{adopter_cpf}</code>
                            <span><?php _e('CPF do adotante', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{adopter_rg}</code>
                            <span><?php _e('RG do adotante', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{adopter_birth}</code>
                            <span><?php _e('Data de nascimento do adotante', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{adopter_address}</code>
                            <span><?php _e('Endereço completo do adotante', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{adopter_phone}</code>
                            <span><?php _e('Telefone do adotante', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{adopter_email}</code>
                            <span><?php _e('E-mail do adotante', 'amigopet-wp'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Pet -->
                    <div class="apwp-placeholder-group">
                        <h3><?php _e('Dados do Pet', 'amigopet-wp'); ?></h3>
                        <div class="apwp-placeholder-item">
                            <code>{pet_name}</code>
                            <span><?php _e('Nome do pet', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{pet_species}</code>
                            <span><?php _e('Espécie do pet', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{pet_breed}</code>
                            <span><?php _e('Raça do pet', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{pet_age}</code>
                            <span><?php _e('Idade do pet', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{pet_gender}</code>
                            <span><?php _e('Sexo do pet', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{pet_size}</code>
                            <span><?php _e('Tamanho do pet', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{pet_chip}</code>
                            <span><?php _e('Número do microchip do pet', 'amigopet-wp'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Voluntário -->
                    <div class="apwp-placeholder-group">
                        <h3><?php _e('Dados do Voluntário', 'amigopet-wp'); ?></h3>
                        <div class="apwp-placeholder-item">
                            <code>{volunteer_name}</code>
                            <span><?php _e('Nome completo do voluntário', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{volunteer_cpf}</code>
                            <span><?php _e('CPF do voluntário', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{volunteer_rg}</code>
                            <span><?php _e('RG do voluntário', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{volunteer_birth}</code>
                            <span><?php _e('Data de nascimento do voluntário', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{volunteer_address}</code>
                            <span><?php _e('Endereço completo do voluntário', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{volunteer_phone}</code>
                            <span><?php _e('Telefone do voluntário', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{volunteer_email}</code>
                            <span><?php _e('E-mail do voluntário', 'amigopet-wp'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Doador -->
                    <div class="apwp-placeholder-group">
                        <h3><?php _e('Dados do Doador', 'amigopet-wp'); ?></h3>
                        <div class="apwp-placeholder-item">
                            <code>{donor_name}</code>
                            <span><?php _e('Nome completo do doador', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{donor_cpf}</code>
                            <span><?php _e('CPF do doador', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{donor_email}</code>
                            <span><?php _e('E-mail do doador', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{donor_phone}</code>
                            <span><?php _e('Telefone do doador', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{donation_amount}</code>
                            <span><?php _e('Valor da doação', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{donation_date}</code>
                            <span><?php _e('Data da doação', 'amigopet-wp'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Data e Hora -->
                    <div class="apwp-placeholder-group">
                        <h3><?php _e('Data e Hora', 'amigopet-wp'); ?></h3>
                        <div class="apwp-placeholder-item">
                            <code>{current_date}</code>
                            <span><?php _e('Data atual', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{current_time}</code>
                            <span><?php _e('Hora atual', 'amigopet-wp'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{current_datetime}</code>
                            <span><?php _e('Data e hora atual', 'amigopet-wp'); ?></span>
                        </div>
                    </div>
                </div>
                
                <h3><?php _e('Exemplo de Uso', 'amigopet-wp'); ?></h3>
                <pre>
Eu, {adopter_name}, portador do CPF {adopter_cpf}, declaro que desejo adotar o pet {pet_name}, 
da espécie {pet_species}, microchip nº {pet_chip}, da organização {org_name}.

Local e data: {org_address}, {current_date}

_______________________
{adopter_name}
CPF: {adopter_cpf}
                </pre>
            </section>
            
            <!-- Outras seções... -->
            
        </div>
    </div>
</div>

<style>
.apwp-help-wrapper {
    display: flex;
    gap: 2rem;
    margin-top: 1rem;
}

.apwp-help-nav {
    flex: 0 0 200px;
}

.apwp-help-nav ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.apwp-help-nav li {
    margin-bottom: 0.5rem;
}

.apwp-help-nav a {
    display: block;
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: #2271b1;
    border-radius: 4px;
}

.apwp-help-nav a:hover,
.apwp-help-nav a.active {
    background: #2271b1;
    color: #fff;
}

.apwp-help-content {
    flex: 1;
    max-width: 800px;
}

.apwp-help-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #ddd;
}

.apwp-help-section:last-child {
    border-bottom: none;
}

.apwp-shortcode-item,
.apwp-placeholder-group {
    background: #fff;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.apwp-shortcode-item h3,
.apwp-placeholder-group h3 {
    margin-top: 0;
    color: #1d2327;
}

.apwp-placeholder-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.apwp-placeholder-item code {
    background: #f0f0f1;
    padding: 0.2rem 0.4rem;
    border-radius: 3px;
    cursor: pointer;
}

.apwp-placeholder-item code:hover {
    background: #2271b1;
    color: #fff;
}

pre {
    background: #f0f0f1;
    padding: 1rem;
    border-radius: 4px;
    overflow-x: auto;
    white-space: pre-wrap;
}

.apwp-help-section h2 {
    color: #1d2327;
    border-bottom: 1px solid #ddd;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

.apwp-help-section p {
    font-size: 14px;
    line-height: 1.5;
    color: #50575e;
}

.apwp-help-section ul {
    margin-left: 1.5rem;
}

.apwp-help-section li {
    margin-bottom: 0.5rem;
}

.apwp-shortcode-item ul {
    list-style: disc;
    margin-left: 1.5rem;
}

.apwp-shortcode-item h4 {
    margin: 1rem 0 0.5rem;
    color: #1d2327;
}

.notice {
    margin: 1rem 0;
    padding: 1rem;
    border-left: 4px solid #00a32a;
    background: #fff;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.notice.notice-info {
    border-left-color: #72aee6;
}

.notice.notice-warning {
    border-left-color: #dba617;
}

@media screen and (max-width: 782px) {
    .apwp-help-wrapper {
        flex-direction: column;
    }

    .apwp-help-nav {
        flex: none;
        margin-bottom: 2rem;
    }

    .apwp-help-content {
        max-width: 100%;
    }
}
</style>
.apwp-help-wrapper {
    display: flex;
    gap: 30px;
    margin-top: 20px;
}

.apwp-help-nav {
    flex: 0 0 200px;
    position: sticky;
    top: 32px;
    max-height: calc(100vh - 32px);
    overflow-y: auto;
}

.apwp-help-nav ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.apwp-help-nav li {
    margin: 0;
    padding: 0;
}

.apwp-help-nav a {
    display: block;
    padding: 8px 12px;
    text-decoration: none;
    color: #2271b1;
    border-left: 4px solid transparent;
}

.apwp-help-nav a:hover,
.apwp-help-nav a.active {
    background: #f0f0f1;
    border-left-color: #2271b1;
}

.apwp-help-content {
    flex: 1;
    max-width: 800px;
}

.apwp-help-section {
    margin-bottom: 40px;
    padding-bottom: 40px;
    border-bottom: 1px solid #dcdcde;
}

.apwp-help-section:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.apwp-help-section h2 {
    margin: 0 0 20px;
    padding: 0;
    font-size: 1.5em;
    color: #1d2327;
}

.apwp-help-section h3 {
    margin: 1.5em 0 1em;
    font-size: 1.3em;
    color: #1d2327;
}

.apwp-shortcode-list {
    display: grid;
    gap: 20px;
}

.apwp-shortcode-item {
    background: #fff;
    padding: 20px;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
}

.apwp-shortcode-item h3 {
    margin: 0 0 10px;
    color: #2271b1;
}

.apwp-shortcode-item h4 {
    margin: 1em 0 0.5em;
    font-size: 1em;
}

.apwp-shortcode-item p {
    margin: 0 0 1em;
}

.apwp-shortcode-item ul {
    margin: 0 0 1em 1.5em;
}

.apwp-shortcode-item code {
    display: inline-block;
    padding: 4px 8px;
    background: #f6f7f7;
    border: 1px solid #dcdcde;
    border-radius: 2px;
    font-family: Consolas, Monaco, monospace;
}

.apwp-placeholder-list {
    display: grid;
    gap: 30px;
}

.apwp-placeholder-group {
    background: #fff;
    padding: 20px;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
}

.apwp-placeholder-group h3 {
    margin: 0 0 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #c3c4c7;
    color: #2271b1;
}

.apwp-placeholder-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    padding: 5px 0;
}

.apwp-placeholder-item:last-child {
    margin-bottom: 0;
}

.apwp-placeholder-item code {
    flex: 0 0 200px;
    margin-right: 15px;
    padding: 4px 8px;
    background: #f0f0f1;
    border: 1px solid #c3c4c7;
    border-radius: 2px;
    font-family: Consolas, Monaco, monospace;
    cursor: pointer;
    transition: all 0.2s ease;
}

.apwp-placeholder-item code:hover {
    background: #2271b1;
    border-color: #2271b1;
    color: #fff;
}

.apwp-placeholder-item span {
    flex: 1;
    color: #50575e;
}

.apwp-help-section pre {
    background: #f6f7f7;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    padding: 15px;
    margin: 15px 0;
    white-space: pre-wrap;
    word-wrap: break-word;
    font-family: Consolas, Monaco, monospace;
}

@media screen and (max-width: 782px) {
    .apwp-help-wrapper {
        flex-direction: column;
    }
    
    .apwp-help-nav {
        flex: none;
        position: static;
        max-height: none;
    }
    
    .apwp-help-content {
        max-width: none;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Copia placeholder ao clicar
    $('.apwp-placeholder-item code').on('click', function() {
        const placeholder = $(this).text();
        const tempInput = $('<input>');
        $('body').append(tempInput);
        tempInput.val(placeholder).select();
        document.execCommand('copy');
        tempInput.remove();
        
        // Feedback visual
        const originalText = $(this).text();
        $(this).text('Copiado!');
        setTimeout(() => {
            $(this).text(originalText);
        }, 1000);
    });
    
    // Navegação suave ao clicar nos links
    $('.apwp-help-nav a').on('click', function(e) {
        e.preventDefault();
        
        const target = $($(this).attr('href'));
        const offset = target.offset().top - 32;
        
        $('.apwp-help-nav a').removeClass('active');
        $(this).addClass('active');
        
        $('html, body').animate({
            scrollTop: offset
        }, 500);
    });
    
    // Atualiza link ativo ao rolar
    $(window).on('scroll', function() {
        const scrollPosition = $(window).scrollTop();
        
        $('.apwp-help-section').each(function() {
            const target = $(this);
            const targetTop = target.offset().top - 100;
            const targetBottom = targetTop + target.outerHeight();
            
            if (scrollPosition >= targetTop && scrollPosition < targetBottom) {
                const id = target.attr('id');
                $('.apwp-help-nav a').removeClass('active');
                $(`.apwp-help-nav a[href="#${id}"]`).addClass('active');
            }
        });
    });
});
</script>
