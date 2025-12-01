<?php
if (!tiene_permiso('inventario_delete')) {
    echo "<h3>No tienes permiso para eliminar productos.</h3>";
    exit();
}

require_once "db.php";

/* -------------------------
   1. Si no hay ID → lista
--------------------------- */
if (!isset($_GET['id'])) {
    echo "<h2>Seleccionar producto para eliminar</h2>";

    $sql = $pdo->query("SELECT id, nombre, cantidad FROM inventario ORDER BY nombre");

    echo "<table border='1' cellpadding='8'>
            <tr><th>Nombre</th><th>Cantidad</th><th>Acciones</th></tr>";

    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['nombre']}</td>
                <td>{$row['cantidad']}</td>
                <td>
                    <a style='color:red;' href='layout.php?page=inventario_eliminar&id={$row['id']}'>🗑 Eliminar</a>
                </td>
              </tr>";
    }

    echo "</table>";
    exit();
}

/* -------------------------
   2. Si hay ID, eliminar
--------------------------- */
$id = intval($_GET['id']);

$del = $pdo->prepare("DELETE FROM inventario WHERE id = ?");
$del->execute([$id]);

echo "<h3 style='color:green;'>Producto eliminado correctamente ✔</h3>";
echo "<a href='layout.php?page=inventario_eliminar'>Volver</a>";
