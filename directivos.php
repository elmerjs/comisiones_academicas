<?php
require('include/headerz.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Directivos · Unicauca</title>
    <!-- Bootstrap 4 y Font Awesome (por si headerz no los tiene) -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ===== VARIABLES INSTITUCIONALES ===== */
        :root {
            --azul-oscuro: #002A9E;
            --morado: #4C19AF;
            --azul-cielo: #16A8E1;
            --verde: #249337;
            --gris-border: #E9EEF3;
            --shadow-card: 0 12px 28px rgba(0,0,0,0.05);
        }
        body {
            background: #F1F5F9;
            font-family: 'Segoe UI', system-ui;
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
        .nav-tabs {
            border-bottom: 2px solid var(--gris-border);
            padding: 0 1.5rem;
            margin-top: 0.5rem;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #4a5568;
            font-weight: 600;
            padding: 0.75rem 1.2rem;
            font-size: 0.85rem;
            transition: all 0.2s;
            border-radius: 30px 30px 0 0;
        }
        .nav-tabs .nav-link i {
            margin-right: 8px;
        }
        .nav-tabs .nav-link:hover {
            color: var(--azul-oscuro);
            background: #F1F5F9;
            border: none;
        }
        .nav-tabs .nav-link.active {
            color: var(--azul-oscuro);
            background: white;
            border-bottom: 3px solid var(--azul-oscuro);
            font-weight: 700;
        }
        .tab-content {
            padding: 1.5rem;
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
            .nav-tabs { padding: 0 0.5rem; }
            .nav-tabs .nav-link { padding: 0.5rem 0.8rem; font-size: 0.75rem; }
        }
    </style>
</head>
<body>
<div id="contenido">
    <div class="uc-page-wrapper">
        <div class="uc-card-header">
            <h5 class="uc-card-title"><i class="fas fa-users"></i> Gestión de Directivos</h5>
            <button type="button" class="btn btn-uc-secondary" onclick="window.location.href='report_terceros.php'">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="rector-tab" data-toggle="tab" href="#rector" role="tab">
                    <i class="fas fa-gavel"></i> Rectores
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="vicerrector-tab" data-toggle="tab" href="#vicerrector" role="tab">
                    <i class="fas fa-chalkboard-user"></i> Vicerrectores
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="revisa-tab" data-toggle="tab" href="#revisa" role="tab">
                    <i class="fas fa-check-double"></i> Revisores
                </a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade" id="rector" role="tabpanel">
                <?php include 'gestion_rectores.php'; ?>
            </div>
            <div class="tab-pane fade" id="vicerrector" role="tabpanel">
                <?php include 'gestion_vicerrectores.php'; ?>
            </div>
            <div class="tab-pane fade" id="revisa" role="tabpanel">
                <?php include 'gestion_revisores.php'; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============================================= -->
<!-- CARGA CONDICIONAL DE JQUERY Y BOOTSTRAP       -->
<!-- ============================================= -->
<script>
// Cargar jQuery solo si no está ya cargado
if (typeof jQuery === 'undefined') {
    document.write('<script src="https://code.jquery.com/jquery-3.5.1.min.js"><\/script>');
}
</script>
<script>
// Cargar Bootstrap JS solo si $.fn.modal no existe aún
if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal === 'undefined') {
    document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"><\/script>');
}
</script>

<script>
$(document).ready(function(){
    // Restaurar pestaña activa desde localStorage
    var activeTab = localStorage.getItem('activeTab');
    if (activeTab && $('#myTab a[href="' + activeTab + '"]').length) {
        $('#myTab a[href="' + activeTab + '"]').tab('show');
    } else {
        $('#myTab a:first').tab('show');
    }

    $('#myTab a').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
        localStorage.setItem('activeTab', $(this).attr('href'));
    });
});
</script>
</body>
</html>