<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Lateral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background-color: #343a40;
            color: #ffffff;
            position: fixed;
            height: 100%;
            top: 0;
            left: 0;
            padding-top: 20px;
        }

        #sidebar .nav-link {
            color: #ffffff;
        }

        #sidebar .nav-link:hover {
            background-color: #495057;
        }

        #content {
            flex-grow: 1;
            margin-left: 250px; /* Espacio para que el contenido no quede debajo del menú */
            padding: 20px;
            height: 100%;
            overflow-y: auto;
        }

        .nav-item {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <!-- Menú lateral -->
    <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar" style="background-color: #0056b3;">

        <div class="position-sticky">
            <h3 class="text-center py-3 text-white">Menú</h3>
            <ul class="nav flex-column">
                <!-- Libros -->
                <li class="nav-item">
                    <a class="nav-link text-white" data-bs-toggle="collapse" href="#menuLibros" role="button" aria-expanded="false" aria-controls="menuLibros">
                        <i class="fa-solid fa-book me-2"></i> Libros
                    </a>
                    <div class="collapse ps-3" id="menuLibros">
                        <ul class="nav flex-column">
                            <li><a href="gestionar_libros.php" class="nav-link text-white">Gestionar Libros</a></li>
                            <li><a href="agregar_libro.php" class="nav-link text-white">Agregar Libro</a></li>
                            <li><a href="buscar_libro.php" class="nav-link text-white">Buscar Libro</a></li>
                        </ul>
                    </div>
                </li>

                <!-- Usuarios -->
                <li class="nav-item">
                    <a class="nav-link text-white" data-bs-toggle="collapse" href="#menuUsuarios" role="button" aria-expanded="false" aria-controls="menuUsuarios">
                        <i class="fa-solid fa-users me-2"></i> Usuarios
                    </a>
                    <div class="collapse ps-3" id="menuUsuarios">
                        <ul class="nav flex-column">
                            <li><a href="gestionar_usuario.php" class="nav-link text-white">Gestionar Usuarios</a></li>
                            <li><a href="agregar_usuario.php" class="nav-link text-white">Agregar Usuario</a></li>
                        </ul>
                    </div>
                </li>

                <!-- Otros -->
                <li class="nav-item">
                    <a href="estadisticas.php" class="nav-link text-white">
                        <i class="fa-solid fa-chart-line me-2"></i> Estadísticas
                    </a>
                </li>
                <li class="nav-item">
                    <a href="configuracion.php" class="nav-link text-white">
                        <i class="fa-solid fa-cog me-2"></i> Reserva de Libros
                    </a>
                </li>

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-white">
                    <i class="fa-solid fa-right-from-bracket"></i> Salir
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div id="content">
        <!-- Aquí se incluirá el contenido principal de cada página -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
