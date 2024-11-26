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
    <meta name="viewport" content="widtd=device-widtd, initial-scale=1.0">
    <title>Gestionar Libros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .table-container {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .btn-primary {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }

        .btn-primary:hover {
            background-color: #45A049;
        }

        h1 {
            color: #333;
        }

        .btn.dropdown-toggle::after {
            display: none;
        }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row">
        <!-- Menú lateral -->
        <div class="col-md-2 bg-dark text-white vh-100">
            <?php include '../menu.php'; ?>
        </div>

            <!-- Contenido principal -->
            <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4">
                <div class="container mt-5">
                    <h1 class="text-center mb-4"><i class="fa-solid fa-book"></i> Gestionar Libros</h1>
<!-- 
                    <div class="d-flex justify-content-between mb-3">
                        <a href="gestionar_libros.php" class="btn btn-secondary"><i class="fa-solid fa-list"></i> Ver Listado</a>
                        <a href="agregar_libro.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Agregar Nuevo Libro</a>
                    </div> -->

                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr class="table-primary fw-bold">
                                    <td class="text-center">Título</td>
                                    <td class="text-center">Autor</td>
                                    <td class="text-center">Cantidad</td>
                                    <td class="text-center Acciones">Acciones</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($libros) > 0): ?>
                                    <?php foreach ($libros as $libro): ?>
                                        <tr>
                                            <td class="text-center"><?= htmlspecialchars($libro['titulo']); ?></td>
                                            <td class="text-center"><?= htmlspecialchars($libro['autor']); ?></td>
                                            <td class="text-center"><?= $libro['cantidad']; ?></td>
                                            <td class="text-center">
                                                <a href="editar_libro.php?id=<?= $libro['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                                <a href="eliminar_libro.php?id=<?= $libro['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este libro?')">Eliminar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-muted text-center">No hay libros registrados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>