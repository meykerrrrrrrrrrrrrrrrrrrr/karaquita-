<?php
// gestion_roles.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';
require_once 'permisos.php';

if (!tiene_permiso('roles_crud')) {
    echo "<h2>No tienes permiso para gestionar roles.</h2>";
    exit();
}

// Obtener roles
$roles = $pdo->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de permisos disponibles (acciones únicas)
$perms = $pdo->query("SELECT DISTINCT accion FROM permisos ORDER BY accion ASC")->fetchAll(PDO::FETCH_ASSOC);

// Cuando se selecciona un rol, se muestra su configuración actual
$rol_seleccionado = isset($_POST['rol']) ? (int)$_POST['rol'] : null;

// Si se intenta guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {

    $rol_id = (int)$_POST['rol'];

    // PROTECCIÓN: El root (rol 1) NUNCA pierde permisos
    if ($rol_id == 1) {

        // Fuerza todos los permisos ON
        $pdo->prepare("UPDATE permisos SET permitido = 1 WHERE rol_id = 1")->execute();

        echo "<div style='color: #00ff7f; padding: 10px;'>✔ Permisos del ROOT actualizados (no se pueden desactivar).</div>";
    } else {

        // Actualización normal de permisos
        foreach ($perms as $p) {
            $act = $p['accion'];
            $permitido = isset($_POST['perm_' . $act]) ? 1 : 0;

            $pdo->prepare("UPDATE permisos 
                           SET permitido = :v 
                           WHERE rol_id = :r AND accion = :a")
                ->execute([
                    'v' => $permitido,
                    'r' => $rol_id,
                    'a' => $act
                ]);
        }

        echo "<div style='color: #00ff7f; padding: 10px;'>✔ Permisos actualizados correctamente.</div>";
    }

    // Forzar actualización inmediata de permisos del usuario actual
    $_SESSION['permisos_cache'] = null;

    // Refrescar la vista
    $rol_seleccionado = $rol_id;
}
?>
<div class="card">
    <h2>Gestión de roles y permisos</h2>

    <form method="POST">

        <label><b>Seleccionar rol:</b></label>
        <select name="rol" onchange="this.form.submit()" style="padding:6px;">
            <option value="">-- Seleccionar --</option>
            <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id'] ?>" 
                    <?= ($rol_seleccionado == $r['id']) ? 'selected' : '' ?>>
                    <?= strtoupper(htmlspecialchars($r['nombre'])) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br><br>

        <?php if ($rol_seleccionado): ?>

            <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; background: #111; color: #eee;">
                <thead>
                    <tr style="background: #222;">
                        <th>Permiso</th>
                        <th>Permitido</th>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    // Traer permisos actuales del rol
                    $stmt = $pdo->prepare("SELECT accion, permitido FROM permisos WHERE rol_id = ?");
                    $stmt->execute([$rol_seleccionado]);
                    $permisos_actuales = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                    ?>

                    <?php foreach ($perms as $p): 
                        $accion = $p['accion'];
                        $checked = (!empty($permisos_actuales[$accion])) ? "checked" : "";
                    ?>

                        <tr>
                            <td><?= htmlspecialchars($accion) ?></td>
                            <td>

                                <?php if ($rol_seleccionado == 1): ?>
                                    <!-- Root no modificable -->
                                    <input type="checkbox" checked disabled>
                                <?php else: ?>
                                    <!-- Para roles normales -->
                                    <input type="checkbox" name="perm_<?= $accion ?>" <?= $checked ?>>
                                <?php endif; ?>

                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>

            <br>

            <button name="save" style="padding: 10px 20px; background:#28a745; color:white; border:none;">
                Guardar cambios
            </button>

        <?php endif; ?>

    </form>
</div>