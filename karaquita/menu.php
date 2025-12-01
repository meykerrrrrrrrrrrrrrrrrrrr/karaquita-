<?php
if (!isset($_SESSION)) session_start();
require_once "permisos.php";

// Si no tiene sesión, no mostrar menú
if (!isset($_SESSION['user_id'])) exit();

$usuario = $_SESSION['username'];
$rol_id  = $_SESSION['role_id'];

// Si es ROOT (rol 1) → mostrar TODO sin restricciones
$es_root = ($rol_id == 1);
?>

<style>
.sidebar{
    width:260px;
    background:#111;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    padding:20px;
    overflow-y:auto;
    color:white;
}
.sidebar a{
    display:block;
    padding:10px;
    margin:5px 0;
    background:#1d1d1d;
    color:white;
    text-decoration:none;
    border-radius:5px;
}
.sidebar a:hover{
    background:#333;
}
.with-sidebar{
    margin-left:280px;
    padding:20px;
}
</style>

<aside class="sidebar">
    <h2>📦 Karaquita</h2>
    <p>👤 <?= htmlspecialchars($usuario) ?></p>
    <hr>

    <a href="layout.php?page=dashboard">🏠 Dashboard</a>

    <!-- INVENTARIO -->
    <?php if ($es_root || tiene_permiso('inventario_ver')): ?>
        <a href="layout.php?page=inventario_listar">📋 Ver Inventario</a>
    <?php endif; ?>

    <?php if ($es_root || tiene_permiso('inventario_crud')): ?>
        <a href="layout.php?page=inventario_agregar">➕ Agregar Inventario</a>
    <?php endif; ?>

    <!-- MOVIMIENTOS -->
    <?php if ($es_root || tiene_permiso('movimientos_crud')): ?>
        <a href="layout.php?page=registrar_movimiento">🔄 Registrar Movimiento</a>
        <a href="layout.php?page=ver_movimientos">📜 Ver Movimientos</a>
    <?php endif; ?>

    <!-- PRESTAMOS -->
    <?php if ($es_root || tiene_permiso('prestamos_insert')): ?>
        <a href="layout.php?page=solicitar_prestamo">📝 Solicitar Préstamo</a>
    <?php endif; ?>

    <?php if ($es_root || tiene_permiso('prestamos_crud')): ?>
        <a href="layout.php?page=ver_prestamos">📚 Admin. Préstamos</a>
    <?php endif; ?>

    <!-- CATEGORÍAS -->
    <?php if ($es_root || tiene_permiso('categorias_ver') || tiene_permiso('categorias_crud')): ?>
        <a href="layout.php?page=categorias_listar">📂 Categorías</a>
    <?php endif; ?>

    <!-- USUARIOS Y ROLES -->
    <?php if ($es_root || tiene_permiso('usuarios_crud')): ?>
        <a href="layout.php?page=gestion_usuarios">👥 Usuarios</a>
        <a href="layout.php?page=gestion_roles">🛡 Roles y Permisos</a>
    <?php endif; ?>

    <!-- MIS PERMISOS -->
    

    <!-- REPORTES -->
    <?php if ($es_root || tiene_permiso('reportes')): ?>
        <a href="layout.php?page=reportes">📊 Reportes</a>
    <?php endif; ?>

    <hr>
    <a href="logout.php" style="background:#8b0000;">🚪 Cerrar sesión</a>

</aside>
<a href="layout.php?page=mis_permisos">🔐 Mis Permisos</a>