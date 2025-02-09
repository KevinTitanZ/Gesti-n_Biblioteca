<?php
session_start();
include '../includes/db.php';

// Verifica si el usuario es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

// Obtener lista de usuarios
$sql = "SELECT id, nombre, email, rol, creado_en FROM usuarios";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios</title>
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
                <h1 class="text-center mb-4"><i class="fa-solid fa-users"></i> Gestionar Usuarios</h1>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Creado En</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['creado_en']); ?></td>
                                <td>
                                    <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-edit"></i> Editar</a>
                                    <a href="eliminar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="agregar_usuario.php" class="btn btn-success"><i class="fa-solid fa-user-plus"></i> Agregar Usuario</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
