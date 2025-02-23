<?php
namespace AmigoPetWp\Domain\Database\Migrations;

use AmigoPetWp\Domain\Database\Migration;

class CreateSettings extends Migration {
    private array $default_settings = [
        'organization_name' => '',
        'organization_email' => '',
        'organization_phone' => '',
        'google_maps_key' => '',
        'petfinder_key' => '',
        'payment_gateway_key' => '',
        'payment_gateway_secret' => '',
        'payment_gateway_sandbox' => true,

        // Termos e Condições
        'terms_settings' => [
            'adoption' => "TERMO DE ADOÇÃO RESPONSÁVEL\n\n{organization_name}, inscrita no CNPJ sob o nº XX.XXX.XXX/XXXX-XX, com sede em {organization_address}, doravante denominada ORGANIZADORA, e {adopter_name}, portador(a) do documento {adopter_document}, residente em {adopter_address}, doravante denominado(a) ADOTANTE, firmam o presente termo de adoção responsável.\n\nCLÁUSULA 1ª - DO OBJETO\nO presente termo tem como objeto a adoção do animal {pet_name}, {pet_type}, da raça {pet_breed}.\n\nCLÁUSULA 2ª - DAS RESPONSABILIDADES\nO ADOTANTE compromete-se a:\n- Proporcionar ambiente adequado e seguro\n- Fornecer alimentação adequada e água fresca\n- Manter a vacinação em dia\n- Não abandonar o animal\n\nData: {current_date}\n\n_____________________\nOrganização\n\n_____________________\nAdotante",

            'volunteer' => "TERMO DE VOLUNTARIADO\n\n{organization_name}, inscrita no CNPJ sob o nº XX.XXX.XXX/XXXX-XX, com sede em {organization_address}, doravante denominada ORGANIZADORA, e {volunteer_name}, portador(a) do documento {volunteer_document}, residente em {volunteer_address}, doravante denominado(a) VOLUNTÁRIO(A), firmam o presente termo de voluntariado.\n\nCLÁUSULA 1ª - DO OBJETO\nO presente termo tem como objeto a prestação de serviços voluntários, sem vínculo empregatício.\n\nCLÁUSULA 2ª - DAS ATIVIDADES\nO VOLUNTÁRIO se compromete a:\n- Seguir as orientações da organização\n- Manter sigilo sobre informações confidenciais\n- Zelar pelo bem-estar dos animais\n\nData: {current_date}\n\n_____________________\nOrganização\n\n_____________________\nVoluntário(a)",

            'donation' => "TERMO DE DOAÇÃO\n\n{organization_name}, inscrita no CNPJ sob o nº XX.XXX.XXX/XXXX-XX, com sede em {organization_address}, doravante denominada ORGANIZADORA, declara ter recebido de {donor_name}, portador(a) do documento {donor_document}, residente em {donor_address}, doravante denominado(a) DOADOR(A), a doação no valor de {donation_amount}, na modalidade {donation_type}.\n\nA doação será utilizada exclusivamente para os fins estabelecidos no estatuto da organização.\n\nData: {current_date}\n\n_____________________\nOrganização\n\n_____________________\nDoador(a)"
        ],

        // QR Code
        'qrcode_settings' => [
            'size' => 300,
            'margin' => 10,
            'foreground_color' => '#000000',
            'background_color' => '#FFFFFF',
            'error_correction' => 'M',
            'logo_enabled' => true,
            'logo_size' => 50,
            'logo_path' => ''
        ],
        'smtp_settings' => [
            'host' => '',
            'port' => '',
            'secure' => 'tls',
            'auth' => true,
            'username' => '',
            'password' => ''
        ],
        'email_templates' => [
            'adoption_approved' => [
                'subject' => 'Parabéns! Sua adoção foi aprovada',
                'body' => 'Olá {adopter_name},\n\nParabéns! Sua adoção do pet {pet_name} foi aprovada.'
            ],
            'adoption_rejected' => [
                'subject' => 'Atualização sobre sua adoção',
                'body' => 'Olá {adopter_name},\n\nInfelizmente sua adoção do pet {pet_name} não foi aprovada.'
            ],
            'donation_received' => [
                'subject' => 'Obrigado pela sua doação!',
                'body' => 'Olá {donor_name},\n\nMuito obrigado pela sua doação de {donation_amount}.'
            ],
            'volunteer_application' => [
                'subject' => 'Recebemos sua inscrição para voluntariado',
                'body' => 'Olá {volunteer_name},\n\nRecebemos sua inscrição para ser voluntário.'
            ]
        ],
        'qrcode_settings' => [
            'size' => 300,
            'margin' => 10,
            'foreground_color' => '#000000',
            'background_color' => '#FFFFFF',
            'error_correction' => 'M',
            'logo_enabled' => true,
            'logo_size' => 50,
            'logo_path' => ''
        ],
        'adoption_workflow' => [
            'require_home_visit' => true,
            'require_adoption_fee' => true,
            'require_terms_acceptance' => true,
            'require_adopter_documents' => true
        ],
        'notification_workflow' => [
            'notify_new_adoption' => true,
            'notify_adoption_status' => true,
            'notify_new_donation' => true,
            'notify_new_volunteer' => true
        ],
        'terms_settings' => [
            'adoption_terms' => '',
            'donation_terms' => '',
            'volunteer_terms' => ''
        ]
    ];

    public function getVersion(): string {
        return '1.0.0';
    }

    public function getDescription(): string {
        return 'Cria as configurações padrão do sistema';
    }

    public function up(): void {
        foreach ($this->default_settings as $key => $value) {
            update_option('apwp_' . $key, $value, false);
        }
    }

    public function down(): void {
        foreach (array_keys($this->default_settings) as $key) {
            delete_option('apwp_' . $key);
        }
    }
}
