<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener información del usuario
$sql = "SELECT nombre, email, rol, creado_en FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar la actualización del perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nuevo_nombre = $_POST['nombre'] ?? '';
    $nuevo_email = $_POST['email'] ?? '';
    $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';

    // Validar y actualizar los datos
    if (!empty($nuevo_nombre) && !empty($nuevo_email)) {
        $sql = "UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nuevo_nombre, $nuevo_email, $id_usuario]);

        // Actualizar contraseña si se proporciona una nueva
        if (!empty($nueva_contrasena)) {
            $contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$contrasena_hash, $id_usuario]);
        }

        $_SESSION['mensaje'] = "Perfil actualizado con éxito.";
        header('Location: perfil.php');
        exit;
    } else {
        $_SESSION['mensaje'] = "Por favor, complete todos los campos obligatorios.";
    }
}

// Obtener estadísticas de reservas
$sql = "SELECT COUNT(*) as total_reservas, 
               SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as reservas_pendientes,
               SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as reservas_completadas
        FROM reservas 
        WHERE id_usuario = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="../css/consulta.css">
    <link rel="stylesheet" href="../css/libros_reservas.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <i class="fas fa-book-open"></i>
                <h2>Biblioteca Virtual</h2>
            </div>
            <div class="user-info">
                <span>Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?></span>
                <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </div>
        </header>

        <nav>
            <ul>
                <!-- <li><a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a></li> -->
                <li><a href="libros_disponibles.php"><i class="fas fa-book"></i> Libros Disponibles</a></li>
                <li><a href="mis_reservas.php"><i class="fas fa-bookmark"></i> Mis Reservas</a></li>
                <li><a href="perfil.php" class="active"><i class="fas fa-user"></i> Mi Perfil</a></li>
            </ul>
        </nav>

        <main>
            <div class="page-header">
                <h1>Mi Perfil</h1>
            </div>

            <?php
            if (isset($_SESSION['mensaje'])) {
                echo "<div class='mensaje " . (strpos($_SESSION['mensaje'], 'éxito') !== false ? 'success' : 'error') . "'>";
                echo "<i class='" . (strpos($_SESSION['mensaje'], 'éxito') !== false ? 'fas fa-check-circle' : 'fas fa-exclamation-circle') . "'></i> ";
                echo $_SESSION['mensaje'];
                echo "</div>";
                unset($_SESSION['mensaje']);
            }
            ?>

            <div class="profile-container">
                <div class="profile-info">
                    <h2>Información Personal</h2>
                    <form action="perfil.php" method="POST">
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="nueva_contrasena">Nueva Contraseña (dejar en blanco para no cambiar):</label>
                            <input type="password" id="nueva_contrasena" name="nueva_contrasena">
                        </div>
                        <div class="form-group">
                            <label>Rol:</label>
                            <span><?php echo ucfirst($usuario['rol']); ?></span>
                        </div>
                        <div class="form-group">
                            <label>Miembro desde:</label>
                            <span><?php echo date('d/m/Y', strtotime($usuario['creado_en'])); ?></span>
                        </div>
                        <button type="submit" class="btn-primary">Actualizar Perfil</button>
                    </form>
                </div>
                <div class="profile-stats">
                    <h2>Estadísticas de Reservas</h2>
                    <div class="stat-item">
                        <i class="fas fa-book-reader"></i>
                        <span>Total de Reservas: <?php echo $estadisticas['total_reservas']; ?></span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-clock"></i>
                        <span>Reservas Pendientes: <?php echo $estadisticas['reservas_pendientes']; ?></span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Reservas Completadas: <?php echo $estadisticas['reservas_completadas']; ?></span>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Biblioteca Virtual. Todos los derechos reservados.</p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-ocultar mensajes después de 5 segundos
            const mensajes = document.querySelectorAll('.mensaje');
            mensajes.forEach(mensaje => {
                setTimeout(() => {
                    mensaje.style.opacity = '0';
                    setTimeout(() => {
                        mensaje.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>