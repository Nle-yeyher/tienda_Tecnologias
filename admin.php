<?php
session_start();
if ($_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
</head>
<body>
<?php include "menu.php"; ?>
<div class="main">
    <!-- Aquí tu contenido -->
    <h1>Bienvenido a tu panel</h1>
</div>
</body>
</html>
