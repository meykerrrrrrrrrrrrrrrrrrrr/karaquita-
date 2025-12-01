<?php
require_once "db.php";
require_once "permisos.php";

if (!tiene_permiso('categorias_ver')) {
    echo "<div class='form-container'><h3>No tienes permiso para ver categorías.</h3></div>";
    exit;
}
?>

<div class="form-container">
    <h2>Categorías</h2>
    <p>Desde aquí puedes administrar las categorías del inventario.</p>

    <p>
        <?php if (tiene_permiso('categorias_crud')): ?>
            <a class="neon-button" href="layout.php?page=categorias_agregar">➕ Nueva categoría</a>
        <?php endif; ?>
    </p>
</div>
