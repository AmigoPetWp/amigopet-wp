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

<div class="pr-filters">
    <div class="pr-filters-grid">
        <div class="pr-filter-group">
            <label class="pr-filter-label" for="species">Espécie</label>
            <select class="pr-filter-select" name="species" id="species">
                <option value="">Todas</option>
                <option value="dog">Cachorro</option>
                <option value="cat">Gato</option>
                <option value="other">Outros</option>
            </select>
        </div>

        <div class="pr-filter-group">
            <label class="pr-filter-label" for="size">Porte</label>
            <select class="pr-filter-select" name="size" id="size">
                <option value="">Todos</option>
                <option value="small">Pequeno</option>
                <option value="medium">Médio</option>
                <option value="large">Grande</option>
            </select>
        </div>

        <div class="pr-filter-group">
            <label class="pr-filter-label" for="age">Idade</label>
            <select class="pr-filter-select" name="age" id="age">
                <option value="">Todas</option>
                <option value="puppy">Filhote</option>
                <option value="adult">Adulto</option>
                <option value="senior">Idoso</option>
            </select>
        </div>

        <div class="pr-filter-group">
            <label class="pr-filter-label" for="gender">Sexo</label>
            <select class="pr-filter-select" name="gender" id="gender">
                <option value="">Todos</option>
                <option value="male">Macho</option>
                <option value="female">Fêmea</option>
            </select>
        </div>
    </div>
</div>

<div class="pr-messages"></div>

<div class="pr-animals-grid">
    <?php if (!empty($animals)) : ?>
        <?php foreach ($animals as $animal) : ?>
            <div class="pr-animal-card" data-animal-id="<?php echo esc_attr($animal->id); ?>">
                <div class="pr-animal-image">
                    <?php if ($animal->photo_url) : ?>
                        <img src="<?php echo esc_url($animal->photo_url); ?>" alt="<?php echo esc_attr($animal->name); ?>">
                    <?php else : ?>
                        <img src="<?php echo esc_url(APWP_PLUGIN_URL . 'public/images/no-photo.png'); ?>" alt="Sem foto">
                    <?php endif; ?>
                    
                    <div class="pr-animal-status pr-status-<?php echo esc_attr($animal->status); ?>">
                        <?php echo esc_html(APWP_Core::get_status_label($animal->status)); ?>
                    </div>
                </div>

                <div class="pr-animal-info">
                    <h3 class="pr-animal-name"><?php echo esc_html($animal->name); ?></h3>
                    
                    <div class="pr-animal-details">
                        <div class="pr-animal-detail">
                            <i class="fas fa-paw"></i>
                            <?php echo esc_html(APWP_Core::get_species_label($animal->species)); ?>
                        </div>
                        
                        <div class="pr-animal-detail">
                            <i class="fas fa-ruler-vertical"></i>
                            <?php echo esc_html(APWP_Core::get_size_label($animal->size)); ?>
                        </div>
                        
                        <div class="pr-animal-detail">
                            <i class="fas fa-birthday-cake"></i>
                            <?php echo esc_html(APWP_Core::get_age_label($animal->age)); ?>
                        </div>
                        
                        <div class="pr-animal-detail">
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
        <div class="pr-no-results">
            <p>Nenhum animal encontrado com os filtros selecionados.</p>
        </div>
    <?php endif; ?>
</div>

<?php if ($total_pages > 1) : ?>
    <div class="pr-pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
            <a href="#" class="<?php echo $current_page === $i ? 'current' : ''; ?>" data-page="<?php echo esc_attr($i); ?>">
                <?php echo esc_html($i); ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
