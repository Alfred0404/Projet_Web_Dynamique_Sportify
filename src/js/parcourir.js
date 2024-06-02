document.addEventListener('DOMContentLoaded', () => {
    // Récupération de tous les éléments de la classe 'card'
    const items = document.querySelectorAll('.liste-activites li.card');
    console.log(items);
    items.forEach(item => {

        // Pour chaque élément, on récupère les classes et on garde celle qui n'est pas 'card' ni 'card-activite'
        const classes = Array.from(item.classList);
        console.log(classes);
        const sportClass = classes.find(cls => cls !== 'card-activite' && cls !== 'card');

        // Si on a trouvé une classe différente de 'card' et 'card-activite', on l'utilise pour afficher le background-image
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

const activites = document.getElementsByClassName("card");
if (activites) {
    console.log("activites : ", activites);

    // trier les activités par catégorie
    function sort_activities() {
        const boutons = Array.from(document.getElementsByClassName('bouton-activite'));
        console.log(boutons);
        boutons.forEach((bouton) => {
            bouton.addEventListener('click', (e) => {
                console.log(bouton);
                console.log(bouton.id)
                const classe = bouton.id;
                console.log("classe a afficher : ", classe);

                // afficher les activités correspondantes
                Array.from(activites).forEach((element) => {
                    if (element.classList.contains(classe)) {
                        console.log("afficher : ", element);
                        element.style.display = 'block';
                        element.classList.add("show");
                    } else {
                        console.log("cacher : ", element);
                        element.style.display = 'none';
                        element.classList.add("hide");
                    }
                });
            });
        });
    }
} else {
    console.error("L'élément avec la classe 'liste-activites' n'a pas été trouvé.");
}