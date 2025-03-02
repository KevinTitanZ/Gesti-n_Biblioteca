<?php
session_start();

include '../includes/db.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

// Procesar la aceptación de reserva
if (isset($_POST['aceptar_reserva'])) {
    $id_reserva = $_POST['id_reserva'];
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    try {
        // Actualizar el estado de la reserva a 'completado'
        $sql = "UPDATE reservas SET estado = 'completado' WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_reserva]);
        
        // Actualizar el estado del libro a 'prestado'
        $sql = "UPDATE libros l
                JOIN reservas r ON l.id = r.id_libro
                SET l.estado = 'prestado'
                WHERE r.id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_reserva]);
        
        $pdo->commit();
        $_SESSION['mensaje'] = "Reserva aceptada con éxito.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['mensaje'] = "Error al aceptar la reserva: " . $e->getMessage();
    }
    
    header('Location: configuracion.php');
    exit;
}

// Obtener todas las reservas pendientes
$sql = "SELECT r.id, r.fecha_reserva, u.nombre as nombre_usuario, l.titulo, l.autor
        FROM reservas r
        JOIN usuarios u ON r.id_usuario = u.id
        JOIN libros l ON r.id_libro = l.id
        WHERE r.estado = 'pendiente'
        ORDER BY r.fecha_reserva ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Reservas de Usuarios</title>
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
                <h1 class="text-center mb-4"><i class="fas fa-book-reader"></i> Administrar Reservas de Usuarios</h1>

                <?php
                if (isset($_SESSION['mensaje'])) {
                    $tipo = (strpos($_SESSION['mensaje'], 'éxito') !== false) ? 'success' : 'danger';
                    echo "<div class='alert alert-{$tipo} alert-dismissible fade show' role='alert'>";
                    echo $_SESSION['mensaje'];
                    echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
                    echo "</div>";
                    unset($_SESSION['mensaje']);
                }
                ?>

                <?php if (empty($reservas)): ?>
                    <div class="alert alert-info" role="alert">
                        No hay reservas pendientes en este momento.
                    </div>
                <?php else: ?>
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Usuario</th>
                                <th>Libro</th>
                                <th>Autor</th>
                                <th>Fecha de Reserva</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservas as $reserva): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reserva['nombre_usuario']); ?></td>
                                    <td><?php echo htmlspecialchars($reserva['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($reserva['autor']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reserva['fecha_reserva'])); ?></td>
                                    <td>
                                        <form action="configuracion.php" method="POST">
                                            <input type="hidden" name="id_reserva" value="<?php echo $reserva['id']; ?>">
                                            <button type="submit" name="aceptar_reserva" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Aceptar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>