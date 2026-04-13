<?php
require 'conn.php';
require('include/headerz.php');

if(isset($_GET['id'])) {
    $comision_id = $_GET['id'];

    $query = "SELECT * FROM comision_academica WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $comision_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $comision = $result->fetch_assoc();

    if (!$comision) {
        echo "No se encontraron datos para el ID de comisión proporcionado.";
        exit();
    }

    $doc = $comision['documento'];
    $query_profesor = "SELECT t.documento_tercero, t.nombre_completo, d.depto_nom_propio, f.nombre_fac_min, t.CARGO_ADMIN
                       FROM tercero t
                       LEFT JOIN deparmanentos d ON t.fk_depto = d.PK_DEPTO
                       LEFT JOIN facultad f ON d.FK_FAC = f.PK_FAC
                       WHERE t.documento_tercero = ?";
    $stmt_profesor = $conn->prepare($query_profesor);
    $stmt_profesor->bind_param("s", $doc);
    $stmt_profesor->execute();
    $result_profesor = $stmt_profesor->get_result();
    $profesor = $result_profesor->fetch_assoc();

    if (!$profesor) {
        echo "No se encontraron datos para el documento del profesor proporcionado.";
        exit();
    }
} else {
    echo "No se proporcionó un ID de comisión válido.";
    exit();
}

// Consultas para selects
$rectores_query = "SELECT rector_cc, rector_nombre FROM rector ORDER BY rector_nombre ASC";
$rectores_result = $conn->query($rectores_query);
$vicerrectores_query = "SELECT vice_cc, vice_nombre FROM vicerrector ORDER BY vice_nombre ASC";
$vicerrectores_result = $conn->query($vicerrectores_query);
$user_query = "SELECT name FROM users ORDER BY name ASC";
$user_result = $conn->query($user_query);
$revisa_query = "SELECT revisa_nom_propio FROM revisa WHERE revisa_cc NOT IN (888, 555) ORDER BY revisa_nom_propio DESC";
$revisa_result = $conn->query($revisa_query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Comisión · Unicauca</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
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
        }
        .uc-card-header {
            background: white;
            padding: 0.8rem 1.5rem;
            border-bottom: 2px solid var(--gris-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .uc-card-title {
            font-weight: 700;
            font-size: 1.2rem;
            background: linear-gradient(135deg, var(--azul-oscuro), var(--morado));
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            margin: 0;
        }
        .form-group {
            margin-bottom: 0.8rem;
        }
        .form-group label {
            font-weight: 600;
            font-size: 0.75rem;
            margin-bottom: 0.2rem;
            color: #1e293b;
        }
        .form-control, .custom-select {
            border-radius: 10px;
            border: 1px solid var(--gris-border);
            padding: 0.4rem 0.6rem;
            font-size: 0.8rem;
            height: auto;
        }
        .bordered-section {
            border: 1px solid var(--gris-border);
            border-radius: 16px;
            padding: 0.6rem 1rem; /* más compacto */
            margin: 0.5rem 0;
            position: relative;
        }
        .section-label {
            position: absolute;
            top: -10px;
            left: 15px;
            background: white;
            padding: 0 8px;
            font-size: 0.7rem;
            font-weight: bold;
            color: var(--azul-oscuro);
        }
        /* Ajuste para los checkboxes dentro del presupuesto */
        .bordered-section .custom-control {
            margin-bottom: 0;
        }
        .btn-uc-primary {
            background: var(--verde);
            color: white;
            border-radius: 30px;
            padding: 6px 18px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .btn-uc-secondary {
            background: #e9ecef;
            color: #1e293b;
            border-radius: 30px;
            padding: 6px 18px;
            font-size: 0.8rem;
        }
        .add-destination-btn, .remove-destination-btn {
            background: #f1f5f9;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 0.7rem;
        }
        @media (max-width: 768px) {
            .uc-page-wrapper { margin: 0 10px 1rem; }
            .form-group label { font-size: 0.7rem; }
        }
    </style>
</head>
<body>
<div id="contenido">
    <div class="uc-page-wrapper">
        <div class="uc-card-header">
            <h5 class="uc-card-title"><i class="fas fa-edit"></i> Editar Solicitud de Comisión</h5>
            <button type="button" class="btn btn-uc-secondary" onclick="window.location.href='report_terceros.php'"><i class="fas fa-arrow-left"></i> Volver</button>
        </div>
        <div class="p-3">
            <form action="actualizar_solicitud_formacion.php" method="post">
                <input type="hidden" name="comision_id" value="<?= $comision_id ?>">

                <!-- Datos del profesor (sin cambios) -->
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>CC</label>
                        <input type="text" class="form-control" name="numero" value="<?= htmlspecialchars($profesor['documento_tercero']) ?>" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Profesor</label>
                        <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($profesor['nombre_completo']) ?>" readonly>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Departamento</label>
                        <input type="text" class="form-control" name="depto" value="<?= htmlspecialchars($profesor['depto_nom_propio']) ?>" readonly>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Facultad</label>
                        <input type="text" class="form-control" name="facultad" value="<?= htmlspecialchars($profesor['nombre_fac_min']) ?>" readonly>
                    </div>
                </div>

                <!-- Resolución y tipo (sin cambios) -->
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>No. Resolución *</label>
                        <input type="text" class="form-control" name="No_resolucion" value="<?= htmlspecialchars($comision['No_resolucion']) ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Fecha Resolución *</label>
                        <input type="date" class="form-control" name="fecha_resolucion" value="<?= $comision['fecha_resolucion'] ?>" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>INT/EXT *</label>
                        <select class="form-control" name="tipo_estudio" id="tipo_estudio" onchange="handleTipoEstudioChange()" required>
                            <option value="INT" <?= $comision['tipo_estudio'] == 'INT' ? 'selected' : '' ?>>Interior</option>
                            <option value="EXT" <?= $comision['tipo_estudio'] == 'EXT' ? 'selected' : '' ?>>Exterior</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Participación *</label>
                        <select class="form-control" name="tipo_participacion" required>
                            <option value="Participante" <?= $comision['tipo_participacion'] == 'Participante' ? 'selected' : '' ?>>Participante</option>
                            <option value="Ponente" <?= $comision['tipo_participacion'] == 'Ponente' ? 'selected' : '' ?>>Ponente</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Modalidad</label>
                        <select class="form-control" name="modalidad" id="modalidad" required>
                            <option value="Presencial" <?= (isset($comision['modalidad']) && $comision['modalidad'] == 'Presencial') ? 'selected' : '' ?>>Presencial</option>
                            <option value="Online" <?= (isset($comision['modalidad']) && $comision['modalidad'] == 'Online') ? 'selected' : '' ?>>Online</option>
                        </select>
                    </div>
                </div>

                <!-- Destinos dinámicos (sin cambios) -->
                <div id="destinos">
                    <?php
                    $destinos_query = "SELECT * FROM destino WHERE id_comision = ?";
                    $stmt_destinos = $conn->prepare($destinos_query);
                    $stmt_destinos->bind_param("i", $comision_id);
                    $stmt_destinos->execute();
                    $result_destinos = $stmt_destinos->get_result();
                    while($destino = $result_destinos->fetch_assoc()):
                    ?>
                    <div class="form-row destino mb-2">
                        <div class="form-group col-md-3">
                            <label>País</label>
                            <input type="text" class="form-control" name="pais[]" value="<?= htmlspecialchars($destino['pais']) ?>" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Ciudad</label>
                            <input type="text" class="form-control" name="ciudad[]" value="<?= htmlspecialchars($destino['ciudad']) ?>" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="remove-destination-btn" onclick="eliminarDestino(this)"><i class="fas fa-minus"></i> Eliminar</button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div class="form-row mb-3">
                    <div class="col-md-12">
                        <button type="button" class="add-destination-btn" onclick="agregarDestino()"><i class="fas fa-plus"></i> Agregar destino</button>
                    </div>
                </div>

                <!-- Fechas (sin cambios) -->
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Fecha Inicio *</label>
                        <input type="date" class="form-control" id="fechaINI" name="fechaINI" value="<?= $comision['fechaINI'] ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Fecha Fin *</label>
                        <input type="date" class="form-control" id="vence" name="vence" value="<?= $comision['vence'] ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Fecha Aval</label>
                        <input type="date" class="form-control" name="fecha_aval" value="<?= $comision['fecha_aval'] ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Duración Horas</label>
                        <input type="number" class="form-control" id="duracion_horas" name="duracion_horas" value="<?= $comision['duracion_horas'] ?>" readonly>
                    </div>
                </div>

                <!-- Evento, Organizado Por, Nombre del Trabajo y Observaciones (4 columnas) -->
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Evento *</label>
                        <textarea class="form-control" name="evento" id="evento" rows="2" oninput="detectarModalidad()" required><?= htmlspecialchars($comision['evento']) ?></textarea>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Organizado Por</label>
                        <textarea class="form-control" name="organizado_por" rows="2"><?= htmlspecialchars($comision['organizado_por']) ?></textarea>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Nombre del Trabajo</label>
                        <textarea class="form-control" name="nombre_trabajo" rows="2"><?= htmlspecialchars($comision['nombre_trabajo']) ?></textarea>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Observaciones</label>
                        <textarea class="form-control" name="observacion" rows="2"><?= htmlspecialchars($comision['observacion']) ?></textarea>
                    </div>
                </div>

                <!-- Justificación, Vigencia, Periodo, Estado, Link Resolución (5 columnas) -->
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Justificación</label>
                        <textarea class="form-control" name="justificacion" rows="2"><?= htmlspecialchars($comision['justificacion']) ?></textarea>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Vigencia *</label>
                        <input type="text" class="form-control" name="vigencia" value="<?= htmlspecialchars($comision['vigencia']) ?>" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Periodo *</label>
                        <input type="text" class="form-control" name="periodo" value="<?= htmlspecialchars($comision['periodo']) ?>" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Estado</label>
                        <select class="form-control" name="estado" required>
                            <option value="Activa" <?= $comision['estado'] == 'Activa' ? 'selected' : '' ?>>Activa</option>
                            <option value="finalizada" <?= $comision['estado'] == 'finalizada' ? 'selected' : '' ?>>Finalizada</option>
                            <option value="anulada" <?= $comision['estado'] == 'anulada' ? 'selected' : '' ?>>Anulada</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Link Resolución (Drive)</label>
                        <input type="text" class="form-control" name="link_resolucion" value="<?= htmlspecialchars($comision['link_resolucion']) ?>" placeholder="https://drive.google.com/...">
                    </div>
                </div>

                <!-- Presupuesto compacto: checkboxes estrechos + Cargo A ancho -->
                <div class="bordered-section">
                    <div class="section-label">Presupuesto</div>
                    <div class="form-row align-items-end">
                        <div class="form-group col-md-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="viaticos" name="viaticos" value="1" <?= $comision['viaticos'] ? 'checked' : '' ?> onclick="handleViaticosChange()">
                                <label class="custom-control-label" for="viaticos">Viáticos</label>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="tiquetes" name="tiquetes" value="1" <?= $comision['tiquetes'] ? 'checked' : '' ?> onclick="handleViaticosChange()">
                                <label class="custom-control-label" for="tiquetes">Tiquetes</label>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="inscripcion" name="inscripcion" value="1" <?= $comision['inscripcion'] ? 'checked' : '' ?> onclick="handleViaticosChange()">
                                <label class="custom-control-label" for="inscripcion">Inscripción</label>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="cargo_a">Cargo A</label>
                            <input type="text" class="form-control" id="cargo_a" name="cargo_a" value="<?= htmlspecialchars($comision['cargo_a']) ?>" placeholder="Opcional">
                        </div>
                    </div>
                    <!-- Valor y CDP comentados temporalmente -->
                    <?php /*
                    <div id="adminFields" style="display: <?= (($comision['viaticos'] || $comision['tiquetes'] || $comision['inscripcion']) && in_array($profesor['CARGO_ADMIN'], ['JEFE','DECANO','DIRECTOR'])) ? 'block' : 'none' ?>;">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Valor</label>
                                <input type="number" step="0.01" class="form-control" name="valor" value="<?= $comision['valor'] ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label>CDP</label>
                                <input type="text" class="form-control" name="cdp" value="<?= htmlspecialchars($comision['cdp']) ?>">
                            </div>
                        </div>
                    </div>
                    */ ?>
                </div>

                <!-- Firmas (sin cambios) -->
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Rector *</label>
                        <select class="form-control" name="rector" required>
                            <?php while($rector = $rectores_result->fetch_assoc()): ?>
                            <option value="<?= $rector['rector_cc'] ?>" <?= $comision['id_rector'] == $rector['rector_cc'] ? 'selected' : '' ?>><?= htmlspecialchars($rector['rector_nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Vicerrector *</label>
                        <select class="form-control" name="vicerrector" required>
                            <?php while($vicerrector = $vicerrectores_result->fetch_assoc()): ?>
                            <option value="<?= $vicerrector['vice_cc'] ?>" <?= $comision['id_vice'] == $vicerrector['vice_cc'] ? 'selected' : '' ?>><?= htmlspecialchars($vicerrector['vice_nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Tramitó *</label>
                        <select class="form-control" name="tramito" required>
                            <?php while($user = $user_result->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($user['name']) ?>" <?= $comision['tramito'] == $user['name'] ? 'selected' : '' ?>><?= htmlspecialchars($user['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Revisó *</label>
                        <select class="form-control" name="reviso" required>
                            <?php while($revisa = $revisa_result->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($revisa['revisa_nom_propio']) ?>" <?= $comision['reviso'] == $revisa['revisa_nom_propio'] ? 'selected' : '' ?>><?= htmlspecialchars($revisa['revisa_nom_propio']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <!-- Botones -->
                <div class="form-row mt-3">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-uc-primary btn-block"><i class="fas fa-save"></i> Actualizar Solicitud</button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-uc-secondary btn-block" onclick="window.location.href='report_terceros.php'"><i class="fas fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// (El resto del script JavaScript se mantiene exactamente igual que antes)
var paises = ["Argentina","Bolivia","Brasil","Chile","Colombia","Ecuador","Perú","Uruguay","Venezuela","España","Francia","Alemania","Italia","Estados Unidos","México","Canadá","Reino Unido","Portugal","Países Bajos","Suiza","Suecia","China","Japón","India","Australia"];
var ciudadesColombia = ["Bogotá","Medellín","Cali","Barranquilla","Cartagena","Cúcuta","Bucaramanga","Pereira","Santa Marta","Ibagué","Manizales","Villavicencio","Neiva","Armenia","Popayán","Sincelejo","Riohacha","Leticia","San Andrés","Quibdó","Yopal","Mocoa","Florencia","Puerto Carreño","Inírida","Mitú","San José del Guaviare","Arauca","Tunja","Duitama","Sogamoso","Chiquinquirá","Facatativá","Zipaquirá","Soacha","Girardot","Melgar","Espinal","Honda","La Dorada","Chinchiná","Rionegro","Envigado","Itagüí","Bello","Turbo","Apartadó","Caucasia","Sabanalarga","Soledad","Malambo","Galapa","Baranoa","Sabanagrande","Fundación","El Banco","Ciénaga","Santa Ana","Aracataca","Zona Bananera","Maicao","Uribia","Manaure","San Juan del César","Fonseca","Villanueva","Barrancas","Dibulla","Hatonuevo","San Andrés de Tumaco","Pasto","Ipiales","Túquerres","La Unión","Guapi","Puerto Asís","Orito","Sibundoy","La Hormiga","San Vicente del Caguán","Cartagena del Chairá","El Doncello","Puerto Rico","Valparaíso","Belén de los Andaquíes","Albania","Morelia","Solano","San José del Fragua","Curillo","Montelíbano","Tierralta","Ayapel","Planeta Rica","Lorica","Cereté","Sahagún","Chinú","San Antero","San Bernardo del Viento","Montería","Corozal","Sincelejo","Morroa","San Marcos","San Onofre","Tolú","Coveñas","Sincé","Sampués","Ovejas","Los Palmitos","Colosó","Majagual","Sucre","Guaranda","San Benito Abad","Caimito","La Unión","La Libertad"];

function applyAutocomplete() {
    $('input[name="pais[]"]').autocomplete({ source: paises });
    $('input[name="ciudad[]"]').autocomplete({ source: ciudadesColombia });
}

function agregarDestino() {
    var destinoHTML = `<div class="form-row destino mb-2">
        <div class="form-group col-md-3">
            <label>País</label>
            <input type="text" class="form-control" name="pais[]" required>
        </div>
        <div class="form-group col-md-3">
            <label>Ciudad</label>
            <input type="text" class="form-control" name="ciudad[]" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="remove-destination-btn" onclick="eliminarDestino(this)"><i class="fas fa-minus"></i> Eliminar</button>
        </div>
    </div>`;
    $('#destinos').append(destinoHTML);
    applyAutocomplete();
}

function eliminarDestino(btn) {
    $(btn).closest('.destino').remove();
}

function calculateHours() {
    const startVal = document.getElementById("fechaINI").value;
    const endVal = document.getElementById("vence").value;
    if (startVal.length === 10 && endVal.length === 10) {
        const start = new Date(startVal);
        const end = new Date(endVal);
        if (!isNaN(start) && !isNaN(end) && end >= start) {
            const diffInTime = end.getTime() - start.getTime();
            const days = (diffInTime / (1000 * 60 * 60 * 24)) + 1;
            document.getElementById("duracion_horas").value = Math.round(days * 8);
        }
    }
}

function validateDates() {
    const start = document.getElementById("fechaINI").value;
    const end = document.getElementById("vence").value;
    if (start.length === 10 && end.length === 10) {
        if (new Date(end) < new Date(start)) {
            alert("La fecha de finalización no puede ser anterior a la fecha de inicio.");
            document.getElementById("vence").value = "";
        } else {
            calculateHours();
        }
    }
}

function handleTipoEstudioChange() {
    const tipo = document.getElementById("tipo_estudio").value;
    $('input[name="pais[]"]').each(function() {
        if (tipo === "INT") $(this).val("Colombia");
        else if ($(this).val() === "Colombia") $(this).val("");
    });
}

function detectarModalidad() {
    const evento = document.getElementById("evento").value.toLowerCase();
    const modalidad = document.getElementById("modalidad");
    const terminosOnline = ["virtual", "online", "remoto", "webinar", "asincronico", "asincrónico"];
    modalidad.value = terminosOnline.some(termino => evento.includes(termino)) ? "Online" : "Presencial";
}

function handleViaticosChange() {
    // Función simplificada porque Valor y CDP están comentados
    // Se mantiene para futura reactivación
}

$(document).ready(function() {
    if (typeof applyAutocomplete === "function") applyAutocomplete();
    $('#fechaINI, #vence').on('blur', validateDates);
    $('#evento').on('input', detectarModalidad);
    $('#tipo_estudio').on('change', handleTipoEstudioChange);
    handleTipoEstudioChange();
    detectarModalidad();
    if (document.getElementById("fechaINI").value && document.getElementById("vence").value) calculateHours();
});
</script>
</body>
</html>