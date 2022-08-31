// Bulma mobile navigation bar
const navbarBurger = document.querySelector('.navbar-burger');
const navbarMenu = document.querySelector('.navbar-menu');
if (navbarBurger != null && navbarMenu != null) {
    navbarBurger.addEventListener('click', event => {
        event.preventDefault();
        navbarBurger.classList.toggle('is-active');
        navbarMenu.classList.toggle('is-active');
    });
}
