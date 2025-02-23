# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/).

## [2.0.0] - 2025-02-22

### Adicionado
- Sistema completo de histórico médico para pets
  - Registro de vacinas, exames e consultas
  - Anexos para documentos médicos
  - Histórico de veterinários
  - Alertas de vacinação
- Sistema de galeria de fotos
  - Upload múltiplo de fotos
  - Geração automática de thumbnails
  - Foto de perfil destacada
  - Organização por álbuns
- Workflow aprimorado de adoção
  - Processo em etapas: documentos → pagamento → aprovação
  - Validação automática de requisitos
  - Notificações para adotantes e organização
  - Histórico completo de alterações

### Melhorado
- Sistema de segurança
  - Implementado sistema robusto de permissões
  - Adicionada proteção contra CSRF
  - Implementado rate limiting para APIs
  - Adicionado log de acessos e ações
- Banco de dados
  - Implementado sistema de migrations
  - Adicionado backup automático antes de alterações
  - Suporte a rollback em caso de problemas
  - Versionamento do schema

### Técnico
- Atualizado requisito mínimo para PHP 7.4+
- Atualizado requisito mínimo para MySQL 5.7+
- Adicionada dependência TCPDF para geração de documentos
- Implementado sistema próprio de migrations
- Adicionada biblioteca de processamento de imagens

### Correções
- Corrigido bug na validação de documentos
- Melhorada performance no carregamento de imagens
- Corrigido problema de codificação em PDFs
- Otimizado processo de backup

## [1.0.0] - 2024-01-20

### Adicionado
- Estrutura inicial do plugin usando WordPress Plugin Boilerplate
- Sistema de gerenciamento de roles e capabilities
- Classes principais para gerenciamento de animais
- Sistema de adoção com formulários e processos
- Painel administrativo completo
- Suporte a internacionalização (i18n)
- Tradução para português do Brasil (pt_BR)

### Recursos Principais
- Gerenciamento de animais para adoção
- Sistema de roles: Adotante, Anunciante e Organização
- Formulários de adoção personalizáveis
- Painel de controle para ONGs e abrigos
- Sistema de notificações
- Relatórios e estatísticas
- Suporte multilíngue

### Segurança
- Implementação de verificações de segurança
- Sanitização e validação de dados
- Controle de acesso baseado em roles
- Proteção contra XSS e CSRF

### Técnico
- Compatibilidade com WordPress 6.0+
- Suporte a PHP 7.2+
- Estrutura modular e extensível
- Código documentado seguindo padrões WordPress
- Sistema de cache para otimização

### Correções
- N/A (primeira versão)
