<?php
session_start();
include '../includes/db.php';

// Verifica si el usuario es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

// Verifica si se recibió un ID de libro para editar
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Obtén los datos del libro actual
    $sql = "SELECT * FROM libros WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $libro = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$libro) {
        header('Location: gestionar_libros.php');
        exit;
    }
}

// Procesa el formulario al enviar los datos actualizados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $cantidad = $_POST['cantidad'];

    $sql = "UPDATE libros SET titulo = :titulo, autor = :autor, cantidad = :cantidad WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'titulo' => $titulo,
        'autor' => $autor,
        'cantidad' => $cantidad,
        'id' => $id
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
    <title>Editar Libro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Editar Libro</h1>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" id="titulo" name="titulo" class="form-control" value="<?= htmlspecialchars($libro['titulo']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="autor" class="form-label">Autor</label>
                <input type="text" id="autor" name="autor" class="form-control" value="<?= htmlspecialchars($libro['autor']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="cantidad" class="form-label">Cantidad</label>
                <input type="number" id="cantidad" name="cantidad" class="form-control" value="<?= htmlspecialchars($libro['cantidad']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="gestionar_libros.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>
</body>
</html>
