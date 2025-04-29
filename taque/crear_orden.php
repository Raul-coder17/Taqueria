<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'cliente') {
    header("Location: index.php");
    exit();
}

$menu = $pdo->query("SELECT * FROM menu ORDER BY categoria, subcategoria, nombre")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $items = $_POST['items'] ?? [];
    if (!empty($items)) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO ordenes (cliente_id, estado) VALUES (?, 'pendiente')");
            $stmt->execute([$_SESSION['user_id']]);
            $orden_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO ordenes_detalles (orden_id, platillo_id, cantidad) VALUES (?, ?, ?)");
            foreach ($items as $platillo_id => $cantidad) {
                if ($cantidad > 0) {
                    $stmt->execute([$orden_id, $platillo_id, $cantidad]);
                }
            }

            $pdo->commit();
            $success = "Orden creada con éxito.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Error al crear la orden: " . $e->getMessage();
        }
    } else {
        $error = "Debes seleccionar al menos un ítem.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Orden - Taquería</title>
    <link rel="stylesheet" href="styles.css">
    <script src="orden.js"></script>
</head>
<body>
    <div class="container">
        <h1>Crear Orden</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <form class="menu-form" method="POST">
            <h2>Tacos</h2>
            <div class="order-items">
                <?php foreach ($menu as $item) { if ($item['categoria'] === 'Tacos') { ?>
                    <div class="order-item">
                        <label>
                            <?php echo $item['nombre']; ?> ($<?php echo $item['precio']; ?>)
                            <input type="number" name="items[<?php echo $item['id']; ?>]" min="0" value="0" onchange="updateTotal()">
                        </label>
                    </div>
                <?php } } ?>
            </div>
            <h2>Bebidas</h2>
            <?php
            $subcategorias = ['Refrescos', 'Jugos'];
            foreach ($subcategorias as $subcat) {
                $has_items = false;
                foreach ($menu as $item) {
                    if ($item['categoria'] === 'Bebidas' && $item['subcategoria'] === $subcat) {
                        $has_items = true;
                        break;
                    }
                }
                if ($has_items) {
            ?>
                <h3><?php echo $subcat; ?></h3>
                <div class="order-items">
                    <?php foreach ($menu as $item) { if ($item['categoria'] === 'Bebidas' && $item['subcategoria'] === $subcat) { ?>
                        <div class="order-item">
                            <label>
                                <?php echo $item['nombre']; ?> ($<?php echo $item['precio']; ?>)
                                <input type="number" name="items[<?php echo $item['id']; ?>]" min="0" value="0" onchange="updateTotal()">
                            </label>
                        </div>
                    <?php } } ?>
                </div>
            <?php } } ?>
            <p>Total: $<span id="total">0.00</span></p>
            <button type="submit">Crear Orden</button>
        </form>
        <a class="back-button" href="dashboard.php">Volver</a>
    </div>
</body>
</html>