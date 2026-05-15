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

    $actionsContent = '<div class="admin-actions-container">';

    if ($status === 'Pendiente') {
        $actionsContent .= '<a href="dashboard_ver.php?action=process&id=' . $id . '" class="action-btn approve-btn">Atender</a>';
    }

    if ($status != 'Completado' && $status != 'Cancelado') {
        $actionsContent .= '<a href="dashboard_ver.php?action=complete&id=' . $id . '" class="action-btn approve-btn" style="background:#28a745">Finalizar</a>';
        $actionsContent .= '<button class="action-btn cancel-btn cancel-trigger" data-id="' . $id . '">Cancelar</button>';
    }

    $actionsContent .= '<button class="action-btn view-btn view-trigger" data-detail=\'' . htmlspecialchars(json_encode($servicio)) . '\'>Ver Ficha</button>';

    $actionsContent .= '</div>';

    echo '<tr>';
    echo '<td>#' . $id . '</td>';
    echo '<td><strong>' . htmlspecialchars($servicio['client_phone']) . '</strong></td>';
    echo '<td>' . htmlspecialchars($servicio['service_type']) . '</td>';
    echo '<td>' . htmlspecialchars($servicio['service_address']) . '</td>';
    echo '<td>' . htmlspecialchars($servicio['service_date']) . '</td>';
    echo '<td><span class="' . $class . '">' . $status . '</span></td>';
    echo '<td>' . $actionsContent . '</td>';
    echo '</tr>';
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];
    $new_status = '';

    if ($action == 'process')
        $new_status = 'En Camino';
    if ($action == 'complete')
        $new_status = 'Completado';
    if ($action == 'cancel')
        $new_status = 'Cancelado';

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
}

try {
    $query = "SELECT * FROM service_requests ";
    if ($status_filter != 'all') {
        $query .= " WHERE status = :status_filter";
    }
    $query .= " ORDER BY created_at DESC";

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
                    <th>Tel. Cliente</th>
                    <th>Tipo Servicio</th>
                    <th>Ubicación</th>
                    <th>Fecha/Hora</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
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

    <div id="detail-modal" class="upload-form-modal" style="display: none;">
        <h3 class="form-subtitle">Ficha del Servicio #<span id="detail-id"></span></h3>
        <div class="detail-content" style="color: #444; line-height: 1.8;">
            <p><strong>Teléfono Cliente:</strong> <span id="detail-phone"></span></p>
            <p><strong>Tipo de Servicio:</strong> <span id="detail-type"></span></p>
            <p><strong>Dirección:</strong> <span id="detail-address"></span></p>
            <p><strong>Fecha Programada:</strong> <span id="detail-date"></span></p>
            <p><strong>Estatus Actual:</strong> <span id="detail-status"></span></p>
            <p><strong>Notas adicionales:</strong> <span id="detail-notes"></span></p>
            <div id="comprobante-area" style="margin-top: 15px; display: none;">
                <strong>Evidencia/Comprobante:</strong> <br>
                <img id="detail-img" src=""
                    style="max-width: 100%; border-radius: 10px; margin-top: 10px; border: 1px solid #ddd;">
            </div>
        </div>
        <button type="button" class="option-btn" onclick="document.getElementById('detail-modal').style.display='none';"
            style="margin-top: 20px;">CERRAR FICHA</button>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const detailModal = document.getElementById('detail-modal');

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
                document.getElementById('detail-phone').textContent = data.client_phone;
                document.getElementById('detail-type').textContent = data.service_type;
                document.getElementById('detail-status').textContent = data.status;
                document.getElementById('detail-address').textContent = data.service_address;
                document.getElementById('detail-date').textContent = data.service_date;
                document.getElementById('detail-notes').textContent = data.notes || 'Sin notas';

                const imgArea = document.getElementById('comprobante-area');
                const imgTag = document.getElementById('detail-img');

                if (data.payment_proof) {
                    imgTag.src = data.payment_proof;
                    imgArea.style.display = 'block';
                } else {
                    imgArea.style.display = 'none';
                }

                detailModal.style.display = 'block';
                detailModal.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>