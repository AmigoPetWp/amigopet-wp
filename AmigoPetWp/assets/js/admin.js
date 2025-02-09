/**
 * Scripts do admin
 */
jQuery(document).ready(function($) {
    // Inicializa Select2 nos campos de workflow
    if ($('.apwp-settings').length) {
        $('#adoption_workflow, #volunteer_workflow, #donation_workflow').select2({
            placeholder: apwp.i18n.select_steps,
            allowClear: true,
            theme: 'default',
            width: '100%',
            maximumSelectionLength: 10,
            language: {
                noResults: function() {
                    return apwp.i18n.no_results;
                },
                maximumSelected: function() {
                    return apwp.i18n.max_steps;
                }
            }
        }).on('select2:select select2:unselect', function(e) {
            // Reordena os itens selecionados
            const values = $(this).val() || [];
            updateWorkflowSteps($(this).attr('id').replace('_workflow', ''), values);
        });
        
        // Inicializa os passos do workflow
        ['adoption', 'volunteer', 'donation'].forEach(function(type) {
            const values = $(`#${type}_workflow`).val() || [];
            updateWorkflowSteps(type, values);
        });
    }
    
    // Atualiza a visualização dos passos do workflow
    function updateWorkflowSteps(type, steps) {
        const container = $(`#${type}_workflow`).closest('td').find('.apwp-workflow-steps');
        if (!container.length) {
            $(`#${type}_workflow`).closest('td').append('<div class="apwp-workflow-steps"></div>');
        }
        
        const stepsHtml = steps.map(function(step, index) {
            const stepInfo = getStepInfo(type, step);
            return `
                <div class="apwp-workflow-step">
                    <div class="apwp-workflow-step-number">${index + 1}</div>
                    <div class="apwp-workflow-step-content">
                        <div class="apwp-workflow-step-title">${stepInfo.title}</div>
                        <div class="apwp-workflow-step-description">${stepInfo.description}</div>
                    </div>
                </div>
            `;
        }).join('');
        
        $(`#${type}_workflow`).closest('td').find('.apwp-workflow-steps').html(
            steps.length ? stepsHtml : ''
        );
    }
    
    // Retorna informações sobre cada passo do workflow
    function getStepInfo(type, step) {
        const info = {
            adoption: {
                form: {
                    title: apwp.i18n.adoption_form,
                    description: apwp.i18n.adoption_form_desc
                },
                review: {
                    title: apwp.i18n.adoption_review,
                    description: apwp.i18n.adoption_review_desc
                },
                interview: {
                    title: apwp.i18n.adoption_interview,
                    description: apwp.i18n.adoption_interview_desc
                },
                home_visit: {
                    title: apwp.i18n.adoption_home_visit,
                    description: apwp.i18n.adoption_home_visit_desc
                },
                contract: {
                    title: apwp.i18n.adoption_contract,
                    description: apwp.i18n.adoption_contract_desc
                },
                followup: {
                    title: apwp.i18n.adoption_followup,
                    description: apwp.i18n.adoption_followup_desc
                }
            },
            volunteer: {
                form: {
                    title: apwp.i18n.volunteer_form,
                    description: apwp.i18n.volunteer_form_desc
                },
                interview: {
                    title: apwp.i18n.volunteer_interview,
                    description: apwp.i18n.volunteer_interview_desc
                },
                training: {
                    title: apwp.i18n.volunteer_training,
                    description: apwp.i18n.volunteer_training_desc
                },
                trial: {
                    title: apwp.i18n.volunteer_trial,
                    description: apwp.i18n.volunteer_trial_desc
                },
                evaluation: {
                    title: apwp.i18n.volunteer_evaluation,
                    description: apwp.i18n.volunteer_evaluation_desc
                }
            },
            donation: {
                form: {
                    title: apwp.i18n.donation_form,
                    description: apwp.i18n.donation_form_desc
                },
                payment: {
                    title: apwp.i18n.donation_payment,
                    description: apwp.i18n.donation_payment_desc
                },
                receipt: {
                    title: apwp.i18n.donation_receipt,
                    description: apwp.i18n.donation_receipt_desc
                },
                thanks: {
                    title: apwp.i18n.donation_thanks,
                    description: apwp.i18n.donation_thanks_desc
                },
                report: {
                    title: apwp.i18n.donation_report,
                    description: apwp.i18n.donation_report_desc
                }
            }
        };
        
        return info[type][step] || {
            title: step,
            description: ''
        };
    }
    // Inicializa os gráficos se estiver na página do dashboard
    if ($('#adoptions-chart').length && $('#donations-chart').length) {
        // Gráfico de adoções
        const adoptionsChart = new Chart($('#adoptions-chart'), {
            type: 'line',
            data: {
                labels: [], // Será preenchido com os meses
                datasets: [{
                    label: apwp.i18n.adoptions,
                    data: [], // Será preenchido com os dados
                    borderColor: '#2271b1',
                    backgroundColor: 'rgba(34, 113, 177, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        // Gráfico de doações
        const donationsChart = new Chart($('#donations-chart'), {
            type: 'line',
            data: {
                labels: [], // Será preenchido com os meses
                datasets: [{
                    label: apwp.i18n.donations,
                    data: [], // Será preenchido com os dados
                    borderColor: '#2ea2cc',
                    backgroundColor: 'rgba(46, 162, 204, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('pt-BR', {
                                    style: 'currency',
                                    currency: 'BRL'
                                }).format(value);
                            }
                        }
                    }
                }
            }
        });
        
        // Carrega os dados dos gráficos
        const start = moment().subtract(11, 'months').startOf('month').format('YYYY-MM-DD');
        const end = moment().endOf('month').format('YYYY-MM-DD');
        
        // Adoções
        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: {
                action: 'apwp_get_reports',
                _ajax_nonce: apwp.nonce,
                type: 'adoptions',
                start: start,
                end: end
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    adoptionsChart.data.labels = data.map(item => item.month);
                    adoptionsChart.data.datasets[0].data = data.map(item => item.total);
                    adoptionsChart.update();
                }
            }
        });
        
        // Doações
        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: {
                action: 'apwp_get_reports',
                _ajax_nonce: apwp.nonce,
                type: 'donations',
                start: start,
                end: end
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    donationsChart.data.labels = data.map(item => item.month);
                    donationsChart.data.datasets[0].data = data.map(item => item.total);
                    donationsChart.update();
                }
            }
        });
    }
    
    // Carrega as últimas atividades se estiver na página do dashboard
    if ($('#recent-activities').length) {
        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: {
                action: 'apwp_get_recent_activities',
                _ajax_nonce: apwp.nonce
            },
            success: function(response) {
                if (response.success) {
                    const activities = response.data;
                    const container = $('#recent-activities');
                    
                    if (activities.length > 0) {
                        const list = $('<ul class="apwp-activities-list"></ul>');
                        
                        activities.forEach(function(activity) {
                            list.append(`
                                <li class="apwp-activity-item">
                                    <span class="apwp-activity-time">
                                        ${activity.time}
                                    </span>
                                    <span class="apwp-activity-text">
                                        ${activity.text}
                                    </span>
                                </li>
                            `);
                        });
                        
                        container.html(list);
                    } else {
                        container.html(`
                            <p class="apwp-no-activities">
                                ${apwp.i18n.no_activities}
                            </p>
                        `);
                    }
                }
            }
        });
    }
});
