<?php
require 'conexion.php';

$sql = "SELECT nombre_producto, cantidad, fecha FROM abastecimientos ORDER BY fecha DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Abastecimientos</title>
    <link rel="stylesheet" href="productos.css">
</head>
<body>
    <h1>Reporte de Abastecimientos</h1>
    <a href="admin.php" class="btn">← Volver al Panel</a>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nombre_producto']) ?></td>
                    <td><?= $row['cantidad'] ?></td>
                    <td><?= $row['fecha'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
