document.addEventListener('DOMContentLoaded', function() {
    // Éléments DOM essentiels
    const fileInput = document.getElementById('meter-photo');
    const selectPhotoBtn = document.getElementById('select-photo-btn');
    const photoPreview = document.getElementById('photo-preview');
    const previewImg = photoPreview ? photoPreview.querySelector('img') : null;
    const removePhotoBtn = document.getElementById('remove-photo-btn');
    const calculateBtn = document.getElementById('calculate-btn');
    const currentValueInput = document.getElementById('current-value');
    const estimationSummary = document.getElementById('estimation-summary');
    const consumptionForm = document.getElementById('consumption-form');
    
    // Récupérer la valeur précédente dynamiquement
    const PREVIOUS_VALUE = currentValueInput ? parseFloat(currentValueInput.dataset.previousValue) : 0;
    
    // Constantes de tarification
    const TARIFS = {
        tranche1: { max: 100, prix: 100 },   // 0-100 kWh à 100 MAD/kWh
        tranche2: { max: 300, prix: 90 },    // 101-300 kWh à 90 MAD/kWh
        tranche3: { prix: 80 }                // > 300 kWh à 80 MAD/kWh
    };
    const TVA_RATE = 0.18;
    
    // Gestion de l'upload de photo
    if (selectPhotoBtn && fileInput) {
        selectPhotoBtn.addEventListener('click', () => fileInput.click());
    }
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewImg) {
                        previewImg.src = e.target.result;
                        if (photoPreview) photoPreview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }
    
    if (removePhotoBtn) {
        removePhotoBtn.addEventListener('click', function() {
            if (fileInput) fileInput.value = '';
            if (photoPreview) photoPreview.style.display = 'none';
            if (previewImg) previewImg.src = '';
        });
    }
    
    // Calcul de l'estimation de facturation
    if (calculateBtn) {
        calculateBtn.addEventListener('click', function() {
            if (!currentValueInput) return;
            
            const currentValue = parseFloat(currentValueInput.value);
            
            if (!currentValue || currentValue <= PREVIOUS_VALUE) {
                showAlert('Veuillez saisir une valeur supérieure au relevé précédent', 'warning');
                return;
            }
            
            const consumption = currentValue - PREVIOUS_VALUE;
            let priceHT = calculerPrixHT(consumption);
            const tva = priceHT * TVA_RATE;
            const priceTTC = priceHT + tva;
            
            // Mettre à jour l'interface
            document.getElementById('consumption-value').textContent = `${consumption} kWh`;
            document.getElementById('price-ht').textContent = formatPrice(priceHT);
            document.getElementById('price-tva').textContent = formatPrice(tva);
            document.getElementById('price-ttc').textContent = formatPrice(priceTTC);
            
            if (estimationSummary) estimationSummary.style.display = 'block';
        });
    }
    
    // Calculer le prix HT selon les tranches
    function calculerPrixHT(consumption) {
        let priceHT = 0;
        
        if (consumption <= TARIFS.tranche1.max) {
            priceHT = consumption * TARIFS.tranche1.prix;
        } else if (consumption <= TARIFS.tranche2.max) {
            priceHT = TARIFS.tranche1.max * TARIFS.tranche1.prix + 
                     (consumption - TARIFS.tranche1.max) * TARIFS.tranche2.prix;
        } else {
            priceHT = TARIFS.tranche1.max * TARIFS.tranche1.prix + 
                     (TARIFS.tranche2.max - TARIFS.tranche1.max) * TARIFS.tranche2.prix +
                     (consumption - TARIFS.tranche2.max) * TARIFS.tranche3.prix;
        }
        
        return priceHT;
    }
    
    // Fonction pour afficher la photo en modal
    window.openPhotoModal = function(src) {
        const modalImage = document.getElementById('modal-image');
        if (modalImage) modalImage.src = src;
        openModal('photo-modal');
    };
});

// Fonction pour formater le prix si non définie dans main.js
if (typeof formatPrice !== 'function') {
    function formatPrice(price) {
        return new Intl.NumberFormat('fr-FR', { 
            style: 'currency', 
            currency: 'MAD',
            maximumFractionDigits: 0
        }).format(price);
    }
}