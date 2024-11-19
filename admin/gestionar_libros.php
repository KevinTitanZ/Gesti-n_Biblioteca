<?php
session_start();
include '../includes/db.php';

// Verifica si el usuario es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

// Obtener todos los libros
$sql = "SELECT * FROM libros";
$stmt = $pdo->query($sql);
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Libros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Gestionar Libros</h1>
        <a href="agregar_libro.php" class="btn btn-primary mb-3">Agregar Nuevo Libro</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Cantidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($libros as $libro): ?>
                <tr>
                    <td><?= $libro['id']; ?></td>
                    <td><?= $libro['titulo']; ?></td>
                    <td><?= $libro['autor']; ?></td>
                    <td><?= $libro['cantidad']; ?></td>
                    <td>
                        <a href="editar_libro.php?id=<?= $libro['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="eliminar_libro.php?id=<?= $libro['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
