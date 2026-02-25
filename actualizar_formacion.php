<?php
require 'conn.php';
require('include/headerz.php');

if(isset($_GET['id'])) {
    $comision_id = $_GET['id'];

    // Consulta para obtener los datos de la comisión basada en el id
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

    // Consulta para obtener los datos del profesor basado en el documento del profesor
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

// Consultas para obtener las listas de rectores, vicerrectores, usuarios y revisores
$rectores_query = "SELECT rector_cc, rector_nombre FROM rector ORDER BY rector_nombre ASC";
$rectores_result = $conn->query($rectores_query);

$vicerrectores_query = "SELECT vice_cc, vice_nombre FROM vicerrector ORDER BY vice_nombre ASC";
$vicerrectores_result = $conn->query($vicerrectores_query);

$user_query = "SELECT Name FROM users ORDER BY name ASC";
$user_result = $conn->query($user_query);

$revisa_query = "SELECT revisa_nom_propio FROM revisa ORDER BY revisa_nom_propio ASC";
$revisa_result = $conn->query($revisa_query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Solicitud de Formación</title>
  <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Incluir jQuery UI -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- Incluir jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">


    <style>
        #destinos {
            margin-top: 15px;
        }
        .destino {
            margin-bottom: 5px;
        }
        .add-destination-btn, .remove-destination-btn {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            color: #6c757d;
            padding: 5px 10px;
            font-size: 10px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }
        .add-destination-btn:hover, .remove-destination-btn:hover {
            background-color: #e2e6ea;
            color: #495057;
        }
        .add-destination-container {
            display: flex;
            align-items: left;
            margin-top: 25px;
        }
        .remove-destination-btn {
            margin-left: 1px;
        }
        .presupuesto-container {
            margin-top: 20px;
            position: relative;
            padding: 20px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
        .bordered-section {
            position: relative;
            border: 1px solid #ced4da;
            padding: 20px;
            margin-top: 10px;
        }
        .bordered-section .section-label {
            position: absolute;
            top: -12px;
            left: 15px;
            background: #fff;
            padding: 0 10px;
            font-weight: bold;
            color: #495057;
        }
    </style>
</head>
<body>
    <br><br><br>
<div class="container">
    <h4 class="my-4">Editar Solicitud de Formación</h4>
    <form action="actualizar_solicitud_formacion.php" method="post">
        <input type="hidden" name="comision_id" value="<?= $comision_id ?>">

        <div class="row mb-3">
            <div class="col-md-2">
                <label for="numero">CC:</label>
                <input type="text" class="form-control" id="numero" name="numero" value="<?= $profesor['documento_tercero'] ?>" readonly>
            </div>
            <div class="col-md-4">
                <label for="nombre">Nombre del Profesor:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $profesor['nombre_completo'] ?>" readonly>
            </div>
            <div class="col-md-3">
                <label for="depto">Departamento:</label>
                <input type="text" class="form-control" id="depto" name="depto" value="<?= $profesor['depto_nom_propio'] ?>" readonly>
            </div>
            <div class="col-md-3">
                <label for="facultad">Facultad:</label>
                <input type="text" class="form-control" id="facultad" name="facultad" value="<?= $profesor['nombre_fac_min'] ?>" readonly>
            </div>
        </div>
        <br>
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="No_resolucion">No. Resolución:</label>
                <input type="text" class="form-control" id="No_resolucion" name="No_resolucion" value="<?= $comision['No_resolucion'] ?>" required>
            </div>
            <div class="col-md-3">
                <label for="fecha_resolucion">Fecha Resolución:</label>
                <input type="date" class="form-control" id="fecha_resolucion" name="fecha_resolucion" value="<?= $comision['fecha_resolucion'] ?>" required>
            </div>
            <div class="col-md-3">
                <label for="tipo_estudio">Comisión: INT/EXT</label>
                <select class="form-control" id="tipo_estudio" name="tipo_estudio" onchange="handleTipoEstudioChange()">
                    <option value="INT" <?= $comision['tipo_estudio'] == 'INT' ? 'selected' : '' ?>>Interior</option>
                    <option value="EXT" <?= $comision['tipo_estudio'] == 'EXT' ? 'selected' : '' ?>>Exterior</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="tipo_participacion">PARTICIPANTE/PONENTE:</label>
                <select class="form-control" id="tipo_participacion" name="tipo_participacion" required>
                    <option value="Participante" <?= $comision['tipo_participacion'] == 'Participante' ? 'selected' : '' ?>>Participante</option>
                    <option value="Ponente" <?= $comision['tipo_participacion'] == 'Ponente' ? 'selected' : '' ?>>Ponente</option>
                </select>
            </div>
        </div>
        <br>
        <div id="destinos">
            <?php
            // Supongamos que los destinos están almacenados en una tabla separada llamada 'destinos_comision'
            $destinos_query = "SELECT * FROM destino WHERE id_comision = ?";
            $stmt_destinos = $conn->prepare($destinos_query);
            $stmt_destinos->bind_param("i", $comision_id);
            $stmt_destinos->execute();
            $result_destinos = $stmt_destinos->get_result();

            while($destino = $result_destinos->fetch_assoc()):
            ?>
            <div class="row mb-3 destino">
                <div class="col-md-3">
                    <label for="pais">País:</label>
                    <input type="text" class="form-control" name="pais[]" value="<?= $destino['pais'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="ciudad">Ciudad:</label>
                    <input type="text" class="form-control" name="ciudad[]" value="<?= $destino['ciudad'] ?>" required>
                </div>
                <div class="col-md-2 add-destination-container">
                    <button type="button" class="remove-destination-btn" onclick="eliminarDestino(this)">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
      
        <div class="row mb-3">
            <div class="col-md-12">
                <button type="button" class="add-destination-btn" onclick="agregarDestino()">
                    <i class="fas fa-plus"></i> Agregar destino
                </button>
            </div>
        </div>
          <br>
        <div class="row mb-3">
    <div class="col-md-4">
        <label for="fechaINI">Fecha de Inicio:</label>
        <input type="date" class="form-control" id="fechaINI" name="fechaINI" value="<?= $comision['fechaINI'] ?>" required>
    </div>
    <div class="col-md-4">
        <label for="vence">Fecha de Fin:</label>
        <input type="date" class="form-control" id="vence" name="vence" value="<?= $comision['vence'] ?>" required>
    </div>
    <div class="col-md-4">
        <label for="fecha_aval">Fecha Aval:</label>
        <input type="date" class="form-control" id="fecha_aval" name="fecha_aval" value="<?= $comision['fecha_aval'] ?>" required>
    </div>
</div>
          <br>
        <div class="row mb-3">
    <div class="col-md-3">
        <label for="evento">Evento:</label>
        <textarea class="form-control" id="evento" name="evento" rows="3" required><?= $comision['evento'] ?></textarea>
    </div>
          <div class="col-md-1">
        <label for="modalidad">Modalidad:</label>
        <select class="form-control" id="modalidad" name="modalidad" required>
            <option value="Presencial" <?= (isset($comision['modalidad']) && $comision['modalidad'] === 'Presencial') ? 'selected' : '' ?>>Presencial</option>
            <option value="Online" <?= (isset($comision['modalidad']) && $comision['modalidad'] === 'Online') ? 'selected' : '' ?>>Online</option>
        </select>
    </div>
    <div class="col-md-3">
        <label for="organizado_por">Organizado Por:</label>
        <textarea class="form-control" id="organizado_por" name="organizado_por" rows="3" required><?= $comision['organizado_por'] ?></textarea>
    </div>
    <div class="col-md-3">
        <label for="nombre_trabajo">Nombre del Trabajo:</label>
        <textarea class="form-control" id="nombre_trabajo" name="nombre_trabajo" rows="3"><?= $comision['nombre_trabajo'] ?></textarea>
    </div>
    <div class="col-md-2">
        <label for="justificacion">Justificación:</label>
        <textarea class="form-control" id="justificacion" name="justificacion" rows="3"><?= $comision['justificacion'] ?></textarea>
    </div>
</div>
          <br>
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="duracion_horas">Duración Horas:</label>
                <input type="text" class="form-control" id="duracion_horas" name="duracion_horas" value="<?= $comision['duracion_horas'] ?>" >
            </div>
            <div class="col-md-3">
                <label for="vigencia">Vigencia:</label>
                <input type="text" class="form-control" id="vigencia" name="vigencia" value="<?= $comision['vigencia'] ?>" required>
            </div>
            <div class="col-md-3">
                <label for="periodo">Periodo:</label>
                <input type="text" class="form-control" id="periodo" name="periodo" value="<?= $comision['periodo'] ?>" required>
            </div>
            <div class="col-md-3">
               <label for="estado">Estado:</label>
    <select class="form-control" id="estado" name="estado" required>
        <option value="Activa" <?= ($comision['estado'] === "Activa") ? 'selected' : '' ?>>Activa</option>
        <option value="finalizada" <?= ($comision['estado'] === "finalizada") ? 'selected' : '' ?>>Finalizada</option>
        <option value="anulada" <?= ($comision['estado'] === "anulada") ? 'selected' : '' ?>>Anulada</option>
    </select>
            </div>
        </div>
          <br>
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="viaticos">Viáticos:</label>
                <input type="checkbox" id="viaticos" name="viaticos" <?= $comision['viaticos'] ? 'checked' : '' ?>>
            </div>
            <div class="col-md-3">
                <label for="tiquetes">Tiquetes:</label>
                <input type="checkbox" id="tiquetes" name="tiquetes" <?= $comision['tiquetes'] ? 'checked' : '' ?>>
            </div>
            <div class="col-md-3">
                <label for="inscripcion">Inscripción:</label>
                <input type="checkbox" id="inscripcion" name="inscripcion" <?= $comision['inscripcion'] ? 'checked' : '' ?>>
            </div>
        </div>
          <br>
        <!-- Nuevos campos añadidos aquí -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="cargo_a">Cargo A:</label>
                <input type="text" class="form-control" id="cargo_a" name="cargo_a" value="<?= $comision['cargo_a'] ?>" >
            </div>
            <div class="col-md-4">
                <label for="cdp">CDP:</label>
                <input type="text" class="form-control" id="cdp" name="cdp" value="<?= $comision['cdp'] ?>" >
            </div>
            <div class="col-md-4">
                <label for="valor">Valor:</label>
                <input type="number" class="form-control" id="valor" name="valor" value="<?= $comision['valor'] ?>" >
            </div>
        </div>
          <br>
        <div class="row mb-3">
    <div class="col-md-12">
        <label for="observacion">Observaciones:</label>
        <textarea class="form-control" id="observacion" name="observacion"><?= $comision['observacion'] ?></textarea>
    </div>
</div>
  <br>
   <div class="row mb-3">
    <div class="col-md-12">
        <label for="link_resolucion">Link:</label>
        <input type="text" class="form-control" id="link_resolucion" name="link_resolucion" value="<?= htmlspecialchars($comision['link_resolucion']) ?>" placeholder="Ingrese o actualice el enlace" onfocus="this.select();" />
    </div>
</div>
    <br>      
    <div class="row mb-3">
            <div class="col-md-3">
                <label for="rector">Rector:</label>
                <select class="form-control" id="rector" name="rector">
                    <?php while($rector = $rectores_result->fetch_assoc()): ?>
                    <option value="<?= $rector['rector_cc'] ?>" <?= $comision['id_rector'] == $rector['rector_cc'] ? 'selected' : '' ?>><?= $rector['rector_nombre'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="vicerrector">Vicerrector:</label>
                <select class="form-control" id="vicerrector" name="vicerrector">
                    <?php while($vicerrector = $vicerrectores_result->fetch_assoc()): ?>
                    <option value="<?= $vicerrector['vice_cc'] ?>" <?= $comision['id_vice'] == $vicerrector['vice_cc'] ? 'selected' : '' ?>><?= $vicerrector['vice_nombre'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="reviso">Revisó:</label>
                <select class="form-control" id="reviso" name="reviso">
                    <?php while($revisa = $revisa_result->fetch_assoc()): ?>
                    <option value="<?= $revisa['revisa_nom_propio'] ?>" <?= $comision['reviso'] == $revisa['revisa_nom_propio'] ? 'selected' : '' ?>><?= $revisa['revisa_nom_propio'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="tramito">Tramitó:</label>
                <select class="form-control" id="tramito" name="tramito">
                    <?php while($user = $user_result->fetch_assoc()): ?>
                    <option value="<?= $user['Name'] ?>" <?= $comision['tramito'] == $user['Name'] ? 'selected' : '' ?>><?= $user['Name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
          <br>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>

<script>
  document.getElementById("vence").addEventListener("blur", function() {
    var startDate = document.getElementById("fechaINI").value;
    var endDate = document.getElementById("vence").value;

    if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
        alert("La fecha de fin no puede ser anterior a la fecha de inicio.");
        document.getElementById("vence").value = ""; // Limpiar el campo si la validación falla
    }
});
     function calculateHours() {
        var startDate = new Date(document.getElementById("fechaINI").value);
        var endDate = new Date(document.getElementById("vence").value);
        
        // Calcular la diferencia en días (se incluye el mismo día si las fechas son iguales)
        var timeDifference = endDate - startDate;
        var dayDifference = timeDifference / (1000 * 60 * 60 * 24) + 1;

        // Calcular el número de horas basándose en 8 horas por día
        var hours = dayDifference * 8;

        // Establecer el valor por defecto en el campo de horas
        document.getElementById("duracion_horas").value = hours;
    }

    // Asignar el cálculo al cambio de fecha
    document.getElementById("fechaINI").addEventListener("change", calculateHours);
    document.getElementById("vence").addEventListener("change", calculateHours);

    // Realizar el cálculo inicialmente cuando se carga la página
    calculateHours();
function agregarDestino() {
    const destinoHTML = `
        <div class="row mb-3 destino">
            <div class="col-md-3">
                <label for="pais">País:</label>
                <input type="text" class="form-control" name="pais[]" required>
            </div>
            <div class="col-md-3">
                <label for="ciudad">Ciudad:</label>
                <input type="text" class="form-control" name="ciudad[]" required>
            </div>
            <div class="col-md-2 add-destination-container">
                <button type="button" class="remove-destination-btn" onclick="eliminarDestino(this)">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>`;
    $('#destinos').append(destinoHTML);
}

function eliminarDestino(button) {
    $(button).closest('.destino').remove();
}

function handleTipoEstudioChange() {
    // Aquí puedes agregar la lógica para manejar cambios en el tipo de estudio
}
</script>
</body>
</html>
