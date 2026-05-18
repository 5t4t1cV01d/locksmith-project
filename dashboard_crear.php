<?php
// ARCHIVO: dashboard_crear.php
$current_page = 'dashboard_crear'; 
require 'conexion.php';
require 'seguridad.php';

// Asegura que solo el administrador pueda acceder
require_admin_login(); 

$db = getDB();
$status_message = '';
$status_class = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $phone = trim($_POST['phone'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $age = isset($_POST['age']) && $_POST['age'] !== '' ? (int)$_POST['age'] : 0;
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $service_type = $_POST['service_type'] ?? 'Otro';
        $fecha_cita = !empty($_POST['fecha_cita']) ? trim($_POST['fecha_cita']) : null;
        $hora_cita = !empty($_POST['hora_cita']) && $_POST['hora_cita'] !== 'Inmediato' ? trim($_POST['hora_cita']) : null;
        $notes = trim($_POST['notes'] ?? '');

        // Registrar nuevo cliente si no existe
        if (!empty($name)) {
            $check_client = $db->prepare("SELECT phone FROM client WHERE phone = :phone");
            $check_client->execute([':phone' => $phone]);
            if ($check_client->rowCount() == 0) {
                $insert_client = $db->prepare("INSERT INTO client (phone, name, age, email, address) VALUES (:phone, :name, :age, :email, :address)");
                $insert_client->execute([
                    ':phone' => $phone,
                    ':name' => $name,
                    ':age' => $age,
                    ':email' => $email,
                    ':address' => $address
                ]);
            }
        }

        // Crear la Orden de Servicio
        $query_service = "INSERT INTO service_requests (client_phone, age, service_type, service_address, appointment_date, appointment_time, status, notes, service_date) 
                          VALUES (:phone, :age, :type, :address, :fecha_cita, :hora_cita, 'Pendiente', :notes, NOW())";
        
        $stmt_service = $db->prepare($query_service);
        $stmt_service->bindParam(':phone', $phone);
        $stmt_service->bindParam(':age', $age);
        $stmt_service->bindParam(':type', $service_type);
        $stmt_service->bindParam(':address', $address);
        $stmt_service->bindParam(':fecha_cita', $fecha_cita);
        $stmt_service->bindParam(':hora_cita', $hora_cita);
        $stmt_service->bindParam(':notes', $notes);
        
        if ($stmt_service->execute()) {
            $order_id = $db->lastInsertId();
            header("Location: dashboard_ver.php?status_msg=" . urlencode("Orden #$order_id registrada con éxito."));
            exit();
        }
    } catch (Exception $e) {
        $status_message = "Error: " . $e->getMessage();
        $status_class = 'error';
    }
}

include 'includes/header.php'; 
?>
    <section class="admin-dashboard-section">
        <h1 class="page-title">NUEVA ORDEN DE TRABAJO</h1>
        <p class="subtitle">Ingrese el teléfono del cliente para comenzar el registro del servicio.</p>

        <div class="admin-form-container">
            <?php if ($status_message): ?>
                <div class="status-alert <?php echo $status_class; ?> animate-slide">
                    <?php echo $status_message; ?>
                </div>
            <?php endif; ?>

            <form id="create-service-form" class="admin-form" method="POST" action="dashboard_crear.php">

                <!-- PASO 1: IDENTIFICACIÓN -->
                <div id="phone-step" class="form-group phone-highlight-box u-block">
                    <label for="phone" class="phone-label-premium">TELÉFONO DEL CLIENTE:</label>
                    <input type="tel" id="phone" name="phone" placeholder="Ej: 9991234567" class="phone-input-premium">
                    <div id="phone-status" class="phone-status-info"></div>
                </div>
                
                <!-- PASO 2: DATOS DEL CLIENTE (DINÁMICO) -->
                <div id="client-data-step" class="form-step-section reg-section">
                    <h3 class="step-title-reg">Registro de Cliente</h3>
                    
                    <div id="error-reg-container"></div>

                    <div class="form-group">
                        <label for="name">NOMBRE COMPLETO:</label>
                        <input type="text" id="name" name="name" placeholder="Nombre del cliente">
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="age">EDAD:</label>
                            <input type="number" id="age" name="age" min="18" max="100" placeholder="Ej. 25">
                        </div>
                        <div class="form-group">
                            <label for="email">CORREO ELECTRÓNICO:</label>
                            <input type="email" id="email" name="email" placeholder="cliente@ejemplo.com">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">DIRECCIÓN DEL SERVICIO:</label>
                        <textarea id="address" name="address" rows="2" placeholder="Calle, número, cruzamientos y colonia"></textarea>
                    </div>
                </div>

                <!-- PASO 3: DETALLES DE LA ORDEN -->
                <div id="work-details-step" class="form-step-section service-section">
                    <h3 class="step-title-service">Detalles de la Orden</h3>

                    <div id="error-service-container"></div>

                    <div class="form-group">
                        <label for="service_type">TIPO DE SERVICIO:</label>
                        <select id="service_type" name="service_type">
                            <option value="" disabled selected>Seleccione una opción...</option>
                            <option value="Urgencias 24/7">🚨 Urgencias 24/7</option>
                            <option value="Residencial">🏠 Residencial</option>
                            <option value="Automotriz">🚗 Automotriz</option>
                            <option value="Seguridad">🛡️ Seguridad / Cajas Fuertes</option>
                            <option value="Otro">🔧 Otro / Mantenimiento</option>
                        </select>
                    </div>

                    <!-- APARTADO DE CITA PARA EL ADMIN -->
                    <div id="appointment-section" class="appointment-container">
                        <div class="appointment-title">
                            <span>📅 Programar Cita</span>
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
                            <div class="time-slot" data-time="09:00">09:00 AM</div>
                            <div class="time-slot" data-time="10:00">10:00 AM</div>
                            <div class="time-slot" data-time="11:00">11:00 AM</div>
                            <div class="time-slot" data-time="12:00">12:00 PM</div>
                            <div class="time-slot" data-time="16:00">04:00 PM</div>
                            <div class="time-slot" data-time="17:00">05:00 PM</div>
                            <div class="time-slot" data-time="18:00">06:00 PM</div>
                            <div class="time-slot" data-time="19:00">07:00 PM</div>
                            <div class="time-slot" data-time="20:00">08:00 PM</div>
                        </div>
                    </div>

                    <div class="form-group u-mt-15">
                        <label for="notes">NOTAS O DIAGNÓSTICO INICIAL:</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Ej: Llave quebrada dentro del cilindro..."></textarea>
                    </div>
                    
                    <button type="submit" class="option-btn btn-submit-order" id="save-user-btn">CREAR ORDEN DE SERVICIO</button>
                </div>
            </form>
        </div>
    </section>

<script src="js/dashboard.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    const nameInput = document.getElementById('name');
    const submitBtn = document.getElementById('save-user-btn');

    function updateBtn() {
        const phone = phoneInput.value.trim();
        const name = nameInput.value.trim();
        
        if (phone.length >= 10) {
            // Si el nombre está vacío, asumimos que es registro nuevo
            if (name === "") {
                submitBtn.textContent = "REGISTRAR CLIENTE Y SOLICITAR";
            } else {
                submitBtn.textContent = "CREAR ORDEN DE TRABAJO";
            }
        } else {
            submitBtn.textContent = "SOLICITAR SERVICIO";
        }
    }

    phoneInput.addEventListener('input', updateBtn);
    nameInput.addEventListener('input', updateBtn);
});
</script>

<?php include 'includes/footer.php'; ?>