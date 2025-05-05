document.addEventListener('DOMContentLoaded', function () {
    const hearts = document.querySelectorAll('.favoriet-heart');

    // Laad favorieten uit localStorage of cookie
    let favorieten = JSON.parse(localStorage.getItem('favorieten') || '[]');

    // Initialiseer visueel de juiste hartjes
    hearts.forEach(heart => {
        const id = heart.dataset.id;
        if (favorieten.includes(id)) {
            heart.classList.add('favoriet');
        }

        heart.addEventListener('click', function () {
            const id = this.dataset.id;

            if (favorieten.includes(id)) {
                favorieten = favorieten.filter(fav => fav !== id);
                this.classList.remove('favoriet');
            } else {
                favorieten.push(id);
                this.classList.add('favoriet');
            }

            localStorage.setItem('favorieten', JSON.stringify(favorieten));
            document.cookie = "favorieten=" + JSON.stringify(favorieten) + "; path=/; max-age=" + (60 * 60 * 24 * 30);
        });
    });
});
