document.addEventListener('DOMContentLoaded', function() {
    // Référence au modal
    const modal = document.getElementById('add-client-modal');
    const addClientBtn = document.getElementById('add-client-btn');
    const closeBtn = modal.querySelector('.close');
    const addClientForm = document.getElementById('add-client-form');
    
    // Ouverture du modal
    if (addClientBtn) {
        addClientBtn.addEventListener('click', function() {
            document.querySelector('#add-client-modal .modal-header h2').textContent = 'Ajouter un nouveau client';
            modal.style.display = 'block';
            addClientForm.reset();
        });
    }
    
    // Fermeture du modal
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }
    
    // Clic en dehors du modal
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Gestion des actions sur les clients
    setupClientActions();
    
    // Recherche et filtrage
    setupFilters();
    
    // Soumission du formulaire d'ajout/modification
    if (addClientForm) {
        addClientForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const isEditing = document.querySelector('#add-client-modal .modal-header h2').textContent.includes('Modifier');
            const message = isEditing 
                ? 'Client modifié avec succès' 
                : 'Nouveau client ajouté avec succès';
                
            showAlert(message, 'success');
            modal.style.display = 'none';
            addClientForm.reset();
        });
    }
});

// Configuration des actions sur les lignes clients
function setupClientActions() {
    // Voir le client
    document.querySelectorAll('.client-actions .view').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const clientId = row.cells[0].textContent;
            const clientName = row.cells[1].textContent;
            
            alert(`Affichage des détails du client: ${clientName} (${clientId})`);
            // Dans une implémentation réelle, on redirigerait vers une page de détails
        });
    });
    
    // Modifier le client
    document.querySelectorAll('.client-actions .edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const clientName = row.cells[1].textContent;
            const clientAddress = row.cells[2].textContent;
            const clientPhone = row.cells[3].textContent;
            
            // Remplir le formulaire
            document.getElementById('client-name').value = clientName;
            document.getElementById('client-address').value = clientAddress;
            document.getElementById('client-phone').value = clientPhone;
            document.getElementById('client-email').value = clientName.toLowerCase().replace(' ', '.') + '@example.com';
            
            // Changer le titre du modal
            document.querySelector('#add-client-modal .modal-header h2').textContent = `Modifier le client: ${clientName}`;
            
            // Afficher le modal
            document.getElementById('add-client-modal').style.display = 'block';
        });
    });
    
    // Supprimer le client
    document.querySelectorAll('.client-actions .delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const clientId = row.cells[0].textContent;
            const clientName = row.cells[1].textContent;
            
            if (confirm(`Voulez-vous vraiment supprimer le client ${clientName} (${clientId}) ?`)) {
                row.remove();
                showAlert(`Client ${clientName} supprimé avec succès`, 'success');
            }
        });
    });
}

// Configuration des filtres de recherche
function setupFilters() {
    const searchInput = document.getElementById('search-client');
    const statusFilter = document.getElementById('status-filter');
    const regionFilter = document.getElementById('region-filter');
    
    function filterClients() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusTerm = statusFilter.value.toLowerCase();
        const regionTerm = regionFilter.value.toLowerCase();
        
        document.querySelectorAll('.client-table tbody tr').forEach(row => {
            const clientName = row.cells[1].textContent.toLowerCase();
            const clientAddress = row.cells[2].textContent.toLowerCase();
            const clientStatus = row.cells[4].textContent.toLowerCase();
            
            // Dans un cas réel, il faudrait avoir une colonne région
            // Ici on simule en déduisant de l'adresse
            let clientRegion = 'dakar'; // valeur par défaut
            if (clientAddress.includes('paris')) {
                clientRegion = 'dakar';
            } else if (clientAddress.includes('lyon')) {
                clientRegion = 'thies';
            } else if (clientAddress.includes('marseille')) {
                clientRegion = 'saint-louis';
            }
            
            const matchesSearch = searchTerm === '' || 
                              clientName.includes(searchTerm) || 
                              clientAddress.includes(searchTerm);
            
            const matchesStatus = statusTerm === '' || clientStatus.includes(statusTerm);
            
            const matchesRegion = regionTerm === '' || clientRegion === regionTerm;
            
            if (matchesSearch && matchesStatus && matchesRegion) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    if (searchInput) searchInput.addEventListener('input', filterClients);
    if (statusFilter) statusFilter.addEventListener('change', filterClients);
    if (regionFilter) regionFilter.addEventListener('change', filterClients);
}

// Afficher une alerte
function showAlert(message, type) {
    const alertMessage = document.createElement('div');
    alertMessage.className = `alert alert-${type}`;
    alertMessage.innerHTML = message;
    
    const mainContent = document.querySelector('.main-content');
    mainContent.insertBefore(alertMessage, mainContent.firstChild);
    
    setTimeout(() => {
        alertMessage.remove();
    }, 3000);
}
