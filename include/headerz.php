<?php
session_start();
$currentYear = date("Y");

// =============================================
// VALIDACIÓN DE SESIÓN (ANTES DE CUALQUIER HTML)
// =============================================
if (!isset($_SESSION['loggedin'])) {
    header('Location: /comisiones_academicas/index.html');
    exit;
}
$now = time();
if ($now > $_SESSION['expire']) {
    session_destroy();
    header('Location: /comisiones_academicas/index.html');
    exit;
}
// Sesión válida, guardamos nombre seguro
$userName = htmlspecialchars($_SESSION['name']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Gestión Comisiones Académicas Docentes Unicauca</title>
    <style>
        /* ===== VARIABLES INSTITUCIONALES UNICAUCA ===== */
        :root {
            --azul-oscuro: #002A9E;
            --morado: #4C19AF;
            --azul-rey: #0051C6;
            --azul-cielo: #16A8E1;
            --turquesa: #04B2B5;
            --verde: #249337;
            --verde-limon: #8CBD22;
            --rojo: #E52724;
            --naranja: #EC6C1F;
            --amarillo: #F8AE15;
            --gris-fondo: #F4F7FC;
            --gris-borde: #E2E8F0;
            --sombra-suave: 0 8px 20px rgba(0,0,0,0.05);
            --transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            --transition-layout: all 0.3s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background-color: var(--gris-fondo);
            overflow-x: hidden;
        }

        /* ===== HEADER HORIZONTAL ===== */
        .uc-header {
            background: linear-gradient(135deg, var(--azul-oscuro) 0%, #001844 100%);
            height: 64px !important;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .uc-header h1 {
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            margin: 0 !important;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1 1 auto;
            min-width: 0;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            padding-left: 8px;
        }

        .uc-header h1::before {
            content: "📘";
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .uc-header #login {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(4px);
            padding: 6px 12px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            flex: 0 0 auto;
            margin: 0;
        }

        .uc-header #login .login-nombre {
            font-style: normal;
            color: #FFE6B3;
            max-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .uc-header #login a {
            color: white;
            text-decoration: none;
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 30px;
            transition: var(--transition);
            white-space: nowrap;
        }
        .uc-header #login a:hover {
            background: var(--amarillo);
            color: var(--azul-oscuro);
        }

        /* ===== MENÚ LATERAL ===== */
        #menu-lateral {
            position: fixed;
            top: 64px; left: 0;
            width: 260px;
            height: calc(100% - 64px);
            background: white;
            box-shadow: 2px 0 12px rgba(0,0,0,0.05);
            z-index: 999;
            transition: transform var(--transition-layout);
            overflow-y: auto;
            border-right: 1px solid var(--gris-borde);
            transform: translateX(0);
        }

        #contenido {
            margin-left: 260px;
            padding: 90px 20px 40px 20px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        body.sidebar-collapsed #menu-lateral { transform: translateX(-100%); }
        body.sidebar-collapsed #contenido    { margin-left: 0; }

        /* ===== ITEMS DEL MENÚ ===== */
        #menu-lateral nav ul { list-style: none; padding: 20px 12px; }
        #menu-lateral nav ul li { margin-bottom: 6px; }
        #menu-lateral nav ul li a {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; border-radius: 14px;
            color: #1E293B; font-weight: 500;
            text-decoration: none; transition: var(--transition); font-size: 0.9rem;
        }
        #menu-lateral nav ul li a::before {
            font-family: "Font Awesome 6 Free";
            font-weight: 600;
            width: 24px;
            font-size: 1.1rem;
        }
        nav ul li:first-child a::before    { content: "\f007"; }
        .submenu-container > a::before     { content: "\f0c6"; }
        nav ul li:nth-child(3) > a::before { content: "\f15c"; }
        nav ul li:nth-child(4) a::before   { content: "\f0e0"; }
        nav ul li:nth-child(5) a::before   { content: "\f4c4"; }
        nav ul li:nth-child(6) a::before   { content: "\f080"; }
        #menu-lateral nav ul li a:hover {
            background: #F1F5F9; color: var(--morado); transform: translateX(4px);
        }

        /* Submenú */
        .submenu { display: none; padding-left: 28px; margin-top: 6px; }
        .submenu.visible { display: block; }
        .submenu li a { padding: 10px 16px; font-size: 0.85rem; background: #F8FAFE; border-radius: 12px; }
        .submenu li a::before { content: "\f061"; font-size: 0.7rem; opacity: 0.7; }

        /* Toggle menú */
        #toggle-menu {
            position: static;
            flex: 0 0 auto;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 40px;
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: none;
            transition: var(--transition);
        }
        #toggle-menu:hover {
            background: rgba(255,255,255,0.28);
            transform: scale(0.96);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .uc-header { padding: 0 10px; gap: 6px; }
            .uc-header h1 { font-size: 0.9rem; gap: 4px; padding-left: 0px; }
            .uc-header h1::before { font-size: 1.1rem; }
            .uc-header #login { padding: 4px 8px; font-size: 0.75rem; gap: 6px; }
            .uc-header #login .login-nombre { max-width: 70px; }
            .uc-header #login a { padding: 2px 8px; }
            #menu-lateral { transform: translateX(-100%); }
            #contenido { margin-left: 0; padding: 80px 16px 30px 16px; }
            body.sidebar-open #menu-lateral { transform: translateX(0); }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div id="menu-lateral">
        <nav>
            <ul>
                <li><a href="../../comisiones_academicas/report_terceros.php">Tramitar por Profesor</a></li>
                <li class="submenu-container">
                    <a href="#">Comisiones</a>
                    <ul class="submenu">
                        <li><a href="../../comisiones_academicas/comisionesb.php?anio=<?php echo $currentYear; ?>"><?php echo $currentYear; ?></a></li>
                        <li><a href="../../comisiones_academicas/comisionesb.php">General</a></li>
                    </ul>
                </li>
                <li class="submenu-container">
                    <a href="#">Informes de Comisión</a>
                    <ul class="submenu">
                        <li><a href="../../comisiones_academicas/report_pendientes.php?anio=<?php echo $currentYear; ?>"><?php echo $currentYear; ?></a></li>
                        <li><a href="../../comisiones_academicas/report_pendientes.php">General</a></li>
                    </ul>
                </li>
                <li><a href="../../comisiones_academicas/sinlegalizar.php">Emails Inf. pendientes</a></li>
                <li><a href="../../comisiones_academicas/directivos.php">Gestionar Encargos</a></li>
                <li><a href="../../comisiones_academicas/powerbics.php">PB-Gráficos</a></li>
            </ul>
        </nav>
    </div>

    <header class="uc-header">
        <button id="toggle-menu">☰</button>
        <h1>Comisiones Académicas Unicauca</h1>
        <div id="login">
            <span class='login-nombre'><?= $userName ?></span>
            <a href='../../comisiones_academicas/logout.php'>Cerrar sesión</a>
        </div>
    </header>

    <script>
        const toggleBtn = document.getElementById('toggle-menu');
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                document.body.classList.toggle('sidebar-open');
            } else {
                document.body.classList.toggle('sidebar-collapsed');
            }
            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
            }, 310);
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                document.body.classList.remove('sidebar-open');
            } else {
                document.body.classList.remove('sidebar-collapsed');
            }
        });

        document.querySelectorAll('.submenu-container > a').forEach(parentLink => {
            parentLink.addEventListener('click', (e) => {
                e.preventDefault();
                const submenu = parentLink.nextElementSibling;
                if (submenu) submenu.classList.toggle('visible');
            });
        });
    </script>
</body>
</html>