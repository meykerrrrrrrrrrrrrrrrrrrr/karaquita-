<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "db.php";
require_once "permisos.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$page = $_GET['page'] ?? 'dashboard';

$allowed = [
    'dashboard',
    'inventario_listar', 'inventario_agregar', 'inventario_editar', 'inventario_eliminar',
    'registrar_movimiento', 'ver_movimientos',
    'categorias', 'categorias_listar', 'categorias_agregar', 'categorias_editar', 'categorias_eliminar',
    'solicitar_prestamo', 'registrar_prestamo', 'ver_prestamos', 'aprobar_prestamo', 'devolver_prestamo',
    'gestion_usuarios', 'gestion_roles', 'mis_permisos',
    'reportes'
];

if (!in_array($page, $allowed)) {
    $page = "dashboard";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel - Sistema Inventario</title>
<link rel="stylesheet" href="styles.css">
</head>

<body>

<!-- SIDEBAR -->
<?php include "menu.php"; ?>

<!-- CONTENIDO -->
<div class="with-sidebar fade">

    <div class="container fade">

        <?php
        $file = $page . ".php";
        if (file_exists($file)) {
            include $file;
        } else {
            echo "<h2>Módulo no encontrado</h2>";
        }
        ?>

    </div>

</div>

</body>
</html>
