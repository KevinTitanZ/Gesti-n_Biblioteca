<?php
session_start();
include '../includes/db.php';

// Verifica si el usuario está autenticado
if (!isset($_SESSION['rol'])) {
    header('Location: ../login.php');
    exit;
}

$searchTerm = '';
$libros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchTerm = trim($_POST['search']);
    
    $sql = "SELECT * FROM libros WHERE titulo LIKE :search OR autor LIKE :search OR categoria LIKE :search";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search' => "%$searchTerm%"]);
    $libros = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Libro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 bg-dark text-white vh-100">
                <?php include '../menu.php'; ?>
            </div>

            <div class="col-md-10 p-4">
                <h1 class="text-center mb-4"><i class="fa-solid fa-search"></i> Buscar Libro</h1>
                <form action="" method="POST" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por título, autor o categoría" value="<?php echo htmlspecialchars($searchTerm); ?>" required>
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Buscar</button>
                    </div>
                </form>
                
                <?php if (!empty($libros)): ?>
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>Categoría</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($libros as $libro): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($libro['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($libro['autor']); ?></td>
                                    <td><?php echo htmlspecialchars($libro['categoria']); ?></td>
                                    <td><?php echo htmlspecialchars($libro['cantidad']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <div class="alert alert-warning">No se encontraron resultados.</div>
                <?php endif; ?>
                
                <a href="gestionar_libros.php" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
