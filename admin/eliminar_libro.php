<?php
session_start();
include '../includes/db.php';

// Verifica si el usuario es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

// Verifica si se recibió un ID de libro para eliminar
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Primero elimina las reservas asociadas al libro
    $sql_reservas = "DELETE FROM reservas WHERE id_libro = :id";
    $stmt_reservas = $pdo->prepare($sql_reservas);
    $stmt_reservas->execute(['id' => $id]);

    // Luego elimina el libro
    $sql_libro = "DELETE FROM libros WHERE id = :id";
    $stmt_libro = $pdo->prepare($sql_libro);
    $stmt_libro->execute(['id' => $id]);

    header('Location: gestionar_libros.php');
    exit;
}
?>
