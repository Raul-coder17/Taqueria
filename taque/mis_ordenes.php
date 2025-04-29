<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'cliente') {
    header("Location: index.php");
    exit();
}

// Obtener órdenes del cliente
$ordenes_stmt = $pdo->prepare("
    SELECT o.* 
    FROM ordenes o 
    WHERE o.cliente_id = ? 
    ORDER BY o.fecha DESC
");
$ordenes_stmt->execute([$_SESSION['user_id']]);
$ordenes = $ordenes_stmt->fetchAll();

$detalles_stmt = $pdo->prepare("
    SELECT od.*, m.nombre AS platillo, m.categoria, m.subcategoria 
    FROM ordenes_detalles od 
    JOIN menu m ON od.platillo_id = m.id 
    WHERE od.orden_id = ? 
    ORDER BY m.categoria, m.subcategoria, m.nombre
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Órdenes - Taquería</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>Mis Órdenes</h1>
        <p class="dashboard-subtitle">Revisa el estado de tus pedidos</p>
        <?php if (empty($ordenes)) { ?>
            <p class="no-orders">Aún no tienes órdenes. ¡Crea una ahora!</p>
        <?php } else { ?>
            <div class="orders-grid">
                <?php foreach ($ordenes as $orden) { ?>
                    <div class="order-card <?php echo $orden['estado']; ?>">
                        <div class="order-header">
                            <h3>Orden #<?php echo $orden['id']; ?></h3>
                            <span class="order-status">
                                <i class="fas <?php 
                                    echo $orden['estado'] == 'pendiente' ? 'fa-hourglass-start' : 
                                        ($orden['estado'] == 'en_preparacion' ? 'fa-spinner' : 'fa-check-circle'); 
                                ?>"></i>
                                <?php 
                                    echo $orden['estado'] == 'pendiente' ? 'Pendiente' : 
                                        ($orden['estado'] == 'en_preparacion' ? 'En Preparación' : 'Entregada'); 
                                ?>
                            </span>
                        </div>
                        <p class="order-date">Fecha: <?php echo $orden['fecha']; ?></p>
                        <?php
                        $detalles_stmt->execute([$orden['id']]);
                        $detalles = $detalles_stmt->fetchAll();
                        $has_tacos = false;
                        $has_refrescos = false;
                        $has_jugos = false;

                        foreach ($detalles as $detalle) {
                            if ($detalle['categoria'] == 'Tacos') $has_tacos = true;
                            if ($detalle['categoria'] == 'Bebidas' && $detalle['subcategoria'] == 'Refrescos') $has_refrescos = true;
                            if ($detalle['categoria'] == 'Bebidas' && $detalle['subcategoria'] == 'Jugos') $has_jugos = true;
                        }
                        ?>
                        <div class="order-details-content">
                            <?php if ($has_tacos) { ?>
                                <h4>Tacos</h4>
                                <table>
                                    <tr>
                                        <th>Platillo</th>
                                        <th>Cantidad</th>
                                    </tr>
                                    <?php foreach ($detalles as $detalle) { if ($detalle['categoria'] == 'Tacos') { ?>
                                        <tr>
                                            <td><?php echo $detalle['platillo']; ?></td>
                                            <td><?php echo $detalle['cantidad']; ?></td>
                                        </tr>
                                    <?php } } ?>
                                </table>
                            <?php } else { ?>
                                <p>No se pidieron tacos.</p>
                            <?php } ?>
                            <?php if ($has_refrescos) { ?>
                                <h4>Bebidas - Refrescos</h4>
                                <table>
                                    <tr>
                                        <th>Platillo</th>
                                        <th>Cantidad</th>
                                    </tr>
                                    <?php foreach ($detalles as $detalle) { if ($detalle['categoria'] == 'Bebidas' && $detalle['subcategoria'] == 'Refrescos') { ?>
                                        <tr>
                                            <td><?php echo $detalle['platillo']; ?></td>
                                            <td><?php echo $detalle['cantidad']; ?></td>
                                        </tr>
                                    <?php } } ?>
                                </table>
                            <?php } ?>
                            <?php if ($has_jugos) { ?>
                                <h4>Bebidas - Jugos</h4>
                                <table>
                                    <tr>
                                        <th>Platillo</th>
                                        <th>Cantidad</th>
                                    </tr>
                                    <?php foreach ($detalles as $detalle) { if ($detalle['categoria'] == 'Bebidas' && $detalle['subcategoria'] == 'Jugos') { ?>
                                        <tr>
                                            <td><?php echo $detalle['platillo']; ?></td>
                                            <td><?php echo $detalle['cantidad']; ?></td>
                                        </tr>
                                    <?php } } ?>
                                </table>
                            <?php } ?>
                            <?php if (!$has_refrescos && !$has_jugos) { ?>
                                <p>No se pidieron bebidas.</p>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <a class="back-button" href="dashboard.php">Volver al Dashboard</a>
    </div>
</body>
</html>