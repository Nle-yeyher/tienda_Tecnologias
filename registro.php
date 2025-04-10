<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $contrasena = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);
    $rol = $_POST["rol"];

    $sql = "INSERT INTO usuarios (nombre, correo, contraseña, rol, fecha_registro)
            VALUES (?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $correo, $contrasena, $rol);

// ...

if ($stmt->execute()) {
    session_start();
    $_SESSION['usuarios'] = $correo;
    $_SESSION['rol'] = $rol;

    // Redirigir según rol (en la misma carpeta)
    if ($rol === "admin") {
        header("Location: admin.php");
    } elseif ($rol === "cliente") {
        header("Location: cliente.php");
    } elseif ($rol === "proveedor") {
        header("Location: proveedor.php");
    } else {
        echo "Rol no reconocido.";
    }
    exit();
}


    $stmt->close();
    $conn->close();
}
?>

<!-- Formulario HTML -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="registro.css">
</head>
<body>
    <h2>Registro de Usuario</h2>
    <form method="post">
        <input type="text" name="nombre" placeholder="Nombre" required><br>
        <input type="email" name="correo" placeholder="Correo" required><br>
        <input type="password" name="contrasena" placeholder="Contraseña" required><br>
        
        <select name="rol" required>
            <option value="">Selecciona un rol</option>
            <option value="admin">Administrador</option>
            <option value="cliente">Cliente</option>
            <option value="proveedor">Proveedor</option>
        </select><br><br>

        <input type="submit" value="Registrarse">
    </form>
</body>
</html>
