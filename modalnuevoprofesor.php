<style>

</style>
<!-- Ventana modal -->
<div id="myModalb" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
   
    <form id="resolucionForm" method="post" action="inserttercero.php">
  <div class="row">
         <div class="col-md-12">
             <h4 class="text-center">Crear Tercero</h4>
         </div>
     </div>
     
     <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="documento" class="control-label">Documento:</label>
                <input type="text" id="documento" class="form-control" name="documento">
            </div>
        </div>
         <div class="col-md-6">
                <div class="form-group">
                        <label for="sexo" class="control-label">Sexo:</label>

                    <select id="sexo" name="sexo" required class="form-control">
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                        <option value="O">Otro</option>
                       
                    </select>
                </div> 
            </div>
               
     </div>
        
         <div class="row">
            <div class="col-md-6">
             <div class="form-group">
            <label for="nombre1" class="control-label">Primer Nombre:</label>
            <input type="text" id="nombre1" class="form-control" name="nombre1"  >
        </div>
            </div>
               
            <div class="col-md-6">
             <div class="form-group">
            <label for="nombre2" class="control-label">Segundo Nombre:</label>
            <input type="text" id="nombre2" class="form-control" name="nombre2"  >
        </div>
            </div>
         </div>
        
                <div class="row">
            <div class="col-md-6">
             <div class="form-group">
            <label for="apellido1" class="control-label">Primer Apellido:</label>
            <input type="text" id="apellido1" class="form-control" name="apellido1" >
        </div>
            </div>
               
            <div class="col-md-6">
             <div class="form-group">
            <label for="apellido2" class="control-label">Segundo Apellido:</label>
            <input type="text" id="apellido2" class="form-control" name="apellido2"  >
        </div>
            </div>
         </div>
        
       
        
        
            <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="vincul" class="control-label">Vinculación:</label>
                    <select id="vincul" name="vincul" required class="form-control">
                                               <option value="PLANTA"></option>

                        <option value="PLANTA">PLANTA</option>
                        <option value="OCASIONAL">OCASIONAL</option>
                        <option value="HORA CATEDRA">HORA CATEDRA</option>
                         <option value="DOCENTES ENCARGO ADM.">DOCENTES ENCARGO ADM.</option>
                    </select>
                </div> 
            </div>
               
              <div class="col-md-4"> 
      <div class="form-group">
            <label for="vinculacion" class="control-label">Dedicación:</label>
                    <select id="vinculacion" name="vinculacion" required class="form-control">
                       
                        <option value="TC">Tiempo Completo</option>
                        <option value="MT">Medio Tiempo</option>
                 
                    </select>
                </div> 
    </div>
                
                
           <div class="col-md-4"> 
      <div class="form-group">
            <label for="escalafon" class="control-label">Escalafon:</label>
                    <select id="escalafon" name="escalafon" required class="form-control">
                            
                        <option value="TITULAR">TITULAR</option>
                        <option value="ASOCIADO">ASOCIADO</option>
                        <option value="ASISTENTE">ASISTENTE</option>
                        <option value="AUXILIAR">AUXILIAR</option>
                        <option value="CATEGORIA A">CATEGORIA A</option>
                        <option value="CATEGORIA B">CATEGORIA B</option>
                        <option value="CATEGORI C">CATEGORIA C</option>
                        <option value="CATEGORIA D">CATEGORIA D</option>
                        
                 
                    </select>
                </div> 
    </div>
                
         </div>
        
        
            <div class="row">
            <div class="col-md-6">
               
                
                
         
                
         <div class="form-group">
    <label for="depto" class="control-label">Departamento:</label>
    <select id="depto" name="depto" required class="form-control">
        <?php
        // Obtener el departamento del tercero seleccionado
       // $departamento_tercero = $departamento; // Ajusta esto al nombre de la variable que contiene el departamento del tercero

        // Realizar consulta SQL para obtener los departamentos con su facultad asociada
        $query_departamentos = "SELECT d.PK_DEPTO, d.NOMBRE_DEPTO_CORT, f.NOMBREC_FAC
                                FROM deparmanentos d
                                INNER JOIN facultad f ON d.FK_FAC = f.PK_FAC order by  d.NOMBRE_DEPTO_CORT asc";
        $resultado_departamentos = $conn->query($query_departamentos);

        // Verificar si hay resultados y generar opciones del select
        if ($resultado_departamentos->num_rows > 0) {
            while ($fila_departamento = $resultado_departamentos->fetch_assoc()) {
                $pk_depto = $fila_departamento['PK_DEPTO'];
                $nombre_depto_corto = $fila_departamento['NOMBRE_DEPTO_CORT'];
                $nombre_facultad = $fila_departamento['NOMBREC_FAC'];

                // Verificar si el departamento actual coincide con el departamento del tercero
               
                    // Utilizar PK_DEPTO como valor y NOMBRE_DEPTO_CORT junto con el nombre de la facultad como texto visible para la opción seleccionada
                    echo '<option value="' . $pk_depto . '" selected>' . $nombre_depto_corto . ' - ' . $nombre_facultad . '</option>';
               
            }
        } else {
            // Si no hay resultados, mostrar un mensaje indicando que no hay departamentos disponibles
            echo '<option value="">No hay departamentos disponibles</option>';
        }
        ?>
    </select>
</div>

                
                
                
                
            </div>
               
        <div class="col-md-6">
    <div class="form-group">
        <label for="cargo" class="control-label">Cargo administrativo:</label>
        <select id="cargo" name="cargo" class="form-control">
            <option value="">Seleccionar...</option> <!-- Opción por defecto con valor nulo -->
            <?php
            // Lista de opciones y sus valores asociados
            $opciones = array(
                "COORDINADORPS" => "COORDINADOR Posg",
                "COORDINADORPR" => "COORDINADOR Preg",
                "JEFE" => "JEFE DE DEPTO",
                "DIRECTOR" => "DIRECTOR",
                "DECANO" => "DECANO"
            );

            // Iterar sobre las opciones para crear las etiquetas <option>
            foreach ($opciones as $valor => $etiqueta) {
                // Imprimir la etiqueta <option> con el valor y etiqueta correspondientes
                echo "<option value=\"$valor\">$etiqueta</option>";
            }
            ?>
        </select>
    </div>
</div>
         </div> 
        
        
          <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                        <label for="email" class="control-label">Email:</label>
                        <input type="email" id="email" class="form-control" name="email">
                </div> 
            </div>
               
              <div class="col-md-4">
                <div class="form-group">
                        <label for="estado" class="control-label">Estado:</label>

                    <select id="estado" name="estado" required class="form-control">
                        <option value="ac">ACTIVO</option>
                        <option value="in">INACTIVO</option>
                    </select>
                </div> 
            </div>
               <div class="col-md-4">
                <div class="form-group">
                        <label for="fecha_ingreso" class="control-label">fecha ingreso:</label>
                <input type="date" id="fecha_ingreso" class="form-control" name="fecha_ingreso" required>

                </div> 
            </div>
         </div>
        
     
            
      <button type="submit">Guardar</button>
    </form>
  </div>
</div>




<script src="script.js"></script>

                 
        <script>
$(document).ready(function() {
    $('#profesor').change(function() {
        var profesorId = $(this).val(); // Obtén el valor seleccionado del campo profesor
        // Realiza una petición AJAX para obtener la información del profesor y su departamento
        $.ajax({
            url: 'obtener_departamento.php', // El archivo PHP que maneja la solicitud AJAX
            method: 'GET',
            data: { profesor_id: profesorId }, // Envía el ID del profesor como parámetro
            dataType: 'json',
            success: function(response) {
                // Llena el campo de departamento con el valor obtenido
                $('#departamento').val(response.departamento);
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener el departamento:', error);
            }
        });
    });
});
            
            
       
            
            
</script>  
                 
                  <script>
$(document).ready(function() {
    $('#profesor').change(function() {
        var profesorId = $(this).val(); // Obtén el valor seleccionado del campo profesor
        // Realiza una petición AJAX para obtener la información del profesor y su fac
        $.ajax({
            url: 'obtener_facultad.php', // El archivo PHP que maneja la solicitud AJAX
            method: 'GET',
            data: { profesor_id: profesorId }, // Envía el ID del profesor como parámetro
            dataType: 'json',
            success: function(response) {
                // Llena el campo de departamento con el valor obtenido
                $('#facultad').val(response.facultad);
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener la fac:', error);
            }
        });
    });
});
            
            
       
            
            
</script>        


