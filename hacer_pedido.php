<?php
require 'conexion.php';
session_start();

// Consulta productos disponibles del proveedor
$sql = "SELECT id, nombre, stock FROM productos_proveedor WHERE stock > 0";
$result = $conn->query($sql);

// Si el formulario se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Se obtienen las cantidades de productos seleccionados
    if (isset($_POST['cantidades'])) {
        // Se recorren las cantidades y se insertan en la base de datos
        foreach ($_POST['cantidades'] as $id_producto => $cantidad) {
            // Verificamos que la cantidad sea mayor que cero
            if ($cantidad > 0) {
                // Se inserta el pedido con estado pendiente
                $stmt = $conn->prepare("INSERT INTO pedidos_tienda (id_producto_proveedor, cantidad_solicitada, estado) VALUES (?, ?, 'pendiente')");
                $stmt->bind_param("ii", $id_producto, $cantidad);
                $stmt->execute();
            }
        }

        // Redirigir a una página de éxito o mensaje
        echo "<script>alert('Pedido realizado con éxito. Estado: Pendiente'); window.location.href='admin.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hacer Pedido al Proveedor</title>
    <link rel="stylesheet" href="productos.css">
</head>
<body>
    <h1>Hacer Pedido al Proveedor</h1>
    <a href="admin.php" class="btn">← Volver al Panel</a>

    <form method="post">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Stock disponible</th>
                    <th>Cantidad a pedir</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= $row['stock'] ?></td>
                        <td>
                            <input type="number" name="cantidades[<?= $row['id'] ?>]" min="0" max="<?= $row['stock'] ?>" value="0">
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button type="submit">Enviar Pedido</button>
    </form>
</body>
</html>
