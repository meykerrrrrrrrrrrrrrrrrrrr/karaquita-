<?php
if (!tiene_permiso('inventario_insert')) {
    echo "<h3>No tienes permiso para agregar inventario.</h3>";
    exit();
}

require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre      = trim($_POST['nombre']);
    $cantidad    = intval($_POST['cantidad']);
    $categoria   = intval($_POST['categoria']);
    $descripcion = trim($_POST['descripcion']);
    $estado = $_POST['estado'];
    $ubicacion   = trim($_POST['ubicacion']);

    $sql = $pdo->prepare("
        INSERT INTO inventario (nombre, cantidad, categoria_id, descripcion,estado, ubicacion, fecha_creacion)
        VALUES (?, ?, ?, ?, ?,?, NOW())
    ");

    $sql->execute([$nombre, $cantidad, $categoria, $descripcion, $estado,$ubicacion]);

    echo "<div style='color:lightgreen; margin-bottom:10px;'>✔ Producto agregado correctamente</div>";
}

$cats = $pdo->query("SELECT id, nombre FROM categorias ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h2>Agregar producto</h2>

    <form method="POST">

        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Cantidad:</label>
        <input type="number" name="cantidad" min="0" required>

        <label>Categoría:</label>
        <select name="categoria">
            <?php foreach($cats as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Descripción:</label>
        <textarea name="descripcion"></textarea>

        <label>Ubicación:</label>
        <input type="text" name="ubicacion" placeholder="Ej: Estante A, Caja 3" required>
        <label>Estado del producto:</label>
        <select name="estado" required>
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
            <option value="mantenimiento">En mantenimiento</option>
        </select>

        <button type="submit">Agregar</button>

    </form>
</div>