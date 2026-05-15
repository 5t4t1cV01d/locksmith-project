// js/contacto.js
document.addEventListener('DOMContentLoaded', () => {
    const opinionForm = document.getElementById('opinion-form');
    const successBox = document.getElementById('success-message');
    const formContent = document.getElementById('form-content');

    if (opinionForm) {
        opinionForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(opinionForm);

            // Simulación de envío (En el futuro se conectará a php/guardar_opinion.php)
            // fetch('php/guardar_opinion.php', {
            //     method: 'POST',
            //     body: formData
            // })
            // .then(response => response.json())
            // .then(data => { ... });

            // Por ahora, solo mostramos el mensaje de éxito premium
            formContent.style.opacity = '0';
            setTimeout(() => {
                formContent.style.display = 'none';
                successBox.style.display = 'block';
                successBox.innerHTML = `
                    <div class="status-alert success">
                        <h3>¡GRACIAS POR TU OPINIÓN!</h3>
                        <p>Tu reseña ha sido publicada. Apreciamos mucho que te tomes el tiempo de calificar nuestro trabajo.</p>
                        <button onclick="location.reload()" class="option-btn" style="width: auto; padding: 10px 30px; margin-top: 20px;">CALIFICAR OTRA VEZ</button>
                    </div>
                `;
            }, 300);
        });
    }

    // Efecto visual para el checkbox de anónimo
    const anonCheckbox = document.getElementById('anonimo');
    const nameInput = document.getElementById('nombre_opinion');

    if (anonCheckbox && nameInput) {
        anonCheckbox.addEventListener('change', () => {
            if (anonCheckbox.checked) {
                nameInput.value = 'Anónimo';
                nameInput.disabled = true;
                nameInput.style.backgroundColor = '#eee';
            } else {
                nameInput.value = '';
                nameInput.disabled = false;
                nameInput.style.backgroundColor = '';
            }
        });
    }
});
