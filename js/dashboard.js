// ARCHIVO: js/dashboard.js
document.addEventListener('DOMContentLoaded', function () {
    const serviceForm = document.getElementById('create-service-form');
    const phoneInput = document.getElementById('phone');
    const phoneStatus = document.getElementById('phone-status');
    
    const clientDataStep = document.getElementById('client-data-step');
    const workDetailsStep = document.getElementById('work-details-step');
    const appointmentSection = document.getElementById('appointment-section');
    
    const errorRegContainer = document.getElementById('error-reg-container');
    const errorServiceContainer = document.getElementById('error-service-container');
    
    const nameInput = document.getElementById('name');
    const ageInput = document.getElementById('age');
    const emailInput = document.getElementById('email');
    const addressInput = document.getElementById('address');
    const serviceTypeInput = document.getElementById('service_type');
    const notesInput = document.getElementById('notes');
    const submitBtn = document.getElementById('save-user-btn');

    // Cita fields
    const fechaInput = document.getElementById('fecha_cita');
    const horaHidden = document.getElementById('hora_cita');
    const timeDisplay = document.getElementById('selected-time-display');
    const timeSlots = document.querySelectorAll('.time-slot');

    if (fechaInput) {
        const today = new Date().toISOString().split('T')[0];
        fechaInput.setAttribute('min', today);
    }

    // 1. Lógica de Despliegue Progresivo para el ADMIN
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
            const phone = this.value.trim();

            const clearFields = () => {
                nameInput.value = '';
                if(ageInput) ageInput.value = '';
                if(emailInput) emailInput.value = '';
                addressInput.value = '';
                serviceTypeInput.value = '';
                notesInput.value = '';
                if(errorRegContainer) errorRegContainer.innerHTML = '';
                if(errorServiceContainer) errorServiceContainer.innerHTML = '';
                if(phoneStatus) phoneStatus.className = 'phone-status-info';
                if(appointmentSection) appointmentSection.style.display = 'none';
                if(horaHidden) horaHidden.value = '';
                if(timeDisplay) timeDisplay.innerHTML = 'Seleccione una hora...';
            };

            if (phone.length === 10) {
                phoneStatus.style.display = 'block';
                phoneStatus.classList.add('status-searching');
                phoneStatus.innerHTML = "🔍 Buscando cliente...";

                fetch(`php/get_client.php?phone=${phone}`)
                    .then(response => response.json())
                    .then(data => {
                        clearFields();
                        phoneStatus.style.display = 'block';

                        if (data.success && data.client) {
                            phoneStatus.classList.add('status-success');
                            phoneStatus.innerHTML = `✅ Cliente encontrado: ${data.client.name}`;
                            nameInput.value = data.client.name || '';
                            addressInput.value = data.client.address || '';
                            clientDataStep.style.display = 'none';
                            workDetailsStep.style.display = 'block';
                            submitBtn.textContent = "SOLICITAR";
                        } else {
                            phoneStatus.classList.add('status-new');
                            phoneStatus.innerHTML = "➕ Cliente nuevo. Registre sus datos.";
                            clientDataStep.style.display = 'block';
                            workDetailsStep.style.display = 'block';
                            submitBtn.textContent = "REGISTRAR Y SOLICITAR";
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        clearFields();
                        phoneStatus.style.display = 'block';
                        phoneStatus.innerHTML = "⚠️ Error de conexión.";
                        clientDataStep.style.display = 'block';
                        workDetailsStep.style.display = 'block';
                    });
            } else {
                clearFields();
                phoneStatus.style.display = 'none';
                clientDataStep.style.display = 'none';
                workDetailsStep.style.display = 'none';
            }
        });
    }

    // 2. Lógica de Citas en el Dashboard
    if (serviceTypeInput) {
        serviceTypeInput.addEventListener('change', function() {
            if (this.value === 'Urgencias 24/7') {
                appointmentSection.style.display = 'none';
                horaHidden.value = 'Inmediato';
            } else {
                appointmentSection.style.display = 'block';
                if (horaHidden.value === 'Inmediato') {
                    horaHidden.value = '';
                    timeDisplay.innerHTML = 'Seleccione una hora...';
                }
            }
        });
    }

    if (timeSlots) {
        timeSlots.forEach(slot => {
            slot.addEventListener('click', function() {
                timeSlots.forEach(s => s.classList.remove('selected'));
                this.classList.add('selected');
                horaHidden.value = this.getAttribute('data-time');
                timeDisplay.innerHTML = `✅ Hora: ${this.innerText}`;
            });
        });
    }

    // 3. VALIDACIÓN SEGMENTADA PARA EL ADMIN
    if (serviceForm) {
        serviceForm.addEventListener('submit', function (e) {
            errorRegContainer.innerHTML = '';
            errorServiceContainer.innerHTML = '';
            
            let regErrors = [];
            let serviceErrors = [];

            if (!/^\d{10}$/.test(phoneInput.value.trim())) {
                serviceErrors.push("El teléfono debe tener 10 dígitos.");
            }

            if (clientDataStep.style.display !== 'none') {
                if (nameInput.value.trim().length < 3) regErrors.push("Nombre demasiado corto.");
                if (ageInput && (!ageInput.value || ageInput.value < 18 || ageInput.value > 100)) {
                    regErrors.push("La edad debe estar entre 18 y 100 años.");
                }
                if (addressInput.value.trim().length < 10) regErrors.push("Dirección insuficiente (mínimo 10 caracteres).");
            }

            if (!serviceTypeInput.value) serviceErrors.push("Falta tipo de servicio.");
            
            // Validación de cita
            if (serviceTypeInput.value && serviceTypeInput.value !== 'Urgencias 24/7') {
                if (!fechaInput.value) serviceErrors.push("Falta fecha de la cita.");
                if (!horaHidden.value || horaHidden.value === 'Inmediato') serviceErrors.push("Falta hora de la cita.");
                
                const selectedDate = new Date(fechaInput.value + 'T00:00:00');
                if (selectedDate.getDay() === 0) {
                    serviceErrors.push("No se agendan citas los domingos.");
                }
            }

            if (notesInput.value.trim().length < 5) serviceErrors.push("Describa el diagnóstico (obligatorio).");

            if (regErrors.length > 0) {
                const alert = document.createElement('div');
                alert.className = 'status-alert error animate-slide';
                alert.innerHTML = `<b>⚠️ Faltan datos del cliente:</b><br>• ${regErrors.join('<br>• ')}`;
                errorRegContainer.appendChild(alert);
            }

            if (serviceErrors.length > 0) {
                const alert = document.createElement('div');
                alert.className = 'status-alert error animate-slide';
                alert.innerHTML = `<b>⚠️ Faltan detalles de la orden:</b><br>• ${serviceErrors.join('<br>• ')}`;
                errorServiceContainer.appendChild(alert);
            }

            if (regErrors.length > 0 || serviceErrors.length > 0) {
                e.preventDefault();
                const target = regErrors.length > 0 ? errorRegContainer : errorServiceContainer;
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                submitBtn.classList.add('btn-loading');
                submitBtn.innerHTML = "REGISTRANDO ORDEN...";
            }
        });
    }
});
