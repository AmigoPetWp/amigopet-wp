<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template para página de ajuda no admin
 */

?>

<div class="wrap">
    <h1><?php esc_html_e('Ajuda do AmigoPet', 'amigopet'); ?></h1>

     <?php if (!empty($apwp_data['organization'])): ?>
    <div class="notice notice-info">
        <p>
            <strong><?php esc_html_e('Organização:', 'amigopet'); ?></strong> <?php echo esc_html($apwp_data['organization']['name'] ?? ''); ?><br>
            <strong><?php esc_html_e('Email:', 'amigopet'); ?></strong> <?php echo esc_html($apwp_data['organization']['email'] ?? ''); ?><br>
            <strong><?php esc_html_e('Telefone:', 'amigopet'); ?></strong> <?php echo esc_html($apwp_data['organization']['phone'] ?? ''); ?><br>
            <strong><?php esc_html_e('Versão do Plugin:', 'amigopet'); ?></strong> <?php echo esc_html($apwp_data['version'] ?? '1.0.0'); ?>
        </p>
    </div>
     <?php endif; ?>

     <?php if (!empty($apwp_data['stats'])): ?>
    <div class="notice">
        <p>
            <strong><?php esc_html_e('Estatísticas Atuais:', 'amigopet'); ?></strong><br>
            <?php esc_html_e('Pets:', 'amigopet'); ?> <?php echo esc_html($apwp_data['stats']['pets'] ?? 0); ?> |
            <?php esc_html_e('Adoções:', 'amigopet'); ?> <?php echo esc_html($apwp_data['stats']['adoptions'] ?? 0); ?> |
            <?php esc_html_e('Eventos:', 'amigopet'); ?> <?php echo esc_html($apwp_data['stats']['events'] ?? 0); ?> |
            <?php esc_html_e('Doações:', 'amigopet'); ?> <?php echo esc_html($apwp_data['stats']['donations'] ?? 0); ?>
        </p>
    </div>
    <?php endif; ?>
    
    <div class="apwp-help-wrapper">
        <!-- Menu de navegação -->
        <div class="apwp-help-nav">
            <ul>
                <li>
                    <a href="#getting-started" class="active">
                        <?php esc_html_e('Primeiros Passos', 'amigopet'); ?>
                    </a>
                </li>
                <li>
                    <a href="#shortcodes">
                        <?php esc_html_e('Shortcodes', 'amigopet'); ?>
                    </a>
                </li>
                <li>
                    <a href="#pets">
                        <?php esc_html_e('Gerenciando Pets', 'amigopet'); ?>
                    </a>
                </li>
                <li>
                    <a href="#adoptions">
                        <?php esc_html_e('Processo de Adoção', 'amigopet'); ?>
                    </a>
                </li>
                <li>
                    <a href="#volunteers">
                        <?php esc_html_e('Gerenciando Voluntários', 'amigopet'); ?>
                    </a>
                </li>
                <li>
                    <a href="#donations">
                        <?php esc_html_e('Sistema de Doações', 'amigopet'); ?>
                    </a>
                </li>
                <li>
                    <a href="#events">
                        <?php esc_html_e('Eventos', 'amigopet'); ?>
                    </a>
                </li>
                <li>
                    <a href="#reports">
                        <?php esc_html_e('Relatórios', 'amigopet'); ?>
                    </a>
                </li>
                <li>
                    <a href="#settings">
                        <?php esc_html_e('Configurações', 'amigopet'); ?>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Conteúdo -->
        <div class="apwp-help-content">
            <!-- Primeiros Passos -->
            <section id="getting-started" class="apwp-help-section">
                <h2><?php esc_html_e('Primeiros Passos', 'amigopet'); ?></h2>
                
                <h3><?php esc_html_e('Configuração Inicial', 'amigopet'); ?></h3>
                <ol>
                    <li><?php esc_html_e('Acesse o menu "Configurações" e preencha os dados da sua organização', 'amigopet'); ?></li>
                    <li><?php esc_html_e('Configure as informações de contato que aparecerão nos formulários', 'amigopet'); ?></li>
                    <li><?php esc_html_e('Personalize os modelos de email para comunicação com adotantes e doadores', 'amigopet'); ?></li>
                </ol>

                <h3><?php esc_html_e('Cadastro de Pets', 'amigopet'); ?></h3>
                <ol>
                    <li><?php esc_html_e('Acesse the menu "Pets" para adicionar novos animais', 'amigopet'); ?></li>
                    <li><?php esc_html_e('Preencha todas as informações do pet: nome, espécie, idade, etc', 'amigopet'); ?></li>
                    <li><?php esc_html_e('Adicione fotos de qualidade para aumentar as chances de adoção', 'amigopet'); ?></li>
                    <li><?php esc_html_e('Mantenha o status do pet atualizado (disponível, adotado, etc)', 'amigopet'); ?></li>
                </ol>

                <h3><?php esc_html_e('Gerenciamento de Adoções', 'amigopet'); ?></h3>
                <ol>
                    <li><?php esc_html_e('Acompanhe as solicitações de adoção no menu "Adoções"', 'amigopet'); ?></li>
                    <li><?php esc_html_e('Avalie cada solicitação cuidadosamente', 'amigopet'); ?></li>
                    <li><?php esc_html_e('Use os termos de adoção para formalizar o processo', 'amigopet'); ?></li>
                    <li><?php esc_html_e('Mantenha contato com os adotantes após a adoção', 'amigopet'); ?></li>
                </ol>
                
                <h3><?php esc_html_e('Instalação', 'amigopet'); ?></h3>
                <ol>
                    <li><?php esc_html_e('Instale e ative o plugin através do WordPress', 'amigopet'); ?></li>
                    <li><?php esc_html_e('Acesse o menu "AmigoPet" no painel administrativo', 'amigopet'); ?></li>
                    <li><?php esc_html_e('Configure as informações básicas da sua organização em "Configurações"', 'amigopet'); ?></li>
                    <li><?php esc_html_e('Comece cadastrando seus pets disponíveis para adoção', 'amigopet'); ?></li>
                </ol>
                
                <h3><?php esc_html_e('Requisitos', 'amigopet'); ?></h3>
                <ul>
                    <li><?php esc_html_e('WordPress 5.0 ou superior', 'amigopet'); ?></li>
                    <li><?php esc_html_e('PHP 7.4 ou superior', 'amigopet'); ?></li>
                    <li><?php esc_html_e('MySQL 5.6 ou superior', 'amigopet'); ?></li>
                </ul>
            </section>
            
            <!-- Shortcodes -->
            <section id="shortcodes" class="apwp-help-section">
                <h2><?php esc_html_e('Shortcodes', 'amigopet'); ?></h2>
                
                <div class="notice notice-info">
                    <p><?php esc_html_e('Use estes shortcodes para exibir conteúdo do AmigoPet em qualquer página ou post do seu site.', 'amigopet'); ?></p>
                </div>
                
                <div class="apwp-shortcode-list">
                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_pets]</h3>
                        <p><?php esc_html_e('Exibe a grade de pets disponíveis para adoção.', 'amigopet'); ?></p>
                        <h4><?php esc_html_e('Atributos:', 'amigopet'); ?></h4>
                        <ul>
                            <li><code>limit</code> - <?php esc_html_e('Número máximo de pets para exibir (ex: limit="12")', 'amigopet'); ?></li>
                            <li><code>species</code> - <?php esc_html_e('Filtrar por espécie (ex: species="dog")', 'amigopet'); ?></li>
                            <li><code>columns</code> - <?php esc_html_e('Número de colunas na grade (ex: columns="3")', 'amigopet'); ?></li>
                        </ul>
                        <p><strong><?php esc_html_e('Exemplo:', 'amigopet'); ?></strong> <code>[amigopet_pets limit="6" species="cat" columns="2"]</code></p>
                    </div>

                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_adoption_form]</h3>
                        <p><?php esc_html_e('Exibe o formulário de solicitação de adoção.', 'amigopet'); ?></p>
                        <h4><?php esc_html_e('Atributos:', 'amigopet'); ?></h4>
                        <ul>
                            <li><code>pet_id</code> - <?php esc_html_e('ID do pet específico (opcional)', 'amigopet'); ?></li>
                            <li><code>title</code> - <?php esc_html_e('Título personalizado do formulário', 'amigopet'); ?></li>
                        </ul>
                        <p><strong><?php esc_html_e('Exemplo:', 'amigopet'); ?></strong> <code>[amigopet_adoption_form pet_id="123" title="Adote este Pet"]</code></p>
                    </div>

                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_donation_form]</h3>
                        <p><?php esc_html_e('Exibe o formulário de doação.', 'amigopet'); ?></p>
                        <h4><?php esc_html_e('Atributos:', 'amigopet'); ?></h4>
                        <ul>
                            <li><code>type</code> - <?php esc_html_e('Tipo de doação (money, supplies, volunteer)', 'amigopet'); ?></li>
                            <li><code>title</code> - <?php esc_html_e('Título personalizado do formulário', 'amigopet'); ?></li>
                        </ul>
                        <p><strong><?php esc_html_e('Exemplo:', 'amigopet'); ?></strong> <code>[amigopet_donation_form type="money" title="Faça uma Doação"]</code></p>
                    </div>

                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_events]</h3>
                        <p><?php esc_html_e('Exibe a lista de eventos próximos.', 'amigopet'); ?></p>
                        <h4><?php esc_html_e('Atributos:', 'amigopet'); ?></h4>
                        <ul>
                            <li><code>limit</code> - <?php esc_html_e('Número de eventos para exibir', 'amigopet'); ?></li>
                            <li><code>view</code> - <?php esc_html_e('Tipo de visualização (list, calendar)', 'amigopet'); ?></li>
                        </ul>
                        <p><strong><?php esc_html_e('Exemplo:', 'amigopet'); ?></strong> <code>[amigopet_events limit="5" view="calendar"]</code></p>
                    </div>
                        <ul>
                            <li><code>limit</code>: <?php esc_html_e('Número de pets por página (padrão: 12)', 'amigopet'); ?></li>
                            <li><code>species</code>: <?php esc_html_e('Filtrar por espécie (ex: "cachorro", "gato")', 'amigopet'); ?></li>
                            <li><code>size</code>: <?php esc_html_e('Filtrar por tamanho (pequeno, medio, grande)', 'amigopet'); ?></li>
                            <li><code>age</code>: <?php esc_html_e('Filtrar por idade (filhote, adulto, idoso)', 'amigopet'); ?></li>
                        </ul>
                        <p><strong><?php esc_html_e('Exemplo:', 'amigopet'); ?></strong></p>
                        <code>[amigopet_pets limit="6" species="cachorro" size="pequeno"]</code>
                    </div>
                    
                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_adoption_form]</h3>
                        <p><?php esc_html_e('Exibe o formulário de adoção para um pet específico.', 'amigopet'); ?></p>
                        <h4><?php esc_html_e('Atributos:', 'amigopet'); ?></h4>
                        <ul>
                            <li><code>pet_id</code>: <?php esc_html_e('ID do pet (obrigatório)', 'amigopet'); ?></li>
                        </ul>
                        <p><strong><?php esc_html_e('Exemplo:', 'amigopet'); ?></strong></p>
                        <code>[amigopet_adoption_form pet_id="123"]</code>
                    </div>
                    
                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_events]</h3>
                        <p><?php esc_html_e('Exibe a lista de eventos programados.', 'amigopet'); ?></p>
                        <h4><?php esc_html_e('Atributos:', 'amigopet'); ?></h4>
                        <ul>
                            <li><code>limit</code>: <?php esc_html_e('Número de eventos por página (padrão: 10)', 'amigopet'); ?></li>
                            <li><code>type</code>: <?php esc_html_e('Filtrar por tipo de evento', 'amigopet'); ?></li>
                            <li><code>view</code>: <?php esc_html_e('Tipo de visualização (grid, list, calendar)', 'amigopet'); ?></li>
                        </ul>
                        <p><strong><?php esc_html_e('Exemplo:', 'amigopet'); ?></strong></p>
                        <code>[amigopet_events limit="4" type="feira" view="grid"]</code>
                    </div>
                    
                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_volunteer_form]</h3>
                        <p><?php esc_html_e('Exibe o formulário de cadastro de voluntários.', 'amigopet'); ?></p>
                        <h4><?php esc_html_e('Atributos:', 'amigopet'); ?></h4>
                        <ul>
                            <li><code>redirect</code>: <?php esc_html_e('URL para redirecionamento após envio', 'amigopet'); ?></li>
                        </ul>
                        <p><strong><?php esc_html_e('Exemplo:', 'amigopet'); ?></strong></p>
                        <code>[amigopet_volunteer_form redirect="/obrigado"]</code>
                    </div>
                    
                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_donation_form]</h3>
                        <p><?php esc_html_e('Exibe o formulário de doação.', 'amigopet'); ?></p>
                        <h4><?php esc_html_e('Atributos:', 'amigopet'); ?></h4>
                        <ul>
                            <li><code>type</code>: <?php esc_html_e('Tipo de doação (money, food, supplies)', 'amigopet'); ?></li>
                            <li><code>amount</code>: <?php esc_html_e('Valor predefinido para doação em dinheiro', 'amigopet'); ?></li>
                            <li><code>recurring</code>: <?php esc_html_e('Permitir doação recorrente (true/false)', 'amigopet'); ?></li>
                        </ul>
                        <p><strong><?php esc_html_e('Exemplo:', 'amigopet'); ?></strong></p>
                        <code>[amigopet_donation_form type="money" amount="50" recurring="true"]</code>
                    </div>
                    
                    <div class="apwp-shortcode-item">
                        <h3>[amigopet_stats]</h3>
                        <p><?php esc_html_e('Exibe estatísticas da organização.', 'amigopet'); ?></p>
                        <h4><?php esc_html_e('Atributos:', 'amigopet'); ?></h4>
                        <ul>
                            <li><code>show</code>: <?php esc_html_e('Estatísticas a exibir (pets, adoptions, donations, volunteers)', 'amigopet'); ?></li>
                            <li><code>period</code>: <?php esc_html_e('Período das estatísticas (all, year, month)', 'amigopet'); ?></li>
                        </ul>
                        <p><strong><?php esc_html_e('Exemplo:', 'amigopet'); ?></strong></p>
                        <code>[amigopet_stats show="pets,adoptions" period="year"]</code>
                    </div>
                </div>
            </section>
            
            <!-- Placeholders para Termos -->
            <section id="terms-placeholders" class="apwp-help-section">
                <h2><?php esc_html_e('Placeholders para Termos', 'amigopet'); ?></h2>
                <p><?php esc_html_e('Use os seguintes placeholders nos seus modelos de termos para inserir automaticamente informações:', 'amigopet'); ?></p>
                
                <div class="apwp-placeholder-list">
                    <!-- Organização -->
                    <div class="apwp-placeholder-group">
                        <h3><?php esc_html_e('Dados da Organização', 'amigopet'); ?></h3>
                        <div class="apwp-placeholder-item">
                            <code>{org_name}</code>
                            <span><?php esc_html_e('Nome da organização', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{org_cnpj}</code>
                            <span><?php esc_html_e('CNPJ da organização', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{org_address}</code>
                            <span><?php esc_html_e('Endereço completo da organização', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{org_phone}</code>
                            <span><?php esc_html_e('Telefone da organização', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{org_email}</code>
                            <span><?php esc_html_e('E-mail da organização', 'amigopet'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Adotante -->
                    <div class="apwp-placeholder-group">
                        <h3><?php esc_html_e('Dados do Adotante', 'amigopet'); ?></h3>
                        <div class="apwp-placeholder-item">
                            <code>{adopter_name}</code>
                            <span><?php esc_html_e('Nome completo do adotante', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{adopter_cpf}</code>
                            <span><?php esc_html_e('CPF do adotante', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{adopter_rg}</code>
                            <span><?php esc_html_e('RG do adotante', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{adopter_birth}</code>
                            <span><?php esc_html_e('Data de nascimento do adotante', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{adopter_address}</code>
                            <span><?php esc_html_e('Endereço completo do adotante', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{adopter_phone}</code>
                            <span><?php esc_html_e('Telefone do adotante', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{adopter_email}</code>
                            <span><?php esc_html_e('E-mail do adotante', 'amigopet'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Pet -->
                    <div class="apwp-placeholder-group">
                        <h3><?php esc_html_e('Dados do Pet', 'amigopet'); ?></h3>
                        <div class="apwp-placeholder-item">
                            <code>{pet_name}</code>
                            <span><?php esc_html_e('Nome do pet', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{pet_species}</code>
                            <span><?php esc_html_e('Espécie do pet', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{pet_breed}</code>
                            <span><?php esc_html_e('Raça do pet', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{pet_age}</code>
                            <span><?php esc_html_e('Idade do pet', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{pet_gender}</code>
                            <span><?php esc_html_e('Sexo do pet', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{pet_size}</code>
                            <span><?php esc_html_e('Tamanho do pet', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{pet_chip}</code>
                            <span><?php esc_html_e('Número do microchip do pet', 'amigopet'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Voluntário -->
                    <div class="apwp-placeholder-group">
                        <h3><?php esc_html_e('Dados do Voluntário', 'amigopet'); ?></h3>
                        <div class="apwp-placeholder-item">
                            <code>{volunteer_name}</code>
                            <span><?php esc_html_e('Nome completo do voluntário', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{volunteer_cpf}</code>
                            <span><?php esc_html_e('CPF do voluntário', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{volunteer_rg}</code>
                            <span><?php esc_html_e('RG do voluntário', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{volunteer_birth}</code>
                            <span><?php esc_html_e('Data de nascimento do voluntário', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{volunteer_address}</code>
                            <span><?php esc_html_e('Endereço completo do voluntário', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{volunteer_phone}</code>
                            <span><?php esc_html_e('Telefone do voluntário', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{volunteer_email}</code>
                            <span><?php esc_html_e('E-mail do voluntário', 'amigopet'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Doador -->
                    <div class="apwp-placeholder-group">
                        <h3><?php esc_html_e('Dados do Doador', 'amigopet'); ?></h3>
                        <div class="apwp-placeholder-item">
                            <code>{donor_name}</code>
                            <span><?php esc_html_e('Nome completo do doador', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{donor_cpf}</code>
                            <span><?php esc_html_e('CPF do doador', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{donor_email}</code>
                            <span><?php esc_html_e('E-mail do doador', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{donor_phone}</code>
                            <span><?php esc_html_e('Telefone do doador', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{donation_amount}</code>
                            <span><?php esc_html_e('Valor da doação', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{donation_date}</code>
                            <span><?php esc_html_e('Data da doação', 'amigopet'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Data e Hora -->
                    <div class="apwp-placeholder-group">
                        <h3><?php esc_html_e('Data e Hora', 'amigopet'); ?></h3>
                        <div class="apwp-placeholder-item">
                            <code>{current_date}</code>
                            <span><?php esc_html_e('Data atual', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{current_time}</code>
                            <span><?php esc_html_e('Hora atual', 'amigopet'); ?></span>
                        </div>
                        <div class="apwp-placeholder-item">
                            <code>{current_datetime}</code>
                            <span><?php esc_html_e('Data e hora atual', 'amigopet'); ?></span>
                        </div>
                    </div>
                </div>
                
                <h3><?php esc_html_e('Exemplo de Uso', 'amigopet'); ?></h3>
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