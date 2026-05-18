<?php
$current_page = 'dashboard_ver';
require 'conexion.php';
require 'seguridad.php';

require_admin_login();

$home_url = 'dashboard_ver.php';

$db = getDB();
$status_filter = $_GET['status'] ?? 'all';
$admin_id = $_SESSION['user_id'];
$servicios = [];
$status_msg = '';

// --- ACCIÓN AJAX: Cambio de estado desde el <select> de la tabla ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_update_status'])) {
    header('Content-Type: application/json');
    $id = (int)($_POST['id'] ?? 0);
    $new_status = $_POST['new_status'] ?? '';
    $allowed = ['Pendiente', 'En Camino', 'Completado', 'Cancelado'];
    if ($id && in_array($new_status, $allowed)) {
        try {
            $stmt = $db->prepare("UPDATE service_requests SET status = :s WHERE id = :id");
            $stmt->execute([':s' => $new_status, ':id' => $id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    }
    exit();
}

function render_admin_service_row($servicio)
{
    $statuses = ['Pendiente', 'En Camino', 'Completado', 'Cancelado'];
    $status_class_map = [
        'Pendiente'  => 'status-pending',
        'En Camino'  => 'status-verification',
        'Completado' => 'status-confirmed',
        'Cancelado'  => 'status-cancelled'
    ];
    $status = htmlspecialchars($servicio['status']);
    $class  = $status_class_map[$status] ?? 'status-default';
    $id     = htmlspecialchars($servicio['id']);

    // Programación / Cita
    $cita_info = '<span class="text-emergency">🚨 INMEDIATO</span>';
    if (!empty($servicio['appointment_date']) && $servicio['appointment_date'] !== '0000-00-00') {
        $cita_info  = '<b>📅 ' . htmlspecialchars($servicio['appointment_date']) . '</b><br>';
        $cita_info .= '<small>⏰ ' . htmlspecialchars($servicio['appointment_time']) . '</small>';
    }

    // Dropdown de estado (inline)
    $select = '<select class="status-select-inline ' . $class . '" data-id="' . $id . '">';
    foreach ($statuses as $s) {
        $sel = ($s === $status) ? 'selected' : '';
        $select .= '<option value="' . $s . '" ' . $sel . '>' . $s . '</option>';
    }
    $select .= '</select>';

    // Botón Ver Ficha (único en acciones)
    $view_btn = '<button class="action-btn-mini btn-ver view-trigger grid-2nd-col" '
              . 'data-detail=\'' . htmlspecialchars(json_encode($servicio)) . '\' '
              . 'title="Ver Ficha"><i class="fa-solid fa-eye"></i> Ver Ficha</button>';

    echo '<tr>';
    echo '<td>#' . $id . '</td>';
    echo '<td class="u-fw-bold">' . htmlspecialchars($servicio['client_name'] ?? 'N/A') . '</td>';
    echo '<td>' . htmlspecialchars($servicio['client_phone']) . '</td>';
    echo '<td>' . htmlspecialchars($servicio['service_type']) . '</td>';
    echo '<td>' . $cita_info . '</td>';
    echo '<td>' . htmlspecialchars($servicio['service_address']) . '</td>';
    echo '<td>' . $select . '</td>';
    echo '<td><div class="admin-actions-grid">' . $view_btn . '</div></td>';
    echo '</tr>';
}

// --- ACCIONES GET (eliminar, etc.) ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id     = (int) $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'cancel_status') {
        try {
            $stmt = $db->prepare("UPDATE service_requests SET status = 'Cancelado' WHERE id = :id");
            $stmt->execute([':id' => $id]);
            header("Location: dashboard_ver.php?status_msg=" . urlencode("Servicio #$id marcado como Cancelado."));
            exit();
        } catch (PDOException $e) {}
    }

    if ($action == 'delete') {
        try {
            $stmt = $db->prepare("DELETE FROM service_requests WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            header("Location: dashboard_ver.php?status_msg=" . urlencode("Servicio #$id eliminado permanentemente."));
            exit();
        } catch (PDOException $e) {}
    }
}

// --- CARGA DE DATOS ---
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
} catch (PDOException $e) {}

if (isset($_GET['status_msg'])) {
    $status_msg = htmlspecialchars(urldecode($_GET['status_msg']));
}

include 'includes/header.php';
?>
<section class="admin-dashboard-section">
    <h1 class="page-title">GESTIÓN DE SERVICIOS</h1>
    <p class="subtitle">Monitoreo de solicitudes de cerrajería y estatus de técnicos.</p>

    <?php if (!empty($status_msg)): ?>
        <div class="status-alert success"><?php echo $status_msg; ?></div>
    <?php endif; ?>

    <div class="controls-panel">
        <label for="filter-status">Filtrar por Estado:</label>
        <form method="GET" class="u-inline">
            <select name="status" id="filter-status" onchange="this.form.submit()">
                <option value="all"       <?php echo $status_filter == 'all'       ? 'selected' : ''; ?>>Todos los Servicios</option>
                <option value="Pendiente" <?php echo $status_filter == 'Pendiente' ? 'selected' : ''; ?>>Pendientes</option>
                <option value="En Camino" <?php echo $status_filter == 'En Camino' ? 'selected' : ''; ?>>En Camino</option>
                <option value="Completado"<?php echo $status_filter == 'Completado'? 'selected' : ''; ?>>Completados</option>
                <option value="Cancelado" <?php echo $status_filter == 'Cancelado' ? 'selected' : ''; ?>>Cancelados</option>
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
            class="<?php echo empty($servicios) ? 'u-block' : 'u-hidden'; ?> u-mt-30 text-center text-color-global">
            No hay servicios para mostrar en este filtro.
        </p>
    </div>

    <!-- Overlay del modal -->
    <div id="modal-overlay" class="modal-overlay"></div>

    <!-- MODAL FICHA -->
    <div id="detail-modal" class="upload-form-modal">
        <!-- Botón X para cerrar -->
        <button type="button" id="close-modal-x" class="modal-close-x" title="Cerrar">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <h3 class="form-subtitle">Ficha del Servicio #<span id="detail-id"></span></h3>

        <div class="detail-content modal-info-box">
            <div class="modal-separator">
                <p><strong>Cliente:</strong> <span id="detail-name"></span></p>
                <p><strong>Teléfono:</strong> <span id="detail-phone"></span></p>
                <p><strong>Edad:</strong> <span id="detail-age"></span> años | <strong>Email:</strong> <span id="detail-email"></span></p>
            </div>
            <p><strong>Tipo de Servicio:</strong> <span id="detail-type"></span></p>
            <p><strong>Dirección:</strong> <span id="detail-address"></span></p>
            <p><strong>Programación:</strong> <span id="detail-scheduling"></span></p>
            <p><strong>Estatus Actual:</strong> <span id="detail-status"></span></p>

            <!-- MONTO DEL SERVICIO -->
            <p><strong>Monto Estimado:</strong> <span id="detail-price"></span></p>

            <p class="u-mt-15"><strong>Notas adicionales:</strong><br><span id="detail-notes" class="modal-note-highlight"></span></p>

            <div id="comprobante-area" class="u-mt-15 u-hidden">
                <strong>Evidencia/Comprobante:</strong><br>
                <img id="detail-img" src="" class="modal-evidence-img">
            </div>
        </div>

        <!-- Footer: solo Eliminar -->
        <div class="modal-footer-actions u-mt-25">
            <button type="button" id="modal-delete-btn" class="btn-modal-delete" style="width:100%; font-size:15px;">
                <i class="fa-solid fa-trash-can"></i> ELIMINAR REGISTRO
            </button>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const detailModal   = document.getElementById('detail-modal');
    const closeX        = document.getElementById('close-modal-x');
    const statusClasses = {
        'Pendiente' : 'status-pending',
        'En Camino' : 'status-verification',
        'Completado': 'status-confirmed',
        'Cancelado' : 'status-cancelled'
    };

    /* ── Cerrar modal ── */
    function closeModal() {
        detailModal.classList.add('hide-anim');
        detailModal.classList.remove('show-anim');
        setTimeout(() => {
            detailModal.classList.add('u-hidden');
            detailModal.style.display = '';
            detailModal.classList.remove('hide-anim');
        }, 500);
    }
    closeX.addEventListener('click', closeModal);

    /* ── Dropdown de estado (AJAX) ── */
    document.querySelectorAll('.status-select-inline').forEach(sel => {
        sel.addEventListener('change', function () {
            const id         = this.getAttribute('data-id');
            const new_status = this.value;
            const selectEl   = this;

            // Actualizar clases visuales de inmediato
            Object.values(statusClasses).forEach(c => selectEl.classList.remove(c));
            if (statusClasses[new_status]) selectEl.classList.add(statusClasses[new_status]);

            // Enviar AJAX
            const fd = new FormData();
            fd.append('ajax_update_status', '1');
            fd.append('id', id);
            fd.append('new_status', new_status);

            fetch('dashboard_ver.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        alert('Error al actualizar el estado. Recarga la página.');
                    }
                })
                .catch(() => alert('Error de conexión al actualizar el estado.'));
        });
    });

    /* ── Ver Ficha ── */
    document.querySelectorAll('.view-trigger').forEach(button => {
        button.addEventListener('click', function () {
            const data = JSON.parse(this.getAttribute('data-detail'));

            document.getElementById('detail-id').textContent    = data.id;
            document.getElementById('detail-name').textContent  = data.client_name || 'Cliente Particular';
            document.getElementById('detail-phone').textContent = data.client_phone;
            document.getElementById('detail-age').textContent   = data.age || '--';
            document.getElementById('detail-email').textContent = data.email || '--';
            document.getElementById('detail-type').textContent  = data.service_type;
            document.getElementById('detail-address').textContent = data.service_address;
            document.getElementById('detail-status').textContent  = data.status;
            document.getElementById('detail-notes').textContent   = data.notes || 'Sin notas adicionales.';

            // Monto según tipo de servicio
            const precios = {
                'Urgencias 24/7'          : 'Desde $350 MXN',
                'Residencial'             : 'Desde $500 MXN',
                'Automotriz'              : 'Desde $600 MXN',
                'Seguridad'               : 'Desde $1,200 MXN',
                'Otro'                    : 'Por cotizar'
            };
            document.getElementById('detail-price').textContent =
                precios[data.service_type] || 'Por cotizar';

            // Programación
            let sched = "<span style='color:#ea3323;font-weight:bold;'>🚨 INMEDIATO (Urgencia)</span>";
            if (data.appointment_date && data.appointment_date !== '0000-00-00') {
                sched = "<b>📅 " + data.appointment_date + "</b> | ⏰ " + (data.appointment_time || '');
            }
            document.getElementById('detail-scheduling').innerHTML = sched;

            // Comprobante
            const imgArea = document.getElementById('comprobante-area');
            const imgTag  = document.getElementById('detail-img');
            if (data.payment_proof) {
                imgTag.src = data.payment_proof;
                imgArea.classList.remove('u-hidden');
            } else {
                imgArea.classList.add('u-hidden');
            }

            // Configurar botón de eliminar
            document.getElementById('modal-delete-btn').setAttribute('data-id', data.id);

            // Mostrar modal
            detailModal.classList.remove('u-hidden');
            detailModal.classList.add('u-block');
            setTimeout(() => {
                detailModal.classList.add('show-anim');
                detailModal.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 10);
        });
    });

    /* ── Eliminar Registro (desde modal) ── */
    document.getElementById('modal-delete-btn').addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        if (confirm(`⚠️ ¡ADVERTENCIA CRÍTICA!\n\n¿Está seguro que desea ELIMINAR permanentemente el servicio #${id}?\nEsta acción no se puede deshacer.`)) {
            window.location.href = `dashboard_ver.php?action=delete&id=${id}`;
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>