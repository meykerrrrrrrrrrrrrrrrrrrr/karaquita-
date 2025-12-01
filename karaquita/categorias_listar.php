<?php
require_once "db.php";
require_once "permisos.php";

if (!tiene_permiso('categorias_ver')) {
    echo "<div class='form-container'><h3>No tienes permiso para ver categorías.</h3></div>";
    exit;
}

$cats = $pdo->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h2>Categorías</h2>

    <?php if (tiene_permiso('categorias_crud')): ?>
        <p><a class="btn" href="layout.php?page=categorias_agregar">➕ Nueva categoría</a></p>
    <?php endif; ?>

    <table class="neon-table">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($cats as $c): ?>
    <tr>
        <td><?= $c['id'] ?></td>
        <td><?= htmlspecialchars($c['nombre']) ?></td>
        <td>
            <?php if (tiene_permiso('categorias_update')): ?>
                <a class="neon-button" href="layout.php?page=categorias_editar&id=<?= $c['id'] ?>">Editar</a>
            <?php endif; ?>

            <?php if (tiene_permiso('categorias_delete')): ?>
                <a class="neon-button" style="background:#ff0033;" 
                   onclick="return confirm('¿Eliminar categoría?')" 
                   href="layout.php?page=categorias_eliminar&id=<?= $c['id'] ?>">Eliminar</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </table>
</div>
