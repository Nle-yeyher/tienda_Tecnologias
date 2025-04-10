<?php
session_start();
require 'conexion.php';

$proveedor = $_SESSION['usuarios'] ?? null;

if (!$proveedor) {
    echo "<p style='color:red;'>Acceso no autorizado.</p>";
    exit();
}

// Obtener ID del proveedor
$sql_proveedor = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
$sql_proveedor->bind_param("s", $proveedor);
$sql_proveedor->execute();
$res = $sql_proveedor->get_result();
$row = $res->fetch_assoc();
$proveedor_id = $row['id'] ?? null;

// Eliminar producto
if (isset($_POST['eliminar']) && isset($_POST['id_producto'])) {
    $id_producto = $_POST['id_producto'];
    $del = $conn->prepare("DELETE FROM productos_proveedor WHERE id = ? AND proveedor = ?");
    $del->bind_param("ii", $id_producto, $proveedor_id);
    $del->execute();
}

// Agregar producto
if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    $insert = $conn->prepare("INSERT INTO productos_proveedor (nombre, descripcion, precio, stock, proveedor) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("ssdii", $nombre, $descripcion, $precio, $stock, $proveedor_id);
    $insert->execute();
}

// Editar producto (precio/stock)
if (isset($_POST['editar'])) {
    $id_producto = $_POST['id_producto'];
    $nuevo_precio = $_POST['nuevo_precio'];
    $nuevo_stock = $_POST['nuevo_stock'];

    $update = $conn->prepare("UPDATE productos_proveedor SET precio = ?, stock = ? WHERE id = ? AND proveedor = ?");
    $update->bind_param("diii", $nuevo_precio, $nuevo_stock, $id_producto, $proveedor_id);
    $update->execute();
}

// Mostrar productos
$query = $conn->prepare("SELECT * FROM productos_proveedor WHERE proveedor = ?");
$query->bind_param("i", $proveedor_id);
$query->execute();
$resultado = $query->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Productos</title>
    <link rel="stylesheet" href="productos.css">
    <style>
        .btn {
            background-color: #2c3e50;
            color: white;
            border: none;
            padding: 7px 12px;
            border-radius: 5px;
            cursor: pointer;
            margin: 2px;
        }
        .btn:hover {
            background-color: #1a252f;
        }
        #formAgregar {
            display: none;
            width: 90%;
            margin: auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        input[type="number"], input[type="text"], textarea {
            width: 95%;
            padding: 8px;
            margin-bottom: 10px;
        }
    </style>
    <script>
        function toggleForm() {
            const form = document.getElementById("formAgregar");
            form.style.display = (form.style.display === "none") ? "block" : "none";
        }
    </script>
</head>
<body>

<h1>Mis Productos</h1>
<a href="proveedor.php" class="btn">← Volver al Panel</a>

<button class="btn" onclick="toggleForm()">Agregar Producto</button>

<!-- Formulario agregar producto -->
<div id="formAgregar">
    <h2>Agregar Nuevo Producto</h2>
    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre del producto" required><br>
        <textarea name="descripcion" placeholder="Descripción del producto" required></textarea><br>
        <input type="number" step="0.01" name="precio" placeholder="Precio" required><br>
        <input type="number" name="stock" placeholder="Stock" required><br>
        <input class="btn" type="submit" name="agregar" value="Agregar Producto">
    </form>
</div>

<!-- Tabla de productos -->
<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($producto = $resultado->fetch_assoc()): ?>
            <tr>
                <form method="POST">
                    <td><?= htmlspecialchars($producto['nombre']) ?></td>
                    <td><?= htmlspecialchars($producto['descripcion']) ?></td>
                    <td>
                        <input type="number" step="0.01" name="nuevo_precio" value="<?= $producto['precio'] ?>" required>
                    </td>
                    <td>
                        <input type="number" name="nuevo_stock" value="<?= $producto['stock'] ?>" required>
                    </td>
                    <td>
                        <input type="hidden" name="id_producto" value="<?= $producto['id'] ?>">
                        <button class="btn" type="submit" name="editar">Guardar</button>
                        <button class="btn" type="submit" name="eliminar" onclick="return confirm('¿Eliminar producto?')">Eliminar</button>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
