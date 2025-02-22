<?php
/**
 * Template para exibição de eventos no frontend
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="apwp-events-grid">
    <?php if (!empty($events)): ?>
        <div class="apwp-events-filters">
            <select id="apwp-events-status-filter">
                <option value=""><?php _e('Todos os eventos', 'amigopet-wp'); ?></option>
                <option value="upcoming"><?php _e('Próximos eventos', 'amigopet-wp'); ?></option>
                <option value="ongoing"><?php _e('Eventos em andamento', 'amigopet-wp'); ?></option>
            </select>
        </div>
        
        <div class="apwp-events-list">
            <?php foreach ($events as $event): ?>
                <div class="apwp-event-card" data-status="<?php echo esc_attr($event->status); ?>">
                    <div class="apwp-event-date">
                        <span class="apwp-event-day"><?php echo esc_html($event->day); ?></span>
                        <span class="apwp-event-month"><?php echo esc_html($event->month); ?></span>
                    </div>
                    
                    <div class="apwp-event-info">
                        <h3 class="apwp-event-title"><?php echo esc_html($event->title); ?></h3>
                        
                        <div class="apwp-event-meta">
                            <div class="apwp-event-time">
                                <i class="fas fa-clock"></i>
                                <?php echo esc_html($event->formatted_time); ?>
                            </div>
                            
                            <div class="apwp-event-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo esc_html($event->location); ?>
                            </div>
                            
                            <div class="apwp-event-slots">
                                <i class="fas fa-users"></i>
                                <?php
                                printf(
                                    /* translators: %1$s: número de vagas preenchidas, %2$s: número total de vagas */
                                    __('%1$s/%2$s vagas', 'amigopet-wp'),
                                    $event->registered_slots,
                                    $event->total_slots
                                );
                                ?>
                            </div>
                        </div>
                        
                        <div class="apwp-event-description">
                            <?php echo wp_trim_words($event->description, 20); ?>
                        </div>
                        
                        <div class="apwp-event-actions">
                            <?php if ($event->status === 'upcoming' && $event->has_slots): ?>
                                <button type="button" class="apwp-register-event button" data-id="<?php echo esc_attr($event->id); ?>">
                                    <?php _e('Inscrever-se', 'amigopet-wp'); ?>
                                </button>
                            <?php elseif ($event->status === 'upcoming'): ?>
                                <span class="apwp-event-full">
                                    <?php _e('Vagas esgotadas', 'amigopet-wp'); ?>
                                </span>
                            <?php endif; ?>
                            
                            <button type="button" class="apwp-view-event button button-secondary" data-id="<?php echo esc_attr($event->id); ?>">
                                <?php _e('Ver detalhes', 'amigopet-wp'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($total_pages > 1): ?>
            <div class="apwp-events-pagination">
                <?php
                echo paginate_links([
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo; Anterior', 'amigopet-wp'),
                    'next_text' => __('Próximo &raquo;', 'amigopet-wp'),
                    'total' => $total_pages,
                    'current' => $current_page
                ]);
                ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="apwp-no-events">
            <p><?php _e('Nenhum evento encontrado.', 'amigopet-wp'); ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de Detalhes do Evento -->
<div id="apwp-event-modal" class="apwp-modal" style="display: none;">
    <div class="apwp-modal-content">
        <span class="apwp-modal-close">&times;</span>
        <div id="apwp-event-modal-content"></div>
    </div>
</div>

<!-- Modal de Inscrição -->
<div id="apwp-register-modal" class="apwp-modal" style="display: none;">
    <div class="apwp-modal-content">
        <span class="apwp-modal-close">&times;</span>
        <div id="apwp-register-modal-content">
            <h3><?php _e('Inscrição no Evento', 'amigopet-wp'); ?></h3>
            
            <form id="apwp-event-register-form">
                <input type="hidden" name="event_id" id="event_id" value="">
                
                <div class="apwp-form-row">
                    <label for="name"><?php _e('Nome', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    <input type="text" name="name" id="name" required>
                </div>
                
                <div class="apwp-form-row">
                    <label for="email"><?php _e('E-mail', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    <input type="email" name="email" id="email" required>
                </div>
                
                <div class="apwp-form-row">
                    <label for="phone"><?php _e('Telefone', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    <input type="tel" name="phone" id="phone" required>
                </div>
                
                <div class="apwp-form-row">
                    <button type="submit" class="button button-primary">
                        <?php _e('Confirmar Inscrição', 'amigopet-wp'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Filtro de status
    $('#apwp-events-status-filter').on('change', function() {
        var status = $(this).val();
        
        if (status) {
            $('.apwp-event-card').hide()
                .filter('[data-status="' + status + '"]').show();
        } else {
            $('.apwp-event-card').show();
        }
    });
    
    // Modal de detalhes
    $('.apwp-view-event').on('click', function() {
        var event_id = $(this).data('id');
        var data = {
            action: 'apwp_get_event_details',
            _ajax_nonce: apwp.nonce,
            event_id: event_id
        };
        
        $.post(apwp.ajax_url, data, function(response) {
            if (response.success) {
                $('#apwp-event-modal-content').html(response.data.html);
                $('#apwp-event-modal').show();
            } else {
                alert(response.data.message);
            }
        });
    });
    
    // Modal de inscrição
    $('.apwp-register-event').on('click', function() {
        var event_id = $(this).data('id');
        $('#event_id').val(event_id);
        $('#apwp-register-modal').show();
    });
    
    // Fechar modais
    $('.apwp-modal-close').on('click', function() {
        $(this).closest('.apwp-modal').hide();
    });
    
    $(window).on('click', function(e) {
        if ($(e.target).hasClass('apwp-modal')) {
            $('.apwp-modal').hide();
        }
    });
    
    // Formulário de inscrição
    $('#apwp-event-register-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submit = $form.find(':submit');
        
        $submit.prop('disabled', true);
        
        var data = $form.serialize();
        data += '&action=apwp_register_event';
        data += '&_ajax_nonce=' + apwp.nonce;
        
        $.post(apwp.ajax_url, data, function(response) {
            if (response.success) {
                alert(response.data.message);
                $('#apwp-register-modal').hide();
                $form[0].reset();
                
                // Atualiza o número de vagas
                var $eventCard = $('.apwp-event-card[data-id="' + $('#event_id').val() + '"]');
                $eventCard.find('.apwp-event-slots').html(response.data.slots_html);
                
                if (!response.data.has_slots) {
                    $eventCard.find('.apwp-register-event')
                        .replaceWith('<span class="apwp-event-full">' + apwp.i18n.slots_full + '</span>');
                }
            } else {
                alert(response.data.message);
            }
            $submit.prop('disabled', false);
        }).fail(function() {
            alert(apwp.i18n.error_registering);
            $submit.prop('disabled', false);
        });
    });
});
</script>
