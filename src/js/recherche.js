document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('.liste-activites li.card');
    console.log(items);
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