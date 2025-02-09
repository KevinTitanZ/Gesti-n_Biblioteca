<?php
session_start();
include '../includes/db.php'; // Asegúrate de que tu archivo de conexión a la base de datos esté incluido

// Validar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>Swal.fire('Error', 'Debes iniciar sesión para realizar una reserva.', 'error');</script>";
    exit;
}

$id_usuario = $_SESSION['id_usuario']; // Obtener el ID del usuario logueado

if (isset($_GET['reservar'])) {
    $id_libro = $_GET['reservar'];

    try {
        // Verificar si el libro está disponible y obtener la cantidad
        $sql_verificar_libro = "SELECT estado, cantidad FROM libros WHERE id = :id_libro";
        $stmt_verificar_libro = $pdo->prepare($sql_verificar_libro);
        $stmt_verificar_libro->execute(['id_libro' => $id_libro]);
        $libro = $stmt_verificar_libro->fetch(PDO::FETCH_ASSOC);

        if (!$libro) {
            echo "<script>Swal.fire('Error', 'El libro seleccionado no existe.', 'error');</script>";
            exit;
        }

        // Verificar si el libro tiene cantidad disponible
        if ($libro['estado'] !== 'disponible' || $libro['cantidad'] <= 0) {
            echo "<script>Swal.fire('Error', 'El libro no está disponible para reserva. Estado: " . $libro['estado'] . "', 'error');</script>";
            exit;
        }

        // Verificar si el usuario ya ha reservado el libro
        $sql_verificar_reserva = "SELECT * FROM reservas WHERE id_usuario = :id_usuario AND id_libro = :id_libro AND estado = 'pendiente'";
        $stmt_verificar_reserva = $pdo->prepare($sql_verificar_reserva);
        $stmt_verificar_reserva->execute(['id_usuario' => $id_usuario, 'id_libro' => $id_libro]);

        if ($stmt_verificar_reserva->rowCount() > 0) {
            echo "<script>Swal.fire('Aviso', 'Ya has reservado este libro. Espera a que se confirme la reserva.', 'info');</script>";
            exit;
        }

        // Insertar la reserva en la base de datos
        $sql_reserva = "INSERT INTO reservas (id_usuario, id_libro) VALUES (:id_usuario, :id_libro)";
        $stmt_reserva = $pdo->prepare($sql_reserva);
        $stmt_reserva->execute(['id_usuario' => $id_usuario, 'id_libro' => $id_libro]);

        // Actualizar la cantidad de libros disponibles
        $sql_actualizar_cantidad = "UPDATE libros SET cantidad = cantidad - 1 WHERE id = :id_libro";
        $stmt_actualizar_cantidad = $pdo->prepare($sql_actualizar_cantidad);
        $stmt_actualizar_cantidad->execute(['id_libro' => $id_libro]);

        // Cambiar el estado del libro a "reservado" si la cantidad llega a 0
        if ($libro['cantidad'] - 1 == 0) {
            $sql_actualizar_estado = "UPDATE libros SET estado = 'reservado' WHERE id = :id_libro";
            $stmt_actualizar_estado = $pdo->prepare($sql_actualizar_estado);
            $stmt_actualizar_estado->execute(['id_libro' => $id_libro]);
        }

        // Después de la reserva exitosa
echo "<script>
Swal.fire('Éxito', '¡Reserva realizada con éxito! El libro ha sido reservado.', 'success').then(() => {
    window.location.href = 'consulta.php'; // Redirige a consulta.php después de mostrar la alerta
});
</script>";
    } catch (PDOException $e) {
        echo "<script>Swal.fire('Error', 'Error en la base de datos: " . $e->getMessage() . "', 'error');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros Disponibles para Reserva</title>
    <link rel="stylesheet" href="../css/consulta.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.3/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.3/dist/sweetalert2.min.js"></script>
</head>
<body>

<header>
    <h1>Libros Disponibles para Reserva</h1>
</header>

<table>
    <thead>
        <tr>
            <th>Título</th>
            <th>Autor</th>
            <th>Categoría</th>
            <th>Cantidad</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Obtener los libros disponibles
        $sql = "SELECT id, titulo, autor, categoria, cantidad, estado FROM libros WHERE estado = 'disponible' AND cantidad > 0";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($libros as $libro):
        ?>
            <tr>
                <td><?php echo htmlspecialchars($libro['titulo']); ?></td>
                <td><?php echo htmlspecialchars($libro['autor']); ?></td>
                <td><?php echo htmlspecialchars($libro['categoria']); ?></td>
                <td><?php echo htmlspecialchars($libro['cantidad']); ?></td>
                <td><?php echo $libro['estado']; ?></td>
                <td>
                    <!-- Mostrar botón de reserva solo si está disponible -->
                    <a href="consulta.php?reservar=<?php echo $libro['id']; ?>">Reservar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<footer>
    <p>&copy; 2025 Biblioteca Digital. Todos los derechos reservados.</p>
</footer>

</body>
</html>
