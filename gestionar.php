<?php
require 'conexion.php';

// Agregar o actualizar un usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'], $_POST['correo'], $_POST['rol'])) {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];

    // Si se está actualizando un usuario, se necesita el id
    if (isset($_POST['id'])) {
        // Actualizar usuario
        $id = $_POST['id'];

        // Verificar si el correo ya existe y no es el del usuario que estamos editando
        $sql_check = "SELECT COUNT(*) FROM usuarios WHERE correo = ? AND id != ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("si", $correo, $id);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count > 0) {
            echo "<script>alert('El correo ya está registrado. Por favor, use otro correo.'); window.location.href='gestionar.php?id=$id';</script>";
        } else {
            // Si el correo no existe, proceder con la actualización
            $sql_update = "UPDATE usuarios SET nombre = ?, correo = ?, rol = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("sssi", $nombre, $correo, $rol, $id);

            if ($stmt_update->execute()) {
                echo "<script>alert('Usuario actualizado correctamente'); window.location.href='gestionar.php';</script>";
            } else {
                echo "Error al actualizar el usuario: " . $stmt_update->error;
            }
        }
    } else {
        // Agregar nuevo usuario
        $contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT); // Cifra la contraseña

        // Verificar si el correo ya existe
        $sql_check = "SELECT COUNT(*) FROM usuarios WHERE correo = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $correo);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count > 0) {
            echo "<script>alert('El correo ya está registrado. Por favor, use otro correo.'); window.location.href='gestionar.php';</script>";
        } else {
            // Si el correo no existe, proceder con la inserción
            $sql = "INSERT INTO usuarios (nombre, correo, contraseña, rol) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $nombre, $correo, $contraseña, $rol);

            if ($stmt->execute()) {
                echo "<script>alert('Usuario agregado exitosamente'); window.location.href='gestionar.php';</script>";
            } else {
                echo "Error al agregar usuario: " . $stmt->error;
            }
        }
    }
}

// Obtener todos los usuarios
$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);

// Obtener datos para la edición si se pasa un id
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql_edit = "SELECT * FROM usuarios WHERE id = ?";
    $stmt_edit = $conn->prepare($sql_edit);
    $stmt_edit->bind_param("i", $id);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    $user = $result_edit->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios</title>
    <link rel="stylesheet" href="gestionar.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Usuarios</h1>
        <!-- Botón para volver al panel -->
        <a href="admin.php" class="back-btn">Volver al Panel</a>


        <!-- Formulario para agregar o editar usuario -->
        <h2><?= isset($user) ? 'Editar Usuario' : 'Agregar Nuevo Usuario' ?></h2>
        <form method="POST">
            <?php if (isset($user)): ?>
                <input type="hidden" name="id" value="<?= $user['id'] ?>"> <!-- Campo oculto para el ID del usuario -->
            <?php endif; ?>

            <input type="text" name="nombre" placeholder="Nombre" value="<?= isset($user) ? $user['nombre'] : '' ?>" required>
            <input type="email" name="correo" placeholder="Correo" value="<?= isset($user) ? $user['correo'] : '' ?>" required>
            
            <?php if (!isset($user)): ?>
                <input type="password" name="contraseña" placeholder="Contraseña" required>
            <?php endif; ?>

            <select name="rol" required>
                <option value="admin" <?= isset($user) && $user['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                <option value="proveedor" <?= isset($user) && $user['rol'] == 'proveedor' ? 'selected' : '' ?>>Proveedor</option>
                <option value="cliente" <?= isset($user) && $user['rol'] == 'cliente' ? 'selected' : '' ?>>Cliente</option>
            </select>
            <button type="submit"><?= isset($user) ? 'Actualizar Usuario' : 'Agregar Usuario' ?></button>
        </form>

        <!-- Mostrar lista de usuarios -->
        <h2>Usuarios Registrados</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr id="usuario-<?= $row['id'] ?>">
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['nombre'] ?></td>
                            <td><?= $row['correo'] ?></td>
                            <td><?= ucfirst($row['rol']) ?></td>
                            <td>
                                <a href="gestionar.php?id=<?= $row['id'] ?>" class="edit-btn">Editar</a> | 
                                <a href="eliminar_usuario.php?id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">Eliminar</a>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay usuarios registrados.</p>
        <?php endif; ?>
    </div>
</body>
</html>
