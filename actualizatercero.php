

<?php
	$servername = "localhost";
    $username = "root";
  	$password = "";
  	$dbname = "comisiones_academicas";

	$conn = new mysqli($servername, $username, $password, $dbname);
      if($conn->connect_error){
        die("Conexión fallida: ".$conn->connect_error);
      }

   
    $idter= $_REQUEST["id"];

    $query = "select * from   tercero where id_tercero ='$idter' ORDER By id_tercero";


    if (isset($_REQUEST['id'])) {
    	$q = $conn->real_escape_string($_REQUEST['id']);
    	$query = "SELECT * FROM `tercero`, deparmanentos WHERE 
tercero.fk_depto = deparmanentos.PK_DEPTO AND
id_tercero ='$idter' ORDER By id_tercero";
    }

    $resultado = $conn->query($query);  

    if ($resultado->num_rows>0) {
    	while ($fila = $resultado->fetch_assoc()) {
           
    		            $id_tercero=$fila['id_tercero'];
         
                        $documento = $fila['documento_tercero'];
                        $nombrec= $fila['nombre_completo'];
                        $apellido1 =  $fila['apellido1'];
                        $apellido2 =  $fila['apellido2'];
                        $nombre1 =  $fila['nombre1'];
                        $nombre2 =  $fila['nombre2'];
                        $email=  $fila['email'];

                        $fk_depto=$fila['fk_depto'];
                        $vincul=$fila['vincul'];
              $escalafon=  $fila['escalafon'];
                          $fecha_ingreso=  $fila['fecha_ingreso'];

                        $departamento=$fila['NOMBRE_DEPTO_CORT'];
                        $sexo=$fila['sexo'];
                        $estado=$fila['estado'];
    				    $vinculacion=$fila['vinculacion'];
                        $cargo_admin=$fila['cargo_admin'];
                    
                } 
    }
?>

<!DOCTYPE html>    
<html>
 <head>
     
     
  <link type="text/css" rel="stylesheet" href="css/cssprueba.css">
   <!-- La linea de arriba es para importar estilos CSS a nuestro formulario -->
  <title>tercero</title>
     
     <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

     
     
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <!-- JavaScript de Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="styles.css"> <!-- Aquí se llama al archivo CSS -->
    <link rel="stylesheet" media="screen" href="../css/cssprueban2.css">
    <link rel="stylesheet" media="screen" href="../css/modal3.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>  
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.5.0/js/all.js" integrity="sha384-GqVMZRt5Gn7tB9D9q7ONtcp4gtHIUEW/yG7h98J7IpE3kpi+srfFyyB/04OV6pG0" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />  
    <link rel="stylesheet" type="text/css" href="../css/bootstrap2.min.css"> 


 </head>
 <body>

  <section>
<div id="myModal" class ="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <form id="resolucionForm" method="post" action="updatetercero.php">    
        <div class="row">
         <div class="col-md-12">
             <h4 class="text-center">Actualizar Tercero</h4>
         </div>
     </div>
           <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                     <label for="documento" class="control-label">Documento:</label>
                    <input type="text" id="documento" class="form-control" name="documento" value = "<?php echo $documento; ?>" readonly>
                   
        <input type="hidden" id="id" class="form-control" name="id" value = "<?php echo $id_tercero; ?>" >
                </div>
            </div>
                    <div class="col-md-6">
                <div class="form-group">
                        <label for="sexo" class="control-label">Sexo:</label>

                    <select id="sexo" name="sexo" required class="form-control">
                        <option value="<?php echo $sexo; ?>"><?php if($sexo=="M") {echo "Masculino";} elseif ($sexo == "F") {echo "Femenino";} else {"Otro";} ?></option>
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
            <input type="text" id="nombre1" class="form-control" name="nombre1"  value = "<?php echo $nombre1; ?>">
        </div>
            </div>
               
            <div class="col-md-6">
             <div class="form-group">
            <label for="nombre2" class="control-label">Segundo Nombre:</label>
            <input type="text" id="nombre2" class="form-control" name="nombre2"  value = "<?php echo $nombre2; ?>">
        </div>
            </div>
         </div>
        
                <div class="row">
            <div class="col-md-6">
             <div class="form-group">
            <label for="apellido1" class="control-label">Primer Apellido:</label>
            <input type="text" id="apellido1" class="form-control" name="apellido1"  value = "<?php echo $apellido1; ?>">
        </div>
            </div>
               
            <div class="col-md-6">
             <div class="form-group">
            <label for="apellido2" class="control-label">Segundo Apellido:</label>
            <input type="text" id="apellido2" class="form-control" name="apellido2"  value = "<?php echo $apellido2; ?>">
        </div>
            </div>
         </div>
        
       
        
        
            <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="vincul" class="control-label">Vinculación:</label>
                    <select id="vincul" name="vincul" required class="form-control">
                        <option value="<?php echo $vincul; ?>"><?php echo $vincul; ?></option>
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
                            <option value="<?php echo $vinculacion; ?>"><?php echo ($vinculacion == "TC") ? "Tiempo Completo" : "Medio Tiempo"; ?></option>
                        <option value="TC">Tiempo Completo</option>
                        <option value="MT">Medio Tiempo</option>
                 
                    </select>
                </div> 
    </div>
                
                        <div class="col-md-4"> 
      <div class="form-group">
            <label for="$escalafon" class="control-label">Escalafon:</label>
                    <select id="escalafon" name="escalafon" required class="form-control">
                        <option value="<?php echo $escalafon; ?>"><?php echo $escalafon; ?></option>
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
        $departamento_tercero = $departamento; // Ajusta esto al nombre de la variable que contiene el departamento del tercero

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
                if ($departamento_tercero == $nombre_depto_corto) {
                    // Utilizar PK_DEPTO como valor y NOMBRE_DEPTO_CORT junto con el nombre de la facultad como texto visible para la opción seleccionada
                    echo '<option value="' . $pk_depto . '" selected>' . $nombre_depto_corto . ' - ' . $nombre_facultad . '</option>';
                } else {
                    // Utilizar PK_DEPTO como valor y NOMBRE_DEPTO_CORT junto con el nombre de la facultad como texto visible para las opciones restantes
                    echo '<option value="' . $pk_depto . '">' . $nombre_depto_corto . ' - ' . $nombre_facultad . '</option>';
                }
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

                 <select id="cargo" name="cargo" required class="form-control">
    <?php
    // Lista de opciones y sus valores asociados
    $opciones = array(
        "No aplica" => null,
        "COORDINADORPS" => "COORDINADOR Posg",
        "COORDINADORPR" => "COORDINADOR Preg",
        "JEFE" => "JEFE DE DEPTO",
        "DIRECTOR" => "DIRECTOR",
        "DECANO" => "DECANO"
    );

    // Iterar sobre las opciones para crear las etiquetas <option>
    foreach ($opciones as $valor => $etiqueta) {
        // Determinar si la opción actual es la seleccionada
        $seleccionado = ($valor === $cargo_admin) ? 'selected' : '';
        // Imprimir la etiqueta <option> con el valor y etiqueta correspondientes
        echo "<option value=\"$valor\" $seleccionado>$etiqueta</option>";
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
            <input type="text" id="email" class="form-control" name="email" value="<?php echo $email; ?>">
            <span id="emailError" class="text-danger"></span> <!-- Aquí se mostrará el mensaje de error -->
        </div> 
            </div>
              
              
              <script>
document.addEventListener('DOMContentLoaded', function() {
    var emailInput = document.getElementById('email');
    var emailError = document.getElementById('emailError');
    
    emailInput.addEventListener('blur', function() {
        var email = emailInput.value;
        if (!isValidEmail(email)) {
            emailError.textContent = 'Por favor, introduce un email válido';
        } else {
            emailError.textContent = '';
        }
    });
    
    function isValidEmail(email) {
        // Utilizamos una expresión regular para validar el email
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailPattern.test(email);
    }
});
</script>
               
              <div class="col-md-4">
                <div class="form-group">
                        <label for="estado" class="control-label">Estado:</label>

                  <!--  <select id="estado" name="estado" required class="form-control">
                        <option value="
        <?php /*echo $estado; ?>"><?php if($estado=="ac") {echo "ACTIVO";}  else {"INACTIVO";} */?></option>
                        <option value="ac">ACTIVO</option>
                        <option value="in">INACTIVO</option>
                    </select>
                    -->
                    <label for="estado" class="control-label">Estado:</label>
<select id="estado" name="estado" required class="form-control">
    <option value="ac" <?php if($estado == "ac") echo "selected"; ?>>ACTIVO</option>
    <option value="in" <?php if($estado == "in") echo "selected"; ?>>INACTIVO</option>
</select>
                    
                </div> 
            </div>
              
              <div class="col-md-4">
                <div class="form-group">
                        <label for="fecha_ingreso" class="control-label">Fecha ingreso:</label>

                     <input type="date" id="fecha_ingreso" class="form-control" name="fecha_ingreso"  value = "<?php echo $fecha_ingreso; ?>">
                    
                </div> 
            </div>
              
         </div>
        
     
        
       
      
        <button type="submit">Guardar</button>

      </form>
    </div></div>
    
  </section>

    <!-- JavaScript necesario para cerrar el modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
          // Obtener el parámetro "id" de la URL
          const urlParams = new URLSearchParams(window.location.search);
          const id = urlParams.get('id');
          console.log("ID obtenido:", id); // Salida de depuración

          // Si hay un parámetro "id" en la URL, mostrar el modal
          if (id) {
            const modal = document.getElementById('myModal');
            modal.style.display = 'block';
            console.log("Modal mostrado"); // Salida de depuración

            // Obtener el elemento de la "x" en el modal
            const closeButton = document.querySelector('.close');

            // Agregar un evento de clic a la "x" para cerrar el modal
            closeButton.addEventListener('click', function() {
              cerrarModal();
            });
          }
        });

        function cerrarModal() {
            // Oculta el modal
            const modal = document.getElementById('myModal');
            modal.style.display = 'none';

            // Regresa a la página anterior
            window.location.href = document.referrer;
        }
    </script>


 </body>
</html>
