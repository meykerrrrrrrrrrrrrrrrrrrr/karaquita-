<?php
session_start();

// Si ya está logueado, lo mandamos directo al panel
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karaquita - Sistema de Inventario</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="landing-nav">
        <div class="logo">
            ⚡ Karaquita
        </div>
        <div class="nav-links">
            <a href="login.php" class="btn">Iniciar Sesión</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="hero-content">
            <h1 class="fade">Gestión de Inventario del Futuro</h1>
            <p class="fade">Controla tus activos, gestiona préstamos y optimiza tu flujo de trabajo con una interfaz
                rápida, moderna y segura.</p>
            <a href="login.php" class="hero-btn fade">Empezar Ahora</a>
        </div>
    </header>

    <!-- Features Section -->
    <section class="features">
        <h2>Características Principales</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📦</div>
                <h3>Inventario Total</h3>
                <p>Mantén un registro detallado de todos tus activos con categorías, estados y ubicaciones.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔄</div>
                <h3>Préstamos y Devoluciones</h3>
                <p>Gestiona el flujo de equipos con un sistema robusto de préstamos, aprobaciones y devoluciones.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Reportes Avanzados</h3>
                <p>Genera reportes detallados en PDF y Excel para tomar decisiones basadas en datos reales.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <h3>Seguridad y Roles</h3>
                <p>Control de acceso granular con roles de usuario y permisos personalizados.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <p>&copy; <?php echo date('Y'); ?> Karaquita System. Todos los derechos reservados.</p>
    </footer>

</body>

</html>