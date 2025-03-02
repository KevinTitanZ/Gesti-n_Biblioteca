<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    try {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            if (password_verify($contrasena, $usuario['contrasena'])) {
                // Inicia sesión y redirige
                $_SESSION['id_usuario'] = $usuario['id'];
                $_SESSION['rol'] = $usuario['rol'];
                $_SESSION['nombre'] = $usuario['nombre'];

                if ($usuario['rol'] === 'administrador') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: user/libros_disponibles.php');
                }
                exit;
            } else {
                $_SESSION['error'] = "Contraseña incorrecta";
            }
        } else {
            $_SESSION['error'] = "El usuario no existe";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error en la conexión";
    }
    header('Location: login.php');
    exit;
}
?>
