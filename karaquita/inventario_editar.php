<?php
if (!tiene_permiso('inventario_update')) {
    echo "<h3>No tienes permiso para editar inventario.</h3>";
    exit();
}

require_once "db.php";

/* ------------------------------
   1. Si no viene ID → mostrar lista
-------------------------------- */
if (!isset($_GET['id'])) {

    echo "<h2>Seleccionar producto para editar</h2>";

    $sql = $pdo->query("SELECT id, nombre, cantidad FROM inventario ORDER BY nombre");

    echo "<table border='1' cellpadding='8' style='background:#222;color:white;'>
            <tr><th>Producto</th><th>Cantidad</th><th>Acciones</th></tr>";

    while ($p = $sql->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>{$p['nombre']}</td>
                <td>{$p['cantidad']}</td>
                <td>
                    <a href='layout.php?page=inventario_editar&id={$p['id']}'>✏️ Editar</a>
                </td>
              </tr>";
    }

    echo "</table>";
    exit();
}

/* ------------------------------
   2. Cargar producto para editar
-------------------------------- */
$id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM inventario WHERE id = ?");
$stmt->execute([$id]);
$prod = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prod) {
    echo "<h3>Producto no encontrado.</h3>";
    exit();
}

/* ------------------------------
   3. Procesar ACTUALIZACIÓN
-------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre      = trim($_POST['nombre']);
    $cantidad    = intval($_POST['cantidad']);
    $categoria   = intval($_POST['categoria']);
    $descripcion = trim($_POST['descripcion']);
    $ubicacion   = trim($_POST['ubicacion']);

    $sql = $pdo->prepare("
        UPDATE inventario 
        SET 
            nombre = ?, 
            cantidad = ?, 
            categoria_id = ?,
            descripcion = ?,
            estado=?,
            ubicacion = ?,
            fecha_actualizacion = NOW()
        WHERE id = ?
    ");
    
    $sql->execute([
        $nombre, 
        $cantidad, 
        $categoria,
        $descripcion,
        $estado,
        $ubicacion,
        $id
    ]);

    echo "<div style='color:lightgreen;'>✔ Producto actualizado correctamente</div>";

    // Volver a cargar datos actualizados
    $stmt->execute([$id]);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* ------------------------------
   4. Obtener categorías
-------------------------------- */
$cats = $pdo->query("SELECT id, nombre FROM categorias ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="card">
    <h2>Editar producto</h2>

    <form method="POST">

        <label>Nombre del producto:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($prod['nombre']) ?>" required>

        <label>Cantidad:</label>
        <input type="number" name="cantidad" value="<?= $prod['cantidad'] ?>" min="0" required>

        <label>Categoría:</label>
        <select name="categoria" required>
            <?php foreach($cats as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $prod['categoria_id']==$c['id']?'selected':'' ?>>
                    <?= htmlspecialchars($c['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Descripción:</label>
        <textarea name="descripcion" rows="3"><?= htmlspecialchars($prod['descripcion'] ?? '') ?></textarea>

        <label>Ubicación:</label>
        <input type="text" name="ubicacion" value="<?= htmlspecialchars($prod['ubicacion'] ?? '') ?>">
        <label>Estado del producto:</label>
        <select name="estado" required>
            <option value="activo" <?= $prod['estado']=='activo'?'selected':'' ?>>Activo</option>
            <option value="inactivo" <?= $prod['estado']=='inactivo'?'selected':'' ?>>Inactivo</option>
            <option value="mantenimiento" <?= $prod['estado']=='mantenimiento'?'selected':'' ?>>En mantenimiento</option>
        </select>
        

        <button type="submit">Guardar cambios</button>

    </form>
</div>
