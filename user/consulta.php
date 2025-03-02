<?php
session_start();
include '../includes/db.php';  // Asegúrate de que la conexión esté incluida

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');  // Redirigir a login si no está logueado
    exit;
}

// Obtener los libros disponibles para reserva
$sql = "SELECT * FROM libros WHERE estado = 'disponible' AND cantidad > 0";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros Disponibles</title>
    <link rel="stylesheet" href="../css/consulta.css"> <!-- Enlace a tus estilos -->
</head>
<body>
    <h1>Libros Disponibles para Reserva</h1>

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
                        <a href="reservar.php?reservar=<?php echo $libro['id']; ?>">Reservar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script src="../assets/js/sweetalert.js"></script> <!-- Si usas SweetAlert -->
</body>
</html>
