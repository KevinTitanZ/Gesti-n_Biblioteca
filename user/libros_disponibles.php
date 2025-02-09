<?php
session_start();
include '../includes/db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener los libros disponibles para reserva y verificar si el usuario ya los ha reservado
$sql = "SELECT l.*, 
        CASE WHEN r.id IS NOT NULL THEN 1 ELSE 0 END AS ya_reservado
        FROM libros l
        LEFT JOIN reservas r ON l.id = r.id_libro AND r.id_usuario = ?
        WHERE l.estado = 'disponible' AND l.cantidad > 0";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros Disponibles</title>
    <link rel="stylesheet" href="../css/consulta.css">
</head>
<body>
    <h1>Libros Disponibles para Reserva</h1>

    <?php
    if (isset($_SESSION['mensaje'])) {
        echo "<p class='mensaje'>" . $_SESSION['mensaje'] . "</p>";
        unset($_SESSION['mensaje']);
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Autor</th>
                <th>Categoría</th>
                <th>Cantidad</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($libros as $libro): ?>
                <tr>
                    <td><?php echo htmlspecialchars($libro['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($libro['autor']); ?></td>
                    <td><?php echo htmlspecialchars($libro['categoria']); ?></td>
                    <td><?php echo htmlspecialchars($libro['cantidad']); ?></td>
                    <td>
                        <?php if ($libro['ya_reservado']): ?>
                            <span class="ya-reservado">Ya reservado</span>
                        <?php else: ?>
                            <a href="reservar.php?id=<?php echo $libro['id']; ?>" class="reservar-btn">Reservar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="mis_reservas.php" class="btn">Ver Mis Reservas</a>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reservarBtns = document.querySelectorAll('.reservar-btn');
            reservarBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Quieres reservar este libro?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, reservar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>

