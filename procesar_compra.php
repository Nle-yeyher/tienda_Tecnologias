<?php
require 'conexion.php';

// Verificar si se ha seleccionado un método de pago y productos
if (isset($_POST['metodo_pago']) && isset($_POST['productos']) && !empty($_POST['productos'])) {
    $metodo_pago = $_POST['metodo_pago'];
    $productos_seleccionados = $_POST['productos']; // Ya es un array

    // Simula ID de cliente desde sesión
    $id_cliente = 1; // <- Cambia esto por $_SESSION['id_cliente'] si estás usando sesiones

    // Tiempo estimado
    $tiempo_entrega = "Su pedido llegará en 3-5 días hábiles.";

    foreach ($productos_seleccionados as $id_producto) {
        $cantidad = 1;

        // Insertar en la tabla compras
        $sql_compra = "INSERT INTO compras (id_cliente, id_producto, cantidad, tiempo_entrega, metodo_pago)
                       VALUES ('$id_cliente', '$id_producto', '$cantidad', '$tiempo_entrega', '$metodo_pago')";

        // Insertar en la tabla ventas
        $sql_venta = "INSERT INTO ventas (id_cliente, id_producto, cantidad)
                      VALUES ('$id_cliente', '$id_producto', '$cantidad')";

        if ($conn->query($sql_compra) === TRUE && $conn->query($sql_venta) === TRUE) {
            // Si ambas inserciones son exitosas
            continue;
        } else {
            echo "Error al registrar la compra o venta: " . $conn->error;
            exit;
        }
    }

    // Confirmación
    echo "<script>
            alert('Compra realizada con éxito. $tiempo_entrega');
            window.location.href = 'cliente.php';
          </script>";

} else {
    echo "No se ha seleccionado ningún producto o método de pago.";
}
?>
