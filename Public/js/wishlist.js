
   
    const heartButtons = document.querySelectorAll('.btn-heart');

    heartButtons.forEach(button => {
        button.addEventListener('click', function() {
            
           
            const offerId = this.dataset.id;
            
            
            const isActive = this.classList.contains('active');

            
            this.classList.toggle('active');

       
            const url = isActive ? `/wishlist/delete/${offerId}` : `/wishlist/add/${offerId}`;

            
            fetch(url)
                .then(response => {
                    
                    if (!response.ok) {
                        
                        this.classList.toggle('active');
                        alert("Vous devez être connecté pour ajouter une offre à votre wishlist !");
                        window.location.href = "/connexion"; 
                    }
                })
                .catch(error => {
                    console.error("Erreur de connexion au serveur", error);
                });
        });
    });