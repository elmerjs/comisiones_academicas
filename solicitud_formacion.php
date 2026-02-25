<?php
require 'conn.php';
require('include/headerz.php');

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
$rectores_query = "SELECT rector_cc, rector_nombre FROM rector ORDER BY tipo_rector ASC";
$rectores_result = $conn->query($rectores_query);


// Consulta para obtener los nombres de los vcrectores
$vicerrectores_query = "SELECT vice_cc, vice_nombre FROM vicerrector ORDER BY tipo_vice DESC";
$vicerrectores_result = $conn->query($vicerrectores_query);

// Consulta para obtener los nombres de los usaurios 
$user_query = "SELECT name FROM users ORDER BY name ASC";
$user_result = $conn->query($user_query);
$revisa_query = "SELECT revisa_nom_propio FROM revisa ORDER BY revisa_nom_propio desc";
$revisa_result = $conn->query($revisa_query);



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario con Autocompletar</title>
    <!-- Incluir jQuery -->
 <!-- Incluir jQuery -->
     <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Incluir jQuery UI -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- Incluir jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">


    </head>
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
            padding: 5px 10px; /* Reduce el padding */
            font-size: 10px; /* Ajusta el tamaño de la fuente */
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
    
<body>
<br><br>
    <div id="contenido">
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
        <br>
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

<div id="destinos">
    <div class="row mb-3 destino">
        <div class="col-md-3">
            <label for="pais">País:</label>
    <input type="text" class="form-control" name="pais[]" value="Colombia" required>
        </div>
        <div class="col-md-3">
            <label for="ciudad">Ciudad:</label>
            <input type="text" class="form-control" name="ciudad[]" required>
        </div>
        <div class="col-md-2 add-destination-container">
                    <button type="button" class="add-destination-btn" onclick="agregarDestino()">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
    </div>
</div>
  <br>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
            </div>
            <div class="col-md-4">
                <label for="fecha_fin">Fecha de Fin:</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
            </div>
            <div class="col-md-4">
                <label for="fecha_aval">Fecha Aval:</label>
                <input type="date" class="form-control" id="fecha_aval" name="fecha_aval">
            </div>
        </div>
      <br>
<div class="row mb-3">
    <div class="col-md-3">
        <label for="evento">Evento:</label>
        <textarea class="form-control" id="evento" name="evento" required oninput="detectarModalidad()"></textarea>
    </div>
    <div class="col-md-1">
        <label for="modalidad">Modalidad:</label>
        <select class="form-control" id="modalidad" name="modalidad" required>
            <option value="Presencial" selected>Presencial</option>
            <option value="Online">Online</option>
        </select>
    </div>
    <div class="col-md-3">
        <label for="organizado_por">Organizado Por:</label>
        <textarea class="form-control" id="organizado_por" name="organizado_por"></textarea>
    </div>
    <div class="col-md-3">
        <label for="nombre_trabajo">Nombre del Trabajo:</label>
        <textarea class="form-control" id="nombre_trabajo" name="nombre_trabajo"></textarea>
    </div>
    <div class="col-md-2">
        <label for="justificacion">Justificación:</label>
        <textarea class="form-control" id="justificacion" name="justificacion"></textarea>
    </div>
</div>

<script>
function detectarModalidad() {
    const campoEvento = document.getElementById("evento");
    const selectModalidad = document.getElementById("modalidad");
    const textoEvento = campoEvento.value.toLowerCase();
    
    // Palabras clave que indican modalidad Online
    const palabrasClave = ["virtual", "online", "remoto"];
    
    // Si alguna palabra clave está en el texto del evento, cambia a "Online"
    if (palabrasClave.some(palabra => textoEvento.includes(palabra))) {
        selectModalidad.value = "Online";
    } else {
        selectModalidad.value = "Presencial";
    }
}
</script>    <br>
        <div class="row mb-3">
               <div class="col-md-3">
                <label for="duracion_horas">Duración Horas:</label>
                <input type="number"  class="form-control" id="duracion_horas" name="duracion_horas">
            </div>

          <div class="col-md-3">
    <label for="vigencia">Vigencia:</label>
    <select class="form-control" id="vigencia" name="vigencia" required>
        <?php
        // Obtener el año actual
        $currentYear = date("Y");
        
        // Añadir las opciones para el año actual, el año anterior y el año siguiente
        for ($i = -1; $i <= 1; $i++) {
            $year = $currentYear + $i;
            $selected = ($i == 0) ? "selected" : ""; // Marcar como seleccionado el año actual
            echo "<option value=\"$year\" $selected>$year</option>";
        }
        ?>
    </select>
</div>
             <div class="col-md-3">
    <label for="periodo">Periodo:</label>
    <select class="form-control" id="periodo" name="periodo" required>
        <?php
        // Obtener el mes actual
        $currentMonth = date("n");

        // Determinar el periodo basado en el mes actual
        $periodo = ($currentMonth >= 7) ? 2 : 1;

        // Generar las opciones del select
        for ($i = 1; $i <= 2; $i++) {
            $selected = ($i == $periodo) ? "selected" : ""; // Marcar como seleccionado el periodo actual
            echo "<option value=\"$i\" $selected>$i</option>";
        }
        ?>
    </select>
</div>
             <div class="col-md-3">
                <label for="estado">Estado:</label>
                <select class="form-control" id="estado" name="estado" required>
                    <option value="Activa">Activa</option>
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
        <br>
         <div class="row mb-3">
            <div class="col-md-12">
                <label for="observaciones">Observaciones:</label>
                <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
            </div>
           
        </div>
       <br>
        <div class="row mb-3">
           <div class="col-md-3">
                <label for="id_rector">Rector:</label>
                <select class="form-control" id="id_rector" name="id_rector" required>
                    <?php
                    while ($rector = $rectores_result->fetch_assoc()) {
                        echo "<option value=\"" . $rector['rector_cc'] . "\">" . $rector['rector_nombre'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="id_vice">Vice:</label>
                <select class="form-control" id="id_vice" name="id_vice" required>

                    <?php
                    while ($vicerrector = $vicerrectores_result->fetch_assoc()) {
                        echo "<option value=\"" . $vicerrector['vice_cc'] . "\">" . $vicerrector['vice_nombre'] . "</option>";
                    }
                    ?>  </select>
            </div>
             <div class="col-md-3">
                <label for="tramito">Tramitó:</label>
                <select class="form-control" id="tramito" name="tramito" required>
    <?php
$current_user = $_SESSION['name']; // Obtén el nombre del usuario que ha iniciado sesión

    while ($user = $user_result->fetch_assoc()) {
        $selected = ($user['name'] == $current_user) ? 'selected' : '';
        echo "<option value=\"" . $user['name'] . "\" $selected>" . $user['name'] . "</option>";
    }
    ?>
</select>
            </div>
            <div class="col-md-3">
                <label for="reviso">Revisó:</label>
                <select class="form-control" id="reviso" name="reviso" required>
                    <?php
                    while ($revisau = $revisa_result->fetch_assoc()) {
                        echo "<option value=\"" . $revisau['revisa_nom_propio'] . "\">" . $revisau['revisa_nom_propio'] . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <br>
           <div class="row mb-3">
            <div class="col-md-3">
                <button type="submit" class="btn btn-success btn-block">Enviar</button>
            </div>
                        <div class="col-md-3">

    <button type="button" class="btn btn-danger" onclick="window.location.href='report_terceros.php'">Cerrar</button>
    </div>

        </div>
    </form>
   
</div>
    </body>

    
    
    
    
    
    
<script>
    $(document).ready(function() {
        var paises = [
            // América
            "Argentina", "Bolivia", "Brasil", "Chile", "Colombia", "Ecuador", "Guyana", "Paraguay", "Perú", "Surinam", "Uruguay", "Venezuela", // América del Sur
            "Belice", "Costa Rica", "El Salvador", "Guatemala", "Honduras", "Nicaragua", "Panamá", "Canadá", "Estados Unidos", "México", // América Central y Norteamérica

            // Europa
            "Albania", "Alemania", "Andorra", "Armenia", "Austria", "Azerbaiyán", "Bélgica", "Bielorrusia", "Bosnia y Herzegovina", "Bulgaria", "Croacia", "Chipre", "República Checa", "Dinamarca", "Estonia", "Finlandia", "Francia", "Georgia", "Grecia", "Hungría", "Islandia", "Irlanda", "Italia", "Kosovo", "Letonia", "Liechtenstein", "Lituania", "Luxemburgo", "Malta", "Moldavia", "Mónaco", "Montenegro", "Países Bajos", "Macedonia del Norte", "Noruega", "Polonia", "Portugal", "Rumania", "Rusia", "San Marino", "Serbia", "Eslovaquia", "Eslovenia", "España", "Suecia", "Suiza", "Turquía", "Ucrania", "Reino Unido", "Ciudad del Vaticano",

            // África
            "Argelia", "Angola", "Benín", "Botsuana", "Burkina Faso", "Burundi", "Cabo Verde", "Camerún", "República Centroafricana", "Chad", "Comoras", "República Democrática del Congo", "Yibuti", "Egipto", "Guinea Ecuatorial", "Eritrea", "Eswatini", "Etiopía", "Gabón", "Gambia", "Ghana", "Guinea", "Guinea-Bisáu", "Costa de Marfil", "Kenia", "Lesoto", "Liberia", "Libia", "Madagascar", "Malaui", "Malí", "Mauritania", "Mauricio", "Marruecos", "Mozambique", "Namibia", "Níger", "Nigeria", "República del Congo", "Ruanda", "Santo Tomé y Príncipe", "Senegal", "Seychelles", "Sierra Leona", "Somalia", "Sudáfrica", "Sudán", "Sudán del Sur", "Tanzania", "Togo", "Túnez", "Uganda", "Zambia", "Zimbabue",

            // Asia
            "Afganistán", "Arabia Saudita", "Bangladés", "Baréin", "Birmania", "Brunéi", "Bután", "Camboya", "Catar", "China", "Corea del Norte", "Corea del Sur", "Emiratos Árabes Unidos", "Filipinas", "India", "Indonesia", "Irak", "Irán", "Israel", "Japón", "Jordania", "Kazajistán", "Kirguistán", "Kuwait", "Laos", "Líbano", "Malasia", "Maldivas", "Mongolia", "Nepal", "Omán", "Pakistán", "Palaos", "Papúa Nueva Guinea", "Qatar", "Singapur", "Siria", "Sri Lanka", "Tailandia", "Tayikistán", "Timor Oriental", "Turkmenistán", "Turquía", "Uzbekistán", "Vietnam", "Yemen",

            // Oceanía
            "Australia", "Fiyi", "Islas Marshall", "Islas Salomón", "Kiribati", "Micronesia", "Nauru", "Nueva Zelanda", "Palaos", "Papúa Nueva Guinea", "Samoa", "Tonga", "Tuvalu", "Vanuatu"
        ];

      
        var ciudadesColombia = [
            // Ciudades principales
            "Medellín","Cali","Barranquilla","Cartagena","Cúcuta","Bucaramanga","Pereira","Santa Marta","Ibagué","Villavicencio","Valledupar","Montería","Neiva","Manizales","Armenia","Popayán","Sincelejo","Riohacha","Leticia","Puerto Nariño","Envigado","Itagüí","Rionegro","Sabaneta","Envigado","Bello","Turbo","Apartadó","Caucasia","Yarumal","Puerto Berrío",
"Baranoa","Usiacurí","Tubará","Luruaco",
"Campo de la Cruz","Soledad","Malambo","Sabanagrande","Galapa","Arjona","Turbaná","San Estanislao","Cantagallo",
"Regidor","Sogamoso","Duitama","Tunja","Paipa","Chiquinquirá","Sogamoso","Soatá","Sáchica","Puerto Boyacá","Manizales",
"La Dorada","Chinchiná","Villamaría","Pensilvania","Manzanares","Aguadas","Victoria","Marquetalia","Florencia",
"San Vicente del Caguán","Albania","Morelia","Solano","Puerto Rico","Milán","Valparaíso","Belén de los Andaquíes","Cartagena del Chairá","Yopal","Paz de Ariporo","Tauramena","Aguazul","Monterrey","Támara","Trinidad","Recetor","La Salina","Popayán","Santander de Quilichao","Patía","El Tambo","La Sierra","Timbío","Piendamó","Puracé","Páez","Guapi","Valledupar","Aguachica","Agustín Codazzi","Bosconia","Chiriguaná","Pailitas","González","Pueblo Bello","La Gloria","Astrea","Quibdó","Bahía Solano","Nuquí","Tadó","Condoto","Lloró","Cértegui","Istmina","Riosucio","Medio Baudó","Montería","Cereté","Lorica","Sahagún","Tierralta","Montelíbano","Chinú","Ayapel","San Bernardo del Viento","Planeta Rica","Soacha","Facatativá","Zipaquirá","Chía","Madrid","Funza","Mosquera","Cajicá","La Calera","Inírida","Barranco Minas","Mapiripana","San Felipe","Pana Pana","Cacahual","Puerto Colombia","Puerto Carreño","San José del Guaviare","San José de Guaviare",
"El Retorno","Calamar","Miraflores","Neiva","Pitalito","Garzón",
"La Plata","Campoalegre","Algeciras","Acevedo","Palermo","Tello","Baraya","Riohacha","Maicao","Uribia","Manaure",
"San Juan del Cesar","Fonseca","Villanueva","Barrancas","Dibulla","Hatonuevo","Ciénaga","Fundación","El Banco","Pivijay","Santa Ana","Aracataca","Zona Bananera","El Retén","Chibolo","Guamal","Villavicencio","Acacías","Granada","Puerto López","Puerto Gaitán","Castilla la Nueva","La Macarena","Uribe","Mesetas","Lejanías","Tumaco","Pasto","Ipiales","Túquerres","Pupiales","Quilcacé","Taminango","Leiva","Samaniego","El Charco",
"Cúcuta","Pamplona","Ocaña","Tibú","El Zulia","Villa del Rosario","Los Patios","Chinácota","Bochalema","Puerto Santander","Mocoa","Sibundoy","Puerto Asís","Orito","Villagarzón","Colón","Puerto Guzmán","San Francisco","Valle del Guamuez","La Hormiga","Armenia","Calarcá","Montenegro","La Tebaida","Circasia","Filandia","Salento","Córdoba","Génova","Puerto Quimbaya","Pereira",
"Dosquebradas","La Virginia","Santa Rosa de Cabal","Belen de Umbría","Marsella","Quinchía","Guática","La Celia","Santuario","San Andrés","Providencia","Floridablanca","Girón","Piedecuesta","Barrancabermeja","San Gil","Aratoca","Barichara","Cimitarra","Socorro", "Sincelejo","Corozal","Santiago de Tolú","Sincé","San Marcos","San Onofre","Morroa","Colosó","Guaranda","Toluviejo","Ibagué","Espinal","Girardot","Melgar","Líbano","Honda","Chaparral","Natagaima","Purificación","Cajamarca","Buenaventura","Palmira","Tuluá","Cartago","Yumbo","Buga","Jamundí","La Unión","Dagua","Mitú",
"Carurú","Taraira","Pacoa","Papunahua","Yavaraté","Mitu","Puerto Carreño",
"Cumaribo","Santa Rosalía","La Primavera","Santa Rita","La Guadalupe","Cumaribo",
"Jacksonville","Fort Worth","Washington","El Paso","Memphis","Oklahoma City","Louisville","Baltimore","Albuquerque","Tucson","Fresno","Mesa","Lagos",
"Johannesburgo","Kinsasa","Luanda","Dar es-Salam","Jartum","Nairobi","Alejandría","Abiyán","Acra","Kano","Casablanca","Ciudad del Cabo","Argel",
"Adís Abeba","Dakar","Ibadán","Abuya","Bamako","Kampala","Yaundé","Durban","Duala","Kumasi","Lusaka","Túnez",
"Uagadugú","Conakri","Port Harcourt","Maputo","Brazzaville","Lubumbashi","Lomé",
"Mbuji-Mayi","Harare","Rabat","Mogadiscio","Kaduna","Cotonú","Freetown",
"Ciudad de Benín","Monrovia","Orán","Yamena","Port Elizabeth","Antananarivo","Niamey","Fez","Nuakchot","São Paulo","Ciudad de México","Nueva York","Buenos Aires","Los Ángeles","Río de Janeiro","Chicago","Washington D. C.","San Francisco","Boston","Toronto","Filadelfia","Dallas","Houston","Miami",
"Atlanta","Detroit","Guadalajara","Monterrey","Phoenix","Seattle","Tampa","Montreal",
"Denver","Orlando","San Diego","Minneapolis","Puebla","Cleveland",
"Vancouver","Cincinnati","Charlotte","Salt Lake City",
"Portland","San Luis","Toluca de Lerdo","Las Vegas",
"San Antonio","Sacramento","Pittsburgh","Indianápolis",
"Kansas City","Tijuana","Austin","León","Columbus","Hartford","Raleigh",
"Virginia Beach","Milwaukee","Calgary","Ciudad Juárez","Nashville","Santo Domingo",
"Puerto Príncipe","Ciudad de Guatemala","San José","La Habana","San Juan","San Salvador","Tegucigalpa","Ciudad de Panamá","Managua","San Pedro Sula","Lima","Bogotá","Santiago de Chile","Caracas","Belo Horizonte",
"Porto Alegre","Medellín","Brasilia","Recife","Fortaleza","Salvador",
"Curitiba","Maracaibo","Guayaquil","Campinas","Quito","Cali","Goiânia",
"Asunción","Belém","Manaus","Santa Cruz","La Paz","Barranquilla","Barquisimeto","Montevideo","Vitória",
"Santos","Arequipa","Córdoba","São Luís","Maracay","Cochabamba",
"Natal","Rosario","Trujillo","Bucaramanga","Cartagena","Piura","Mendoza","Teresina","San Miguel de Tucumán","Concepción","Cuzco",
"Teherán","Riad","Bagdad","Amán","Dubái","Ankara","Yida","Kuwait","Kabul","Damasco","Taskent","Isfahán","Mashhad","Esmirna","Saná","Tel Aviv","Bakú","Dammam","Doha","La Meca","Almaty","Bursa","Gaza","Alepo","Tabriz","Shiraz","Beirut","Adana",
"Novosibirskn 1","Gaziantep Ekaterimburgon 1",
"Medina","Manama","Basora","Abu Dabi","Ereván","Mascate",
"Cheliábinskn 1","Mosul","Asjabad","Ahvaz","Tiflis","Antalya","Konya",
"Qom","Krasnoyarskn 1","Nursultán","Omskn 1","Biskek","Erbil",
"Delhi","Bombay","Daca","Karachi","Calcuta","Lahore","Bangalore","Chennai","Ahmedabad","Pune","Surat","Chittagong","Colombo","Jaipur","Lucknow","Faisalabad","Kanpur","Rawalpindi",
"Nagpur","Indore","Katmandú","Bhilai","Gujranwala","Coimbatore","Patna","Bhopal","Chandigarh","Peshawar","Agra","Hyderabad","Vadodara","Visakhapatnam","Multan","Nashik","Vijayawada","Ludhiāna","Benarés","Bhubaneswar","Rajkot","Aurangabad","Madurai","Meerut","Jamshedpur","Asansol","Srinagar",
"Cochín","Jabalpur","Jodhpur","Prayagraj","Cantón","Tokio",
"Shanghái","Seúl","Pekín","Osaka","Tianjin","Nagoya","Xiamen","Chengdu","Taipéi","Wuhan","Hangzhou","Chongqing","Shenyang","Shantou","Hong Kong","Xi'an","Nankín","Qingdao",
"Wenzhou","Harbin","Hefei","Zhengzhou","Changsha","Shijiazhuang","Dalian","Jinan",
"Kunming","Busán","Taiyuan","Ürümqi","Fuzhou","Nanchang","Changchun","Zibo","Nanning","Guiyang","Ningbo","Pionyang","Lanzhou","Tangshan","Xuzhou","Huizhou","Kaohsiung",
"Daegu","Fukuoka","Anshan","Luoyang","Sapporo","Yakarta","Manila","Bangkok","Kuala Lumpur","Ciudad Ho Chi Minh","Singapur","Bandung","Surabaya","Rangún","Medan","Hanói","Cebú","Semarang",
"George Town","Nomen","Yogyakarta","Makasar","Malang","Palembang","Surakarta",
"Davao","Mandalay","Denpasar","Ángeles","Batam","Chonburi","Pekanbaru","Bandar Lampung","Karawang","Serang","Cirebon","Đà Nẵng","Londres","París","Madrid",
"Región del Ruhr","Milán","Colonia - Düsseldorf","Barcelona",
"Berlín","Nápoles","Atenas","Roma","Birmingham","Róterdam","Fráncfort del Meno","Mánchester","Hamburgo","Lisboa","Ámsterdam","Stuttgart","Múnich","Viena",
"Leeds","Estocolmo","Bruselas","Lyon","Liverpool","Valencia","Turín","Marsella",
"Glasgow","Copenhague","Sheffield","Mannheim","Newcastle upon Tyne","Zúrich","Nottingham","Sevilla","Dublín","Lille","Helsinki"
,"Oporto","Núremberg","Oslo","Southampton","Hannover","Amberes",
"Bilbao","Málaga","Niza - Cannes","Toulouse","Moscú","Estambuln 2","San Petersburgo","Kiev","Budapest","Katowice","Varsovia","Bucarest","Minsk","Nizhni Nóvgorod",
"Járkov","Donetsk","Praga","Volgogrado","Belgrado","Dnipropetrovsk","Sofía","Samara",
"Rostov del Don","Kazán","Ufá","Odesa","Perm","Sarátov","Vorónezh",
"Sídney","Melbourne","Brisbane","Perth","Auckland","Adelaida","Honolulun 3","Sao Pablo"]; 

      
        function applyAutocomplete() {
            $('input[name="pais[]"]').autocomplete({
                source: paises
            });
            $('input[name="ciudad[]"]').autocomplete({
                source: ciudadesColombia
            });
        }

        // Apply autocomplete on page load
        applyAutocomplete();

        // Apply autocomplete on dynamically added fields
        $(document).on('click', 'button[onclick="agregarDestino()"]', function() {
            setTimeout(applyAutocomplete, 100); // Delay to ensure the new fields are in the DOM
        });
    });
    
    </script>
<style>
    .eliminar-destino-btn {
        margin-top: 5px; /* Ajusta el espacio entre el botón y los campos de país y ciudad */
        margin-left: 5px; /* Ajusta el espacio entre el botón y el campo de ciudad */
        font-size: 16px; /* Ajusta el tamaño de la fuente */
    }
</style>
<script>
function agregarDestino() {
    var destinoHTML = `
        <div class="row mb-3 destino">
            <div class="col-md-3">
                <label for="pais">País:</label>
                <input type="text" class="form-control" name="pais[]" required>
            </div>
            <div class="col-md-3">
                <label for="ciudad">Ciudad:</label>
                <input type="text" class="form-control" name="ciudad[]" required>
            </div>
                  <div class="col-md-3 d-flex align-items-center">
                    <button type="button" class="add-destination-btn" onclick="eliminarDestino(this)">-</button>
                </div>  
        </div>  
        </div>
    `;
    $('#destinos').append(destinoHTML);
}

function eliminarDestino(button) {
    $(button).closest('.destino').remove();
}
</script>
<script>


document.addEventListener("DOMContentLoaded", function() {
    
    
     function calculateHours() {
        var startDate = new Date(document.getElementById("fecha_inicio").value);
        var endDate = new Date(document.getElementById("fecha_fin").value);
        
        // Calcular la diferencia en días (se incluye el mismo día si las fechas son iguales)
        var timeDifference = endDate - startDate;
        var dayDifference = timeDifference / (1000 * 60 * 60 * 24) + 1;

        // Calcular el número de horas basándose en 8 horas por día
        var hours = dayDifference * 8;

        // Establecer el valor por defecto en el campo de horas
        document.getElementById("duracion_horas").value = hours;
    }

    // Asignar el cálculo al cambio de fecha
    document.getElementById("fecha_inicio").addEventListener("change", calculateHours);
    document.getElementById("fecha_fin").addEventListener("change", calculateHours);

    // Realizar el cálculo inicialmente cuando se carga la página
    calculateHours();
    
        // validar fecha menor  fecha inia
function validateDates() {
    var startDate = document.getElementById("fecha_inicio").value;
    var endDate = document.getElementById("fecha_fin").value;

    // Solo validar si ambas fechas están completas y válidas
    if (startDate && endDate && endDate.length === 10) {
        if (new Date(endDate) < new Date(startDate)) {
            alert("La fecha de fin no puede ser menor que la fecha de inicio.");
            document.getElementById("fecha_fin").value = ""; // Limpiar el campo de fecha fin
        } else {
            calculateHours(); // Si las fechas son válidas, calcular las horas
        }
    }
}

// Asignar la validación al perder el foco del campo fecha_fin
document.getElementById("fecha_fin").addEventListener("blur", validateDates);

// Validación al cambiar fecha_inicio y calcular las horas
document.getElementById("fecha_inicio").addEventListener("change", function() {
    if (document.getElementById("fecha_fin").value.length === 10) {
        validateDates();
    }
});

    
   function handleTipoEstudioChange() {
    var tipoEstudio = document.getElementById("tipo_estudio").value;
    var paisInputs = document.querySelectorAll('input[name="pais[]"]'); // Selecciona todos los inputs de país

    paisInputs.forEach(function(paisInput) {
        if (tipoEstudio === "INT") {
            paisInput.value = "Colombia";
        } else {
            if (paisInput.value === "Colombia") {
                paisInput.value = ""; // Limpia el campo si el valor es "Colombia"
            }
            paisInput.disabled = false;
        }
    });

    handlePaisChange();
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
    document.getElementById("viaticos").addEventListener("change", handleViaticosChange);
    document.getElementById("tiquetes").addEventListener("change", handleViaticosChange);
    document.getElementById("inscripcion").addEventListener("change", handleViaticosChange);
    document.getElementById("pais").addEventListener("change", handlePaisChange);

    handleTipoEstudioChange(); // Inicializar estado de campos
    handleViaticosChange(); // Inicializar estado de campos
});
</script>

<?php
//require('include/footer.php');
?>
