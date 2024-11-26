<?php
session_start();
include 'includes/db.php';

// Verificar si el usuario ya está logueado, si es así redirigir
if (isset($_SESSION['id_usuario'])) {
    header('Location: user/consulta.php'); // Redirigir al usuario a la página principal
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];
    $contrasena_confirmar = $_POST['contrasena_confirmar'];

    // Validación del formulario
    if (empty($nombre) || empty($email) || empty($contrasena) || empty($contrasena_confirmar)) {
        $_SESSION['error'] = 'Todos los campos son obligatorios';
    } elseif ($contrasena !== $contrasena_confirmar) {
        $_SESSION['error'] = 'Las contraseñas no coinciden';
    } else {
        try {
            // Verificar si el email ya está registrado
            $sql = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);
            $usuario_existente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario_existente) {
                $_SESSION['error'] = 'El email ya está registrado';
            } else {
                // Insertar el nuevo usuario
                $hashed_contrasena = password_hash($contrasena, PASSWORD_DEFAULT); // Cifrado de la contraseña
                $sql_insertar = "INSERT INTO usuarios (nombre, email, contrasena, rol) 
                                 VALUES (:nombre, :email, :contrasena, 'usuario')";
                $stmt_insertar = $pdo->prepare($sql_insertar);
                $stmt_insertar->execute([
                    'nombre' => $nombre,
                    'email' => $email,
                    'contrasena' => $hashed_contrasena
                ]);

                $_SESSION['success'] = 'Registro exitoso. Ahora puedes iniciar sesión.';
                header('Location: login.php'); // Redirigir al login
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error en la base de datos: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>
<body>
    <h1>Registrar un nuevo usuario</h1>
    
    <!-- Mostrar mensaje de error o éxito -->
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php elseif (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <form action="registro.php" method="POST">
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" required><br>

        <label for="email">Correo electrónico:</label><br>
        <input type="email" id="email" name="email" required><br>

        <label for="contrasena">Contraseña:</label><br>
        <input type="password" id="contrasena" name="contrasena" required><br>

        <label for="contrasena_confirmar">Confirmar Contraseña:</label><br>
        <input type="password" id="contrasena_confirmar" name="contrasena_confirmar" required><br><br>

        <button type="submit">Registrarse</button>
    </form>

    <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
</body>
</html>
