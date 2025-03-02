<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener las reservas del usuario
$sql = "SELECT r.id, l.titulo, l.autor, l.categoria, r.fecha_reserva, r.estado
        FROM reservas r
        JOIN libros l ON r.id_libro = l.id
        WHERE r.id_usuario = ?
        ORDER BY r.fecha_reserva DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Mis Reservas</title>
    <link rel="stylesheet" href="../css/consulta.css">
    <link rel="stylesheet" href="../css/libros_reservas.css">

    <link rel="stylesheet" href="../css/mis_reservas.css">
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
                <li><a href="libros_disponibles.php"><i class="fas fa-book"></i> Libros Disponibles</a></li>
                <li><a href="mis_reservas.php" class="active"><i class="fas fa-bookmark"></i> Mis Reservas</a></li>
                <li><a href="perfil.php"><i class="fas fa-user"></i> Mi Perfil</a></li>
            </ul>
        </nav>

        <main>
            <div class="page-header">
                <h1>Mis Reservas</h1>
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Buscar en mis reservas...">
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
                <?php if (empty($reservas)): ?>
                    <div class="empty-state">
                        <i class="fas fa-book"></i>
                        <p>No tienes reservas actualmente.</p>
                        <a href="libros_disponibles.php" class="btn-primary"><i class="fas fa-plus"></i> Reservar Libros</a>
                    </div>
                <?php else: ?>
                    <table id="reservasTable">
                        <thead>
                            <tr>
                                <th>Título <i class="fas fa-sort"></i></th>
                                <th>Autor <i class="fas fa-sort"></i></th>
                                <th>Categoría</th>
                                <th>Fecha de Reserva</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservas as $reserva): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reserva['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($reserva['autor']); ?></td>
                                    <td><?php echo htmlspecialchars($reserva['categoria']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reserva['fecha_reserva'])); ?></td>
                                    <td>
                                        <span class="estado-badge <?php echo strtolower($reserva['estado']); ?>">
                                            <?php 
                                            $icono = '';
                                            switch(strtolower($reserva['estado'])) {
                                                case 'pendiente':
                                                    $icono = '<i class="fas fa-clock"></i>';
                                                    break;
                                                case 'aprobada':
                                                    $icono = '<i class="fas fa-check-circle"></i>';
                                                    break;
                                                case 'rechazada':
                                                    $icono = '<i class="fas fa-times-circle"></i>';
                                                    break;
                                                case 'finalizada':
                                                    $icono = '<i class="fas fa-flag-checkered"></i>';
                                                    break;
                                                default:
                                                    $icono = '<i class="fas fa-info-circle"></i>';
                                            }
                                            echo $icono . ' ' . htmlspecialchars($reserva['estado']);
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (strtolower($reserva['estado']) == 'pendiente'): ?>
                                            <a href="cancelar_reserva.php?id=<?php echo $reserva['id']; ?>" class="btn-cancel cancelar-btn">
                                                <i class="fas fa-times"></i> Cancelar
                                            </a>
                                        <?php elseif (strtolower($reserva['estado']) == 'aprobada'): ?>
                                            <a href="detalles_reserva.php?id=<?php echo $reserva['id']; ?>" class="btn-details">
                                                <i class="fas fa-info-circle"></i> Detalles
                                            </a>
                                        <?php else: ?>
                                            <a href="detalles_reserva.php?id=<?php echo $reserva['id']; ?>" class="btn-details">
                                                <i class="fas fa-info-circle"></i> Detalles
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="actions-container">
                <a href="libros_disponibles.php" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Libros Disponibles
                </a>
                <a href="#" id="exportPDF" class="btn-secondary">
                    <i class="fas fa-file-pdf"></i> Exportar a PDF
                </a>
            </div>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Biblioteca Virtual. Todos los derechos reservados.</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Búsqueda en la tabla
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            
            if (searchInput && searchBtn) {
                const table = document.getElementById('reservasTable');
                
                function searchTable() {
                    if (!table) return;
                    
                    const filter = searchInput.value.toLowerCase();
                    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
                    let found = false;

                    for (let i = 0; i < rows.length; i++) {
                        const titleCell = rows[i].getElementsByTagName('td')[0];
                        const authorCell = rows[i].getElementsByTagName('td')[1];
                        const categoryCell = rows[i].getElementsByTagName('td')[2];
                        const statusCell = rows[i].getElementsByTagName('td')[4];
                        
                        if (titleCell && authorCell && categoryCell && statusCell) {
                            const titleText = titleCell.textContent || titleCell.innerText;
                            const authorText = authorCell.textContent || authorCell.innerText;
                            const categoryText = categoryCell.textContent || categoryCell.innerText;
                            const statusText = statusCell.textContent || statusCell.innerText;
                            
                            if (titleText.toLowerCase().indexOf(filter) > -1 || 
                                authorText.toLowerCase().indexOf(filter) > -1 || 
                                categoryText.toLowerCase().indexOf(filter) > -1 ||
                                statusText.toLowerCase().indexOf(filter) > -1) {
                                rows[i].style.display = "";
                                found = true;
                            } else {
                                rows[i].style.display = "none";
                            }
                        }
                    }

                    if (!found && rows.length > 0) {
                        // Si no se encontraron resultados, mostrar un mensaje
                        if (table.getElementsByTagName('tbody')[0].querySelector('.no-results') === null) {
                            const noResultsRow = document.createElement('tr');
                            noResultsRow.className = 'no-results';
                            noResultsRow.innerHTML = `<td colspan="6">No se encontraron reservas que coincidan con la búsqueda.</td>`;
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
            }

            // Confirmación para cancelar reserva
            const cancelarBtns = document.querySelectorAll('.cancelar-btn');
            cancelarBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Quieres cancelar esta reserva? Esta acción no se puede deshacer.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, cancelar',
                        cancelButtonText: 'No, mantener'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                });
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

            // Exportar a PDF (simulado)
            const exportPDF = document.getElementById('exportPDF');
            if (exportPDF) {
                exportPDF.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Generando PDF',
                        text: 'Tu reporte de reservas se está generando...',
                        icon: 'info',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        Swal.fire(
                            '¡PDF Generado!',
                            'El reporte de tus reservas ha sido generado exitosamente.',
                            'success'
                        );
                    });
                });
            }
        });
    </script>
</body>
</html>