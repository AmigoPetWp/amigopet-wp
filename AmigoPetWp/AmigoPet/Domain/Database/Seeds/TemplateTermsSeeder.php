<?php

namespace AmigoPetWp\Domain\Database\Seeds;

use AmigoPetWp\Domain\Services\TemplateTermsService;

class TemplateTermsSeeder
{
    private TemplateTermsService $service;

    public function __construct(TemplateTermsService $service)
    {
        $this->service = $service;
    }

    public function seed(): void
    {
        $templates = [
            'adoption' => [
                'title' => 'Termo de Adoção Responsável',
                'content' => "TERMO DE ADOÇÃO RESPONSÁVEL\n\nA {organization_name}, inscrita no CNPJ sob o nº XX.XXX.XXX/XXXX-XX, com sede em {organization_address}, telefone {organization_phone}, e-mail {organization_email}, doravante denominada ORGANIZADORA, e {adopter_name}, portador(a) do documento {adopter_document}, residente em {adopter_address}, telefone {adopter_phone}, e-mail {adopter_email}, doravante denominado(a) ADOTANTE, firmam o presente termo de adoção responsável.\n\nCLÁUSULA 1ª - DO OBJETO\nO presente termo tem como objeto a adoção do animal {pet_name}, {pet_type}, da raça {pet_breed}, conforme descrito na ficha de identificação anexa.\n\nCLÁUSULA 2ª - DAS RESPONSABILIDADES DO ADOTANTE\nO ADOTANTE compromete-se a:\n1. Proporcionar ambiente adequado e seguro para o animal;\n2. Fornecer alimentação adequada e água fresca diariamente;\n3. Manter a vacinação em dia e levar ao veterinário quando necessário;\n4. Não abandonar o animal sob nenhuma circunstância;\n5. Comunicar à ORGANIZADORA qualquer mudança de endereço;\n6. Permitir visitas de acompanhamento pela ORGANIZADORA.\n\nCLÁUSULA 3ª - DO ACOMPANHAMENTO\nA ORGANIZADORA realizará visitas periódicas para acompanhar a adaptação e bem-estar do animal.\n\nCLÁUSULA 4ª - DA RESCISÃO\nO descumprimento das cláusulas deste termo poderá resultar na retomada do animal pela ORGANIZADORA.\n\nData: {current_date}\n\n_____________________\nOrganização\n\n_____________________\nAdotante",
                'description' => 'Termo padrão para adoção de animais'
            ],
            'volunteer' => [
                'title' => 'Termo de Voluntariado',
                'content' => "TERMO DE VOLUNTARIADO\n\nA {organization_name}, inscrita no CNPJ sob o nº XX.XXX.XXX/XXXX-XX, com sede em {organization_address}, telefone {organization_phone}, e-mail {organization_email}, doravante denominada ORGANIZADORA, e {volunteer_name}, portador(a) do documento {volunteer_document}, residente em {volunteer_address}, telefone {volunteer_phone}, e-mail {volunteer_email}, doravante denominado(a) VOLUNTÁRIO(A), firmam o presente termo de voluntariado.\n\nCLÁUSULA 1ª - DO OBJETO\nO presente termo tem como objeto a prestação de serviços voluntários pelo(a) VOLUNTÁRIO(A) à ORGANIZADORA, nos termos da Lei nº 9.608/98.\n\nCLÁUSULA 2ª - DAS ATIVIDADES\nO(A) VOLUNTÁRIO(A) se compromete a:\n1. Dedicar {volunteer_hours} horas semanais às atividades da ORGANIZADORA;\n2. Seguir as normas e procedimentos da ORGANIZADORA;\n3. Manter sigilo sobre informações confidenciais;\n4. Zelar pelo patrimônio e pela imagem da ORGANIZADORA.\n\nCLÁUSULA 3ª - DA GRATUIDADE\nO serviço voluntário não gera vínculo empregatício nem obrigação trabalhista, previdenciária ou afim.\n\nCLÁUSULA 4ª - DO PRAZO\nEste termo tem validade de {volunteer_term_duration} meses, podendo ser renovado por acordo entre as partes.\n\nData: {current_date}\n\n_____________________\nOrganização\n\n_____________________\nVoluntário(a)",
                'description' => 'Termo padrão para voluntariado'
            ]
        ];

        foreach ($templates as $type => $data) {
            $template = new \AmigoPetWp\Domain\Entities\TemplateTerm(
                $type,
                $data['content'],
                $data['title']
            );
            $template->setDescription($data['description']);
            $this->service->save($template);
        }
    }
}
