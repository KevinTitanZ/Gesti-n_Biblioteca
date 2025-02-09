<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

// Obtener la cantidad de libros por categoría
$categorias = $pdo->query("SELECT categoria, COUNT(*) AS cantidad_libros FROM libros GROUP BY categoria")->fetchAll();

// Obtener el número de reservas por categoría
$reservas_por_categoria = $pdo->query("SELECT l.categoria, COUNT(r.id) AS reservas
                                        FROM reservas r
                                        JOIN libros l ON r.id_libro = l.id
                                        GROUP BY l.categoria")->fetchAll();

// Obtener la cantidad de usuarios y administradores
$total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'usuario'")->fetchColumn();
$total_administradores = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'administrador'")->fetchColumn();

// Convertir los datos para los gráficos
$categorias_libros = [];
$cantidad_libros = [];
$reservas = [];

foreach ($categorias as $categoria) {
    $categorias_libros[] = $categoria['categoria'];
    $cantidad_libros[] = $categoria['cantidad_libros'];
}

// Para las reservas por categoría
foreach ($reservas_por_categoria as $reserva) {
    $reservas[$reserva['categoria']] = $reserva['reservas'];
}

// Si alguna categoría no tiene reservas, establecerla en 0
foreach ($categorias_libros as $categoria) {
    if (!isset($reservas[$categoria])) {
        $reservas[$categoria] = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Agregar Chart.js -->
</head>

<body>
<div class="container-fluid">
    <div class="row">
        <!-- Menú lateral -->
        <div class="col-md-2 bg-dark text-white vh-100">
            <?php include '../menu.php'; ?>
        </div>
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4">
            <div class="container mt-4">
                <h1 class="text-center">Estadísticas</h1>

                <!-- Gráfico de dona para cantidad de libros -->
                <div class="row mt-5">
                    <div class="col-md-4">
                        <h3>Cantidad de Libros por Categoría</h3>
                        <canvas id="donaLibros"></canvas> <!-- Gráfico de Dona para libros -->
                    </div>
                    <!-- Gráfico de dona para reservas -->
                    <div class="col-md-4">
                        <h3>Cantidad de Reservas por Categoría</h3>
                        <canvas id="donaReservas"></canvas> <!-- Gráfico de Dona para reservas -->
                    </div>
                    <div class="col-md-4">
                        <h3>Usuarios vs Administradores</h3>
                        <canvas id="usuariosChart"></canvas> <!-- Gráfico de Usuarios -->
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    // Gráfico de dona para cantidad de libros
    var ctxLibros = document.getElementById('donaLibros').getContext('2d');
    var donaLibros = new Chart(ctxLibros, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($categorias_libros); ?>, // Categorías de los libros
            datasets: [{
                label: 'Cantidad de Libros',
                data: <?php echo json_encode($cantidad_libros); ?>, // Cantidad de libros por categoría
                backgroundColor: ['rgba(75, 192, 192, 0.5)', 'rgba(153, 102, 255, 0.5)', 'rgba(255, 159, 64, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 99, 132, 0.5)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });

    // Gráfico de dona para cantidad de reservas
    var ctxReservas = document.getElementById('donaReservas').getContext('2d');
    var donaReservas = new Chart(ctxReservas, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($categorias_libros); ?>, // Categorías de los libros
            datasets: [{
                label: 'Cantidad de Reservas',
                data: <?php echo json_encode(array_values($reservas)); ?>, // Cantidad de reservas por categoría
                backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 159, 64, 0.5)', 'rgba(75, 192, 192, 0.5)', 'rgba(153, 102, 255, 0.5)'],
                borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 159, 64, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });

    // Gráfico de pastel para Usuarios vs Administradores
    var ctxUsuarios = document.getElementById('usuariosChart').getContext('2d');
    var usuariosChart = new Chart(ctxUsuarios, {
        type: 'pie', // Tipo de gráfico (pastel)
        data: {
            labels: ['Usuarios', 'Administradores'], // Etiquetas
            datasets: [{
                label: 'Total de Usuarios',
                data: [<?php echo $total_usuarios; ?>, <?php echo $total_administradores; ?>], // Cantidad de usuarios y administradores
                backgroundColor: ['rgba(75, 192, 192, 0.5)', 'rgba(255, 159, 64, 0.5)'], // Colores para cada segmento
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 159, 64, 1)'], // Colores de los bordes
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });
</script>

</body>

</html>
