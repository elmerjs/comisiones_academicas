<?php
require 'conn.php';
require('include/header.php');

if(isset($_GET['id'])) {
    // Obtener el valor de la variable "id"
    $doc = $_GET['id'];

    // Consultar la base de datos para obtener los detalles del profesor y sus relaciones
    $query = "SELECT t.documento_tercero, t.nombre_completo, d.depto_nom_propio, f.nombre_fac_min, t.CARGO_ADMIN
              FROM tercero t
              LEFT JOIN deparmanentos d ON t.fk_depto = d.PK_DEPTO
              LEFT JOIN facultad f ON d.FK_FAC = f.PK_FAC
              WHERE t.documento_tercero = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $doc);
    $stmt->execute();
    $result = $stmt->get_result();
    $profesor = $result->fetch_assoc();

    if (!$profesor) {
        echo "No se encontraron datos para el documento proporcionado.";
        exit();
    }
} else {
    // Si no se recibió el parámetro "id" en la URL
    echo "No se proporcionó un ID válido.";
    exit();
}


// Consulta para obtener los nombres de los rectores
$rectores_query = "SELECT CC, NOMBRE FROM rector ORDER BY NOMBRE ASC";
$rectores_result = $conn->query($rectores_query);
?>


?>
<br><br>
<div class="container">
    <h4 class="my-4">Solicitud de Formación</h4>
    <form action="nueva_solicitud_formacion.php" method="post">
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
        
           <div class="row mb-3">
           
            <div class="col-md-3">
                <label for="No_resolucion">No. Resolución:</label>
                <input type="text" class="form-control" id="No_resolucion" name="No_resolucion" required>
            </div>
                <div class="col-md-3">
                <label for="fecha_resolucion">Fecha Resolución:</label>
                <input type="date" class="form-control" id="fecha_resolucion" name="fecha_resolucion" required>
            </div>
   
            <div class="col-md-3">
                <label for="tipo_estudio">Comisión:INT/EXT</label>
                <select class="form-control" id="tipo_estudio" name="tipo_estudio" onchange="handleTipoEstudioChange()">
                    <option value="INT">Interior</option>
                    <option value="EXT">Exterior</option>
                </select>
            </div>
               <div class="col-md-3">
                <label for="tipo_participacion">PARTICIPANTE/PONENTE:</label>
                <select class="form-control" id="tipo_participacion" name="tipo_participacion" required>
                    <option value="Participante">Participante</option>
                    <option value="Ponente">Ponente</option>
                </select>
            </div>  
        </div>

 
           <div class="row mb-3">
            <div class="col-md-3">
                <label for="pais">País:</label>
                <select class="form-control" id="pais" name="pais" onchange="handlePaisChange()" required>
                    <option value="COLOMBIA">COLOMBIA</option>
                    <!-- Opciones adicionales de la tabla paises -->
                    <?php
                    $paises_query = "SELECT nombre_pais FROM paises ORDER BY nombre_pais ASC";
                    $paises_result = $conn->query($paises_query);
                    while ($pais = $paises_result->fetch_assoc()) {
                        echo "<option value=\"" . $pais['nombre_pais'] . "\">" . $pais['nombre_pais'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="ciudad">Ciudad:</label>
                <select class="form-control" id="ciudad" name="ciudad" required>
                    <!-- Opciones dinámicas según el país seleccionado -->
                </select>
            </div>
   <div class="col-md-2">
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
            </div>
            <div class="col-md-2">
                <label for="fecha_fin">Fecha de Fin:</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
            </div>
            <div class="col-md-2">
                <label for="fecha_aval">Fecha Aval:</label>
                <input type="date" class="form-control" id="fecha_aval" name="fecha_aval">
            </div>
              
               
        </div>
    
            <div class="row mb-3">
            <div class="col-md-3">
                <label for="evento">Evento:</label>
                <textarea class="form-control" id="evento" name="evento" required></textarea>
            </div>
            <div class="col-md-3">
                <label for="organizado_por">Organizado Por:</label>
                <textarea type="text" class="form-control" id="organizado_por" name="organizado_por"></textarea>
            </div>
                   <div class="col-md-3">
                <label for="nombre_trabajo">Nombre del Trabajo:</label>
                <textarea type="text" class="form-control" id="nombre_trabajo" name="nombre_trabajo"></textarea>
            </div>
                        <div class="col-md-3">
                <label for="justificacion">Justificación:</label>
                <textarea class="form-control" id="justificacion" name="justificacion"></textarea>
            </div>
         
        </div>
     
        <div class="row mb-3">
               <div class="col-md-3">
                <label for="duracion_horas">Duración Horas:</label>
                <input type="number" step="0.01" class="form-control" id="duracion_horas" name="duracion_horas">
            </div>

             <div class="col-md-3">
                <label for="vigencia">Vigencia:</label>
                <select class="form-control" id="vigencia" name="vigencia" required>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="periodo">Periodo:</label>
                <select class="form-control" id="periodo" name="periodo" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>
             <div class="col-md-3">
                <label for="estado">Estado:</label>
                <select class="form-control" id="estado" name="estado" required>
                    <option value="activa">Activa</option>
                    <option value="finalizada">Finalizada</option>
                    <option value="anulada">Anulada</option>
                </select>
            </div>

        </div>
           

     <br>
          <div class="bordered-section mb-3">
            <div class="section-label">Presupuesto</div>
        
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="viaticos">Viáticos:</label>
                <input type="checkbox" id="viaticos" name="viaticos" value="1" onclick="handleViaticosChange()">
            </div>
            <div class="col-md-4">
                <label for="tiquetes">Tiquetes:</label>
                <input type="checkbox" id="tiquetes" name="tiquetes" value="1" onclick="handleViaticosChange()">
            </div>
            <div class="col-md-4">
                <label for="inscripcion">Inscripción:</label>
                <input type="checkbox" id="inscripcion" name="inscripcion" value="1" onclick="handleViaticosChange()">
            </div>
        </div>
        <div id="cargoFields" class="row mb-3" style="display: none;">
            <div class="col-md-6">
                <label for="cargo_a">Cargo A:</label>
                <input type="text" class="form-control" id="cargo_a" name="cargo_a">
            </div>
        </div>
        <div id="adminFields" class="row mb-3" style="display: none;">
            <div class="col-md-6">
                <label for="valor">Valor:</label>
                <input type="number" step="0.01" class="form-control" id="valor" name="valor">
            </div>
            <div class="col-md-6">
                <label for="cdp">CDP:</label>
                <input type="text" class="form-control" id="cdp" name="cdp">
            </div>
        </div>
               </div>
         <div class="row mb-3">
            <div class="col-md-12">
                <label for="observaciones">Observaciones:</label>
                <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
            </div>
           
        </div>
       
        <div class="row mb-3">
           <div class="col-md-4">
                <label for="id_rector">ID Rector:</label>
                <select class="form-control" id="id_rector" name="id_rector" required>
                    <?php
                    while ($rector = $rectores_result->fetch_assoc()) {
                        echo "<option value=\"" . $rector['CC'] . "\">" . $rector['NOMBRE'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="id_vice">ID Vice:</label>
                <input type="text" class="form-control" id="id_vice" name="id_vice">
            </div>
             <div class="col-md-4">
                <label for="tramito">Tramitó:</label>
                <select class="form-control" id="tramito" name="tramito" required>
                    <option value="Elmer Jurado">Elmer Jurado</option>
                    <option value="Karen Montilla">Karen Montilla</option>
                </select>
            </div>
        </div>
        <br>
           <div class="row mb-3">
            <div class="col-md-3">
                <button type="submit" class="btn btn-success btn-block">Enviar</button>
            </div>
                        <div class="col-md-3">

        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
    </div>
        </div>
    </form>
   
</div>

<style>
    .bordered-section {
        border: 1px solid #ced4da;
        padding: 15px;
        position: relative;
    }

    .section-label {
        position: absolute;
        top: -10px;
        left: 10px;
        background-color: white;
        padding: 0 5px;
    }

    .text-center {
        text-align: center;
    }
    
    .btn {
        width: 100%;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    function handleTipoEstudioChange() {
        var tipoEstudio = document.getElementById("tipo_estudio").value;
        var paisSelect = document.getElementById("pais");
        if (tipoEstudio === "INT") {
            paisSelect.value = "COLOMBIA";
            paisSelect.disabled = true;
        } else {
            paisSelect.disabled = false;
        }
        handlePaisChange();
    }
       function handlePaisChange() {
        var pais = document.getElementById("pais").value;
        var ciudadSelect = document.getElementById("ciudad");
        ciudadSelect.innerHTML = ""; // Limpiar opciones previas

        fetch(`get_ciudades.php?pais=${encodeURIComponent(pais)}`)
            .then(response => response.json())
            .then(data => {
                if (data.ciudades) {
                    data.ciudades.forEach(ciudad => {
                        var option = document.createElement("option");
                        option.value = ciudad.id; // Usar id_ciudad como valor
                        option.textContent = ciudad.nombre;
                        ciudadSelect.appendChild(option);
                    });
                } else {
                    console.error("Error fetching cities:", data.error);
                }
            })
            .catch(error => console.error("Error fetching cities:", error));
    }


   function handleViaticosChange() {
        var viaticosChecked = document.getElementById("viaticos").checked;
        var tiquetesChecked = document.getElementById("tiquetes").checked;
        var inscripcionChecked = document.getElementById("inscripcion").checked;
        var cargoFields = document.getElementById("cargoFields");
        var adminFields = document.getElementById("adminFields");
        var cargoAdmin = "<?= $profesor['CARGO_ADMIN'] ?>";

        if (viaticosChecked || tiquetesChecked || inscripcionChecked) {
            cargoFields.style.display = "block";
            if (["JEFE", "DECANO", "DIRECTOR"].includes(cargoAdmin)) {
                adminFields.style.display = "block";
            }
        } else {
            cargoFields.style.display = "none";
            adminFields.style.display = "none";
        }
    }
    document.getElementById("tipo_estudio").addEventListener("change", handleTipoEstudioChange);
    document.getElementById("pais").addEventListener("change", handlePaisChange);
    document.getElementById("viaticos").addEventListener("change", handleViaticosChange);
    document.getElementById("tiquetes").addEventListener("change", handleViaticosChange);
    document.getElementById("inscripcion").addEventListener("change", handleViaticosChange);

    handleTipoEstudioChange(); // Inicializar estado de campos
    handleViaticosChange(); // Inicializar estado de campos
});
</script>

<?php
//require('include/footer.php');
?>
