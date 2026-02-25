<?php
session_start();
?>

<!doctype html>
<html lang="en">
	<head>
		<title>Comisiones Académicas Unicauca</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/all.min.css">
		<link rel="stylesheet" href="css/custom.css">
	</head>

	<body>
        <?php
            // Connection info. file
            include 'conn.php';    

            // Connection variables
            $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

            // Check connection
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // data sent from form login.html
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Query sent to database
            $result = mysqli_query($conn, "SELECT Email, Password, Name, DocUsuario FROM users WHERE Email = '$email'");

            // Variable $row hold the result of the query
            $row = mysqli_fetch_assoc($result);

            // Variable $hash hold the password hash on database
            $hash = $row['Password'];

            /* password_Verify() function verify if the password entered by the user
            match the password hash on the database. If everything is OK the session
            is created for one minute. Change 1 on $_SESSION[start] to 5 for a 5 minutes session.
            */
            if (password_verify($_POST['password'], $hash)) {    
                $_SESSION['loggedin'] = true;
                $_SESSION['name'] = $row['Name'];
                $_SESSION['docusuario'] = $row['DocUsuario'];
                $_SESSION['start'] = time();
                // Sesión de 5 horas (5 * 3600 segundos)
                $_SESSION['expire'] = $_SESSION['start'] + (5 * 3600); 
            }
        ?>

        <div class="container-fluid p-0">
            <header class="unicauca-header shadow-sm">
                <h1 class="unicauca-header-title">Comisiones Académicas Unicauca</h1>
                <div id="login" class="text-right">
                    <?php
                        if (isset($_SESSION['loggedin'])) {  
                            echo "<span class='text-white mr-2'><i class='fas fa-user-circle mr-1'></i>" . $_SESSION['name'] . "</span>";
                            echo "<a href='../../comisiones_academicas/logout.php' class='btn btn-outline-light btn-sm unicauca-logout-btn'><i class='fas fa-sign-out-alt mr-1'></i>Cerrar Sesión</a>";
                        } else {
                            echo "<div class='alert alert-danger mb-0' role='alert'>";
                            echo "    <h5 class='alert-heading'>Acceso denegado</h5>";
                            echo "    <p>Necesitas iniciar sesión para acceder a esta página.</p>";
                            echo "    <hr>";
                            echo "    <p class='mb-0'><a href='/comisiones_academicas/index.html' class='alert-link'>¡Haz clic aquí para iniciar sesión!</a></p>";
                            echo "</div>";
                        }
                    ?>
                </div>
            </header>

            <div class="container mt-5 pt-5"> <?php if (isset($_SESSION['loggedin'])): ?>
                    <div class="alert alert-success text-center mb-4" role="alert">
                        <h4>¡Bienvenido, <?php echo $_SESSION['name']; ?>!</h4>
                        <p>Has iniciado sesión exitosamente.</p>
                    </div>

                    <div id="menu" class="d-flex flex-wrap justify-content-center gap-3">
                        <button class="menu-btn unicauca-menu-btn m-2" onclick="window.location.href='comisionesb.php'">
                            <i class="fas fa-users fa-2x mb-2 d-block mx-auto"></i> Comisiones
                        </button>
                        <button class="menu-btn unicauca-menu-btn m-2" onclick="window.location.href='report_terceros.php'">
                            <i class="fas fa-chalkboard-teacher fa-2x mb-2 d-block mx-auto"></i> Reporte por docentes
                        </button>
                        <button class="menu-btn unicauca-menu-btn m-2" onclick="window.location.href='report_pendientes.php'">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block mx-auto"></i> Informes Pendientes
                        </button>
                        <button class="menu-btn unicauca-menu-btn m-2" onclick="window.location.href='directivos.php'">
                            <i class="fas fa-user-tie fa-2x mb-2 d-block mx-auto"></i> Gestionar Encargos
                        </button>
                    </div>
                <?php endif; ?>
            </div></div><script src="js/jquery-3.3.1.slim.min.js"></script>
		<script src="js/popper.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
        <script>
            // Este script de DataTables debe ir después de la carga de jQuery y el propio DataTables
            $(document).ready(function() {
                // Comprueba si la tabla #tabla_terceros existe antes de inicializar DataTables
                if ($('#tabla_terceros').length) {
                    $('#tabla_terceros').DataTable();
                }
            });
        </script>
	</body>
</html>