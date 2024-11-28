<?php
/**
 * Template para o formulário de adoção
 *
 * @package AmigoPet_Wp
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="pr-adoption-form-wrapper">
    <div class="pr-messages"></div>

    <form class="pr-adoption-form" method="post">
        <input type="hidden" name="animal_id" value="<?php echo esc_attr($animal->id); ?>">
        
        <h2 class="pr-form-title">Formulário de Adoção</h2>
        
        <div class="pr-form-section">
            <h3>Dados Pessoais</h3>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="name">Nome Completo *</label>
                <input type="text" class="pr-form-input" name="name" id="name" required>
            </div>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="cpf">CPF *</label>
                <input type="text" class="pr-form-input" name="cpf" id="cpf" required>
            </div>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="email">E-mail *</label>
                <input type="email" class="pr-form-input" name="email" id="email" required>
            </div>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="phone">Telefone *</label>
                <input type="text" class="pr-form-input" name="phone" id="phone" required>
            </div>
        </div>
        
        <div class="pr-form-section">
            <h3>Endereço</h3>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="cep">CEP *</label>
                <input type="text" class="pr-form-input" name="cep" id="cep" required>
            </div>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="street">Logradouro *</label>
                <input type="text" class="pr-form-input" name="street" id="street" required>
            </div>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="number">Número *</label>
                <input type="text" class="pr-form-input" name="number" id="number" required>
            </div>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="complement">Complemento</label>
                <input type="text" class="pr-form-input" name="complement" id="complement">
            </div>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="neighborhood">Bairro *</label>
                <input type="text" class="pr-form-input" name="neighborhood" id="neighborhood" required>
            </div>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="city">Cidade *</label>
                <input type="text" class="pr-form-input" name="city" id="city" required>
            </div>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="state">Estado *</label>
                <select class="pr-form-input" name="state" id="state" required>
                    <option value="">Selecione</option>
                    <option value="AC">Acre</option>
                    <option value="AL">Alagoas</option>
                    <option value="AP">Amapá</option>
                    <option value="AM">Amazonas</option>
                    <option value="BA">Bahia</option>
                    <option value="CE">Ceará</option>
                    <option value="DF">Distrito Federal</option>
                    <option value="ES">Espírito Santo</option>
                    <option value="GO">Goiás</option>
                    <option value="MA">Maranhão</option>
                    <option value="MT">Mato Grosso</option>
                    <option value="MS">Mato Grosso do Sul</option>
                    <option value="MG">Minas Gerais</option>
                    <option value="PA">Pará</option>
                    <option value="PB">Paraíba</option>
                    <option value="PR">Paraná</option>
                    <option value="PE">Pernambuco</option>
                    <option value="PI">Piauí</option>
                    <option value="RJ">Rio de Janeiro</option>
                    <option value="RN">Rio Grande do Norte</option>
                    <option value="RS">Rio Grande do Sul</option>
                    <option value="RO">Rondônia</option>
                    <option value="RR">Roraima</option>
                    <option value="SC">Santa Catarina</option>
                    <option value="SP">São Paulo</option>
                    <option value="SE">Sergipe</option>
                    <option value="TO">Tocantins</option>
                </select>
            </div>
        </div>
        
        <div class="pr-form-section">
            <h3>Informações Adicionais</h3>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="residence_type">Tipo de Residência *</label>
                <select class="pr-form-input" name="residence_type" id="residence_type" required>
                    <option value="">Selecione</option>
                    <option value="house">Casa</option>
                    <option value="apartment">Apartamento</option>
                    <option value="farm">Chácara/Sítio</option>
                </select>
            </div>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="residence_status">Situação da Residência *</label>
                <select class="pr-form-input" name="residence_status" id="residence_status" required>
                    <option value="">Selecione</option>
                    <option value="own">Própria</option>
                    <option value="rent">Alugada</option>
                    <option value="family">Familiar</option>
                </select>
            </div>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="has_other_pets">Possui outros animais? *</label>
                <select class="pr-form-input" name="has_other_pets" id="has_other_pets" required>
                    <option value="">Selecione</option>
                    <option value="yes">Sim</option>
                    <option value="no">Não</option>
                </select>
            </div>
            
            <div class="pr-form-row pr-other-pets-details" style="display: none;">
                <label class="pr-form-label" for="other_pets_details">Descreva seus outros animais</label>
                <textarea class="pr-form-input pr-form-textarea" name="other_pets_details" id="other_pets_details"></textarea>
            </div>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="adoption_reason">Por que você quer adotar este animal? *</label>
                <textarea class="pr-form-input pr-form-textarea" name="adoption_reason" id="adoption_reason" required></textarea>
            </div>
            
            <div class="pr-form-row">
                <label class="pr-form-label" for="family_agreement">Todos na residência concordam com a adoção? *</label>
                <select class="pr-form-input" name="family_agreement" id="family_agreement" required>
                    <option value="">Selecione</option>
                    <option value="yes">Sim</option>
                    <option value="no">Não</option>
                </select>
            </div>
        </div>
        
        <div class="pr-form-section">
            <h3>Termo de Responsabilidade</h3>
            
            <div class="pr-form-row">
                <div class="pr-checkbox-group">
                    <input type="checkbox" class="pr-form-checkbox" name="terms_agreement" id="terms_agreement" required>
                    <label class="pr-form-label" for="terms_agreement">
                        Declaro que li e concordo com os termos de adoção e me comprometo a cuidar do animal com responsabilidade, carinho e dedicação. *
                    </label>
                </div>
            </div>
            
            <div class="pr-form-row">
                <div class="pr-checkbox-group">
                    <input type="checkbox" class="pr-form-checkbox" name="visit_agreement" id="visit_agreement" required>
                    <label class="pr-form-label" for="visit_agreement">
                        Concordo em receber visitas de acompanhamento pós-adoção para verificar o bem-estar do animal. *
                    </label>
                </div>
            </div>
        </div>
        
        <div class="pr-form-actions">
            <button type="submit" class="pr-button pr-button-primary">
                <i class="fas fa-heart"></i>
                Enviar Formulário
            </button>
        </div>
    </form>
</div>
