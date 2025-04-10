<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rol = $_SESSION['rol'] ?? '';
$usuario = $_SESSION['usuario'] ?? '';

if (!$rol) {
    header("Location: login.php");
    exit();
}
?>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial;
        display: flex;
    }

    .sidebar {
        width: 220px;
        background-color: #2c3e50;
        color: white;
        height: 100vh;
        padding-top: 20px;
        position: fixed;
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 30px;
    }

    .sidebar a {
        display: block;
        padding: 12px 20px;
        text-decoration: none;
        color: white;
        transition: 0.3s;
    }

    .sidebar a:hover {
        background-color: #34495e;
    }

    .main {
        margin-left: 220px;
        padding: 20px;
        flex: 1;
    }
</style>

<div class="sidebar">
    <h2><?php echo ucfirst($rol); ?></h2>
    <p style="text-align:center;">👤 <?php echo htmlspecialchars($usuario); ?></p>
    <hr>

    <?php if ($rol === 'admin'): ?>
        <a href="admin.php">Inicio</a>
        <a href="gestionar.php">Gestionar Usuarios</a>
        <a href="reportes.php">Ver Reportes</a>
        <a href="hacer_pedido.php">Hacer Pedidos</a>
    <?php elseif ($rol === 'cliente'): ?>
        <a href="cliente.php">Inicio</a>
        <a href="pclientes.php">Productos</a>
        <a href="mis_compras.php">Mis Compras</a>
    <?php elseif ($rol === 'proveedor'): ?>
        <a href="proveedor.php">Inicio</a>
        <a href="mis_productos.php">Mis Productos</a>
        <a href="responder_pedido.php">Pedidos</a>
    <?php endif; ?>

    <hr>
    <a href="logout.php">Cerrar Sesión</a>
</div>
