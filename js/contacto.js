// js/contacto.js
document.addEventListener('DOMContentLoaded', () => {
    const opinionForm = document.getElementById('opinion-form');
    const anonCheckbox = document.getElementById('anonimo');
    const nameInput = document.getElementById('nombre_opinion');

    // 1. Manejo del Checkbox Anónimo
    if (anonCheckbox && nameInput) {
        anonCheckbox.addEventListener('change', () => {
            if (anonCheckbox.checked) {
                nameInput.value = 'Anónimo';
                nameInput.readOnly = true; // Mejor usar readOnly que disabled para que se envíe el valor
                nameInput.style.backgroundColor = '#f0f0f0';
                nameInput.style.color = '#888';
            } else {
                nameInput.value = '';
                nameInput.readOnly = false;
                nameInput.style.backgroundColor = '';
                nameInput.style.color = '';
            }
        });
    }

    // 2. Validación antes de enviar (Opcional, pero recomendada)
    if (opinionForm) {
        opinionForm.addEventListener('submit', (e) => {
            const submitBtn = opinionForm.querySelector('button[type="submit"]');
            
            // Si todo está bien, dejamos que el formulario se envíe de forma natural al PHP
            submitBtn.innerHTML = "PUBLICANDO...";
            submitBtn.style.opacity = "0.7";
            submitBtn.style.pointerEvents = "none";
        });
    }
});
