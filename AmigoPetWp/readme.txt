=== AmigoPetWp ===
Contributors: jacksonsa
Tags: adoção, animais, WordPress, QR Code, pet, abrigo, ong
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AmigoPetWp é um plugin que facilita a gestão de adoção de animais, oferecendo funcionalidades como contratos de adoção e QR Codes para verificação de adoções.

== Description ==

O plugin AmigoPetWp oferece uma solução completa para organizações que trabalham com adoção de animais. Com uma interface moderna e intuitiva, o plugin permite gerenciar todo o processo de adoção, desde o cadastro dos animais até o acompanhamento pós-adoção.

= Novidades da Versão 2.0.0 =

* Sistema completo de histórico médico para pets
* Sistema de galeria de fotos com thumbnails
* Workflow aprimorado de adoção em etapas
* Sistema robusto de segurança
* Sistema de migrations para banco de dados

= Características Principais =

* Sistema de gerenciamento de usuários com múltiplos papéis
* Histórico médico completo dos pets com alertas
* Galeria de fotos com upload múltiplo
* Workflow de adoção em três etapas
* Sistema avançado de permissões
* Backup automático do banco de dados
* Sistema de termos dinâmicos com placeholders
* Impressão prévia de termos de adoção

= Sistema de Termos Dinâmicos =

* Termos personalizáveis para adoção, doação e voluntariado
* Placeholders dinâmicos para inserção automática de dados
* Impressão prévia do termo de adoção antes da visita
* Dados da organização, adotante e pet automaticamente preenchidos
* Interface amigável para edição dos termos
* Documentação completa dos placeholders disponíveis

= Sistema de Histórico Médico =

* Registro completo de vacinas, exames e consultas
* Anexos para documentos médicos
* Histórico de veterinários
* Alertas de vacinação
* Relatórios médicos em PDF
* Busca avançada por tipo de registro
* Filtros por data e tipo
* Exportação de dados

= Sistema de Galeria de Fotos =

* Upload múltiplo de fotos
* Geração automática de thumbnails
* Foto de perfil destacada
* Organização por álbuns
* Otimização automática de imagens
* Suporte a vários formatos
* Interface drag-and-drop
* Preview em tempo real

= Sistema de Adoção em Etapas =

* Processo em três etapas: documentos → pagamento → aprovação
* Validação automática de requisitos
* Notificações para adotantes e organização
* Histórico completo de alterações
* Documentos personalizáveis
* Sistema de aprovação
* Acompanhamento em tempo real
* Relatórios de status

= Sistema de Personalização Avançado =

* Grid responsivo com 1-4 colunas
* Três estilos de cards: moderno, clássico e minimalista
* Personalização completa de cores e tipografia
* Sistema de ícones para status dos animais
* Preview em tempo real das alterações
* Importação e exportação de configurações
* Restauração para valores padrão
* Sistema de cache para melhor performance

= Sistema de Papéis de Usuário =

* Administrador: Acesso completo ao sistema
* Anunciante: Pode cadastrar e gerenciar animais
* Adotante: Pode se candidatar para adoções
* Sistema de solicitação e aprovação de papéis

= Segurança e Performance =

* Sanitização completa de dados
* Verificação de nonces
* Controle de acesso baseado em papéis
* Validação de formulários
* Sistema de cache para configurações
* Validação robusta de dados importados
* Backup e restauração de configurações

= Recursos Técnicos =

* Compatibilidade com WordPress 6.0+
* Requer PHP 7.4+
* Sistema próprio de migrations
* Biblioteca TCPDF para documentos
* Processamento avançado de imagens
* Sistema de backup automático
* Rate limiting para APIs
* Log de acessos e ações
* Arquitetura modular e extensível
* Análise estática de código
* Padrões PSR-12
* Testes automatizados

== Installation ==

1. Faça upload dos arquivos do plugin para a pasta `/wp-content/plugins/amigopet-wp`
2. Ative o plugin através do menu 'Plugins' no WordPress
3. Configure as opções do plugin em 'AmigoPetWp > Configurações'
4. Personalize a exibição em 'AmigoPetWp > Configurações de Exibição'

== Frequently Asked Questions ==

= O plugin é compatível com qualquer tema? =

Sim, o plugin foi desenvolvido seguindo as melhores práticas do WordPress e é compatível com a maioria dos temas modernos.

= Como personalizo a aparência dos cards de animais? =

Acesse 'AmigoPetWp > Configurações de Exibição' para usar nossa interface intuitiva de personalização com preview em tempo real. Você pode ajustar cores, tipografia, layout e muito mais, tudo com visualização instantânea das mudanças.

= Posso fazer backup das minhas configurações de exibição? =

Sim! Na página de configurações de exibição, você encontra ferramentas para exportar suas configurações atuais, importar configurações salvas e restaurar para os valores padrão quando necessário.

= Como funciona o preview em tempo real? =

O preview utiliza tecnologia AJAX para mostrar instantaneamente como suas alterações afetarão a aparência dos cards de animais, sem necessidade de recarregar a página ou salvar as configurações.

= O plugin é otimizado para performance? =

Sim! Além do sistema de cache, implementamos:
* Otimização automática de imagens
* Geração assíncrona de thumbnails
* Lazy loading de fotos
* Rate limiting para APIs
* Backup incremental do banco
* Processamento em background
* Cache de consultas complexas

== Screenshots ==

1. Painel de controle principal
2. Interface de personalização com preview em tempo real
3. Formulário de adoção
4. Gerenciamento de usuários e papéis
5. Ferramentas de importação e exportação de configurações
6. Preview responsivo dos cards de animais

== Changelog ==

= 1.1.0 =
* Adicionado sistema de termos com placeholders
* Adicionada funcionalidade de impressão prévia do termo de adoção
* Melhorias na interface do usuário
* Adicionada documentação dos placeholders
* Sistema completo de substituição de dados em termos
* Novo botão de impressão no formulário de adoção
* Layout otimizado para impressão de termos

= 2.0.0 =
* Sistema completo de histórico médico para pets
* Sistema de galeria de fotos com thumbnails
* Workflow aprimorado de adoção em etapas
* Sistema robusto de segurança
* Sistema de migrations para banco de dados
* Atualização de dependências e requisitos
* Várias melhorias de performance

= 1.0.0 =
* Lançamento inicial do plugin
* Sistema completo de gerenciamento de adoções
* Interface de personalização com preview em tempo real
* Sistema de papéis de usuário
* Formulários personalizáveis
* Sistema avançado de configurações com importação/exportação
* Cache de configurações para melhor performance
* Validação robusta de dados
* Suporte responsivo
* Interface moderna e intuitiva

== Upgrade Notice ==

= 1.0.0 =
Versão inicial do plugin com sistema completo de gestão de adoções e personalização avançada.

== Recursos Futuros ==

* Integração com redes sociais
* Sistema de doações
* Estatísticas e relatórios avançados
* Galeria de fotos expandida
* Sistema de eventos e campanhas
* Mais opções de personalização
* Temas predefinidos para cards
* Sistema de notificações avançado
