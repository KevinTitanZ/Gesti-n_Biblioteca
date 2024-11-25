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
    <div class="container mt-5">
        <h1 class="text-center mb-4"><i class="fa-solid fa-book"></i> Gestionar Libros</h1>

        <div class="d-flex justify-content-between mb-3">
            <a href="gestionar_libros.php" class="btn btn-secondary"><i class="fa-solid fa-list"></i> Ver Listado</a>
            <a href="agregar_libro.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Agregar Nuevo Libro</a>
        </div>

        <div class="table-container">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="table-primary fw-bold">
                        <!-- <td class="text-center">ID</td> -->
                        <td class="text-center">Título</td>
                        <td class="text-center">Autor</td>
                        <td class="text-center">Cantidad</td>
                        <td class="text-center Acciones">Acciones</td>
                    </tr>
                </thead>
                <?php if (count($libros) > 0): ?>
                    <?php foreach ($libros as $libro): ?>
                        <!-- <td class="text-center"><?= $libro['id']; ?></td> -->
                        <td class="text-center"><?= htmlspecialchars($libro['titulo']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($libro['autor']); ?></td>
                        <td class="text-center"><?= $libro['cantidad']; ?></td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img class="ButtonSizeImg" src="../imagenes/svg/gear-wide-connected.svg" alt="Opciones" />
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="editar_libro.php?id=<?= $libro['id'] ?>" class="dropdown-item" title="Editar">
                                            <img class="ButtonSizeImg me-2" src="../imagenes/svg/pencil-square.svg" alt="Editar" />
                                            Editar
                                        </a>
                                    </li>
                                    <li>
                                        <a href="eliminar_libro.php?id=<?= $libro['id'] ?>" class="dropdown-item text-danger" title="Eliminar"
                                            onclick="return confirm('¿Estás seguro de eliminar este libro?')">
                                            <img class="ButtonSizeImg me-2" src="../imagenes/svg/trash.svg" alt="Eliminar" />
                                            Eliminar
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-muted text-center">No hay libros registrados.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>