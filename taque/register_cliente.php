<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'cliente')");
        $stmt->execute([$nombre, $email, $password]);
        $success = "Cliente registrado con éxito. <a href='index.php'>Inicia sesión</a>.";
    } catch (PDOException $e) {
        $error = "Error: " . ($e->getCode() == 23000 ? "El nombre o correo ya está registrado." : $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Cliente - Taquería</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Registrar Cliente</h1>
        <p>El nombre debe ser único y se usará para iniciar sesión.</p>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <form method="POST">
            <label for="nombre">Nombre (Único)</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej. Cliente Juan" required>
            <label for="email">Correo</label>
            <input type="email" id="email" name="email" placeholder="Correo" required>
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Registrar</button>
        </form>
        <a class="back-button" href="index.php">Volver</a>
    </div>
</body>
</html>