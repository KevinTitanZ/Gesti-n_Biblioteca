<?php
session_start();
include '../includes/db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_reserva = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener detalles de la reserva
$sql = "SELECT r.id, r.fecha_reserva, r.estado, l.titulo, l.autor, l.categoria
        FROM reservas r
        JOIN libros l ON r.id_libro = l.id
        WHERE r.id = ? AND r.id_usuario = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_reserva, $id_usuario]);
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encuentra la reserva o no pertenece al usuario, redirigir
if (!$reserva) {
    $_SESSION['mensaje'] = "No se encontró la reserva especificada.";
    header('Location: mis_reservas.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Reserva</title>
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
                <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </div>
        </header>

        <nav>
            <ul>
                <li><a href="libros_disponibles.php"><i class="fas fa-book"></i> Libros Disponibles</a></li>
                <li><a href="mis_reservas.php"><i class="fas fa-bookmark"></i> Mis Reservas</a></li>
                <li><a href="perfil.php"><i class="fas fa-user"></i> Mi Perfil</a></li>
            </ul>
        </nav>

        <main>
            <div class="page-header">
                <h1><i class="fas fa-info-circle"></i> Detalles de Reserva</h1>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Reserva #<?php echo $reserva['id']; ?></h2>
                </div>
                <div class="card-body">
                    <p><strong>Título del Libro:</strong> <?php echo htmlspecialchars($reserva['titulo']); ?></p>
                    <p><strong>Autor:</strong> <?php echo htmlspecialchars($reserva['autor']); ?></p>
                    <p><strong>Categoría:</strong> <?php echo htmlspecialchars($reserva['categoria']); ?></p>
                    <p><strong>Fecha de Reserva:</strong> <?php echo date('d/m/Y H:i', strtotime($reserva['fecha_reserva'])); ?></p>
                    <p><strong>Estado:</strong> 
                        <span class="estado-badge <?php echo strtolower($reserva['estado']); ?>">
                            <?php echo ucfirst($reserva['estado']); ?>
                        </span>
                    </p>
                </div>
                <div class="card-footer">
                    <?php if ($reserva['estado'] == 'pendiente'): ?>
                        <form action="cancelar_reserva.php" method="POST" class="d-inline">
                            <input type="hidden" name="id_reserva" value="<?php echo $reserva['id']; ?>">
                            <button type="submit" class="btn-cancel" onclick="return confirm('¿Estás seguro de que deseas cancelar esta reserva?');">
                                <i class="fas fa-times"></i> Cancelar Reserva
                            </button>
                        </form>
                    <?php endif; ?>
                    <!-- <a href="mis_reservas.php" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a Mis Reservas
                    </a> -->
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Biblioteca Virtual. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>