<?php require('include/headerz.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Gráficos · Comisiones Unicauca</title>
    <!-- Estilos adicionales (no se duplica Bootstrap, ya lo trae headerz.php) -->
    <style>
        /* ===== ESTILOS ADICIONALES PARA EL IFRAME ===== */
        .uc-iframe-container {
            position: relative;
            width: 100%;
            height: calc(100vh - 200px); /* Altura dinámica restando header y padding */
            min-height: 500px;
            border-radius: 16px;
            overflow: hidden;
            background: #f8fafc;
        }
        .uc-iframe-container iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
        @media (max-width: 768px) {
            .uc-iframe-container {
                height: calc(100vh - 160px);
                min-height: 400px;
            }
        }
    </style>
</head>
<body>
<div id="contenido">
    <div class="uc-page-wrapper">
        <div class="uc-card-header">
            <h5 class="uc-card-title">
                <i class="fas fa-chart-line"></i> Estadísticas y Gráficos
            </h5>
            <button type="button" class="btn btn-uc-secondary" onclick="window.location.href='report_terceros.php'">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </div>
        <div class="p-3">
            <div class="uc-iframe-container">
                <iframe 
                    title="Comisiones Académicas - Dashboard Power BI"
                    src="https://app.powerbi.com/view?r=eyJrIjoiMjA2ZjBjMTItZTJhNi00NDNkLWJkOGYtOTlhMjE2ZTI0NmIzIiwidCI6ImU4MjE0OTM3LTIzM2ItNGIzNi04NmJmLTBiNWYzMzM3YmVlMSIsImMiOjF9" 
                    allowfullscreen="true">
                </iframe>
            </div>
        </div>
    </div>
</div>

<!-- Estilos complementarios para la card (ya definidos en headerz, pero por si acaso) -->
<style>
    /* Aseguramos que las variables estén definidas (headerz ya las tiene) */
    :root {
        --azul-oscuro: #002A9E;
        --morado: #4C19AF;
        --azul-cielo: #16A8E1;
        --verde: #249337;
        --gris-border: #E9EEF3;
        --shadow-card: 0 12px 28px rgba(0,0,0,0.05);
    }
    .uc-page-wrapper {
        background: white;
        border-radius: 24px;
        box-shadow: var(--shadow-card);
        margin: 0 20px 2rem 20px;
        overflow: hidden;
    }
    .uc-card-header {
        background: white;
        padding: 0.8rem 1.5rem;
        border-bottom: 2px solid var(--gris-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .uc-card-title {
        font-weight: 700;
        font-size: 1.2rem;
        background: linear-gradient(135deg, var(--azul-oscuro), var(--morado));
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-uc-secondary {
        background: #e9ecef;
        color: #1e293b;
        border-radius: 30px;
        padding: 6px 18px;
        font-size: 0.8rem;
        border: none;
        transition: 0.2s;
    }
    .btn-uc-secondary:hover {
        background: #dee2e6;
    }
    @media (max-width: 768px) {
        .uc-page-wrapper { margin: 0 10px 1rem; }
        .uc-card-header { flex-direction: column; align-items: flex-start; }
    }
</style>
</body>
</html>