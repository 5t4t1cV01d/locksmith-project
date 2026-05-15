document.addEventListener('DOMContentLoaded', function () {
    // Función que se llama cada vez que el usuario hace scroll
    function scrollFunction() {
        const nav = document.querySelector('.main-nav');

        if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
            nav.classList.add("scrolled");
        } else {
            nav.classList.remove("scrolled");
        }
    }

    // Inicializar el estado del nav al cargar la página
    window.onload = function () {
        scrollFunction();
    }

    // Asignar el listener de scroll
    window.onscroll = scrollFunction;
});