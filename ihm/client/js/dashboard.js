document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le graphique de consommation
    initConsumptionChart();
    
    // Gestion du bouton de saisie de consommation
    const inputConsumptionBtn = document.getElementById('input-consumption-btn');
    if (inputConsumptionBtn) {
        inputConsumptionBtn.addEventListener('click', function() {
            window.location.href = 'consumption.html';
        });
    }
});

// Initialisation du graphique de consommation mensuelle
function initConsumptionChart() {
    const ctx = document.getElementById('consumption-chart');
    if (!ctx) return;
    
    // Données de consommation des derniers mois
    const data = {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov'],
        datasets: [{
            label: 'Consommation (kWh)',
            data: [290, 300, 310, 305, 315, 325, 340, 335, 330, 315, 325],
            backgroundColor: 'rgba(52, 152, 219, 0.2)',
            borderColor: 'rgba(52, 152, 219, 1)',
            borderWidth: 2
        }]
    };
    
    new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'kWh'
                    }
                }
            }
        }
    });
}