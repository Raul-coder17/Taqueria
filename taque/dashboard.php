<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$rol = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel - Taquería</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>Bienvenido, <?php echo $rol; ?>!</h1>
        <p class="dashboard-subtitle">Administra tu taquería con facilidad</p>
        <?php if ($rol == 'admin') { ?>
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <i class="fas fa-user-shield"></i>
                    <h3>Registrar Administrador</h3>
                    <p>Añade un nuevo administrador al sistema.</p>
                    <a href="register_admin.php">Ir</a>
                </div>
                <div class="dashboard-card">
                    <i class="fas fa-user-tie"></i>
                    <h3>Registrar Mesero</h3>
                    <p>Inscribe a un nuevo mesero para tomar órdenes.</p>
                    <a href="register_mesero.php">Ir</a>
                </div>
                <div class="dashboard-card">
                    <i class="fas fa-utensils"></i>
                    <h3>Gestionar Menú</h3>
                    <p>Agrega, edita o elimina platillos del menú.</p>
                    <a href="gestionar_menu.php">Ir</a>
                </div>
                <div class="dashboard-card">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Ver Órdenes</h3>
                    <p>Revisa y gestiona las órdenes actuales.</p>
                    <a href="ordenes.php">Ir</a>
                </div>
                <div class="dashboard-card">
                    <i class="fas fa-sign-out-alt"></i>
                    <h3>Cerrar Sesión</h3>
                    <p>Sal del sistema de forma segura.</p>
                    <a href="logout.php">Ir</a>
                </div>
            </div>
        <?php } elseif ($rol == 'mesero') { ?>
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Gestionar Órdenes</h3>
                    <p>Toma y actualiza órdenes de los clientes.</p>
                    <a href="ordenes.php">Ir</a>
                </div>
                <div class="dashboard-card">
                    <i class="fas fa-sign-out-alt"></i>
                    <h3>Cerrar Sesión</h3>
                    <p>Sal del sistema de forma segura.</p>
                    <a href="logout.php">Ir</a>
                </div>
            </div>
        <?php } elseif ($rol == 'cliente') { ?>
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <i class="fas fa-utensils"></i>
                    <h3>Ver Menú</h3>
                    <p>Explora los deliciosos platillos disponibles.</p>
                    <a href="menu.php">Ir</a>
                </div>
                <div class="dashboard-card">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Crear Orden</h3>
                    <p>Registra tu orden de tacos y bebidas.</p>
                    <a href="crear_orden.php">Ir</a>
                </div>
                <div class="dashboard-card">
                    <i class="fas fa-list-alt"></i>
                    <h3>Mis Órdenes</h3>
                    <p>Revisa el estado de tus órdenes.</p>
                    <a href="mis_ordenes.php">Ir</a>
                </div>
                <div class="dashboard-card">
                    <i class="fas fa-sign-out-alt"></i>
                    <h3>Cerrar Sesión</h3>
                    <p>Sal del sistema de forma segura.</p>
                    <a href="logout.php">Ir</a>
                </div>
            </div>
        <?php } ?>
    </div>
</body>
</html>