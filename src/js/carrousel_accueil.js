$(document).ready(function () {
    var $carrousel = $('#carrousel'), // on cible le bloc du carrousel
        $link = $('#carrousel .card-activite, #carrousel .show'), // on cible les images contenues dans le carrousel
        index_link = $link.length - 1, // on définit l'index du dernier élément
        i = 0, // on initialise un compteur
        $current_link = $link.eq(i); // enfin, on cible l'image courante, qui possède l'index i (0 pour l'instant)

    $link.css('display', 'none'); // on cache les images
    $current_link.css('display', 'block'); // on affiche seulement l'image courante

    if ($link.length > 1) {
        $carrousel.append('<div class="controls"> <span class="prev">Précédent</span> <span class="next">Suivant</span> </div>');
    }

    // Fonction pour afficher une image
    function showImage(index) {
        $link.css('display', 'none'); // on cache les images
        $current_link = $link.eq(index); // on définit la nouvelle image
        $current_link.css('display', 'block'); // puis on l'affiche
    }

    $('.next').click(function () { // image suivante
        i++; // on incrémente le compteur
        if (i > index_link) {
            i = 0; // revenir au début
        }
        showImage(i);
    });

    $('.prev').click(function () { // image précédente
        i--; // on décrémente le compteur
        if (i < 0) {
            i = index_link; // revenir à la fin
        }
        showImage(i);
    });

    // Fonction pour faire défiler les images
    function slide_link() {
        setTimeout(function () { // on utilise une fonction anonyme
            i++; // on incrémente le compteur
            if (i > index_link) {
                i = 0; // revenir au début
            }
            showImage(i);
            slide_link(); // on relance la fonction à la fin
        }, 4000); // on définit l'intervalle à 4000 millisecondes (4s)
    }
    slide_link(); // on lance la fonction une première fois
});
