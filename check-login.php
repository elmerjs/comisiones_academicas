<?php
session_start();
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comisiones Académicas | Unicauca</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #334155;
        }
        .topbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topbar-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn-logout {
            background: #ef4444;
            border: none;
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 0.85rem;
            font-weight: 500;
            color: white;
            transition: all 0.2s;
        }
        .btn-logout:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }
        .content-area {
            padding: 28px 32px 48px;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .menu-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            color: #334155;
        }
        .menu-card:hover {
            border-color: #3b82f6;
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(59,130,246,0.1);
        }
        .menu-card i {
            font-size: 2rem;
            margin-bottom: 12px;
            display: inline-block;
        }
        .menu-card h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .menu-card p {
            font-size: 0.75rem;
            color: #64748b;
            margin: 0;
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['loggedin'])): ?>
        <div class="topbar">
            <div class="topbar-title">
                <i class="fas fa-chalkboard-user text-primary me-2"></i> Comisiones Académicas
            </div>
            <div class="user-info">
                <span><i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión</a>
            </div>
        </div>
        <div class="content-area">
            <div class="alert alert-success text-center mb-4" role="alert">
                <h4 class="alert-heading">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h4>
                <p>Has iniciado sesión exitosamente. Selecciona una opción para continuar.</p>
            </div>
            <div class="menu-grid">
                <a href="comisionesb.php" class="menu-card">
                    <i class="fas fa-users text-primary"></i>
                    <h3>Comisiones</h3>
                    <p>Gestión de comisiones académicas</p>
                </a>
                <a href="report_terceros.php" class="menu-card">
                    <i class="fas fa-chalkboard-teacher text-success"></i>
                    <h3>Reporte por docentes</h3>
                    <p>Información detallada por profesor</p>
                </a>
                <a href="report_pendientes.php" class="menu-card">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <h3>Informes Pendientes</h3>
                    <p>Comisiones sin informe</p>
                </a>
                <a href="directivos.php" class="menu-card">
                    <i class="fas fa-user-tie text-info"></i>
                    <h3>Gestionar Encargos</h3>
                    <p>Administración de directivos</p>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="container mt-5 pt-5 text-center">
            <div class="alert alert-danger">
                <h5>Acceso denegado</h5>
                <p>Necesitas iniciar sesión para acceder a esta página.</p>
                <hr>
                <a href="index.html" class="alert-link">Volver al inicio de sesión</a>
            </div>
        </div>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>