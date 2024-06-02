document.addEventListener('DOMContentLoaded', () => {

    // Récupération de tous les éléments de la classe 'card'
    const items = document.querySelectorAll('.liste-activites li.card');
    console.log(items);

    // Pour chaque élément, on récupère les classes et on garde celle qui n'est pas 'card'
    items.forEach(item => {
        const classes = Array.from(item.classList);
        const sportClass = classes.find(cls => cls !== 'card');

        // Si on a trouvé une classe différente de 'card', on l'utilise pour afficher le background-image
        if (sportClass) {
            item.style.backgroundImage = `
                linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('../assets/image_${sportClass}.jpg')
            `;
            item.style.backgroundSize = 'cover';
            item.style.backgroundPosition = 'center';
        }
    });
});