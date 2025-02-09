<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
    header('Location: libros_disponibles.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_libro = $_GET['id'];

try {
    $pdo->beginTransaction();

    // Verificar si el libro está disponible y tiene cantidad > 0
    $sql = "SELECT id, titulo, autor, cantidad FROM libros WHERE id = ? AND estado = 'disponible' AND cantidad > 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_libro]);
    $libro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($libro) {
        // Verificar si el usuario ya ha reservado un libro con el mismo título y autor
        $sql = "SELECT COUNT(*) FROM reservas r
                JOIN libros l ON r.id_libro = l.id
                WHERE r.id_usuario = ? AND l.titulo = ? AND l.autor = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_usuario, $libro['titulo'], $libro['autor']]);
        $ya_reservado = $stmt->fetchColumn() > 0;

        if (!$ya_reservado) {
            // Crear la reserva
            $sql = "INSERT INTO reservas (id_usuario, id_libro) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_usuario, $id_libro]);

            // Actualizar la cantidad y el estado del libro si es necesario
            $nueva_cantidad = $libro['cantidad'] - 1;
            $nuevo_estado = $nueva_cantidad > 0 ? 'disponible' : 'reservado';
            $sql = "UPDATE libros SET cantidad = ?, estado = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nueva_cantidad, $nuevo_estado, $id_libro]);

            $pdo->commit();
            $mensaje = "Libro reservado con éxito.";
        } else {
            $pdo->rollBack();
            $mensaje = "Ya has reservado un libro con el mismo título y autor.";
        }
    } else {
        $pdo->rollBack();
        $mensaje = "El libro no está disponible para reserva.";
    }
} catch (Exception $e) {
    $pdo->rollBack();
    $mensaje = "Error al procesar la reserva: " . $e->getMessage();
}

$_SESSION['mensaje'] = $mensaje;
header('Location: libros_disponibles.php');
exit;

