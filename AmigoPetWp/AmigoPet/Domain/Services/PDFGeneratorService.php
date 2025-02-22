<?php
namespace AmigoPetWp\Domain\Services;

use TCPDF;

class PDFGeneratorService {
    private $pdf;
    private $uploadDir;
    private $uploadUrl;

    public function __construct() {
        // Configura o diretório de upload
        $wpUploadDir = wp_upload_dir();
        $this->uploadDir = $wpUploadDir['basedir'] . '/amigopet-documents/';
        $this->uploadUrl = $wpUploadDir['baseurl'] . '/amigopet-documents/';

        // Cria o diretório se não existir
        if (!file_exists($this->uploadDir)) {
            wp_mkdir_p($this->uploadDir);
        }

        // Protege o diretório com .htaccess
        $htaccess = $this->uploadDir . '.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, 'Deny from all');
        }
    }

    /**
     * Gera um PDF a partir do conteúdo HTML
     *
     * @param string $title Título do documento
     * @param string $content Conteúdo HTML do documento
     * @param array $metadata Metadados do documento
     * @return array Array com URL e path do arquivo gerado
     */
    public function generatePDF(string $title, string $content, array $metadata = []): array {
        // Inicializa o TCPDF
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Configura o documento
        $this->setupDocument($title, $metadata);

        // Adiciona o conteúdo
        $this->addContent($content);

        // Gera o arquivo
        return $this->saveFile($title);
    }

    /**
     * Configura o documento PDF
     */
    private function setupDocument(string $title, array $metadata): void {
        // Remove cabeçalho e rodapé padrão
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);

        // Configura as margens
        $this->pdf->SetMargins(15, 15, 15);

        // Configura a fonte
        $this->pdf->SetFont('dejavusans', '', 10);

        // Configura metadados do documento
        $this->pdf->SetCreator(wp_get_current_user()->display_name);
        $this->pdf->SetAuthor($metadata['author'] ?? get_bloginfo('name'));
        $this->pdf->SetTitle($title);
        $this->pdf->SetSubject($metadata['subject'] ?? '');
        $this->pdf->SetKeywords($metadata['keywords'] ?? '');

        // Adiciona a primeira página
        $this->pdf->AddPage();

        // Adiciona o cabeçalho personalizado
        $this->addHeader($title);
    }

    /**
     * Adiciona o cabeçalho ao documento
     */
    private function addHeader(string $title): void {
        // Logo da organização
        $logo = get_option('amigopet_settings')['org_logo'] ?? '';
        if ($logo) {
            $this->pdf->Image($logo, 15, 10, 30);
            $this->pdf->Ln(35);
        }

        // Título do documento
        $this->pdf->SetFont('dejavusans', 'B', 16);
        $this->pdf->Cell(0, 10, $title, 0, 1, 'C');
        $this->pdf->Ln(10);

        // Restaura a fonte padrão
        $this->pdf->SetFont('dejavusans', '', 10);
    }

    /**
     * Adiciona o conteúdo ao documento
     */
    private function addContent(string $content): void {
        // Converte entidades HTML
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');

        // Remove tags não suportadas
        $allowedTags = [
            'p', 'br', 'b', 'strong', 'i', 'em', 'u', 'ul', 'ol', 'li',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'table', 'tr', 'td', 'th'
        ];
        $content = strip_tags($content, $allowedTags);

        // Adiciona o conteúdo
        $this->pdf->writeHTML($content, true, false, true, false, '');
    }

    /**
     * Salva o arquivo PDF
     */
    private function saveFile(string $title): array {
        // Gera um nome único para o arquivo
        $filename = sanitize_file_name(
            strtolower(
                preg_replace('/[^a-zA-Z0-9-]/', '-', $title)
            ) . '-' . uniqid() . '.pdf'
        );

        // Caminho completo do arquivo
        $filepath = $this->uploadDir . $filename;

        // Salva o PDF
        $this->pdf->Output($filepath, 'F');

        // Retorna o path e URL do arquivo
        return [
            'path' => $filepath,
            'url' => $this->uploadUrl . $filename
        ];
    }

    /**
     * Adiciona uma assinatura digital ao PDF
     *
     * @param string $filePath Caminho do arquivo PDF
     * @param string $certificate Caminho do certificado digital
     * @param string $password Senha do certificado
     * @return bool
     */
    public function signPDF(string $filePath, string $certificate, string $password): bool {
        // Verifica se o arquivo existe
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Arquivo PDF não encontrado");
        }

        // Verifica se o certificado existe
        if (!file_exists($certificate)) {
            throw new \InvalidArgumentException("Certificado digital não encontrado");
        }

        try {
            // Configura a assinatura digital
            $this->pdf->setSignature($certificate, $certificate, $password, '', 2, []);
            
            // Adiciona a assinatura visível
            $this->pdf->setSignatureAppearance(180, 60, 15, 15);
            
            // Salva o PDF assinado
            $this->pdf->Output($filePath, 'F');
            
            return true;
        } catch (\Exception $e) {
            throw new \RuntimeException("Erro ao assinar o PDF: " . $e->getMessage());
        }
    }

    /**
     * Protege o PDF com senha
     *
     * @param string $filePath Caminho do arquivo PDF
     * @param string $userPassword Senha para abrir o documento
     * @param string $ownerPassword Senha para editar o documento
     * @return bool
     */
    public function protectPDF(string $filePath, string $userPassword, string $ownerPassword): bool {
        // Verifica se o arquivo existe
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Arquivo PDF não encontrado");
        }

        try {
            // Configura a proteção
            $this->pdf->SetProtection(
                ['print', 'copy'],
                $userPassword,
                $ownerPassword,
                3,
                null
            );
            
            // Salva o PDF protegido
            $this->pdf->Output($filePath, 'F');
            
            return true;
        } catch (\Exception $e) {
            throw new \RuntimeException("Erro ao proteger o PDF: " . $e->getMessage());
        }
    }

    /**
     * Adiciona uma marca d'água ao PDF
     *
     * @param string $filePath Caminho do arquivo PDF
     * @param string $text Texto da marca d'água
     * @return bool
     */
    public function addWatermark(string $filePath, string $text): bool {
        // Verifica se o arquivo existe
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Arquivo PDF não encontrado");
        }

        try {
            // Configura a fonte para a marca d'água
            $this->pdf->SetFont('dejavusans', 'I', 50);
            $this->pdf->SetTextColor(255, 192, 203, 15); // Rosa claro com 15% de opacidade
            
            // Adiciona a marca d'água em todas as páginas
            $numPages = $this->pdf->getNumPages();
            for ($i = 1; $i <= $numPages; $i++) {
                $this->pdf->setPage($i);
                $this->pdf->StartTransform();
                $this->pdf->Rotate(45, 105, 105);
                $this->pdf->Text(50, 190, $text);
                $this->pdf->StopTransform();
            }
            
            // Salva o PDF com marca d'água
            $this->pdf->Output($filePath, 'F');
            
            return true;
        } catch (\Exception $e) {
            throw new \RuntimeException("Erro ao adicionar marca d'água ao PDF: " . $e->getMessage());
        }
    }
}
