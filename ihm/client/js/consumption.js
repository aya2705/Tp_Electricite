document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('consumption-form');
    const photoInput = document.getElementById('meter-photo');
    const photoPreview = document.getElementById('photo-preview');
    const previewImage = photoPreview.querySelector('img');
    const selectPhotoBtn = document.getElementById('select-photo-btn');
    const removePhotoBtn = document.getElementById('remove-photo-btn');

    selectPhotoBtn.addEventListener('click', () => photoInput.click());

    photoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImage.src = e.target.result;
                photoPreview.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    removePhotoBtn.addEventListener('click', () => {
        photoInput.value = '';
        previewImage.src = '';
        photoPreview.style.display = 'none';
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);

        try {
            const response = await fetch('../../traitement/submitConsumption.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                alert('Consommation enregistrée avec succès');
                window.location.href = 'dashboard.php';
            } else {
                alert('Erreur: ' + result.message);
            }
        } catch (error) {
            alert('Une erreur est survenue lors de l\'envoi');
            console.error(error);
        }
    });
});
