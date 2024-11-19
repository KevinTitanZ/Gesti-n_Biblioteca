<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO usuarios (nombre, email, contrasena) VALUES (:nombre, :email, :contrasena)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nombre' => $nombre, 'email' => $email, 'contrasena' => $contrasena]);
        echo "Registro exitoso. ¡Ahora puedes iniciar sesión!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<form method="post" action="registro.php">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="contrasena" placeholder="Contraseña" required>
    <button type="submit">Registrar</button>
</form>
