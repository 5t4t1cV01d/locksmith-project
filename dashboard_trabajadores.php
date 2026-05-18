<?php
// ARCHIVO: dashboard_trabajadores.php
$current_page = 'dashboard_trabajadores';
require 'conexion.php';
require 'seguridad.php';
require_admin_login();

$db = getDB();
$status_message = '';
$status_class   = '';

// --- ELIMINAR ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int) $_GET['id'];
    // Protección: no puede eliminarse a sí mismo
    if ($del_id === (int) $_SESSION['user_id']) {
        $status_message = 'No puedes eliminar tu propio usuario.';
        $status_class   = 'error';
    } else {
        try {
            $stmt = $db->prepare("DELETE FROM admin WHERE id = :id");
            $stmt->execute([':id' => $del_id]);
            header("Location: dashboard_trabajadores.php?status_msg=" . urlencode("Trabajador #$del_id eliminado."));
            exit();
        } catch (PDOException $e) {
            $status_message = 'Error al eliminar: ' . $e->getMessage();
            $status_class   = 'error';
        }
    }
}

if (isset($_GET['status_msg'])) {
    $status_message = htmlspecialchars(urldecode($_GET['status_msg']));
    $status_class   = 'success';
}

// --- REGISTRAR ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$name || !$email || !$password) {
        $status_message = 'Todos los campos son obligatorios.';
        $status_class   = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status_message = 'El email no tiene un formato válido.';
        $status_class   = 'error';
    } elseif (strlen($password) < 6) {
        $status_message = 'La contraseña debe tener al menos 6 caracteres.';
        $status_class   = 'error';
    } else {
        try {
            // Verificar si el email ya existe
            $check = $db->prepare("SELECT id FROM admin WHERE email = :email");
            $check->execute([':email' => $email]);
            if ($check->rowCount() > 0) {
                $status_message = 'Ya existe un trabajador con ese email.';
                $status_class   = 'error';
            } else {
                // Cifrado centralizado mediante seguridad.php
                $hashed = hash_password($password);
                $stmt = $db->prepare("INSERT INTO admin (name, email, password) VALUES (:name, :email, :password)");
                $stmt->execute([':name' => $name, ':email' => $email, ':password' => $hashed]);
                header("Location: dashboard_trabajadores.php?status_msg=" . urlencode("Trabajador \"$name\" registrado con éxito."));
                exit();
            }
        } catch (PDOException $e) {
            $status_message = 'Error al registrar: ' . $e->getMessage();
            $status_class   = 'error';
        }
    }
}

// --- CARGAR LISTA ---
$trabajadores = [];
try {
    $stmt = $db->query("SELECT id, name, email, created_at FROM admin ORDER BY created_at DESC");
    $trabajadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

include 'includes/header.php';
?>
<section class="admin-dashboard-section">
    <h1 class="page-title">GESTIÓN DE TRABAJADORES</h1>
    <p class="subtitle">Administra los usuarios con acceso al panel de administración.</p>

    <?php if (!empty($status_message)): ?>
        <div class="status-alert <?php echo $status_class; ?> animate-slide"><?php echo $status_message; ?></div>
    <?php endif; ?>

    <!-- ========================= FORMULARIO DE REGISTRO ========================= -->
    <div class="admin-form-container">
        <h2 class="section-subtitle-dash">Registrar Nuevo Trabajador</h2>

        <form method="POST" action="dashboard_trabajadores.php" class="admin-form worker-form">

            <div class="worker-fields-wrapper">
                <div class="worker-field-group">
                    <label for="name" class="worker-label">NOMBRE COMPLETO:</label>
                    <input type="text" id="name" name="name" class="worker-input"
                           placeholder="Ej: Juan Pérez" required>
                </div>

                <div class="worker-field-group">
                    <label for="email" class="worker-label">EMAIL:</label>
                    <input type="email" id="email" name="email" class="worker-input"
                           placeholder="trabajador@laganzua.com" required>
                </div>

                <div class="worker-field-group">
                    <label for="password" class="worker-label">CONTRASEÑA:</label>
                    <input type="password" id="password" name="password" class="worker-input"
                           placeholder="Mínimo 6 caracteres" required>
                </div>
            </div>

            <div class="worker-submit-wrapper">
                <button type="submit" class="option-btn worker-submit-btn">
                    <i class="fa-solid fa-user-plus"></i> REGISTRAR TRABAJADOR
                </button>
            </div>
        </form>
    </div>

    <!-- ========================= TABLA DE TRABAJADORES ========================= -->
    <div class="reservations-table-container">
        <h2 class="section-subtitle-dash" style="padding: 20px 20px 0;">Trabajadores Registrados</h2>
        <table class="reservations-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE</th>
                    <th>EMAIL</th>
                    <th>FECHA DE REGISTRO</th>
                    <th>ACCIÓN</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($trabajadores)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:30px; color: var(--3main-color);">
                            No hay trabajadores registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($trabajadores as $t):
                        $is_self = ((int)$t['id'] === (int)$_SESSION['user_id']);
                    ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($t['id']); ?></td>
                        <td class="u-fw-bold">
                            <?php echo htmlspecialchars($t['name']); ?>
                            <?php if ($is_self): ?>
                                <span style="font-size:0.75em; color:var(--2main-color); font-weight:normal;"> (tú)</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($t['email']); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($t['created_at']))); ?></td>
                        <td>
                            <?php if ($is_self): ?>
                                <span style="color:var(--3main-color); font-size:0.85em;">—</span>
                            <?php else: ?>
                                <a href="dashboard_trabajadores.php?action=delete&id=<?php echo $t['id']; ?>"
                                   class="action-btn-mini btn-eliminar"
                                   onclick="return confirm('¿Eliminar a <?php echo htmlspecialchars(addslashes($t['name'])); ?>? Esta acción no se puede deshacer.')">
                                    <i class="fa-solid fa-trash-can"></i> Eliminar
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
