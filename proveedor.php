<?php
session_start();
if ($_SESSION['rol'] !== 'proveedor') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Proveedor</title>
</head>
<body>
<?php include "menu.php"; ?>
<div class="main">
    <div class="contenido">
    <?php
    if (isset($_GET['seccion'])) {
        $seccion = $_GET['seccion'];

        switch ($seccion) {
            case 'inicio':
                echo "<h1>Bienvenido a tu panel</h1>";
                break;

            case 'productos':
                include 'mis_productos.php';
                break;

            case 'pedidos':
                include 'mis_pedidos.php';
                break;

            default:
                echo "<h1>Sección no encontrada</h1>";
        }
    } else {
        echo "<h1>Bienvenido a tu panel</h1>";
    }
    ?>
</div>
</div>
</body>
</html>
