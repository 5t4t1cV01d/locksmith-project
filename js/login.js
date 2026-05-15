document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('.login-form');
    const container = document.querySelector('.login-page-content');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            // Limpiar alertas previas
            const oldAlert = document.querySelector('.status-alert');
            if (oldAlert) oldAlert.remove();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            let errors = [];

            if (!email) {
                errors.push("El correo es obligatorio.");
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errors.push("Formato de correo no válido.");
            }

            if (!password) {
                errors.push("La contraseña es obligatoria.");
            }

            if (errors.length > 0) {
                e.preventDefault();
                
                // Crear alerta con estilo CSS unificado
                const alertDiv = document.createElement('div');
                alertDiv.className = 'status-alert error';
                
                alertDiv.innerHTML = "<b>❌ Error:</b> " + errors.join(" ");
                
                // Insertar antes del formulario
                container.insertBefore(alertDiv, loginForm);
            }
        });
    }
});