<?php
session_start();
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Nota: Se recomienda usar consultas preparadas para evitar inyección SQL
    $result = mysqli_query($conn, "SELECT Email, Password, Name, DocUsuario FROM users WHERE Email = '$email'");
    $row = mysqli_fetch_assoc($result);

    if ($row && password_verify($password, $row['Password'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['name'] = $row['Name'];
        $_SESSION['docusuario'] = $row['DocUsuario'];
        $_SESSION['start'] = time();
        $_SESSION['expire'] = $_SESSION['start'] + (5 * 3600);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Comisiones Académicas · Unicauca</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --azul-oscuro: #002A9E;
            --morado: #4C19AF;
            --azul-rey: #0051C6;
            --azul-cielo: #16A8E1;
            --verde: #249337;
            --rojo: #E52724;
            --gris-fondo: #F4F7FC;
            --gris-borde: #E9EEF3;
            --shadow-card: 0 12px 28px rgba(0,0,0,0.05), 0 0 0 1px rgba(0,0,0,0.02);
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #eef2f9 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        /* Contenedor principal */
        .login-container {
            max-width: 480px;
            width: 100%;
            background: white;
            border-radius: 32px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, var(--azul-oscuro), var(--morado));
            color: white;
            padding: 2rem 1.5rem;
            text-align: center;
        }
        .login-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .login-header p {
            font-size: 0.85rem;
            opacity: 0.9;
            margin: 0;
        }
        .login-body {
            padding: 2rem 1.8rem;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        .form-group label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #1e293b;
            margin-bottom: 0.4rem;
            display: block;
        }
        .form-control {
            border-radius: 14px;
            border: 1px solid var(--gris-borde);
            padding: 0.7rem 1rem;
            font-size: 0.9rem;
            transition: 0.2s;
        }
        .form-control:focus {
            border-color: var(--azul-cielo);
            box-shadow: 0 0 0 3px rgba(22,168,225,0.1);
        }
        .btn-login {
            background: var(--verde);
            color: white;
            border: none;
            border-radius: 40px;
            padding: 0.75rem;
            font-weight: 600;
            font-size: 0.9rem;
            width: 100%;
            transition: 0.2s;
        }
        .btn-login:hover {
            background: #1a6e2c;
            transform: translateY(-2px);
        }
        /* Menú después del login */
        .dashboard-container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }
        .welcome-card {
            background: white;
            border-radius: 28px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .welcome-header {
            background: linear-gradient(135deg, var(--azul-oscuro), var(--morado));
            color: white;
            padding: 1.2rem 1.8rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .welcome-header h2 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
        }
        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            border-radius: 40px;
            padding: 6px 18px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: 0.2s;
        }
        .btn-logout:hover {
            background: var(--rojo);
            transform: translateY(-2px);
        }
        .welcome-body {
            padding: 1.8rem;
            border-bottom: 2px solid var(--gris-borde);
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-top: 10px;
        }
        .menu-card {
            background: white;
            border: 1px solid var(--gris-borde);
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.2s ease;
            text-decoration: none;
            color: #1e293b;
        }
        .menu-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-card);
            border-color: var(--azul-cielo);
        }
        .menu-card i {
            font-size: 2.5rem;
            margin-bottom: 0.8rem;
            display: inline-block;
        }
        .menu-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
        }
        .menu-card p {
            font-size: 0.8rem;
            color: #5b677b;
            margin: 0;
        }
        .alert-custom {
            background: #e6f4ea;
            border-left: 4px solid var(--verde);
            border-radius: 16px;
            padding: 1rem;
        }
        @media (max-width: 768px) {
            .login-body { padding: 1.5rem; }
            .welcome-header { flex-direction: column; align-items: flex-start; }
            .menu-grid { grid-template-columns: 1fr; gap: 16px; }
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['loggedin'])): ?>
        <div class="dashboard-container">
            <div class="welcome-card">
                <div class="welcome-header">
                    <h2><i class="fas fa-chalkboard-user me-2"></i> Comisiones Académicas Unicauca</h2>
                    <div>
                        <span class="me-3"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                        <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt me-1"></i> Cerrar sesión</a>
                    </div>
                </div>
                <div class="welcome-body">
                    <div class="alert-custom mb-4">
                        <h5 class="mb-1">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h5>
                        <p class="mb-0">Has iniciado sesión correctamente. Selecciona una opción para continuar.</p>
                    </div>
                    <div class="menu-grid">
                        <a href="comisionesb.php" class="menu-card">
                            <i class="fas fa-users" style="color: var(--azul-rey);"></i>
                            <h3>Comisiones</h3>
                            <p>Gestión de comisiones académicas</p>
                        </a>
                        <a href="report_terceros.php" class="menu-card">
                            <i class="fas fa-chalkboard-user" style="color: var(--verde);"></i>
                            <h3>Reporte por docentes</h3>
                            <p>Información detallada por profesor</p>
                        </a>
                        <a href="report_pendientes.php" class="menu-card">
                            <i class="fas fa-exclamation-triangle" style="color: var(--rojo);"></i>
                            <h3>Informes Pendientes</h3>
                            <p>Comisiones sin informe</p>
                        </a>
                        <a href="directivos.php" class="menu-card">
                            <i class="fas fa-user-tie" style="color: var(--azul-cielo);"></i>
                            <h3>Gestionar Encargos</h3>
                            <p>Administración de directivos</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="login-container">
            <div class="login-header">
                <h1><i class="fas fa-chalkboard-user me-2"></i> Comisiones Académicas</h1>
                <p>Universidad del Cauca</p>
            </div>
            <div class="login-body">
                <div class="alert alert-danger text-center py-2" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> Acceso denegado
                </div>
                <p class="text-center text-muted mb-4">Necesitas iniciar sesión para acceder al sistema.</p>
                <a href="index.html" class="btn-login d-block text-center text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Volver al inicio de sesión
                </a>
            </div>
        </div>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>