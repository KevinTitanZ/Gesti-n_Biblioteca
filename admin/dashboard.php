<?php
session_start();

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
<div class="col-md-2 bg-dark text-white vh-100">
<?php include '../menu.php'; ?>
            </div>
            <div class="container mt-4 d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="text-center">
        <h1>Bienvenido, Administrador</h1>
        <p>Desde aquí puedes gestionar las operaciones de la biblioteca.</p>
    </div>
</div>
</body>
</html>
