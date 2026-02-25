<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Comisiones Académicas</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Formulario de Comisiones Académicas</h2>
    <form id="comisionForm" method="POST" action="submit_form.php">
        <label for="documento">Documento del Tercero:</label>
        <input type="text" id="documento" name="documento" required>
        <button type="button" onclick="buscarTercero()">Buscar</button>
        <br>
        <label for="nombre_tercero">Nombre del Tercero:</label>
        <input type="text" id="nombre_tercero" name="nombre_tercero" readonly>
        <br>
        <label for="No_resolucion">No. Resolución:</label>
        <input type="text" id="No_resolucion" name="No_resolucion" required>
        <br>
        <label for="fecha_resolucion">Fecha Resolución:</label>
        <input type="date" id="fecha_resolucion" name="fecha_resolucion" required>
        <br>
        <label for="tipo_estudio">Tipo de Estudio:</label>
        <select id="tipo_estudio" name="tipo_estudio" onchange="cargarCiudades()" required>
            <option value="INT">Internacional</option>
            <option value="EXT">Externo</option>
        </select>
        <br>
        <label for="pais">País:</label>
        <select id="pais" name="pais" required>
            <!-- Aquí se cargarán las opciones de país -->
        </select>
        <br>
        <label for="ciudad">Ciudad:</label>
        <select id="ciudad" name="ciudad" required>
            <!-- Aquí se cargarán las opciones de ciudad -->
        </select>
        <br>
        <label for="estado">Estado:</label>
        <select id="estado" name="estado" required>
            <option value="activa">Activa</option>
            <option value="finalizada">Finalizada</option>
            <option value="anulada">Anulada</option>
        </select>
        <br>
        <label for="vigencia">Vigencia:</label>
        <select id="vigencia" name="vigencia" required>
            <option value="2024">2024</option>
            <option value="2025">2025</option>
        </select>
        <br>
        <label for="periodo">Periodo:</label>
        <select id="periodo" name="periodo" required>
            <option value="1">1</option>
            <option value="2">2</option>
        </select>
        <br>
        <label for="fecha_aval">Fecha Aval:</label>
        <input type="date" id="fecha_aval" name="fecha_aval">
        <br>
        <label for="duracion_horas">Duración Horas:</label>
        <input type="number" step="0.01" id="duracion_horas" name="duracion_horas">
        <br>
        <label for="fechasol">Fecha Solicitud:</label>
        <input type="date" id="fechasol" name="fechasol">
        <br>
        <label for="organizado_por">Organizado Por:</label>
        <input type="text" id="organizado_por" name="organizado_por">
        <br>
        <label for="tipo_participacion">Tipo de Participación:</label>
        <input type="text" id="tipo_participacion" name="tipo_participacion">
        <br>
        <label for="evento">Evento:</label>
        <input type="text" id="evento" name="evento">
        <br>
        <label for="nombre_trabajo">Nombre del Trabajo:</label>
        <input type="text" id="nombre_trabajo" name="nombre_trabajo">
        <br>
        <label for="tramito">Tramitó:</label>
        <select id="tramito" name="tramito" required>
            <option value="Elmer Jurado">Elmer Jurado</option>
            <option value="Karen Montilla">Karen Montilla</option>
        </select>
        <br>
        <label for="observacion">Observación:</label>
        <textarea id="observacion" name="observacion"></textarea>
        <br>
        <label for="fechaIni">Fecha Inicio:</label>
        <input type="date" id="fechaIni" name="fechaIni">
        <br>
        <label for="vence">Vence:</label>
        <input type="date" id="vence" name="vence">
        <br>
        <label for="viaticos">Viáticos:</label>
        <input type="checkbox" id="viaticos" name="viaticos" value="1">
        <br>
        <label for="tiquetes">Tiquetes:</label>
        <input type="checkbox" id="tiquetes" name="tiquetes" value="1">
        <br>
        <label for="inscripcion">Inscripción:</label>
        <input type="checkbox" id="inscripcion" name="inscripcion" value="1">
        <br>
        <div id="cargoFields" style="display: none;">
            <label for="cargo_a">Cargo A:</label>
            <input type="text" id="cargo_a" name="cargo_a">
            <br>
            <label for="justificacion">Justificación:</label>
            <textarea id="justificacion" name="justificacion"></textarea>
            <br>
            <label for="id_rector">ID Rector:</label>
            <input type="text" id="id_rector" name="id_rector">
            <br>
            <label for="id_vice">ID Vice:</label>
            <input type="text" id="id_vice" name="id_vice">
            <br>
            <label for="valor">Valor:</label>
            <input type="number" step="0.01" id="valor" name="valor">
            <br>
            <label for="cdp">CDP:</label>
            <input type="text" id="cdp" name="cdp">
        </div>
        <br>
        <button type="submit">Guardar</button>
    </form>

    <!-- Script para cargar las ciudades y el tercero -->
    <script>
        // Función para buscar el tercero
        function buscarTercero() {
            var documento = document.getElementById("documento").value;
            // Aquí se realiza
 // Puedes implementar esto usando AJAX para hacer una solicitud al servidor
            // y obtener los datos del tercero según el documento ingresado
        }

        // Función para cargar las ciudades
        function cargarCiudades() {
            var tipo_estudio = document.getElementById("tipo_estudio").value;
            // Aquí se realiza la lógica para cargar las ciudades correspondientes según el tipo de estudio
            // Si es "INT", cargar ciudades de Colombia; si es "EXT", mostrar campo abierto para ingresar la ciudad
            // Puedes implementar esto usando AJAX para hacer una solicitud al servidor y obtener las ciudades correspondientes
        }

        // Función para mostrar u ocultar los campos relacionados con el cargo
        function toggleCargoFields() {
            var viaticos = document.getElementById("viaticos").checked;
            var tiquetes = document.getElementById("tiquetes").checked;
            var inscripcion = document.getElementById("inscripcion").checked;
            var display = (viaticos || tiquetes || inscripcion) ? 'block' : 'none';

            document.getElementById("cargoFields").style.display = display;
        }

        // Evento ready de jQuery para cargar las ciudades al cargar la página
        $(document).ready(function() {
            cargarCiudades();
        });
    </script>
</body>
</html>