<?php
require 'db_connect.php';

$email = 'admin@taqueria.com';
$password = 'admin123';

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo "El correo no está registrado";
} elseif (!password_verify($password, $user['password'])) {
    echo "La contraseña es incorrecta";
} else {
    echo "Inicio de sesión exitoso para " . $user['email'] . " (" . $user['rol'] . ")";
}
?>