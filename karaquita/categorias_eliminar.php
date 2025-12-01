<?php
require_once "db.php";
require_once "permisos.php";

if (!tiene_permiso('categorias_delete')) {
    echo "<div class='form-container'><h3>No tienes permiso para eliminar categorías</h3></div>";
    exit;
}

$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM categorias WHERE id=?");
$stmt->execute([$id]);
$cat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cat) {
    echo "<div class='form-container'><h3>Categoría no encontrada</h3></div>";
    exit;
}

$pdo->prepare("DELETE FROM categorias WHERE id=?")->execute([$id]);

echo "<div class='form-container'><h2>✔ Categoría eliminada</h2>
      <p>La categoría \"".htmlspecialchars($cat['nombre'])."\" fue eliminada.</p>
      <p><a class='btn' href='layout.php?page=categorias_listar'>Volver a categorías</a></p>
      </div>";
