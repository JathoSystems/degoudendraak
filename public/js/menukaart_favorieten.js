document.addEventListener('DOMContentLoaded', function () {
    const hearts = document.querySelectorAll('.favoriet-heart');

    // Helper function to get cookie by name
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    // Helper function to set cookie
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + value + expires + "; path=/";
    }

    // Initialize hearts based on cookie
    let favorieten = [];
    const favorietenCookie = getCookie('favorieten');

    if (favorietenCookie) {
        try {
            favorieten = JSON.parse(favorietenCookie);
            // Make sure it's an array
            if (!Array.isArray(favorieten)) {
                favorieten = [];
            }
        } catch (e) {
            // Reset if we can't parse the cookie
            favorieten = [];
        }
    }

    // Initialize the hearts
    hearts.forEach(heart => {
        const id = heart.dataset.id;

        if (favorieten.includes(id)) {
            heart.classList.add('favoriet');
        }

        heart.addEventListener('click', function () {
            const id = this.dataset.id;

            // Toggle favorite status
            if (favorieten.includes(id)) {
                favorieten = favorieten.filter(fav => fav !== id);
                this.classList.remove('favoriet');
            } else {
                favorieten.push(id);
                this.classList.add('favoriet');
            }

            setCookie('favorieten', JSON.stringify(favorieten), 30);

            window.location.reload();
        });
    });

    console.log('Current favorites:', favorieten);
    console.log('Cookie value:', getCookie('favorieten'));
});
