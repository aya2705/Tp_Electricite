document.addEventListener('DOMContentLoaded', function() {
     // Éléments principaux
     const processButtons = document.querySelectorAll('.action-process');
     const resolveButtons = document.querySelectorAll('.action-resolve');
     const searchInput = document.getElementById('search-claim');
     const responseForm = document.getElementById('response-form');
     
     // Traitement des réclamations
     if (processButtons) {
         processButtons.forEach(btn => {
             btn.addEventListener('click', function() {
                 const claimId = this.getAttribute('data-id');
                 document.getElementById('claim-ref').textContent = `#${claimId}`;
                 openModal('process-claim-modal');
             });
         });
     }
     
   
 });
 
 
 // ??
 // Ouvrir un modal
 function openModal(modalId) {
     const modal = document.getElementById(modalId);
     if (modal) {
         modal.style.display = 'block';
     }
 }
 
// 

 // Fermer un modal
 function closeModal(modalId) {
     const modal = document.getElementById(modalId);
     if (modal) {
         modal.style.display = 'none';
     }
 }
 
  

 
 // Fonction pour formater un prix
 function formatPrice(price) {
     return new Intl.NumberFormat('fr-FR', { 
         style: 'currency', 
         currency: 'XOF'
     }).format(price);
 }
 
 // Configuration des modals
 document.addEventListener('DOMContentLoaded', function() {
     // Fermeture des modals avec le bouton X
     document.querySelectorAll('.modal .close').forEach(closeBtn => {
         closeBtn.addEventListener('click', function() {
             const modal = this.closest('.modal');
             modal.style.display = 'none';
         });
     });
     
     // Fermeture des modals en cliquant à l'extérieur
     window.addEventListener('click', function(e) {
         document.querySelectorAll('.modal').forEach(modal => {
             if (e.target === modal) {
                 modal.style.display = 'none';
             }
         });
     });
 });


//  // Fonction pour remplir les informations dans le modal
 function fillClaimDetails(claimData) {
    document.getElementById('client-name').textContent = claimData.clientName;
    document.getElementById('client-id').textContent = claimData.clientId;
    document.getElementById('client-address').textContent = claimData.clientAddress;
    document.getElementById('client-email').textContent = claimData.clientEmail;
    document.getElementById('client-phone').textContent = claimData.clientPhone;
    document.getElementById('claim-ref').textContent = claimData.claimRef;
    document.getElementById('claim-type').textContent = claimData.claimType;
    document.getElementById('claim-date').textContent = claimData.claimDate;
    document.getElementById('claim-description').textContent = claimData.claimDescription;

    // Afficher les pièces jointes
    let attachmentsContainer = document.querySelector('.attachments');
    attachmentsContainer.innerHTML = ''; // Reset current attachments
    claimData.attachments.forEach(function (attachment) {
        let attachmentElement = document.createElement('div');
        attachmentElement.classList.add('attachment');
        attachmentElement.innerHTML = `<i class="fas fa-image"></i><span>${attachment}</span>`;
        attachmentsContainer.appendChild(attachmentElement);
    });
}

// Exemple d'appel AJAX pour récupérer les données de la réclamation
function loadClaimDetails(claimId) {
    fetch(`/getClaimDetails.php?id=${claimId}`)
        .then(response => response.json())
        .then(data => {
            fillClaimDetails(data);
        })
        .catch(error => console.error('Erreur:', error));
}

// Exemple d'appel pour charger les données de la réclamation avec un ID spécifique
loadClaimDetails(123); // Remplace 123 par l'ID réel de la réclamation




document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("click", function (event) {
        if (event.target.closest(".action-process")) {
            let button = event.target.closest(".action-process");
            let reclamationId = button.getAttribute("data-id").replace("REF-", "");
            let detailsContainer = document.querySelector(".claim-detail-info");

            if (!detailsContainer) {
                console.error("Erreur : le conteneur des détails de réclamation est introuvable.");
                return;
            }

            fetch("../../traitement/reclamationService.php?reclamationId=" + reclamationId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Erreur réseau");
                    }
                    return response.text();
                })
                .then(data => {
                    detailsContainer.innerHTML = data;
                })
                .catch(error => console.error("Erreur :", error));
        }
    });
});

// Informations sur la réclamation
document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("click", function (event) {
        if (event.target.closest(".action-process")) {
            let button = event.target.closest(".action-process");
            let reclamationId = button.getAttribute("data-id").replace("REF-", "");

            // Mettre à jour le champ hidden du formulaire avec l'ID de la réclamation
            document.getElementById("reclamation_id").value = reclamationId;

            let detailsContainer = document.querySelector(".claim-detail-info");

            if (!detailsContainer) {
                console.error("Erreur : le conteneur des détails de réclamation est introuvable.");
                return;
            }

            // Charger les détails de la réclamation
            fetch("../../traitement/reclamationService.php?reclamationId=" + reclamationId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Erreur réseau");
                    }
                    return response.text();
                })
                .then(data => {
                    detailsContainer.innerHTML = data;
                    document.getElementById("process-claim-modal").classList.remove("hidden"); // Afficher le modal
                })
                .catch(error => console.error("Erreur :", error));
        }
    });

    // Fermer le modal quand on clique sur le bouton de fermeture
    document.querySelector(".close").addEventListener("click", function () {
        document.getElementById("process-claim-modal").classList.add("hidden");
    });
});

// pour la pagination

document.addEventListener("DOMContentLoaded", function() {
    // Exemple de réclamations dynamiques (remplacez ceci par vos données réelles)
    

    const itemsPerPage = 5; // Nombre d'éléments par page
    let currentPage = 1; // Page actuelle

    // Fonction pour afficher les réclamations sur la page
    function renderClaims(page) {
        const start = (page - 1) * itemsPerPage;
        const end = page * itemsPerPage;
        const claimsToShow = reclamations.slice(start, end);

        const claimsList = document.getElementById("claims-list");
        claimsList.innerHTML = ''; // Réinitialiser la liste avant de la remplir

        claimsToShow.forEach(claim => {
            const claimItem = document.createElement('div');
            claimItem.classList.add('claim-item');
            claimItem.innerHTML = `
                <div class="claim-icon">
                    <i class="fas ${claim.type === 'fuite_externe' ? 'fa-tint' : 'fa-file-invoice'}"></i>
                </div>
                <div class="claim-content">
                    <h3>${claim.type}</h3>
                    <p>${claim.description}</p>
                    <div class="claim-meta">
                        <div><span class="claim-type">${claim.type}</span></div>
                        <span>Soumise le ${claim.date}</span>
                    </div>
                </div>
            `;
            claimsList.appendChild(claimItem);
        });

        renderPagination();
    }

    // Fonction pour afficher les liens de pagination
    function renderPagination() {
        const totalPages = Math.ceil(reclamations.length / itemsPerPage);
        const pagination = document.getElementById("pagination");
        pagination.innerHTML = ''; // Réinitialiser la pagination

        // Précédent
        if (currentPage > 1) {
            const prevLink = document.createElement('a');
            prevLink.href = "#";
            prevLink.textContent = "« Précédent";
            prevLink.addEventListener("click", function() {
                if (currentPage > 1) {
                    currentPage--;
                    renderClaims(currentPage);
                }
            });
            pagination.appendChild(prevLink);
        }

        // Pages
        for (let i = 1; i <= totalPages; i++) {
            const pageLink = document.createElement('a');
            pageLink.href = "#";
            pageLink.textContent = i;
            if (i === currentPage) {
                pageLink.classList.add('active');
            }
            pageLink.addEventListener("click", function() {
                currentPage = i;
                renderClaims(currentPage);
            });
            pagination.appendChild(pageLink);
        }

        // Suivant
        if (currentPage < totalPages) {
            const nextLink = document.createElement('a');
            nextLink.href = "#";
            nextLink.textContent = "Suivant »";
            nextLink.addEventListener("click", function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderClaims(currentPage);
                }
            });
            pagination.appendChild(nextLink);
        }
    }

    // Initialiser la page avec les réclamations de la première page
    renderClaims(currentPage);
});
