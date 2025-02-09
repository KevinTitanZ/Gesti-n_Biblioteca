<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener las reservas del usuario
$sql = "SELECT r.id, l.titulo, l.autor, r.fecha_reserva, r.estado
        FROM reservas r
        JOIN libros l ON r.id_libro = l.id
        WHERE r.id_usuario = ?
        ORDER BY r.fecha_reserva DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas</title>
    <link rel="stylesheet" href="../css/consulta.css">
</head>
<body>
    <h1>Mis Reservas</h1>

    <?php if (empty($reservas)): ?>
        <p>No tienes reservas actualmente.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Fecha de Reserva</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reserva['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['autor']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['fecha_reserva']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['estado']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="libros_disponibles.php" class="btn">Volver a Libros Disponibles</a>
</body>
</html>

