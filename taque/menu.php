<?php
session_start();
require 'db_connect.php';

$stmt = $pdo->query("SELECT * FROM menu ORDER BY categoria, subcategoria, nombre");
$menu = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú - Taquería</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Menú</h1>
        <h2>Tacos</h2>
        <?php foreach ($menu as $item) { if ($item['categoria'] === 'Tacos') { ?>
            <div class="menu-item">
                <h3><?php echo $item['nombre']; ?></h3>
                <p><?php echo $item['descripcion']; ?></p>
                <p>Precio: $<?php echo $item['precio']; ?></p>
            </div>
        <?php } } ?>
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
            <?php foreach ($menu as $item) { if ($item['categoria'] === 'Bebidas' && $item['subcategoria'] === $subcat) { ?>
                <div class="menu-item">
                    <h3><?php echo $item['nombre']; ?></h3>
                    <p><?php echo $item['descripcion']; ?></p>
                    <p>Precio: $<?php echo $item['precio']; ?></p>
                </div>
            <?php } } ?>
        <?php } } ?>
        <a class="back-button" href="dashboard.php">Volver</a>
    </div>
</body>
</html>