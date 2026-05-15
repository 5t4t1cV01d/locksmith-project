<?php
// ARCHIVO: solicitar.php
$current_page = 'solicitar';
include 'includes/header.php';
?>

<section class="request-options-section">
    <div class="request-container-premium">
        <h1 class="page-title">SOLICITAR SERVICIO</h1>
        <p class="subtitle">Atención rápida para clientes registrados. Si es su primera vez, utilice los métodos de contacto directo.</p>

        <?php if (isset($_GET['status'])): ?>
            <div class="status-alert <?php echo $_GET['status']; ?>">
                <?php 
                    if ($_GET['status'] == 'success') echo "✅ ¡Solicitud enviada con éxito! Nos pondremos en contacto pronto.";
                    else echo "❌ Error: " . htmlspecialchars($_GET['msg'] ?? 'Ocurrió un problema.');
                ?>
            </div>
        <?php endif; ?>

        <div class="options-grid">
            <!-- Opción 1: Llamada -->
            <div class="option-card emergency">
                <div class="icon-box">
                    <img src="img/icons/call.svg" alt="Teléfono">
                </div>
                <h3>Llamada de Emergencia</h3>
                <p>Atención inmediata 24/7. El método más rápido para aperturas y urgencias.</p>
                <a href="tel:9991234567" class="option-btn">Llamar Ahora</a>
            </div>

            <!-- Opción 2: WhatsApp -->
            <div class="option-card whatsapp">
                <div class="icon-box">
                    <img src="img/icons/whats.svg" alt="WhatsApp">
                </div>
                <h3>Mensaje de WhatsApp</h3>
                <p>Envíe fotos de su cerradura para una cotización precisa y rápida.</p>
                <a href="https://wa.me/529991234567?text=Hola,%20necesito%20un%20servicio%20de%20cerrajería." target="_blank" class="option-btn">Enviar Mensaje</a>
            </div>

            <!-- Opción 3: Formulario -->
            <div class="option-card schedule">
                <div class="icon-box">
                    <img src="img/icons/mail.svg" alt="Formulario">
                </div>
                <h3>Solicitud Express</h3>
                <p>Exclusivo para clientes registrados. Solicite su servicio en segundos.</p>
                <a href="javascript:void(0);" class="option-btn" id="show-form-btn">Acceder al Formulario</a>
            </div>
        </div>

        <!-- Sección del Formulario Dinámico -->
        <div id="form-section" class="request-form-container-premium hidden-init">
            
            <form action="php/procesar_solicitud.php" method="POST" class="admin-form" id="client-request-form">
                
                <!-- PASO 1: IDENTIFICACIÓN -->
                <div id="phone-step" class="form-group phone-highlight-box">
                    <label for="telefono" class="phone-label-premium">INGRESE SU TELÉFONO REGISTRADO:</label>
                    <input type="tel" id="telefono" name="telefono" placeholder="Ej: 9991234567" class="phone-input-premium">
                    <div id="phone-status" class="phone-status-info"></div>
                </div>

                <!-- CONTENEDOR PARA NO REGISTRADOS -->
                <div id="not-registered-msg" style="display: none;" class="status-alert error animate-slide">
                    <b>⚠️ No encontramos su número en nuestro sistema.</b><br>
                    Para su primer servicio, por favor utilice la opción de <b>Llamada</b> o <b>WhatsApp</b> arriba para que un administrador lo dé de alta. ¡Gracias!
                </div>

                <!-- PASO 2: DETALLES DEL TRABAJO -->
                <div id="work-details-step" class="form-step-section service-section">
                    <h4 class="step-title-service">Detalles de su Solicitud</h4>
                    
                    <div id="error-service-container"></div>

                    <input type="hidden" id="nombre" name="nombre">
                    <input type="hidden" id="domicilio" name="domicilio">

                    <div class="form-group">
                        <label for="tipo_servicio">¿QUÉ TIPO DE TRABAJO REQUIERE?</label>
                        <select id="tipo_servicio" name="tipo_servicio">
                            <option value="" disabled selected>Seleccione una opción...</option>
                            <option value="Urgencias 24/7">🚨 Urgencias 24/7</option>
                            <option value="Residencial">🏠 Residencial</option>
                            <option value="Automotriz">🚗 Automotriz</option>
                            <option value="Seguridad">🛡️ Seguridad / Cajas Fuertes</option>
                            <option value="Otro">🔧 Otro / Mantenimiento</option>
                        </select>
                    </div>

                    <!-- APARTADO DE CITA (Solo si no es urgencia) -->
                    <div id="appointment-section" class="appointment-container">
                        <div class="appointment-title">
                            <span>📅 Programar Cita</span>
                            <small>(Lunes a Sábado)</small>
                        </div>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label>FECHA:</label>
                                <input type="date" id="fecha_cita" name="fecha_cita" class="premium-date-input">
                            </div>
                            <div class="form-group">
                                <label>HORA SELECCIONADA:</label>
                                <input type="hidden" id="hora_cita" name="hora_cita">
                                <div id="selected-time-display" class="time-display-box">Seleccione una hora...</div>
                            </div>
                        </div>
                        <div class="time-slots-grid">
                            <!-- Mañana -->
                            <div class="time-slot" data-time="09:00">09:00 AM</div>
                            <div class="time-slot" data-time="10:00">10:00 AM</div>
                            <div class="time-slot" data-time="11:00">11:00 AM</div>
                            <div class="time-slot" data-time="12:00">12:00 PM</div>
                            <!-- Tarde -->
                            <div class="time-slot" data-time="16:00">04:00 PM</div>
                            <div class="time-slot" data-time="17:00">05:00 PM</div>
                            <div class="time-slot" data-time="18:00">06:00 PM</div>
                            <div class="time-slot" data-time="19:00">07:00 PM</div>
                            <div class="time-slot" data-time="20:00">08:00 PM</div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 15px;">
                        <label for="notas">DETALLES O NOTAS ADICIONALES (OBLIGATORIO):</label>
                        <textarea id="notas" name="notas" rows="3" placeholder="Ej. Se me perdieron las llaves, la chapa está dura..."></textarea>
                    </div>

                    <button type="submit" class="option-btn btn-full-width" id="submit-request-btn">ENVIAR SOLICITUD AHORA</button>
                </div>

            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const showFormBtn = document.getElementById('show-form-btn');
    const formSection = document.getElementById('form-section');
    const phoneInput = document.getElementById('telefono');
    const phoneStatus = document.getElementById('phone-status');
    const workDetailsStep = document.getElementById('work-details-step');
    const notRegisteredMsg = document.getElementById('not-registered-msg');
    const appointmentSection = document.getElementById('appointment-section');
    const serviceTypeSelect = document.getElementById('tipo_servicio');
    
    const fechaInput = document.getElementById('fecha_cita');
    const horaHidden = document.getElementById('hora_cita');
    const timeDisplay = document.getElementById('selected-time-display');
    const timeSlots = document.querySelectorAll('.time-slot');
    
    const errorServiceContainer = document.getElementById('error-service-container');
    
    const nameInput = document.getElementById('nombre');
    const addressInput = document.getElementById('domicilio');
    const notesInput = document.getElementById('notas');
    const submitBtn = document.getElementById('submit-request-btn');
    const requestForm = document.getElementById('client-request-form');

    // Configuración mínima de fecha (hoy)
    const today = new Date().toISOString().split('T')[0];
    fechaInput.setAttribute('min', today);

    // 1. Mostrar Formulario
    showFormBtn.addEventListener('click', function() {
        formSection.classList.remove('hidden-init');
        formSection.style.display = 'block';
        formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        phoneInput.focus();
    });

    // 2. Lógica de Servicio (Cita vs Urgencia)
    serviceTypeSelect.addEventListener('change', function() {
        if (this.value === 'Urgencias 24/7') {
            appointmentSection.style.display = 'none';
            // Reset cita
            horaHidden.value = 'Inmediato';
            fechaInput.value = '';
        } else {
            appointmentSection.style.display = 'block';
            if (horaHidden.value === 'Inmediato') {
                horaHidden.value = '';
                timeDisplay.innerHTML = 'Seleccione una hora...';
            }
        }
    });

    // 3. Selección de Horarios
    timeSlots.forEach(slot => {
        slot.addEventListener('click', function() {
            timeSlots.forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
            const timeValue = this.getAttribute('data-time');
            horaHidden.value = timeValue;
            timeDisplay.innerHTML = `✅ Hora: ${this.innerText}`;
        });
    });

    // 4. Verificación de Teléfono
    phoneInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
        const phone = this.value.trim();
        
        const resetForm = () => {
            workDetailsStep.style.display = 'none';
            notRegisteredMsg.style.display = 'none';
            phoneStatus.className = 'phone-status-info';
            phoneStatus.innerHTML = '';
        };

        if (phone.length === 10) {
            phoneStatus.style.display = 'block';
            phoneStatus.classList.add('status-searching');
            phoneStatus.innerHTML = "🔍 Verificando registro...";

            fetch(`php/get_client.php?phone=${phone}`)
                .then(response => response.json())
                .then(data => {
                    resetForm();
                    phoneStatus.style.display = 'block';

                    if (data.success && data.client) {
                        phoneStatus.classList.add('status-success');
                        phoneStatus.innerHTML = `✅ ¡Bienvenido, ${data.client.name}!`;
                        nameInput.value = data.client.name;
                        addressInput.value = data.client.address;
                        workDetailsStep.style.display = 'block';
                    } else {
                        notRegisteredMsg.style.display = 'block';
                        phoneStatus.style.display = 'none';
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    resetForm();
                    phoneStatus.style.display = 'block';
                    phoneStatus.innerHTML = "⚠️ Error de conexión.";
                });
        } else {
            resetForm();
        }
    });

    // 5. Validación Final
    requestForm.addEventListener('submit', function(e) {
        errorServiceContainer.innerHTML = '';
        let serviceErrors = [];

        if (!serviceTypeSelect.value) serviceErrors.push("Seleccione el tipo de servicio.");
        
        if (serviceTypeSelect.value !== 'Urgencias 24/7') {
            if (!fechaInput.value) serviceErrors.push("Seleccione una fecha para su cita.");
            if (!horaHidden.value || horaHidden.value === 'Inmediato') serviceErrors.push("Seleccione una hora para su cita.");
            
            // Validar que no sea Domingo (Día 0)
            const selectedDate = new Date(fechaInput.value + 'T00:00:00');
            if (selectedDate.getDay() === 0) {
                serviceErrors.push("No laboramos los domingos. Elija de Lunes a Sábado.");
            }
        }

        if (notesInput.value.trim().length < 5) serviceErrors.push("Describa su problema (obligatorio).");

        if (serviceErrors.length > 0) {
            e.preventDefault();
            const alert = document.createElement('div');
            alert.className = 'status-alert error animate-slide';
            alert.innerHTML = `<b>⚠️ Faltan detalles:</b><br>• ${serviceErrors.join('<br>• ')}`;
            errorServiceContainer.appendChild(alert);
            alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            submitBtn.classList.add('btn-loading');
            submitBtn.innerHTML = "ENVIANDO...";
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
