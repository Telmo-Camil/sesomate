document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photo'); // fenêtre de sélection de fichiers
    const profilePreview = document.getElementById('profile-preview'); // image circulaire

    if (photoInput && profilePreview) {
        photoInput.addEventListener('change', function() {
            // Récupérer le fichier sélectionné par l'utilisateur dans l'Explorateur de fichiers.
            const file = this.files[0]; 

            if (file) {
                const reader = new FileReader();

                // Fonction à exécuter une fois la lecture du fichier terminée.
                reader.onload = function(e) {
                    // Insèrer les données du fichier sélectionné dans l'attribut src de l'image (le nom n'a pas d'importance).
                    profilePreview.src = e.target.result;
                };

                // Lire le fichier dans une URL virtuelle (DataURL)
                reader.readAsDataURL(file);
            }
        });
    }
});