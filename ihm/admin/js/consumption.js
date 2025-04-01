document.addEventListener('DOMContentLoaded', function() {
    // Gestion de période de saisie
    const periodToggle = document.getElementById('period-toggle');
    const configBtn = document.getElementById('config-period-btn');
    const periodForm = document.getElementById('period-form');
    
    // Toggle période active/inactive
    if (periodToggle) {
        periodToggle.addEventListener('change', function() {
            const status = document.querySelector('.status-indicator');
            if (status) {
                if (this.checked) {
                    status.className = 'status-indicator status-active';
                    showAlert('Période de saisie activée', 'success');
                } else {
                    status.className = 'status-indicator status-inactive';
                    showAlert('Période de saisie désactivée', 'warning');
                }
            }
        });
    }
    
    // Configuration de la période
    if (configBtn) {
        configBtn.addEventListener('click', function() {
            openModal('config-period-modal');
        });
    }
    
    // Enregistrement des paramètres
    if (periodForm) {
        periodForm.addEventListener('submit', function(e) {
            e.preventDefault();
            showAlert('Période configurée', 'success');
            closeModal('config-period-modal');
        });
    }
    
    // Gestion simplifiée des anomalies
    document.querySelectorAll('.view-anomaly').forEach(btn => {
        btn.addEventListener('click', function() {
            openModal('view-anomaly-modal');
        });
    });
    
    document.querySelectorAll('.resolve-anomaly').forEach(btn => {
        btn.addEventListener('click', function() {
            showAlert('Anomalie résolue', 'success');
        });
    });
    
    const anomalyActionBtn = document.getElementById('save-anomaly-action');
    if (anomalyActionBtn) {
        anomalyActionBtn.addEventListener('click', function() {
            showAlert('Action enregistrée', 'success');
            closeModal('view-anomaly-modal');
        });
    }
});
