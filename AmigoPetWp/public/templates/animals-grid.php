<?php
/**
 * Template para exibição do grid de animais
 *
 * @package AmigoPet_Wp
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="apwp-filters">
    <div class="apwp-filters-grid">
        <div class="apwp-filter-group">
            <label class="apwp-filter-label" for="species">Espécie</label>
            <select class="apwp-filter-select" name="species" id="species">
                <option value="">Todas</option>
                <option value="dog">Cachorro</option>
                <option value="cat">Gato</option>
                <option value="other">Outros</option>
            </select>
        </div>

        <div class="apwp-filter-group">
            <label class="apwp-filter-label" for="size">Porte</label>
            <select class="apwp-filter-select" name="size" id="size">
                <option value="">Todos</option>
                <option value="small">Pequeno</option>
                <option value="medium">Médio</option>
                <option value="large">Grande</option>
            </select>
        </div>

        <div class="apwp-filter-group">
            <label class="apwp-filter-label" for="age">Idade</label>
            <select class="apwp-filter-select" name="age" id="age">
                <option value="">Todas</option>
                <option value="puppy">Filhote</option>
                <option value="adult">Adulto</option>
                <option value="senior">Idoso</option>
            </select>
        </div>

        <div class="apwp-filter-group">
            <label class="apwp-filter-label" for="gender">Sexo</label>
            <select class="apwp-filter-select" name="gender" id="gender">
                <option value="">Todos</option>
                <option value="male">Macho</option>
                <option value="female">Fêmea</option>
            </select>
        </div>
    </div>
</div>

<div class="apwp-messages"></div>

<div class="apwp-animals-grid">
    <?php if (!empty($animals)) : ?>
        <?php foreach ($animals as $animal) : ?>
            <div class="apwp-animal-card" data-animal-id="<?php echo esc_attr($animal->id); ?>">
                <div class="apwp-animal-image">
                    <?php if ($animal->photo_url) : ?>
                        <img src="<?php echo esc_url($animal->photo_url); ?>" alt="<?php echo esc_attr($animal->name); ?>">
                    <?php else : ?>
                        <img src="<?php echo esc_url(APWP_PLUGIN_URL . 'public/images/no-photo.png'); ?>" alt="Sem foto">
                    <?php endif; ?>
                    
                    <div class="apwp-animal-status apwp-status-<?php echo esc_attr($animal->status); ?>">
                        <?php echo esc_html(APWP_Core::get_status_label($animal->status)); ?>
                    </div>
                </div>

                <div class="apwp-animal-info">
                    <h3 class="apwp-animal-name"><?php echo esc_html($animal->name); ?></h3>
                    
                    <div class="apwp-animal-details">
                        <div class="apwp-animal-detail">
                            <i class="fas fa-paw"></i>
                            <?php echo esc_html(APWP_Core::get_species_label($animal->species)); ?>
                        </div>
                        
                        <div class="apwp-animal-detail">
                            <i class="fas fa-ruler-vertical"></i>
                            <?php echo esc_html(APWP_Core::get_size_label($animal->size)); ?>
                        </div>
                        
                        <div class="apwp-animal-detail">
                            <i class="fas fa-birthday-cake"></i>
                            <?php echo esc_html(APWP_Core::get_age_label($animal->age)); ?>
                        </div>
                        
                        <div class="apwp-animal-detail">
                            <?php if ($animal->gender === 'male') : ?>
                                <i class="fas fa-mars"></i>
                                Macho
                            <?php else : ?>
                                <i class="fas fa-venus"></i>
                                Fêmea
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="apwp-no-results">
            <p>Nenhum animal encontrado com os filtros selecionados.</p>
        </div>
    <?php endif; ?>
</div>

<?php if ($total_pages > 1) : ?>
    <div class="apwp-pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
            <a href="#" class="<?php echo $current_page === $i ? 'current' : ''; ?>" data-page="<?php echo esc_attr($i); ?>">
                <?php echo esc_html($i); ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
