<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Login</title>
</head>

<body>
    <div class="container mt-5">
        <h2>Iniciar Sesión</h2>
        <form action="procesar_login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <script>
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "<?php echo $_SESSION['error']; ?>",
                confirmButtonText: "Aceptar"
            });
        </script>
        <?php unset($_SESSION['error']); // Elimina el mensaje después de mostrarlo ?>
    <?php endif; ?>
</body>

</html>
