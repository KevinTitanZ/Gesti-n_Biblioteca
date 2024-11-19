<?php
session_start();

// Verifica si el usuario es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Bienvenido, Administrador</h1>
        <p>Desde aquí puedes gestionar las operaciones de la biblioteca.</p>
        <ul class="list-group">
            <li class="list-group-item"><a href="gestionar_libros.php">Gestionar Libros</a></li>
            <li class="list-group-item"><a href="gestionar_usuarios.php">Gestionar Usuarios</a></li>
            <li class="list-group-item"><a href="estadisticas.php">Ver Estadísticas</a></li>
            <li class="list-group-item"><a href="../logout.php" class="text-danger">Cerrar Sesión</a></li>
        </ul>
    </div>
</body>
</html>
