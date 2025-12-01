<?php
require_once 'db.php';
require_once 'permisos.php';

if (!tiene_permiso('inventario_ver')) {
    echo "<h2>No tienes permiso para ver inventario.</h2>";
    exit;
}

// -------------------------
//  Capturar búsqueda
// -------------------------
$busqueda = $_GET['buscar'] ?? '';

// -------------------------
//  Capturar filtro de estado
// -------------------------
$estado = $_GET['estado'] ?? '';

// -------------------------
//  Construcción dinámica del SQL
// -------------------------
$sql = "SELECT * FROM inventario WHERE 1=1";
$params = [];

// BÚSQUEDA
if ($busqueda != '') {
    $sql .= " AND (nombre LIKE ? OR descripcion LIKE ? OR ubicacion LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
}

// FILTRO DE ESTADO
if ($estado != '') {
    $sql .= " AND estado = ?";
    $params[] = $estado;
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="card">
    <h2>Inventario</h2>

    <!-- BUSCADOR -->
    <form method="GET" action="layout.php">
        <input type="hidden" name="page" value="inventario_listar">
        <input type="text" name="buscar" placeholder="Buscar..." 
            value="<?= htmlspecialchars($busqueda) ?>" 
            style="padding:8px;width:250px;">
        <button>Buscar</button>
    </form>

    <!-- FILTRO POR ESTADO -->
    <form method="GET" action="layout.php" style="margin-top:10px; display:flex; gap:10px;">
        <input type="hidden" name="page" value="inventario_listar">

        <select name="estado" style="padding:6px; border-radius:5px;">
            <option value="">-- Estado --</option>
            <option value="activo" <?= ($estado=="activo")?'selected':'' ?>>Activo</option>
            <option value="inactivo" <?= ($estado=="inactivo")?'selected':'' ?>>Inactivo</option>
            <option value="mantenimiento" <?= ($estado=="mantenimiento")?'selected':'' ?>>En mantenimiento</option>
        </select>

        <button type="submit" style="padding:6px 12px; background:#1d8cf8; color:white; border:none; border-radius:5px;">
            Filtrar
        </button>
    </form>

    <br>

    <table class="tabla">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Cantidad</th>
        <th>Ubicación</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($items as $i): ?>
    <tr>
        <td><?= $i['id'] ?></td>
        <td><?= htmlspecialchars($i['nombre']) ?></td>
        <td><?= htmlspecialchars($i['descripcion']) ?></td>
        <td><?= $i['cantidad'] ?></td>
        <td><?= htmlspecialchars($i['ubicacion']) ?></td>
        <td><?= htmlspecialchars($i['estado'] ?? 'activo') ?></td>

        <td>

            <?php if (tiene_permiso('inventario_update')): ?>
                <a href="layout.php?page=inventario_editar&id=<?= $i['id'] ?>">✏ Editar</a>
            <?php endif; ?>

            <?php if (tiene_permiso('inventario_delete')): ?>
                | <a href="layout.php?page=inventario_eliminar&id=<?= $i['id'] ?>"
                    onclick="return confirm('¿Eliminar este producto?')">🗑 Eliminar</a>
            <?php endif; ?>

        </td>
    </tr>
    <?php endforeach; ?>
    </table>
</div>