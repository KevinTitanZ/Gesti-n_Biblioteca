<?php
session_start();
include '../includes/db.php'; // Asegúrate de que la ruta sea correcta a tu archivo de conexión

// Verifica si el usuario es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

// Procesa el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];

    // Encriptar la contraseña
    $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);

    // Inserta los datos del usuario en la base de datos
    $sql = "INSERT INTO usuarios (nombre, email, contrasena, rol) VALUES (:nombre, :email, :contrasena, :rol)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nombre' => $nombre,
        'email' => $email,
        'contrasena' => $contrasena_hash,
        'rol' => $rol
    ]);

    // Redirigir a la página de gestión de usuarios
    header('Location: gestionar_usuario.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

            <!-- Menú lateral -->
            <div class="col-md-2 bg-dark text-white vh-100">
                <?php include '../menu.php'; ?>
            </div>
    <div class="container mt-5">
        <h2 class="text-center mb-4"><i class="fa-solid fa-user-plus"></i> Agregar Usuario</h2>
        <form action="" method="POST" class="bg-light p-4 rounded shadow">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña:</label>
                <input type="password" name="contrasena" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol:</label>
                <select name="rol" class="form-select">
                    <option value="usuario">Usuario</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Agregar Usuario</button>
            <a href="gestionar_usuario.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        </form>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
