<?php
session_start();
$currentYear = date("Y");
$currentFile = basename($_SERVER['PHP_SELF']);
// Activos
$activeTramitar = ($currentFile == 'report_terceros.php') ? 'active' : '';
$activeComisiones = ($currentFile == 'comisionesb.php') ? 'active' : '';
$activeInformes = (in_array($currentFile, ['report_pendientes.php', 'sinlegalizar.php'])) ? 'active' : '';
$activeDirectivos = ($currentFile == 'directivos.php') ? 'active' : '';
$activePowerBI = ($currentFile == 'powerbics.php') ? 'active' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Comisiones Académicas Docentes Unicauca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --unicauca-blue: #1a2a4a;
            --unicauca-green: #2d6a4f;
            --unicauca-gold: #c8a951;
        }
        body {
            background-color: #f5f7fa;
        }
        /* Sidebar moderno */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100%;
            background-color: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            z-index: 1000;
            transition: transform 0.3s ease;
            padding-top: 70px;
        }
        .sidebar .nav-link {
            color: #4a5568;
            padding: 0.75rem 1.5rem;
            border-radius: 0;
            transition: all 0.2s;
            font-weight: 500;
        }
        .sidebar .nav-link:hover {
            background-color: #f1f5f9;
            color: var(--unicauca-blue);
        }
        .sidebar .nav-link.active {
            background-color: #f1f5f9;
            color: var(--unicauca-blue);
            border-left: 4px solid var(--unicauca-green);
        }
        .sidebar .nav-link i {
            width: 24px;
            margin-right: 12px;
            text-align: center;
        }
        .sidebar .dropdown-toggle::after {
            float: right;
            margin-top: 0.5rem;
        }
        .sidebar .dropdown-menu {
            position: relative;
            width: 100%;
            border: none;
            border-radius: 0;
            background-color: #f8fafc;
            padding: 0;
            margin: 0;
            box-shadow: none;
        }
        .sidebar .dropdown-item {
            padding: 0.5rem 1.5rem 0.5rem 3rem;
            color: #4a5568;
        }
        .sidebar .dropdown-item:hover {
            background-color: #e9ecef;
        }
        /* Contenido principal */
        #contenido {
            margin-left: 260px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        /* Header */
        header {
            background: linear-gradient(135deg, var(--unicauca-blue), #0f1a2f);
            height: 60px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1020;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }
        header h1 {
            font-size: 1.3rem;
            margin: 0;
            font-weight: 600;
        }
        #login a {
            color: var(--unicauca-gold);
            text-decoration: none;
            margin-left: 10px;
        }
        /* Botón toggle móvil */
        .toggle-menu {
            display: none;
            position: fixed;
            top: 70px;
            left: 15px;
            z-index: 1030;
            background-color: var(--unicauca-green);
            border: none;
            color: white;
            border-radius: 4px;
            padding: 6px 12px;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-260px);
            }
            #contenido {
                margin-left: 0;
            }
            .toggle-menu {
                display: block;
            }
        }
    </style>
</head>
<body>
    <button class="toggle-menu" id="toggleMenuBtn">☰ Menú</button>
    <div class="sidebar" id="sidebar">
        <nav>
            <div class="nav flex-column">
                <a href="../../comisiones_academicas/report_terceros.php" class="nav-link <?= $activeTramitar ?>">
                    <i class="fas fa-user-check"></i> Tramitar por Profesor
                </a>
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle <?= $activeComisiones ?>" data-bs-toggle="dropdown">
                        <i class="fas fa-chalkboard-user"></i> Comisiones
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../../comisiones_academicas/comisionesb.php?anio=<?= $currentYear ?>"><?= $currentYear ?></a></li>
                        <li><a class="dropdown-item" href="../../comisiones_academicas/comisionesb.php">General</a></li>
                    </ul>
                </div>
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle <?= $activeInformes ?>" data-bs-toggle="dropdown">
                        <i class="fas fa-file-alt"></i> Informes de Comisión
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../../comisiones_academicas/report_pendientes.php?anio=<?= $currentYear ?>"><?= $currentYear ?></a></li>
                        <li><a class="dropdown-item" href="../../comisiones_academicas/report_pendientes.php">General</a></li>
                        <li><a class="dropdown-item" href="../../comisiones_academicas/sinlegalizar.php">Emails pendientes</a></li>
                    </ul>
                </div>
                <a href="../../comisiones_academicas/directivos.php" class="nav-link <?= $activeDirectivos ?>">
                    <i class="fas fa-user-tie"></i> Gestionar Encargos
                </a>
                <a href="../../comisiones_academicas/powerbics.php" class="nav-link <?= $activePowerBI ?>">
                    <i class="fas fa-chart-line"></i> PB-Gráficos
                </a>
            </div>
        </nav>
    </div>

    <header>
        <h1>Comisiones Académicas Unicauca</h1>
        <div id="login">
            <?php
            if (isset($_SESSION['loggedin'])) {
                $now = time();           
                if ($now > $_SESSION['expire']) {
                    session_destroy();
                    echo "<div class='alert alert-danger'>Sesión expirada. <a href='/comisiones_academicas/index.html'>Login</a></div>";
                    exit;
                }
                echo "<i class='fas fa-user-circle'></i> " . $_SESSION['name'] . " <a href='../../comisiones_academicas/logout.php'><i class='fas fa-sign-out-alt'></i> Logout</a>";
            } else {
                echo "<div class='alert alert-danger'>Necesita iniciar sesión. <a href='/comisiones_academicas/index.html'>Login</a></div>";
                exit;
            }
            ?>
        </div>
    </header>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('toggleMenuBtn').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            var contenido = document.getElementById('contenido');
            if (sidebar.style.transform === 'translateX(0px)' || sidebar.style.transform === '') {
                sidebar.style.transform = 'translateX(-260px)';
                contenido.style.marginLeft = '0';
            } else {
                sidebar.style.transform = 'translateX(0)';
                contenido.style.marginLeft = '260px';
            }
        });
    </script>
</body>
</html>