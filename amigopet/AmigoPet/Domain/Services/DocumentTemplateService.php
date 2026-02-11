<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Services;

if (!defined('ABSPATH')) {
    exit;
}

class DocumentTemplateService {
    /**
     * Templates padrão para cada tipo de documento
     */
    private const DEFAULT_TEMPLATES = [
        'adoption_term' => [
            'title' => 'Termo de Adoção',
            'content' => '
                <h1>TERMO DE ADOÇÃO RESPONSÁVEL</h1>
                
                <p>Pelo presente instrumento, {org_name}, inscrita no CNPJ sob o nº {org_cnpj}, com sede em {org_address}, doravante denominada DOADORA, e {adopter_name}, portador(a) do CPF nº {adopter_cpf} e RG nº {adopter_rg}, residente e domiciliado(a) em {adopter_address}, doravante denominado(a) ADOTANTE, firmam o presente Termo de Adoção Responsável, mediante as seguintes cláusulas e condições:</p>

                <h2>1. DO OBJETO</h2>
                <p>1.1. O presente termo tem por objeto a adoção do animal com as seguintes características:</p>
                <ul>
                    <li>Nome: {pet_name}</li>
                    <li>Espécie: {pet_species}</li>
                    <li>Raça: {pet_breed}</li>
                    <li>Idade aproximada: {pet_age}</li>
                    <li>Sexo: {pet_gender}</li>
                    <li>Porte: {pet_size}</li>
                    <li>Microchip: {pet_chip}</li>
                </ul>

                <h2>2. DAS RESPONSABILIDADES DO ADOTANTE</h2>
                <p>2.1. O ADOTANTE se compromete a:</p>
                <ul>
                    <li>Proporcionar ao animal ambiente adequado, alimentação de qualidade e água fresca;</li>
                    <li>Manter o animal em sua residência, não permitindo que fique solto na rua;</li>
                    <li>Levar o animal regularmente ao veterinário e mantê-lo vacinado;</li>
                    <li>Comunicar à DOADORA qualquer mudança de endereço;</li>
                    <li>Não abandonar o animal sob nenhuma circunstância;</li>
                    <li>Em caso de impossibilidade de manter o animal, devolvê-lo à DOADORA.</li>
                </ul>

                <h2>3. DO ACOMPANHAMENTO</h2>
                <p>3.1. O ADOTANTE autoriza visitas de acompanhamento pela DOADORA para verificar as condições do animal.</p>

                <h2>4. DA CASTRAÇÃO</h2>
                <p>4.1. O ADOTANTE se compromete a castrar o animal no prazo máximo de 6 meses, caso ainda não seja castrado.</p>

                <p>E por estarem assim justos e contratados, firmam o presente em duas vias de igual teor.</p>

                <p>{org_name}<br>
                CNPJ: {org_cnpj}</p>

                <p>{adopter_name}<br>
                CPF: {adopter_cpf}</p>

                <p>{current_datetime}</p>
            '
        ],
        'responsibility_term' => [
            'title' => 'Termo de Responsabilidade',
            'content' => '
                <h1>TERMO DE RESPONSABILIDADE</h1>

                <p>Eu, {adopter_name}, portador(a) do CPF nº {adopter_cpf} e RG nº {adopter_rg}, declaro para os devidos fins que:</p>

                <h2>1. DA RESPONSABILIDADE</h2>
                <p>1.1. Assumo total responsabilidade pelos cuidados com o animal {pet_name}, comprometendo-me a:</p>
                <ul>
                    <li>Zelar pela saúde e bem-estar do animal;</li>
                    <li>Arcar com todas as despesas veterinárias;</li>
                    <li>Manter o animal em ambiente seguro e adequado;</li>
                    <li>Não submeter o animal a maus-tratos ou abandono.</li>
                </ul>

                <h2>2. DAS PENALIDADES</h2>
                <p>2.1. Estou ciente que o abandono e os maus-tratos contra animais são crimes previstos na Lei Federal 9.605/98.</p>

                <p>Por ser expressão da verdade, firmo o presente termo.</p>

                <p>{adopter_name}<br>
                CPF: {adopter_cpf}</p>

                <p>{current_datetime}</p>
            '
        ],
        'health_term' => [
            'title' => 'Declaração de Saúde do Animal',
            'content' => '
                <h1>DECLARAÇÃO DE SAÚDE DO ANIMAL</h1>

                <p>{org_name}, inscrita no CNPJ sob o nº {org_cnpj}, declara que o animal:</p>

                <ul>
                    <li>Nome: {pet_name}</li>
                    <li>Espécie: {pet_species}</li>
                    <li>Raça: {pet_breed}</li>
                    <li>Idade aproximada: {pet_age}</li>
                    <li>Microchip: {pet_chip}</li>
                </ul>

                <p>Foi examinado pela nossa equipe veterinária e encontra-se:</p>
                <ul>
                    <li>Vacinado contra raiva: ( ) Sim ( ) Não</li>
                    <li>Vacinado com V8/V10: ( ) Sim ( ) Não</li>
                    <li>Vermifugado: ( ) Sim ( ) Não</li>
                    <li>Castrado: ( ) Sim ( ) Não</li>
                </ul>

                <p>Observações médicas:</p>
                <p>_____________________________________________</p>
                <p>_____________________________________________</p>

                <p>Médico Veterinário Responsável:</p>
                <p>Nome: _____________________________________</p>
                <p>CRMV: _____________________________________</p>

                <p>{current_datetime}</p>
            '
        ],
        'spay_neuter_term' => [
            'title' => 'Termo de Compromisso de Castração',
            'content' => '
                <h1>TERMO DE COMPROMISSO DE CASTRAÇÃO</h1>

                <p>Eu, {adopter_name}, portador(a) do CPF nº {adopter_cpf}, me comprometo a realizar a castração do animal:</p>

                <ul>
                    <li>Nome: {pet_name}</li>
                    <li>Espécie: {pet_species}</li>
                    <li>Raça: {pet_breed}</li>
                    <li>Idade: {pet_age}</li>
                    <li>Sexo: {pet_gender}</li>
                </ul>

                <p>A castração deverá ser realizada até {current_date + 6 months}, em clínica veterinária de minha escolha ou indicada pela {org_name}.</p>

                <h2>DO COMPROMISSO</h2>
                <p>1. Comprometo-me a:</p>
                <ul>
                    <li>Realizar a castração no prazo estabelecido;</li>
                    <li>Seguir todas as orientações pré e pós-operatórias;</li>
                    <li>Apresentar o comprovante de castração à {org_name};</li>
                    <li>Arcar com os custos do procedimento.</li>
                </ul>

                <p>2. Estou ciente que:</p>
                <ul>
                    <li>A castração é fundamental para o controle populacional;</li>
                    <li>O não cumprimento deste termo pode resultar na retomada do animal;</li>
                    <li>Devo informar qualquer impossibilidade de realizar o procedimento no prazo.</li>
                </ul>

                <p>{adopter_name}<br>
                CPF: {adopter_cpf}</p>

                <p>{current_datetime}</p>
            '
        ]
    ];

    /**
     * Retorna o template padrão para um tipo de documento
     */
    public function getDefaultTemplate(string $documentType): ?array {
        return self::DEFAULT_TEMPLATES[$documentType] ?? null;
    }

    /**
     * Retorna todos os templates padrão
     */
    public function getAllDefaultTemplates(): array {
        return self::DEFAULT_TEMPLATES;
    }

    /**
     * Retorna um template personalizado salvo no WordPress
     */
    public function getCustomTemplate(string $documentType): ?array {
        $template = get_option("apwp_document_template_{$documentType}");
        return $template ? json_decode($template, true) : null;
    }

    /**
     * Salva um template personalizado
     */
    public function saveCustomTemplate(string $documentType, string $title, string $content): bool {
        $template = [
            'title' => $title,
            'content' => $content
        ];
        
        return update_option("apwp_document_template_{$documentType}", json_encode($template));
    }

    /**
     * Exclui um template personalizado
     */
    public function deleteCustomTemplate(string $documentType): bool {
        return delete_option("apwp_document_template_{$documentType}");
    }

    /**
     * Retorna o template ativo para um tipo de documento
     * Se existir um template personalizado, retorna ele
     * Caso contrário, retorna o template padrão
     */
    public function getActiveTemplate(string $documentType): ?array {
        return $this->getCustomTemplate($documentType) ?? $this->getDefaultTemplate($documentType);
    }

    /**
     * Verifica se um tipo de documento tem template personalizado
     */
    public function hasCustomTemplate(string $documentType): bool {
        return get_option("apwp_document_template_{$documentType}") !== false;
    }

    /**
     * Restaura o template padrão para um tipo de documento
     */
    public function restoreDefaultTemplate(string $documentType): bool {
        return $this->deleteCustomTemplate($documentType);
    }

    /**
     * Retorna uma prévia do template com dados de exemplo
     */
    public function getTemplatePreview(string $documentType): ?array {
        $template = $this->getActiveTemplate($documentType);
        if (!$template) {
            return null;
        }

        // Dados de exemplo
        $sampleData = [
            'org_name' => 'ONG Amigo Pet',
            'org_cnpj' => '12.345.678/0001-90',
            'org_address' => 'Rua dos Animais, 123',
            'org_phone' => '(11) 1234-5678',
            'org_email' => 'contato@amigopet.org',
            'adopter_name' => 'João da Silva',
            'adopter_cpf' => '123.456.789-00',
            'adopter_rg' => '12.345.678-9',
            'adopter_birth' => '01/01/1980',
            'adopter_address' => 'Rua das Flores, 456',
            'adopter_phone' => '(11) 98765-4321',
            'adopter_email' => 'joao@email.com',
            'pet_name' => 'Rex',
            'pet_species' => 'Cachorro',
            'pet_breed' => 'SRD',
            'pet_age' => '2 anos',
            'pet_gender' => 'Macho',
            'pet_size' => 'Médio',
            'pet_chip' => '123456789',
            'current_date' => gmdate('d/m/Y'),
            'current_time' => gmdate('H:i'),
            'current_datetime' => gmdate('d/m/Y H:i')
        ];

        // Processa os placeholders
        $processedContent = str_replace(
            array_map(function($key) { return '{' . $key . '}'; }, array_keys($sampleData)),
            array_values($sampleData),
            $template['content']
        );

        return [
            'title' => $template['title'],
            'content' => $processedContent
        ];
    }
}