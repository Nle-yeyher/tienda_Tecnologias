<?php
require 'conexion.php';
session_start();

// Simular ID del admin que hace el pedido (en lugar de id_cliente)
$admin_id = 1; // O traerlo de la sesión si está implementado

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cantidades'])) {
    $cantidades = $_POST['cantidades'];

    // Filtrar solo productos con cantidad mayor a 0
    $productos_pedidos = array_filter($cantidades, function ($cant) {
        return intval($cant) > 0;
    });

    if (!empty($productos_pedidos)) {
        // Insertar en la tabla pedidos_tienda
        $estado = "pendiente";  // El estado por defecto será 'pendiente'
        $fecha = date("Y-m-d H:i:s");

        // Insertar el pedido en la tabla pedidos_tienda
        $stmt_pedido = $conn->prepare("INSERT INTO pedidos_tienda (id_producto_proveedor, cantidad_solicitada, estado, fecha) VALUES (?, ?, ?, ?)");

        // Recorremos los productos pedidos
        foreach ($productos_pedidos as $id_producto_proveedor => $cantidad_solicitada) {
            $cantidad_solicitada = intval($cantidad_solicitada); // Convertir la cantidad a entero
            // Asegúrate de bindear correctamente los parámetros
            $stmt_pedido->bind_param("iiis", $id_producto_proveedor, $cantidad_solicitada, $estado, $fecha);
            $stmt_pedido->execute();
        }

        // Verifica si el pedido fue insertado correctamente
        if ($stmt_pedido->affected_rows > 0) {
            echo "<script>alert('Pedido enviado correctamente.'); window.location.href='hacer_pedido.php';</script>";
        } else {
            echo "<script>alert('Hubo un error al enviar el pedido.'); window.location.href='hacer_pedido.php';</script>";
        }

    } else {
        echo "<script>alert('No seleccionaste productos.'); window.location.href='hacer_pedido.php';</script>";
    }
}
?>
