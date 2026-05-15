<?php
$current_page = 'dashboard_ver';
require 'conexion.php';
require 'seguridad.php';

require_admin_login();

// Definición de URL de inicio para el logo del administrador
$home_url = 'dashboard_ver.php';

$db = getDB();
$status_filter = $_GET['status'] ?? 'all';
$admin_id = $_SESSION['user_id'];
$servicios = [];
$status_msg = '';

function render_admin_service_row($servicio)
{
    $status_class_map = [
        'Pendiente' => 'status-pending',
        'En Camino' => 'status-verification',
        'Completado' => 'status-confirmed',
        'Cancelado' => 'status-cancelled'
    ];
    $status = htmlspecialchars($servicio['status']);
    $class = $status_class_map[$status] ?? 'status-default';
    $id = htmlspecialchars($servicio['id']);

    // Lógica de Programación / Cita
    $cita_info = '<span style="color: #ea3323; font-weight: 900;">🚨 INMEDIATO</span>';
    if (!empty($servicio['appointment_date'])) {
        $cita_info = '<b>📅 ' . htmlspecialchars($servicio['appointment_date']) . '</b><br>';
        $cita_info .= '<small>⏰ ' . htmlspecialchars($servicio['appointment_time']) . '</small>';
    }

    $actionsContent = '<div class="admin-actions-grid">';

    if ($status === 'Pendiente') {
        $actionsContent .= '<a href="dashboard_ver.php?action=process&id=' . $id . '" class="action-btn-mini btn-atender" title="Atender"><i class="fa-solid fa-truck-fast"></i> Atender</a>';
        $actionsContent .= '<a href="dashboard_ver.php?action=complete&id=' . $id . '" class="action-btn-mini btn-finalizar" title="Finalizar"><i class="fa-solid fa-check-double"></i> Finalizar</a>';
        $actionsContent .= '<button class="action-btn-mini btn-cancelar cancel-trigger" data-id="' . $id . '" title="Cancelar" style="grid-column: span 2;"><i class="fa-solid fa-xmark"></i> Cancelar</button>';
    } elseif ($status === 'En Camino') {
        $actionsContent .= '<a href="dashboard_ver.php?action=complete&id=' . $id . '" class="action-btn-mini btn-finalizar" title="Finalizar" style="grid-column: span 2;"><i class="fa-solid fa-check-double"></i> Finalizar</a>';
        $actionsContent .= '<button class="action-btn-mini btn-cancelar cancel-trigger" data-id="' . $id . '" title="Cancelar" style="grid-column: span 2;"><i class="fa-solid fa-xmark"></i> Cancelar</button>';
    }

    $actionsContent .= '<button class="action-btn-mini btn-ver view-trigger" data-detail=\'' . htmlspecialchars(json_encode($servicio)) . '\' title="Ver Ficha" style="grid-column: span 2;"><i class="fa-solid fa-eye"></i> Ver Ficha</button>';

    $actionsContent .= '</div>';

    echo '<tr>';
    echo '<td>#' . $id . '</td>';
    echo '<td style="font-weight: bold;">' . htmlspecialchars($servicio['client_name'] ?? 'N/A') . '</td>';
    echo '<td>' . htmlspecialchars($servicio['client_phone']) . '</td>';
    echo '<td>' . htmlspecialchars($servicio['service_type']) . '</td>';
    echo '<td>' . $cita_info . '</td>';
    echo '<td>' . htmlspecialchars($servicio['service_address']) . '</td>';
    echo '<td><span class="' . $class . '">' . $status . '</span></td>';
    echo '<td>' . $actionsContent . '</td>';
    echo '</tr>';
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];
    $new_status = '';
    $delete_record = false;

    if ($action == 'process') $new_status = 'En Camino';
    if ($action == 'complete') $new_status = 'Completado';
    if ($action == 'cancel') $new_status = 'Cancelado';
    if ($action == 'delete') $delete_record = true;

    if ($new_status) {
        try {
            $query_update = "UPDATE service_requests SET status = :new_status WHERE id = :id";
            $stmt_update = $db->prepare($query_update);
            $stmt_update->bindParam(':new_status', $new_status);
            $stmt_update->bindParam(':id', $id);
            $stmt_update->execute();

            header("Location: dashboard_ver.php?status_msg=" . urlencode("Servicio #$id actualizado."));
            exit();
        } catch (PDOException $e) {
        }
    }

    if ($delete_record) {
        try {
            $query_delete = "DELETE FROM service_requests WHERE id = :id";
            $stmt_delete = $db->prepare($query_delete);
            $stmt_delete->bindParam(':id', $id);
            $stmt_delete->execute();

            header("Location: dashboard_ver.php?status_msg=" . urlencode("Servicio #$id eliminado permanentemente."));
            exit();
        } catch (PDOException $e) {
        }
    }
}

try {
    $query = "SELECT s.*, c.name as client_name, c.age, c.email 
              FROM service_requests s
              LEFT JOIN client c ON s.client_phone = c.phone ";
    if ($status_filter != 'all') {
        $query .= " WHERE s.status = :status_filter";
    }
    $query .= " ORDER BY s.created_at DESC";

    $stmt = $db->prepare($query);
    if ($status_filter != 'all') {
        $stmt->bindParam(':status_filter', $status_filter);
    }
    $stmt->execute();
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
}


if (isset($_GET['status_msg'])) {
    $status_msg = htmlspecialchars(urldecode($_GET['status_msg']));
}

include 'includes/header.php';
?>
<section class="admin-dashboard-section">
    <h1 class="page-title">GESTIÓN DE SERVICIOS</h1>
    <p class="subtitle">Monitoreo de solicitudes de cerrajería y estatus de técnicos.</p>

    <?php if (!empty($status_msg)): ?>
        <div class="status-alert success">
            <?php echo $status_msg; ?>
        </div>
    <?php endif; ?>

    <div class="controls-panel">
        <label for="filter-status">Filtrar por Estado:</label>
        <form method="GET" style="display: inline;">
            <select name="status" id="filter-status" onchange="this.form.submit()">
                <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>Todos los Servicios</option>
                <option value="Pendiente" <?php echo $status_filter == 'Pendiente' ? 'selected' : ''; ?>>Pendientes
                </option>
                <option value="En Camino" <?php echo $status_filter == 'En Camino' ? 'selected' : ''; ?>>En Camino
                </option>
                <option value="Completado" <?php echo $status_filter == 'Completado' ? 'selected' : ''; ?>>Completados
                </option>
                <option value="Cancelado" <?php echo $status_filter == 'Cancelado' ? 'selected' : ''; ?>>Cancelados
                </option>
            </select>
        </form>
    </div>

    <div class="reservations-table-container">
        <table class="reservations-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE</th>
                    <th>TELÉFONO</th>
                    <th>SERVICIO</th>
                    <th>PROGRAMACIÓN</th>
                    <th>DIRECCIÓN</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody id="admin-reservations-table-body">
                <?php foreach ($servicios as $servicio) {
                    render_admin_service_row($servicio);
                } ?>
            </tbody>
        </table>
        <p id="no-reservations-message-admin"
            style="display: <?php echo empty($servicios) ? 'block' : 'none'; ?>; text-align: center; margin-top: 30px; color: var(--text-color);">
            No hay servicios para mostrar en este filtro.
        </p>
    </div>

    <!-- Fondo oscuro para el modal -->
    <div id="modal-overlay" class="modal-overlay"></div>

    <div id="detail-modal" class="upload-form-modal">
        <h3 class="form-subtitle">Ficha del Servicio #<span id="detail-id"></span></h3>
        <div class="detail-content" style="color: #444; line-height: 1.6; font-size: 0.95em;">
            <div style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px;">
                <p><strong>Cliente:</strong> <span id="detail-name"></span></p>
                <p><strong>Teléfono:</strong> <span id="detail-phone"></span></p>
                <p><strong>Edad:</strong> <span id="detail-age"></span> años | <strong>Email:</strong> <span id="detail-email"></span></p>
            </div>
            <p><strong>Tipo de Servicio:</strong> <span id="detail-type"></span></p>
            <p><strong>Dirección:</strong> <span id="detail-address"></span></p>
            <p><strong>Programación:</strong> <span id="detail-scheduling"></span></p>
            <p><strong>Estatus Actual:</strong> <span id="detail-status"></span></p>
            <p><strong>Notas adicionales:</strong> <br><span id="detail-notes" style="background: #f9f9f9; display: block; padding: 10px; border-radius: 5px; margin-top: 5px;"></span></p>
            
            <div id="comprobante-area" style="margin-top: 15px; display: none;">
                <strong>Evidencia/Comprobante:</strong> <br>
                <img id="detail-img" src=""
                    style="max-width: 100%; border-radius: 10px; margin-top: 10px; border: 1px solid #ddd;">
            </div>
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 25px;">
            <button type="button" class="option-btn" id="close-modal-btn" style="flex: 1;">CERRAR FICHA</button>
            <button type="button" id="modal-delete-btn" class="btn-modal-delete" style="flex: 1;">
                <i class="fa-solid fa-trash-can"></i> ELIMINAR REGISTRO
            </button>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const detailModal = document.getElementById('detail-modal');
        const closeModalBtn = document.getElementById('close-modal-btn');

        function closeModal() {
            // Animación de salida suave
            detailModal.classList.add('hide-anim');
            detailModal.classList.remove('show-anim');
            
            // Esperar a que termine la animación (0.5s) antes de ocultar
            setTimeout(() => {
                detailModal.style.display = 'none';
                detailModal.classList.remove('hide-anim');
            }, 500);
        }

        closeModalBtn.addEventListener('click', closeModal);

        document.querySelectorAll('.cancel-trigger').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                if (confirm(`¿Está seguro que desea CANCELAR el servicio #${id}?`)) {
                    window.location.href = `dashboard_ver.php?action=cancel&id=${id}`;
                }
            });
        });

        document.querySelectorAll('.view-trigger').forEach(button => {
            button.addEventListener('click', function () {
                const data = JSON.parse(this.getAttribute('data-detail'));

                document.getElementById('detail-id').textContent = data.id;
                document.getElementById('detail-name').textContent = data.client_name || 'Cliente Particular';
                document.getElementById('detail-phone').textContent = data.client_phone;
                document.getElementById('detail-age').textContent = data.age || '--';
                document.getElementById('detail-email').textContent = data.email || '--';
                
                document.getElementById('detail-type').textContent = data.service_type;
                document.getElementById('detail-address').textContent = data.service_address;
                
                // Lógica de programación (Fecha/Hora)
                let sched = "<span style='color:#ea3323; font-weight:bold;'>🚨 INMEDIATO (Urgencia)</span>";
                if(data.appointment_date && data.appointment_date !== '0000-00-00') {
                    sched = "<b>📅 " + data.appointment_date + "</b> | ⏰ " + (data.appointment_time || '');
                }
                document.getElementById('detail-scheduling').innerHTML = sched;
                
                document.getElementById('detail-status').textContent = data.status;
                document.getElementById('detail-notes').textContent = data.notes || 'Sin notas adicionales.';

                const imgArea = document.getElementById('comprobante-area');
                const imgTag = document.getElementById('detail-img');

                if (data.payment_proof) {
                    imgTag.src = data.payment_proof;
                    imgArea.style.display = 'block';
                } else {
                    imgArea.style.display = 'none';
                }

                // Mostrar con animación de entrada y scroll
                detailModal.style.display = 'block';
                setTimeout(() => {
                    detailModal.classList.add('show-anim');
                    detailModal.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 10);
                
                // Configurar el botón de eliminar del modal
                const modalDeleteBtn = document.getElementById('modal-delete-btn');
                modalDeleteBtn.setAttribute('data-id', data.id);
            });
        });

        // Event listener para el botón de eliminar del modal
        document.getElementById('modal-delete-btn').addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (confirm(`⚠️ ¡ADVERTENCIA CRÍTICA! \n\n¿Está seguro que desea ELIMINAR permanentemente el servicio #${id}?\nEsta acción borrará los datos de la base de datos y no se puede deshacer.`)) {
                window.location.href = `dashboard_ver.php?action=delete&id=${id}`;
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>