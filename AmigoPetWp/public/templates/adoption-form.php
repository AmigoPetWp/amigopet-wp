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

<div class="apwp-form-wrapper">
    <div class="apwp-messages"></div>

    <form class="apwp-form" method="post">
        <input type="hidden" name="animal_id" value="<?php echo esc_attr($animal->id); ?>">
        
        <h2 class="apwp-form-title"><?php esc_html_e('Formulário de Adoção', 'amigopet-wp'); ?></h2>
        
        <div class="apwp-form-section">
            <h3><?php esc_html_e('Dados Pessoais', 'amigopet-wp'); ?></h3>
            
            <div class="apwp-form-grid">
                <div class="apwp-form-row">
                    <label class="apwp-form-label" for="name">
                        <?php esc_html_e('Nome Completo', 'amigopet-wp'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" class="apwp-form-input" name="name" id="name" required>
                </div>
                
                <div class="apwp-form-row">
                    <label class="apwp-form-label" for="cpf">
                        <?php esc_html_e('CPF', 'amigopet-wp'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" class="apwp-form-input" name="cpf" id="cpf" required>
                </div>
            </div>
            
            <div class="apwp-form-grid">
                <div class="apwp-form-row">
                    <label class="apwp-form-label" for="email">
                        <?php esc_html_e('E-mail', 'amigopet-wp'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="email" class="apwp-form-input" name="email" id="email" required>
                </div>
                
                <div class="apwp-form-row">
                    <label class="apwp-form-label" for="phone">
                        <?php esc_html_e('Telefone', 'amigopet-wp'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" class="apwp-form-input" name="phone" id="phone" required>
                </div>
            </div>
        </div>
        
        <div class="apwp-form-section">
            <h3><?php esc_html_e('Endereço', 'amigopet-wp'); ?></h3>
            
            <div class="apwp-form-row">
                <label class="apwp-form-label" for="cep">
                    <?php esc_html_e('CEP', 'amigopet-wp'); ?>
                    <span class="required">*</span>
                </label>
                <input type="text" class="apwp-form-input" name="cep" id="cep" required>
            </div>
            
            <div class="apwp-form-grid">
                <div class="apwp-form-row">
                    <label class="apwp-form-label" for="street">
                        <?php esc_html_e('Logradouro', 'amigopet-wp'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" class="apwp-form-input" name="street" id="street" required>
                </div>
                
                <div class="apwp-form-row">
                    <label class="apwp-form-label" for="number">
                        <?php esc_html_e('Número', 'amigopet-wp'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" class="apwp-form-input" name="number" id="number" required>
                </div>
            </div>
            
            <div class="apwp-form-row">
                <label class="apwp-form-label" for="complement">
                    <?php esc_html_e('Complemento', 'amigopet-wp'); ?>
                </label>
                <input type="text" class="apwp-form-input" name="complement" id="complement">
            </div>
            
            <div class="apwp-form-grid">
                <div class="apwp-form-row">
                    <label class="apwp-form-label" for="neighborhood">
                        <?php esc_html_e('Bairro', 'amigopet-wp'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" class="apwp-form-input" name="neighborhood" id="neighborhood" required>
                </div>
                
                <div class="apwp-form-row">
                    <label class="apwp-form-label" for="city">
                        <?php esc_html_e('Cidade', 'amigopet-wp'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" class="apwp-form-input" name="city" id="city" required>
                </div>
            </div>
            
            <div class="apwp-form-row">
                <label class="apwp-form-label" for="state">
                    <?php esc_html_e('Estado', 'amigopet-wp'); ?>
                    <span class="required">*</span>
                </label>
                <select class="apwp-form-input" name="state" id="state" required>
                    <option value=""><?php esc_html_e('Selecione', 'amigopet-wp'); ?></option>
                    <?php foreach (APWP_States::get_states() as $uf => $state): ?>
                        <option value="<?php echo esc_attr($uf); ?>">
                            <?php echo esc_html($state); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="apwp-form-section">
            <h3><?php esc_html_e('Informações Adicionais', 'amigopet-wp'); ?></h3>
            
            <div class="apwp-form-row">
                <label class="apwp-form-label" for="residence_type">
                    <?php esc_html_e('Tipo de Residência', 'amigopet-wp'); ?>
                    <span class="required">*</span>
                </label>
                <select class="apwp-form-input" name="residence_type" id="residence_type" required>
                    <option value=""><?php esc_html_e('Selecione', 'amigopet-wp'); ?></option>
                    <option value="house"><?php esc_html_e('Casa', 'amigopet-wp'); ?></option>
                    <option value="apartment"><?php esc_html_e('Apartamento', 'amigopet-wp'); ?></option>
                    <option value="farm"><?php esc_html_e('Chácara/Sítio', 'amigopet-wp'); ?></option>
                </select>
            </div>
            
            <div class="apwp-form-row">
                <label class="apwp-form-label" for="has_other_pets">
                    <?php esc_html_e('Possui outros animais?', 'amigopet-wp'); ?>
                    <span class="required">*</span>
                </label>
                <select class="apwp-form-input" name="has_other_pets" id="has_other_pets" required>
                    <option value=""><?php esc_html_e('Selecione', 'amigopet-wp'); ?></option>
                    <option value="yes"><?php esc_html_e('Sim', 'amigopet-wp'); ?></option>
                    <option value="no"><?php esc_html_e('Não', 'amigopet-wp'); ?></option>
                </select>
            </div>
            
            <div class="apwp-form-row">
                <label class="apwp-form-label" for="reason">
                    <?php esc_html_e('Por que deseja adotar este pet?', 'amigopet-wp'); ?>
                    <span class="required">*</span>
                </label>
                <textarea class="apwp-form-input" name="reason" id="reason" rows="5" required></textarea>
            </div>
        </div>
        
        <div class="apwp-form-actions">
            <button type="submit" class="apwp-form-button">
                <?php esc_html_e('Enviar Solicitação', 'amigopet-wp'); ?>
            </button>
        </div>
    </form>
</div>
