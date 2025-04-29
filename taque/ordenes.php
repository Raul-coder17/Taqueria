<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['rol'], ['admin', 'mesero'])) {
    header("Location: index.php");
    exit();
}

// Actualizar estado de la orden
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['orden_id']) && isset($_POST['estado'])) {
    $orden_id = $_POST['orden_id'];
    $estado = $_POST['estado'];
    $stmt = $pdo->prepare("UPDATE ordenes SET estado = ? WHERE id = ?");
    $stmt->execute([$estado, $orden_id]);
    $success = "Estado de la orden actualizado.";
}

// Obtener órdenes con detalles
$ordenes = $pdo->query("
    SELECT o.*, u.nombre AS cliente 
    FROM ordenes o 
    JOIN usuarios u ON o.cliente_id = u.id
    ORDER BY o.fecha DESC
")->fetchAll();

$detalles_stmt = $pdo->prepare("
    SELECT od.*, m.nombre AS platillo, m.categoria, m.subcategoria 
    FROM ordenes_detalles od 
    JOIN menu m ON od.platillo_id = m.id 
    WHERE od.orden_id = ?
    ORDER BY m.categoria, m.subcategoria, m.nombre
");

$clientes = $pdo->query("SELECT * FROM usuarios WHERE rol = 'cliente'")->fetchAll();
$menu = $pdo->query("SELECT * FROM menu ORDER BY categoria, subcategoria, nombre")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Órdenes - Taquería</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestionar Órdenes</h1>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if ($_SESSION['rol'] == 'mesero') { ?>
            <h2>Nueva Orden</h2>
            <form method="POST" action="crear_orden.php">
                <label for="cliente_id">Cliente</label>
                <select id="cliente_id" name="cliente_id" required>
                    <?php foreach ($clientes as $cliente) { ?>
                        <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nombre']; ?></option>
                    <?php } ?>
                </select>
                <label for="platillo_id">Platillo</label>
                <select id="platillo_id" name="platillo_id" required>
                    <optgroup label="Tacos">
                        <?php foreach ($menu as $item) { if ($item['categoria'] === 'Tacos') { ?>
                            <option value="<?php echo $item['id']; ?>"><?php echo $item['nombre']; ?></option>
                        <?php } } ?>
                    </optgroup>
                    <optgroup label="Bebidas - Refrescos">
                        <?php foreach ($menu as $item) { if ($item['categoria'] === 'Bebidas' && $item['subcategoria'] === 'Refrescos') { ?>
                            <option value="<?php echo $item['id']; ?>"><?php echo $item['nombre']; ?></option>
                        <?php } } ?>
                    </optgroup>
                    <optgroup label="Bebidas - Jugos">
                        <?php foreach ($menu as $item) { if ($item['categoria'] === 'Bebidas' && $item['subcategoria'] === 'Jugos') { ?>
                            <option value="<?php echo $item['id']; ?>"><?php echo $item['nombre']; ?></option>
                        <?php } } ?>
                    </optgroup>
                </select>
                <button type="submit">Crear Orden</button>
            </form>
        <?php } ?>
        <h2>Órdenes</h2>
        <?php foreach ($ordenes as $orden) { ?>
            <div class="order-details">
                <h3>Orden #<?php echo $orden['id']; ?> - Cliente: <?php echo $orden['cliente']; ?></h3>
                <p>Fecha: <?php echo $orden['fecha']; ?></p>
                <form method="POST" class="status-form">
                    <input type="hidden" name="orden_id" value="<?php echo $orden['id']; ?>">
                    <label for="estado-<?php echo $orden['id']; ?>">Estado:</label>
                    <select id="estado-<?php echo $orden['id']; ?>" name="estado" required>
                        <option value="pendiente" <?php echo $orden['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="en_preparacion" <?php echo $orden['estado'] == 'en_preparacion' ? 'selected' : ''; ?>>En Preparación</option>
                        <option value="entregada" <?php echo $orden['estado'] == 'entregada' ? 'selected' : ''; ?>>Entregada</option>
                    </select>
                    <button type="submit">Actualizar</button>
                </form>
                <h4>Detalles de la Orden</h4>
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
                <?php if ($has_tacos) { ?>
                    <h5>Tacos</h5>
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
                    <h5>Bebidas - Refrescos</h5>
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
                    <h5>Bebidas - Jugos</h5>
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
        <?php } ?>
        <a class="back-button" href="dashboard.php">Volver</a>
    </div>
</body>
</html>