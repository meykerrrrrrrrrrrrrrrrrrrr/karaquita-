<?php
session_start();
require_once 'db.php';
include "menu.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$rol_id = $_SESSION['role_id'];

function tiene_permiso($accion) {
    global $pdo, $rol_id;
    $stmt = $pdo->prepare("SELECT permitido FROM permisos WHERE rol_id = :rol_id AND accion = :accion");
    $stmt->execute(['rol_id' => $rol_id, 'accion' => $accion]);
    $permiso = $stmt->fetch();
    return $permiso ? $permiso['permitido'] : 0;
}

if (!tiene_permiso('inventario_ver')) {
    echo "No tienes permiso.";
    exit();
}

$stmt = $pdo->query("SELECT * FROM inventario");
$inventario = $stmt->fetchAll();
?>
<div class="with-sidebar">
<h2>Inventario</h2>

<table>
<thead>
<tr>
    <th>Código</th>
    <th>Nombre</th>
    <th>Descripción</th>
    <th>Categoría</th>
    <th>Cantidad</th>
    <th>Tipo</th>
    <th>Estado</th>
    <?php if (tiene_permiso('inventario_crud')): ?>
        <th>Acciones</th>
    <?php endif; ?>
</tr>
</thead>
<tbody>

<?php foreach ($inventario as $item): ?>
<tr>
    <td><?= $item['codigo'] ?></td>
    <td><?= $item['nombre'] ?></td>
    <td><?= $item['descripcion'] ?></td>

    <td>
        <?php
        $cat = $pdo->prepare("SELECT nombre FROM categorias WHERE id = :id");
        $cat->execute(['id' => $item['categoria_id']]);
        echo $cat->fetchColumn();
        ?>
    </td>

    <td><?= $item['cantidad'] ?></td>
    <td><?= $item['tipo'] ?></td>
    <td><?= $item['estado'] ?></td>

    <?php if (tiene_permiso('inventario_crud')): ?>
    <td>
        <a href="editar_inventario.php?id=<?= $item['id'] ?>">✏️</a>
        <a href="eliminar_inventario.php?id=<?= $item['id'] ?>" class="btn-danger">🗑️</a>
    </td>
    <?php endif; ?>

</tr>
<?php endforeach; ?>

</tbody>
</table>

</div>
