<?php
require_once "db.php";
require_once "permisos.php";

if (!tiene_permiso('categorias_update')) {
    echo "<div class='form-container'><h3>No tienes permiso para editar categorías.</h3></div>";
    exit;
}

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
$stmt->execute([$id]);
$cat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cat) {
    echo "<div class='form-container'><h3>Categoría no encontrada.</h3></div>";
    exit;
}

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    if ($nombre !== '') {
        $pdo->prepare("UPDATE categorias SET nombre = ? WHERE id = ?")->execute([$nombre, $id]);
        $mensaje = "Categoría actualizada.";
        // recargar
        $stmt->execute([$id]);
        $cat = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $mensaje = "El nombre no puede estar vacío.";
    }
}
?>

<div class="card">
    <h2>Editar categoría</h2>

    <?php if ($mensaje): ?>
        <div style="color:lightgreen;margin-bottom:10px;"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Nombre</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($cat['nombre']) ?>" required>

        <button class="neon-button" type="submit">Guardar</button>
        <a class="btn btn-secondary" href="layout.php?page=categorias_listar" style="margin-left:8px;">Cancelar</a>
    </form>
</div>
