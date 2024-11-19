<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../includes/db.php';
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    try {
        $sql = "INSERT INTO usuarios (nombre, email, contrasena, rol) VALUES (:nombre, :email, :contrasena, :rol)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $nombre,
            'email' => $email,
            'contrasena' => $contrasena,
            'rol' => $rol
        ]);
        echo "Usuario creado exitosamente.";
    } catch (PDOException $e) {
        echo "Error al crear el usuario: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Crear Usuario</title>
</head>
<body>
<div class="container mt-5">
    <h2>Crear Usuario</h2>
    <form action="crear_usuario.php" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="contrasena" name="contrasena" required>
        </div>
        <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select class="form-select" id="rol" name="rol" required>
                <option value="usuario">Estudiante</option>
                <option value="administrador">Administrador</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Crear Usuario</button>
    </form>
</div>
</body>
</html>
