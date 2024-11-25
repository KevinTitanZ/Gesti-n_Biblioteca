<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

// Consultas para estadísticas
$total_libros = $pdo->query("SELECT COUNT(*) FROM libros")->fetchColumn();
$total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid">
    <div class="row">
        <!-- Menú lateral -->
        <div class="col-md-2 bg-dark text-white vh-100">
            <?php include '../menu.php'; ?>
        </div>
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4">
        <div class="container mt-4">
            <h1>Estadísticas</h1>
            <p>Total de libros: <?= $total_libros; ?></p>
            <p>Total de usuarios: <?= $total_usuarios; ?></p>
        </div>
        </main>
    </div>
    </div>
</body>

</html>