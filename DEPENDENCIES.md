# Dependências do AmigoPet WP

## Requisitos de Sistema

- PHP 7.2 ou superior
- Composer
- WordPress 6.0 ou superior

## Instalação de Dependências

### Método 1: Script de Instalação Automática

```bash
chmod +x install-dependencies.sh
./install-dependencies.sh
```

### Método 2: Instalação Manual com Composer

```bash
# Instalar dependências
composer install

# Copiar dependências para o diretório do plugin
mkdir -p AmigoPetWp/vendor
cp -r vendor/smalot/pdfparser vendor/phpoffice/phpword vendor/tecnickcom/tcpdf AmigoPetWp/vendor/
```

## Dependências Instaladas

- smalot/pdfparser: Extração de texto de PDFs
- phpoffice/phpword: Processamento de documentos DOCX
- tecnickcom/tcpdf: Geração de PDFs

## Solução de Problemas

1. Verifique se o Composer está instalado
2. Certifique-se de ter permissões de escrita no diretório
3. Instale as dependências do sistema:
   ```bash
   sudo apt-get install php-xml php-mbstring antiword
   ```

## Notas Importantes

- As dependências são gerenciadas via Composer
- O plugin tentará localizar as bibliotecas em múltiplos caminhos
- Em caso de erro, verifique a instalação do Composer e das dependências
