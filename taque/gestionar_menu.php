<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];
    $subcategoria = ($categoria === 'Bebidas' && !empty($_POST['subcategoria'])) ? $_POST['subcategoria'] : NULL;

    $stmt = $pdo->prepare("INSERT INTO menu (nombre, descripcion, precio, categoria, subcategoria) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $descripcion, $precio, $categoria, $subcategoria]);
}

$stmt = $pdo->query("SELECT * FROM menu ORDER BY categoria, subcategoria, nombre");
$menu = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Menú - Taquería</title>
    <link rel="stylesheet" href="styles.css">
    <script src="menu.js"></script>
</head>
<body>
    <div class="container">
        <h1>Gestionar Menú</h1>
        <h2>Agregar Platillo</h2>
        <form class="menu-form" method="POST">
            <label for="categoria">Categoría</label>
            <select id="categoria" name="categoria" onchange="toggleSubcategoria()" required>
                <option value="Tacos">Tacos</option>
                <option value="Bebidas">Bebidas</option>
            </select>
            <label for="subcategoria" id="subcategoria-label" style="display: none;">Subcategoría</label>
            <select id="subcategoria" name="subcategoria" style="display: none;">
                <option value="Refrescos">Refrescos</option>
                <option value="Jugos">Jugos</option>
            </select>
            <label for="nombre">Nombre del Platillo</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej. Taco al Pastor" required>
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" placeholder="Ej. Tortilla de maíz con carne al pastor, piña y cilantro"></textarea>
            <label for="precio">Precio (MXN)</label>
            <input type="number" id="precio" name="precio" step="0.01" placeholder="Ej. 25.00" required>
            <button type="submit">Agregar Platillo</button>
        </form>
        <h2>Platillos</h2>
        <h3>Tacos</h3>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
            </tr>
            <?php foreach ($menu as $item) { if ($item['categoria'] === 'Tacos') { ?>
                <tr>
                    <td><?php echo $item['nombre']; ?></td>
                    <td><?php echo $item['descripcion']; ?></td>
                    <td>$<?php echo $item['precio']; ?></td>
                </tr>
            <?php } } ?>
        </table>
        <h3>Bebidas</h3>
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
            <h4><?php echo $subcat; ?></h4>
            <table>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                </tr>
                <?php foreach ($menu as $item) { if ($item['categoria'] === 'Bebidas' && $item['subcategoria'] === $subcat) { ?>
                    <tr>
                        <td><?php echo $item['nombre']; ?></td>
                        <td><?php echo $item['descripcion']; ?></td>
                        <td>$<?php echo $item['precio']; ?></td>
                    </tr>
                <?php } } ?>
            </table>
        <?php } } ?>
        <a class="back-button" href="dashboard.php">Volver</a>
    </div>
</body>
</html>