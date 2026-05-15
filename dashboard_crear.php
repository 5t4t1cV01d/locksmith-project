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
        $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
        $name = htmlspecialchars(trim($_POST['name'] ?? ''));
        $age = (int)($_POST['age'] ?? 0);
        $email = htmlspecialchars(trim($_POST['email'] ?? ''));
        $address = htmlspecialchars(trim($_POST['address'] ?? ''));
        $service_type = $_POST['service_type'] ?? 'Otro';
        $notes = htmlspecialchars(trim($_POST['notes'] ?? ''));

        // Datos de la cita
        $fecha_cita = $_POST['fecha_cita'] ?? '';
        $hora_cita = $_POST['hora_cita'] ?? 'Inmediato';
        
        // Gestión de Cliente (Insertar o Actualizar)
        $query_client = "INSERT INTO client (phone, name, address) 
                        VALUES (:phone, :name, :address) 
                        ON DUPLICATE KEY UPDATE name = :name2, address = :address2";
        
        $stmt_client = $db->prepare($query_client);
        $stmt_client->bindParam(':phone', $phone);
        $stmt_client->bindParam(':name', $name);
        $stmt_client->bindParam(':name2', $name);
        $stmt_client->bindParam(':address', $address);
        $stmt_client->bindParam(':address2', $address);
        $stmt_client->execute();

        // Preparar información de la cita para el Admin
        $cita_info = "";
        if ($service_type === 'Urgencias 24/7' || $hora_cita === 'Inmediato') {
            $cita_info = "🚨 URGENCIA: ATENCIÓN INMEDIATA";
        } else {
            $cita_info = "📅 CITA PROGRAMADA: $fecha_cita a las $hora_cita";
        }

        $full_notes = "$cita_info \n--- DATOS REGISTRO: Edad: $age | Email: $email ---\n\n--- NOTAS: ---\n" . $notes;

        // Crear la Orden de Servicio
        $query_service = "INSERT INTO service_requests (client_phone, service_type, service_address, status, notes, service_date) 
                          VALUES (:phone, :type, :address, 'Pendiente', :notes, NOW())";
        
        $stmt_service = $db->prepare($query_service);
        $stmt_service->bindParam(':phone', $phone);
        $stmt_service->bindParam(':type', $service_type);
        $stmt_service->bindParam(':address', $address);
        $stmt_service->bindParam(':notes', $full_notes);
        
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
                <div id="phone-step" class="form-group phone-highlight-box">
                    <label for="phone" class="phone-label-premium">TELÉFONO DEL CLIENTE (ID):</label>
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

                    <div class="form-group" style="margin-top: 15px;">
                        <label for="notes">NOTAS O DIAGNÓSTICO INICIAL (OBLIGATORIO):</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Ej: Llave quebrada dentro del cilindro, requiere cambio de combinación..."></textarea>
                    </div>
                    
                    <button type="submit" class="option-btn btn-full-width" id="save-user-btn">CREAR ORDEN DE SERVICIO</button>
                </div>
            </form>
        </div>
    </section>

<script src="js/dashboard.js"></script>

<?php include 'includes/footer.php'; ?>