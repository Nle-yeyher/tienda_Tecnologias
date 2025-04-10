<?php
require 'conexion.php';

// Consulta para el reporte de ventas (corregida: se agregó p.nombre AS producto)
$sql_ventas = "SELECT v.id AS id_venta, 
                      v.id_producto, 
                      p.nombre AS producto, 
                      v.cantidad, 
                      v.fecha, 
                      u.nombre AS cliente
               FROM ventas v
               INNER JOIN productos_proveedor p ON v.id_producto = p.id
               INNER JOIN usuarios u ON v.id_cliente = u.id
               ORDER BY v.fecha DESC";

$result_ventas = $conn->query($sql_ventas);

// Consulta para el reporte de auditoría
$sql_auditoria = "SELECT a.id, a.accion, a.tabla_afectada, a.registro_id, a.fecha, u.nombre as usuario
                  FROM auditoria a
                  INNER JOIN usuarios u ON a.usuario_id = u.id
                  ORDER BY a.fecha DESC";

$result_auditoria = $conn->query($sql_auditoria);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes del Sistema</title>
    <link rel="stylesheet" href="reporte.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        h1, h2 {
            margin-top: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 15px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <a href="admin.php" class="back-btn">Volver al Panel</a>

    <!-- Reporte de Ventas -->
    <h1>Reporte de Ventas</h1>
    <?php if ($result_ventas && $result_ventas->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Venta</th>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Cliente</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_ventas->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_venta']) ?></td>
                        <td><?= htmlspecialchars($row['fecha']) ?></td>
                        <td><?= htmlspecialchars($row['producto']) ?></td>
                        <td><?= htmlspecialchars($row['cliente']) ?></td>
                        <td><?= htmlspecialchars($row['cantidad']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay ventas registradas.</p>
    <?php endif; ?>

    <!-- Reporte de Auditoría -->
    <h2>Reporte de Movimientos en la Base de Datos</h2>
    <?php if ($result_auditoria && $result_auditoria->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Movimiento</th>
                    <th>Acción</th>
                    <th>Tabla Afectada</th>
                    <th>ID Registro Afectado</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_auditoria->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['accion']) ?></td>
                        <td><?= htmlspecialchars($row['tabla_afectada']) ?></td>
                        <td><?= htmlspecialchars($row['registro_id']) ?></td>
                        <td><?= htmlspecialchars($row['fecha']) ?></td>
                        <td><?= htmlspecialchars($row['usuario']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay movimientos registrados.</p>
    <?php endif; ?>
</body>
</html>

</html>
