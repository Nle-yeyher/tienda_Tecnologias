<?php
session_start();
if ($_SESSION['rol'] !== 'cliente') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio Cliente</title>
</head>
<body>
<?php include "menu.php"; ?>
<div class="main">
    <!-- Aquí tu contenido -->
    <h1>Bienvenido a tu panel</h1>
</div>
</body>
</html>
