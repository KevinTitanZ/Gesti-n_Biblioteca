<?php
session_start();
include '../includes/db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener los libros disponibles para reserva y verificar si el usuario ya los ha reservado
$sql = "SELECT l.*, 
        CASE WHEN r.id IS NOT NULL THEN 1 ELSE 0 END AS ya_reservado
        FROM libros l
        LEFT JOIN reservas r ON l.id = r.id_libro AND r.id_usuario = ?
        WHERE l.estado = 'disponible' AND l.cantidad > 0";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Función para obtener el nombre del usuario
function obtenerNombreUsuario($pdo, $id_usuario) {
    $sql = "SELECT nombre FROM usuarios WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    return $usuario ? $usuario['nombre'] : 'Usuario';
}

$nombre_usuario = obtenerNombreUsuario($pdo, $id_usuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros Disponibles</title>
    <link rel="stylesheet" href="../css/consulta.css">
        <link rel="stylesheet" href="../css/libros_reservas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <i class="fas fa-book-open"></i>
                <h2>Biblioteca Virtual</h2>
            </div>
            <div class="user-info">
                <span>Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?></span>
                <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </div>
        </header>

        <nav>
            <ul>
                <li><a href="libros_disponibles.php" class="active"><i class="fas fa-book"></i> Libros Disponibles</a></li>
                <li><a href="mis_reservas.php"><i class="fas fa-bookmark"></i> Mis Reservas</a></li>
                <li><a href="perfil.php"><i class="fas fa-user"></i> Mi Perfil</a></li>
            </ul>
        </nav>

        <main>
            <div class="page-header">
                <h1>Libros Disponibles para Reserva</h1>
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Buscar por título, autor o categoría...">
                    <button id="searchBtn"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <?php
            if (isset($_SESSION['mensaje'])) {
                echo "<div class='mensaje " . (strpos($_SESSION['mensaje'], 'éxito') !== false ? 'success' : 'error') . "'>";
                echo "<i class='" . (strpos($_SESSION['mensaje'], 'éxito') !== false ? 'fas fa-check-circle' : 'fas fa-exclamation-circle') . "'></i> ";
                echo $_SESSION['mensaje'];
                echo "</div>";
                unset($_SESSION['mensaje']);
            }
            ?>

            <div class="table-container">
                <table id="librosTable">
                    <thead>
                        <tr>
                            <th>Título <i class="fas fa-sort"></i></th>
                            <th>Autor <i class="fas fa-sort"></i></th>
                            <th>Categoría <i class="fas fa-sort"></i></th>
                            <th>Cantidad</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($libros as $libro): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($libro['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($libro['autor']); ?></td>
                                <td><?php echo htmlspecialchars($libro['categoria']); ?></td>
                                <td><?php echo htmlspecialchars($libro['cantidad']); ?></td>
                                <td>
                                    <?php if ($libro['ya_reservado']): ?>
                                        <span class="ya-reservado"><i class="fas fa-check-circle"></i> Ya reservado</span>
                                    <?php else: ?>
                                        <a href="reservar.php?id=<?php echo $libro['id']; ?>" class="reservar-btn"><i class="fas fa-bookmark"></i> Reservar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <button id="prevPage" disabled><i class="fas fa-chevron-left"></i> Anterior</button>
                <span id="pageInfo">Página 1 de 1</span>
                <button id="nextPage" disabled>Siguiente <i class="fas fa-chevron-right"></i></button>
            </div>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Biblioteca Virtual. Todos los derechos reservados.</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Confirmación de reserva
            const reservarBtns = document.querySelectorAll('.reservar-btn');
            reservarBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Quieres reservar este libro?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, reservar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                });
            });

            // Búsqueda de libros
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const table = document.getElementById('librosTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            function searchTable() {
                const filter = searchInput.value.toLowerCase();
                let found = false;

                for (let i = 0; i < rows.length; i++) {
                    const titleCell = rows[i].getElementsByTagName('td')[0];
                    const authorCell = rows[i].getElementsByTagName('td')[1];
                    const categoryCell = rows[i].getElementsByTagName('td')[2];
                    
                    if (titleCell && authorCell && categoryCell) {
                        const titleText = titleCell.textContent || titleCell.innerText;
                        const authorText = authorCell.textContent || authorCell.innerText;
                        const categoryText = categoryCell.textContent || categoryCell.innerText;
                        
                        if (titleText.toLowerCase().indexOf(filter) > -1 || 
                            authorText.toLowerCase().indexOf(filter) > -1 || 
                            categoryText.toLowerCase().indexOf(filter) > -1) {
                            rows[i].style.display = "";
                            found = true;
                        } else {
                            rows[i].style.display = "none";
                        }
                    }
                }

                if (!found) {
                    // Si no se encontraron resultados, mostrar un mensaje
                    if (table.getElementsByTagName('tbody')[0].querySelector('.no-results') === null) {
                        const noResultsRow = document.createElement('tr');
                        noResultsRow.className = 'no-results';
                        noResultsRow.innerHTML = `<td colspan="5">No se encontraron libros que coincidan con la búsqueda.</td>`;
                        table.getElementsByTagName('tbody')[0].appendChild(noResultsRow);
                    }
                } else {
                    // Si se encontraron resultados, eliminar el mensaje de no resultados si existe
                    const noResultsRow = table.getElementsByTagName('tbody')[0].querySelector('.no-results');
                    if (noResultsRow) {
                        noResultsRow.remove();
                    }
                }
            }

            searchBtn.addEventListener('click', searchTable);
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    searchTable();
                }
            });

            // Auto-ocultar mensajes después de 5 segundos
            const mensajes = document.querySelectorAll('.mensaje');
            mensajes.forEach(mensaje => {
                setTimeout(() => {
                    mensaje.style.opacity = '0';
                    setTimeout(() => {
                        mensaje.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>