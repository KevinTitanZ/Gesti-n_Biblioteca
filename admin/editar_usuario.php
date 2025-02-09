<?php
// editar_usuario.php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    
    $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, rol = :rol WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nombre' => $nombre, 'email' => $email, 'rol' => $rol, 'id' => $id]);
    
    header('Location: gestionar_Usuarios.php');
    exit;
}

$id = $_GET['id'];
$sql = "SELECT * FROM usuarios WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$usuario = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4"><i class="fa-solid fa-user-edit"></i> Editar Usuario</h2>
        <form action="" method="POST" class="bg-light p-4 rounded shadow">
            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo $usuario['nombre']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" value="<?php echo $usuario['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Rol:</label>
                <select name="rol" class="form-select">
                    <option value="usuario" <?php if ($usuario['rol'] == 'usuario') echo 'selected'; ?>>Usuario</option>
                    <option value="administrador" <?php if ($usuario['rol'] == 'administrador') echo 'selected'; ?>>Administrador</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-save"></i> Actualizar</button>
            <a href="gestionar_usuario.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
