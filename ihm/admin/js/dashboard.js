document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des graphiques
    initCharts();
    
    // Gestion de l'importation de fichiers
    setupImport();
    
    // Gestion des statistiques en temps réel (simulation)
    updateStats();
});

// Initialisation des graphiques avec Chart.js
function initCharts() {
    // Graphique de consommation par région
    const regionChart = document.getElementById('region-consumption-chart');
    if (regionChart) {
        new Chart(regionChart, {
            type: 'bar',
            data: {
                labels: ['Dakar', 'Thiès', 'Saint-Louis', 'Ziguinchor', 'Diourbel'],
                datasets: [{
                    label: 'Consommation moyenne (kWh)',
                    data: [420, 380, 310, 290, 325],
                    backgroundColor: 'rgba(52, 152, 219, 0.7)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'kWh'
                        }
                    }
                }
            }
        });
    }
    
    // Graphique d'évolution des factures
    const invoicesChart = document.getElementById('invoices-chart');
    if (invoicesChart) {
        new Chart(invoicesChart, {
            type: 'line',
            data: {
                labels: ['Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre'],
                datasets: [{
                    label: 'Montant facturé (M XOF)',
                    data: [38.2, 39.1, 40.5, 41.2, 42.0, 42.5],
                    borderColor: 'rgba(46, 204, 113, 1)',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'M XOF'
                        }
                    }
                }
            }
        });
    }
}

// Gestion de l'importation de fichiers
function setupImport() {
    const importZone = document.getElementById('import-zone');
    const fileUpload = document.getElementById('file-upload');
    const importBtn = document.getElementById('import-consumption-btn');
    const importModal = document.getElementById('import-modal');
    const importForm = document.getElementById('import-form');
    const closeBtn = importModal ? importModal.querySelector('.close') : null;
    
    // Zone de glisser-déposer
    if (importZone && fileUpload) {
        importZone.addEventListener('click', function() {
            fileUpload.click();
        });
        
        importZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            importZone.style.backgroundColor = '#e9ecef';
            importZone.style.borderColor = 'var(--secondary-color)';
        });
        
        importZone.addEventListener('dragleave', function() {
            importZone.style.backgroundColor = '#f8f9fa';
            importZone.style.borderColor = '#ccc';
        });
        
        importZone.addEventListener('drop', function(e) {
            e.preventDefault();
            importZone.style.backgroundColor = '#f8f9fa';
            importZone.style.borderColor = '#ccc';
            
            if (e.dataTransfer.files.length) {
                fileUpload.files = e.dataTransfer.files;
                const fileName = e.dataTransfer.files[0].name;
                importZone.querySelector('p').textContent = `Fichier sélectionné: ${fileName}`;
            }
        });
        
        fileUpload.addEventListener('change', function() {
            if (this.files.length) {
                const fileName = this.files[0].name;
                importZone.querySelector('p').textContent = `Fichier sélectionné: ${fileName}`;
            }
        });
    }
    
    // Bouton d'importation
    if (importBtn && importModal) {
        importBtn.addEventListener('click', function() {
            openModal('import-modal');
        });
    }
    
    // Fermeture du modal
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            closeModal('import-modal');
        });
    }
    
    // Soumission du formulaire d'importation
    if (importForm) {
        importForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simuler une importation
            showAlert('Importation réussie. 1245 enregistrements traités.', 'success');
            
            closeModal('import-modal');
            
            // Réinitialiser la zone d'importation
            if (importZone) {
                importZone.querySelector('p').textContent = 'Glisser-déposer votre fichier ici ou cliquer pour parcourir';
            }
            
            // Réinitialiser le formulaire
            importForm.reset();
        });
    }
}

// Mise à jour simulée des statistiques
function updateStats() {
    // Cette fonction pourrait être utilisée pour mettre à jour les statistiques 
    // en temps réel via des appels AJAX dans une application réelle
    
    // Simulation d'une mise à jour toutes les 30 secondes
    setInterval(function() {
        const statValues = document.querySelectorAll('.stat-card .value');
        
        if (statValues.length >= 4) {
            // Augmenter légèrement le nombre de clients
            let clients = parseInt(statValues[0].textContent.replace(/,/g, ''));
            clients += Math.floor(Math.random() * 3);
            statValues[0].textContent = clients.toLocaleString();
            
            // Augmenter légèrement le montant facturé
            let billing = parseFloat(statValues[1].textContent);
            billing += 0.1 * Math.random();
            statValues[1].textContent = billing.toFixed(1) + ' M';
            
            // Augmenter la consommation
            let consumption = parseInt(statValues[2].textContent.replace(/,/g, ''));
            consumption += Math.floor(Math.random() * 100);
            statValues[2].textContent = consumption.toLocaleString();
            
            // Varier le nombre de réclamations
            let claims = parseInt(statValues[3].textContent);
            claims += Math.floor(Math.random() * 3) - 1;
            claims = Math.max(0, claims);
            statValues[3].textContent = claims;
        }
    }, 30000);
}

// Fonction d'aide pour afficher une alerte
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    const mainContent = document.querySelector('.main-content');
    mainContent.insertBefore(alertDiv, mainContent.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
