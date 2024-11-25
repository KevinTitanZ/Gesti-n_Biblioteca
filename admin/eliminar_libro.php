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

    $sql = "DELETE FROM libros WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    header('Location: gestionar_libros.php');
    exit;
}
?>
