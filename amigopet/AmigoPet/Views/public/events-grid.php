<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template para exibição de eventos no frontend
 */

$apwp_template_vars = get_defined_vars();
$apwp_events = isset($apwp_template_vars['events']) && is_array($apwp_template_vars['events']) ? $apwp_template_vars['events'] : [];
$apwp_total_pages = isset($apwp_template_vars['total_pages']) ? (int) $apwp_template_vars['total_pages'] : 1;
$apwp_current_page = isset($apwp_template_vars['current_page']) ? (int) $apwp_template_vars['current_page'] : 1;
?>

<div class="apwp-events-grid">
    <?php if (!empty($apwp_events)): ?>
        <div class="apwp-events-filters">
            <select id="apwp-events-status-filter">
                <option value="">
                    <?php esc_html_e('Todos os eventos', 'amigopet'); ?>
                </option>
                <option value="upcoming">
                    <?php esc_html_e('Próximos eventos', 'amigopet'); ?>
                </option>
                <option value="ongoing">
                    <?php esc_html_e('Eventos em andamento', 'amigopet'); ?>
                </option>
            </select>
        </div>

        <div class="apwp-events-list">
            <?php foreach ($apwp_events as $apwp_event): ?>
                <div class="apwp-event-card" data-status="<?php echo esc_attr($apwp_event->status); ?>">
                    <div class="apwp-event-date">
                        <span class="apwp-event-day">
                            <?php echo esc_html($apwp_event->day); ?>
                        </span>
                        <span class="apwp-event-month">
                            <?php echo esc_html($apwp_event->month); ?>
                        </span>
                    </div>

                    <div class="apwp-event-info">
                        <h3 class="apwp-event-title">
                            <?php echo esc_html($apwp_event->title); ?>
                        </h3>

                        <div class="apwp-event-meta">
                            <div class="apwp-event-time">
                                <i class="fas fa-clock"></i>
                                <?php echo esc_html($apwp_event->formatted_time); ?>
                            </div>

                            <div class="apwp-event-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo esc_html($apwp_event->location); ?>
                            </div>

                            <div class="apwp-event-slots">
                                <i class="fas fa-users"></i>
                                <?php
                                /* translators: 1: registered slots, 2: total slots */
                                $apwp_slots_label = esc_html__('%1$d/%2$d vagas', 'amigopet');
                                echo esc_html(
                                    sprintf(
                                        $apwp_slots_label,
                                        (int) $apwp_event->registered_slots,
                                        (int) $apwp_event->total_slots
                                    )
                                );
                                ?>
                            </div>
                        </div>

                        <div class="apwp-event-description">
                            <?php echo esc_html(wp_trim_words($apwp_event->description, 20)); ?>
                        </div>

                        <div class="apwp-event-actions">
                            <?php if ($apwp_event->status === 'upcoming' && $apwp_event->has_slots): ?>
                                <button type="button" class="apwp-register-event button"
                                    data-id="<?php echo esc_attr($apwp_event->id); ?>">
                                    <?php esc_html_e('Inscrever-se', 'amigopet'); ?>
                                </button>
                            <?php elseif ($apwp_event->status === 'upcoming'): ?>
                                <span class="apwp-event-full">
                                    <?php esc_html_e('Vagas esgotadas', 'amigopet'); ?>
                                </span>
                            <?php endif; ?>

                            <button type="button" class="apwp-view-event button button-secondary"
                                data-id="<?php echo esc_attr($apwp_event->id); ?>">
                                <?php esc_html_e('Ver detalhes', 'amigopet'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($apwp_total_pages > 1): ?>
            <div class="apwp-events-pagination">
                <?php
                echo wp_kses_post(paginate_links([
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => esc_html__('&laquo; Anterior', 'amigopet'),
                    'next_text' => esc_html__('Próximo &raquo;', 'amigopet'),
                    'total' => $apwp_total_pages,
                    'current' => $apwp_current_page
                ]));
                ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="apwp-no-events">
            <p>
                <?php esc_html_e('Nenhum evento encontrado.', 'amigopet'); ?>
            </p>
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
            <h3>
                <?php esc_html_e('Inscrição no Evento', 'amigopet'); ?>
            </h3>

            <form id="apwp-event-register-form">
                <input type="hidden" name="event_id" id="event_id" value="">

                <div class="apwp-form-row">
                    <label for="name">
                        <?php esc_html_e('Nome', 'amigopet'); ?> <span class="required">*</span>
                    </label>
                    <input type="text" name="name" id="name" required>
                </div>

                <div class="apwp-form-row">
                    <label for="email">
                        <?php esc_html_e('E-mail', 'amigopet'); ?> <span class="required">*</span>
                    </label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="apwp-form-row">
                    <label for="phone">
                        <?php esc_html_e('Telefone', 'amigopet'); ?> <span class="required">*</span>
                    </label>
                    <input type="tel" name="phone" id="phone" required>
                </div>

                <div class="apwp-form-row">
                    <button type="submit" class="button button-primary">
                        <?php esc_html_e('Confirmar Inscrição', 'amigopet'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        // Filtro de status
        $('#apwp-events-status-filter').on('change', function () {
            var status = $(this).val();

            if (status) {
                $('.apwp-event-card').hide()
                    .filter('[data-status="' + status + '"]').show();
            } else {
                $('.apwp-event-card').show();
            }
        });

        // Modal de detalhes
        $('.apwp-view-event').on('click', function () {
            var event_id = $(this).data('id');
            var data = {
                action: 'apwp_get_event_details',
                _ajax_nonce: typeof apwp !== 'undefined' ? apwp.nonce : '',
                event_id: event_id
            };

            $.post(typeof apwp !== 'undefined' ? apwp.ajax_url : '/wp-admin/admin-ajax.php', data, function (response) {
                if (response.success) {
                    $('#apwp-event-modal-content').html(response.data.html);
                    $('#apwp-event-modal').show();
                } else {
                    alert(response.data.message);
                }
            });
        });

        // Modal de inscrição
        $('.apwp-register-event').on('click', function () {
            var event_id = $(this).data('id');
            $('#event_id').val(event_id);
            $('#apwp-register-modal').show();
        });

        // Fechar modais
        $('.apwp-modal-close').on('click', function () {
            $(this).closest('.apwp-modal').hide();
        });

        $(window).on('click', function (e) {
            if ($(e.target).hasClass('apwp-modal')) {
                $('.apwp-modal').hide();
            }
        });

        // Formulário de inscrição
        $('#apwp-event-register-form').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var $submit = $form.find(':submit');

            $submit.prop('disabled', true);

            var data = $form.serialize();
            data += '&action=apwp_register_event';
            data += '&_ajax_nonce=' + (typeof apwp !== 'undefined' ? apwp.nonce : '');

            $.post(typeof apwp !== 'undefined' ? apwp.ajax_url : '/wp-admin/admin-ajax.php', data, function (response) {
                if (response.success) {
                    alert(response.data.message);
                    $('#apwp-register-modal').hide();
                    $form[0].reset();

                    // Atualiza o número de vagas
                    var $eventCard = $('.apwp-event-card[data-id="' + $('#event_id').val() + '"]');
                    $eventCard.find('.apwp-event-slots').html(response.data.slots_html);

                    if (!response.data.has_slots) {
                        $eventCard.find('.apwp-register-event')
                            .replaceWith('<span class="apwp-event-full">Vagas esgotadas</span>');
                    }
                } else {
                    alert(response.data.message);
                }
                $submit.prop('disabled', false);
            }).fail(function () {
                alert('Erro ao realizar inscrição');
                $submit.prop('disabled', false);
            });
        });
    });
</script>