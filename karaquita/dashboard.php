<?php
require_once "db.php";
require_once "permisos.php";

echo '<h1 style="margin-bottom:20px;">Panel principal</h1>';

/* ================================
   ESTADÍSTICAS DEL SISTEMA
   ================================ */
$inventario_total = $pdo->query("SELECT COUNT(*) FROM inventario")->fetchColumn();
$activos          = $pdo->query("SELECT COUNT(*) FROM inventario WHERE estado='activo'")->fetchColumn();
$inactivos        = $pdo->query("SELECT COUNT(*) FROM inventario WHERE estado='inactivo'")->fetchColumn();
$mantenimiento    = $pdo->query("SELECT COUNT(*) FROM inventario WHERE estado='mantenimiento'")->fetchColumn();

$movimientos_total = $pdo->query("SELECT COUNT(*) FROM movimientos")->fetchColumn();
$prestamos_pend    = $pdo->query("SELECT COUNT(*) FROM prestamos WHERE estado='pendiente'")->fetchColumn();
$prestamos_aprob   = $pdo->query("SELECT COUNT(*) FROM prestamos WHERE estado='aprobado'")->fetchColumn();
$prestamos_dev     = $pdo->query("SELECT COUNT(*) FROM prestamos WHERE estado='devuelto'")->fetchColumn();

?>

<style>
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.card-stat {
    background: rgba(15,15,15,0.55);
    padding: 22px;
    border-radius: 14px;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(0,132,255,0.25);
    box-shadow: 0 0 18px #0007;
    animation: fadeIn .7s ease;
    transition: .25s;
}
.card-stat:hover {
    transform: translateY(-5px) scale(1.03);
    box-shadow: 0 0 25px #008cffaa;
}

.card-title {
    font-size: 17px;
    margin-bottom: 12px;
    opacity: .8;
}

.card-number {
    font-size: 32px;
    font-weight: bold;
    text-shadow: 0 0 10px #008cff;
}

.icon {
    font-size: 45px;
    float: right;
    opacity: .8;
}

.section-title {
    margin-top: 40px;
    margin-bottom: 15px;
    font-size: 20px;
    border-left: 4px solid #008cff;
    padding-left: 12px;
}
</style>

<!-- TARJETAS PRINCIPALES -->
<div class="dashboard-grid">

    <div class="card-stat fade">
        <div class="icon">📦</div>
        <div class="card-title">Productos Totales</div>
        <div class="card-number"><?= $inventario_total ?></div>
    </div>

    <div class="card-stat fade">
        <div class="icon">✔️</div>
        <div class="card-title">Activos</div>
        <div class="card-number" style="color:#00d8ff;"><?= $activos ?></div>
    </div>

    <div class="card-stat fade">
        <div class="icon">⛔</div>
        <div class="card-title">Inactivos</div>
        <div class="card-number" style="color:#ff3b3b;"><?= $inactivos ?></div>
    </div>

    <div class="card-stat fade">
        <div class="icon">🛠️</div>
        <div class="card-title">En mantenimiento</div>
        <div class="card-number" style="color:#ffaa00;"><?= $mantenimiento ?></div>
    </div>

    <div class="card-stat fade">
        <div class="icon">🔄</div>
        <div class="card-title">Movimientos Registrados</div>
        <div class="card-number"><?= $movimientos_total ?></div>
    </div>

    <div class="card-stat fade">
        <div class="icon">📚</div>
        <div class="card-title">Préstamos Pendientes</div>
        <div class="card-number" style="color:#ff3b3b;"><?= $prestamos_pend ?></div>
    </div>

    <div class="card-stat fade">
        <div class="icon">✅</div>
        <div class="card-title">Préstamos Aprobados</div>
        <div class="card-number" style="color:#00ff88;"><?= $prestamos_aprob ?></div>
    </div>

    <div class="card-stat fade">
        <div class="icon">📦</div>
        <div class="card-title">Préstamos Devueltos</div>
        <div class="card-number" style="color:#00d8ff;"><?= $prestamos_dev ?></div>
    </div>

</div>


<!-- SECCIÓN DE ACCESOS RÁPIDOS -->
<h2 class="section-title">Accesos rápidos</h2>
<div class="dashboard-grid">

    <a href="layout.php?page=inventario_listar" class="card-stat fade" style="text-decoration:none;color:white;">
        <div class="icon">📋</div>
        <div class="card-title">Ver inventario</div>
        <div class="card-number" style="font-size:22px;">Ingresar →</div>
    </a>

    <a href="layout.php?page=registrar_movimiento" class="card-stat fade" style="text-decoration:none;color:white;">
        <div class="icon">🔄</div>
        <div class="card-title">Registrar Movimiento</div>
        <div class="card-number" style="font-size:22px;">Ingresar →</div>
    </a>

    <a href="layout.php?page=ver_prestamos" class="card-stat fade" style="text-decoration:none;color:white;">
        <div class="icon">📚</div>
        <div class="card-title">Administrar Préstamos</div>
        <div class="card-number" style="font-size:22px;">Ingresar →</div>
    </a>

</div>
