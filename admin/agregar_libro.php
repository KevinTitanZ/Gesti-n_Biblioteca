<?php
session_start();
include '../includes/db.php';

// Verifica si el usuario es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $cantidad = $_POST['cantidad'];
    $categoria = $_POST['categoria'];

    $sql = "INSERT INTO libros (titulo, autor, cantidad,categoria) VALUES (:titulo, :autor, :cantidad, :categoria)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'titulo' => $titulo,
        'autor' => $autor,
        'cantidad' => $cantidad,
        'categoria' => $categoria
    ]);

    header('Location: gestionar_libros.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Libro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Menú lateral -->
            <div class="col-md-2 bg-dark text-white vh-100">
                <?php include '../menu.php'; ?>
            </div>

            <!-- Contenido principal -->
            <div class="col-md-10 p-4">
                <h1 class="text-center mb-4"><i class="fa-solid fa-book"></i> Agregar Nuevo Libro</h1>
                <!-- <div class="d-flex justify-content-between mb-3">
                    <a href="gestionar_libros.php" class="btn btn-secondary">
                        <i class="fa-solid fa-list"></i> Ver Listado
                    </a>
                    <a href="agregar_libro.php" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Agregar Nuevo Libro
                    </a>
                </div> -->
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" id="titulo" name="titulo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="autor" class="form-label">Autor</label>
                        <input type="text" id="autor" name="autor" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" id="cantidad" name="cantidad" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoria" class="form-label">Categoria</label>
                        <input type="text" id="categoria" name="categoria" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <a href="gestionar_libros.php" class="btn btn-secondary">Volver</a>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
