<?php
require_once "db.php";
require_once "permisos.php";

if (!tiene_permiso('inventario_insert')) {
    echo "<div class='form-container'><h3>No tienes permiso para agregar inventario.</h3></div>";
    exit();
}

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $categoria = intval($_POST['categoria_id'] ?? 0);
    $tipo = $_POST['tipo'] ?? 'equipo';
    $cantidad = intval($_POST['cantidad'] ?? 0);
    $estado = $_POST['estado'] ?? 'activo';
    $ubicacion = trim($_POST['ubicacion'] ?? '');

    // Inserción (mantengo la funcionalidad original)
    $sql = $pdo->prepare("
        INSERT INTO inventario (codigo, nombre, descripcion, categoria_id, tipo, cantidad, estado, ubicacion)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $sql->execute([$codigo, $nombre, $descripcion, $categoria, $tipo, $cantidad, $estado, $ubicacion]);
    $mensaje = "Producto agregado correctamente.";
}
?>

<div class="form-container">
    <h2>Agregar inventario</h2>

    <?php if ($mensaje): ?>
        <div style="color:lightgreen; margin-bottom:12px;"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Código:</label>
        <input type="text" name="codigo" required>

        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Descripción:</label>
        <textarea name="descripcion"></textarea>

        <label>Categoría:</label>
        <select name="categoria_id">
            <?php
            $cats = $pdo->query("SELECT id,nombre FROM categorias ORDER BY nombre");
            while ($c = $cats->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$c['id']}'>".htmlspecialchars($c['nombre'])."</option>";
            }
            ?>
        </select>

        <label>Tipo:</label>
        <select name="tipo">
            <option value="equipo">Equipo</option>
            <option value="insumo">Insumo</option>
        </select>

        <label>Cantidad:</label>
        <input type="number" name="cantidad" min="0" required>

        <label>Estado:</label>
        <select name="estado">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
            <option value="mantenimiento">Mantenimiento</option>
        </select>

        <label>Ubicación:</label>
        <input type="text" name="ubicacion">

        <button class="neon-button" type="submit">Guardar</button>
    </form>
</div>
