document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('.liste-activites li.card');
    items.forEach(item => {
        const classes = Array.from(item.classList);
        const sportClass = classes.find(cls => cls !== 'card');
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

// Thanks to Pavel Dobryakov //

const ANGLE = 40;

let card = document.querySelectorAll(".card");

card.forEach((element, i) => {
    floatable(element);
});

function floatable(panel) {
    let content = panel.querySelector(".content");
    panel.addEventListener('mouseout', e => {
        content.style.transform = `perspective(400px)
                   rotateX(0deg)
                   rotateY(0deg)
                   rotateZ(0deg)
                    translateZ(40px)`;
        content.style.transition = `all 2s linear`;
    });

    panel.addEventListener('mousemove', e => {
        let w = panel.clientWidth;
        let h = panel.clientHeight;
        let y = (e.offsetX - w * 0.5) / w * ANGLE;
        let x = (1 - (e.offsetY - h * 0.5)) / h * ANGLE;

        content.style.transform = `perspective(400px)
                   rotateX(${x}deg)
                   rotateY(${y}deg)`;
    });
}

const activites = document.getElementsByClassName("card");
if (activites) {
    console.log("activites : ", activites);
    console.log("bonjour");

    function sort_activities() {
        const boutons = Array.from(document.getElementsByClassName('bouton-activite'));
        console.log(boutons);
        boutons.forEach((bouton) => {
            bouton.addEventListener('click', (e) => {
                console.log(bouton);
                console.log(bouton.id)
                const classe = bouton.id;
                console.log("classe a afficher : ", classe);
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

// if (activites.length > 0) {
//     console.log("activites : ", activites);
//     console.log("bonjour");

//     function sort_activities() {
//         const boutons = Array.from(document.getElementsByClassName('bouton-activite'));
//         console.log(boutons);
//         boutons.forEach((bouton) => {
//             bouton.addEventListener('click', (e) => {
//                 const classe = e.target.id;
//                 console.log("classe a afficher : ", classe);
//                 Array.from(activites).forEach((element) => {
//                     if (element.classList.contains(classe)) {
//                         console.log("afficher : ", element);
//                         element.style.display = 'block';
//                     } else {
//                         console.log("cacher : ", element);
//                         element.style.display = 'none';
//                     }
//                 });
//             });
//         });
//     }

//     sort_activities();
// } else {
//     console.error("L'élément avec la classe 'card' n'a pas été trouvé.");
// }


// boutons.forEach((bouton) => {
//     console.log("bouton");
//     bouton.addEventListener('click', (e) => {
//         const classe = e.target.id.replace('btn-', '');
//         activites.childNodes.forEach((element) => {
//             if (element.classList.contains(classe)) {
//                 element.style.display = 'block';
//             } else {
//                 element.style.display = 'none';
//             }
//         });
//     });
// });