<?php
session_start();
include '../includes/db.php'; // Asegúrate de que tu archivo de conexión a la base de datos esté incluido

// Validar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    echo "<p>Debes iniciar sesión para realizar una reserva.</p>";
    exit;
}

$id_usuario = $_SESSION['id_usuario']; // Obtener el ID del usuario logueado

// Verificar si se ha enviado una solicitud de reserva
if (isset($_GET['reservar'])) {
    $id_libro = $_GET['reservar'];

    try {
        // Verificar si el libro está disponible y obtener la cantidad
        $sql_verificar_libro = "SELECT estado, cantidad FROM libros WHERE id = :id_libro";
        $stmt_verificar_libro = $pdo->prepare($sql_verificar_libro);
        $stmt_verificar_libro->execute(['id_libro' => $id_libro]);
        $libro = $stmt_verificar_libro->fetch(PDO::FETCH_ASSOC);

        if (!$libro) {
            echo "<p>El libro seleccionado no existe.</p>";
            exit;
        }

        // Verificar si el libro tiene cantidad disponible
        if ($libro['estado'] !== 'disponible' || $libro['cantidad'] <= 0) {
            echo "<p>El libro no está disponible para reserva. Estado: " . $libro['estado'] . "</p>";
            exit;
        }

        // Verificar si el usuario ya ha reservado el libro
        $sql_verificar_reserva = "SELECT * FROM reservas WHERE id_usuario = :id_usuario AND id_libro = :id_libro AND estado = 'pendiente'";
        $stmt_verificar_reserva = $pdo->prepare($sql_verificar_reserva);
        $stmt_verificar_reserva->execute(['id_usuario' => $id_usuario, 'id_libro' => $id_libro]);

        if ($stmt_verificar_reserva->rowCount() > 0) {
            echo "<p>Ya has reservado este libro. Espera a que se confirme la reserva.</p>";
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

        echo "<p>¡Reserva realizada con éxito! El libro ha sido reservado.</p>";
    } catch (PDOException $e) {
        echo "<p>Error en la base de datos: " . $e->getMessage() . "</p>";
    }
}
?>

<!-- Mostrar los libros disponibles -->
<h1>Libros Disponibles para Reserva</h1>
<table border="1">
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
