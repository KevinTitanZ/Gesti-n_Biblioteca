<?php
session_start();
include 'includes/db.php'; // Ruta a tu archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    try {
        // Buscar al usuario por su email
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Validar la contraseña
            if (password_verify($contrasena, $usuario['contrasena'])) {
                // Credenciales válidas
                $_SESSION['id_usuario'] = $usuario['id'];
                $_SESSION['rol'] = $usuario['rol'];
                $_SESSION['nombre'] = $usuario['nombre'];

                // Redirigir según el rol
                if ($usuario['rol'] === 'administrador') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: user/consulta.php');
                }
                exit;
            } else {
                echo "Credenciales incorrectas.";
            }
        } else {
            echo "Credenciales incorrectas.";
        }
    } catch (PDOException $e) {
        echo "Error en la conexión: " . $e->getMessage();
    }
}
?>
