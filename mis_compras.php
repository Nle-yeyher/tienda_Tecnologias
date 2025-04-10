<?php
require 'conexion.php';
session_start();

// Simulando que el cliente está logueado
$id_cliente = $_SESSION['id_cliente'] ?? 1; // Reemplaza con sesión real si tienes login

$sql = "SELECT c.*, p.nombre AS nombre_producto
        FROM compras c
        INNER JOIN productos_proveedor p ON c.id_producto = p.id
        WHERE c.id_cliente = '$id_cliente'
        ORDER BY c.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Compras</title>
    <link rel="stylesheet" href="mis_compras.css"> <!-- Puedes crear un CSS aparte si deseas -->
</head>
<body>
    <div class="container">
        <h1>Mis Compras</h1>
        <a href="cliente.php">Volver al Panel</a>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Método de Pago</th>
                        <th>Entrega Estimada</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['nombre_producto'] ?></td>
                            <td><?= $row['cantidad'] ?></td>
                            <td><?= $row['metodo_pago'] ?></td>
                            <td><?= $row['tiempo_entrega'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No has realizado ninguna compra aún.</p>
        <?php endif; ?>
    </div>
</body>
</html>
