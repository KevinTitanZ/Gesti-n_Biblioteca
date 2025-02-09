<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Eliminar primero las reservas del usuario
    $sqlReservas = "DELETE FROM reservas WHERE id_usuario = :id";
    $stmtReservas = $pdo->prepare($sqlReservas);
    $stmtReservas->execute(['id' => $id]);

    // Ahora sí eliminar el usuario
    $sqlUsuario = "DELETE FROM usuarios WHERE id = :id";
    $stmtUsuario = $pdo->prepare($sqlUsuario);
    $stmtUsuario->execute(['id' => $id]);

    header('Location: gestionar_usuario.php');
    exit;
}
?>
