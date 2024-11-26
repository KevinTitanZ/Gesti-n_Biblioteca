<?php
// Si el usuario está logueado y desea reservar
session_start();
if (isset($_SESSION['id_usuario']) && isset($_GET['reservar'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $id_libro = $_GET['reservar'];

    // Insertar la reserva en la tabla 'reservas'
    $sql_reserva = "INSERT INTO reservas (id_usuario, id_libro) VALUES (:id_usuario, :id_libro)";
    $stmt_reserva = $pdo->prepare($sql_reserva);
    $stmt_reserva->execute(['id_usuario' => $id_usuario, 'id_libro' => $id_libro]);

    // Actualizar el estado del libro a 'reservado'
    $sql_actualizar_estado = "UPDATE libros SET estado = 'reservado' WHERE id = :id_libro";
    $stmt_actualizar_estado = $pdo->prepare($sql_actualizar_estado);
    $stmt_actualizar_estado->execute(['id_libro' => $id_libro]);

    echo "<p>¡Libro reservado con éxito!</p>";
}
?>

<!-- En la tabla de libros disponibles -->
<?php foreach ($libros as $libro): ?>
    <tr>
        <td><?php echo htmlspecialchars($libro['titulo']); ?></td>
        <td><?php echo htmlspecialchars($libro['autor']); ?></td>
        <td><?php echo htmlspecialchars($libro['categoria']); ?></td>
        <td><?php echo htmlspecialchars($libro['cantidad']); ?></td>
        <td><?php echo $libro['estado']; ?></td>
        <td>
            <!-- Solo mostrar el botón si el libro está disponible -->
            <?php if ($libro['estado'] == 'disponible'): ?>
                <a href="consulta.php?reservar=<?php echo $libro['id']; ?>">Reservar</a>
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
