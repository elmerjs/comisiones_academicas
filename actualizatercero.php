<?php
require 'conn.php';
require('include/headerz.php');

// Obtener ID del tercero
if (!isset($_REQUEST['id'])) {
    echo "No se proporcionó un ID válido.";
    exit();
}
$idter = $_REQUEST['id'];

// Consulta datos del tercero junto con su departamento
$query = "SELECT t.*, d.NOMBRE_DEPTO_CORT 
          FROM tercero t
          LEFT JOIN deparmanentos d ON t.fk_depto = d.PK_DEPTO
          WHERE t.id_tercero = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $idter);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    echo "No se encontraron datos para el ID proporcionado.";
    exit();
}
$fila = $resultado->fetch_assoc();

// Asignar variables
$id_tercero = $fila['id_tercero'];
$documento = $fila['documento_tercero'];
$nombre1 = $fila['nombre1'];
$nombre2 = $fila['nombre2'];
$apellido1 = $fila['apellido1'];
$apellido2 = $fila['apellido2'];
$email = $fila['email'];
$fk_depto = $fila['fk_depto'];
$vincul = $fila['vincul'];
$escalafon = $fila['escalafon'];
$fecha_ingreso = $fila['fecha_ingreso'];
$departamento_nombre = $fila['NOMBRE_DEPTO_CORT'];
$sexo = $fila['sexo'];
$estado = $fila['estado'];
$vinculacion = $fila['vinculacion'];
$cargo_admin = $fila['cargo_admin'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Profesor · Unicauca</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        @media (max-width: 768px) {
            .uc-page-wrapper { margin: 0 10px 1rem; }
        }
    </style>
</head>
<body>
<div id="contenido">
    <div class="uc-page-wrapper">
        <div class="uc-card-header">
            <h5 class="uc-card-title"><i class="fas fa-user-edit"></i> Actualizar Profesor</h5>
            <button type="button" class="btn btn-uc-secondary" onclick="window.location.href='report_terceros.php'"><i class="fas fa-arrow-left"></i> Volver</button>
        </div>
        <div class="p-3">
            <form action="updatetercero.php" method="post">
                <input type="hidden" name="id" value="<?= $id_tercero ?>">

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Documento *</label>
                        <input type="text" class="form-control" name="documento" value="<?= htmlspecialchars($documento) ?>" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Sexo *</label>
                        <select name="sexo" class="form-control" required>
                            <option value="M" <?= $sexo == 'M' ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= $sexo == 'F' ? 'selected' : '' ?>>Femenino</option>
                            <option value="O" <?= $sexo == 'O' ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Primer Nombre</label>
                        <input type="text" class="form-control" name="nombre1" value="<?= htmlspecialchars($nombre1) ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Segundo Nombre</label>
                        <input type="text" class="form-control" name="nombre2" value="<?= htmlspecialchars($nombre2) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Primer Apellido</label>
                        <input type="text" class="form-control" name="apellido1" value="<?= htmlspecialchars($apellido1) ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Segundo Apellido</label>
                        <input type="text" class="form-control" name="apellido2" value="<?= htmlspecialchars($apellido2) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Vinculación *</label>
                        <select name="vincul" class="form-control" required>
                            <option value="PLANTA" <?= $vincul == 'PLANTA' ? 'selected' : '' ?>>PLANTA</option>
                            <option value="OCASIONAL" <?= $vincul == 'OCASIONAL' ? 'selected' : '' ?>>OCASIONAL</option>
                            <option value="HORA CATEDRA" <?= $vincul == 'HORA CATEDRA' ? 'selected' : '' ?>>HORA CATEDRA</option>
                            <option value="DOCENTES ENCARGO ADM." <?= $vincul == 'DOCENTES ENCARGO ADM.' ? 'selected' : '' ?>>DOCENTES ENCARGO ADM.</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Dedicación *</label>
                        <select name="vinculacion" class="form-control" required>
                            <option value="TC" <?= $vinculacion == 'TC' ? 'selected' : '' ?>>Tiempo Completo</option>
                            <option value="MT" <?= $vinculacion == 'MT' ? 'selected' : '' ?>>Medio Tiempo</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Escalafón *</label>
                        <select name="escalafon" class="form-control" required>
                            <option value="TITULAR" <?= $escalafon == 'TITULAR' ? 'selected' : '' ?>>TITULAR</option>
                            <option value="ASOCIADO" <?= $escalafon == 'ASOCIADO' ? 'selected' : '' ?>>ASOCIADO</option>
                            <option value="ASISTENTE" <?= $escalafon == 'ASISTENTE' ? 'selected' : '' ?>>ASISTENTE</option>
                            <option value="AUXILIAR" <?= $escalafon == 'AUXILIAR' ? 'selected' : '' ?>>AUXILIAR</option>
                            <option value="CATEGORIA A" <?= $escalafon == 'CATEGORIA A' ? 'selected' : '' ?>>CATEGORIA A</option>
                            <option value="CATEGORIA B" <?= $escalafon == 'CATEGORIA B' ? 'selected' : '' ?>>CATEGORIA B</option>
                            <option value="CATEGORI C" <?= $escalafon == 'CATEGORI C' ? 'selected' : '' ?>>CATEGORIA C</option>
                            <option value="CATEGORIA D" <?= $escalafon == 'CATEGORIA D' ? 'selected' : '' ?>>CATEGORIA D</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Departamento *</label>
                        <select name="depto" class="form-control" required>
                            <?php
                            $query_deptos = "SELECT d.PK_DEPTO, d.NOMBRE_DEPTO_CORT, f.NOMBREC_FAC
                                            FROM deparmanentos d
                                            INNER JOIN facultad f ON d.FK_FAC = f.PK_FAC
                                            ORDER BY d.NOMBRE_DEPTO_CORT ASC";
                            $deptos = $conn->query($query_deptos);
                            while ($depto = $deptos->fetch_assoc()) {
                                $selected = ($depto['PK_DEPTO'] == $fk_depto) ? 'selected' : '';
                                echo '<option value="' . $depto['PK_DEPTO'] . '" ' . $selected . '>' 
                                     . htmlspecialchars($depto['NOMBRE_DEPTO_CORT']) . ' - ' 
                                     . htmlspecialchars($depto['NOMBREC_FAC']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Cargo administrativo</label>
                        <select name="cargo" class="form-control">
                            <option value="" <?= empty($cargo_admin) ? 'selected' : '' ?>>Ninguno</option>
                            <option value="COORDINADORPS" <?= $cargo_admin == 'COORDINADORPS' ? 'selected' : '' ?>>COORDINADOR Posgrado</option>
                            <option value="COORDINADORPR" <?= $cargo_admin == 'COORDINADORPR' ? 'selected' : '' ?>>COORDINADOR Pregrado</option>
                            <option value="JEFE" <?= $cargo_admin == 'JEFE' ? 'selected' : '' ?>>JEFE DE DEPTO</option>
                            <option value="DIRECTOR" <?= $cargo_admin == 'DIRECTOR' ? 'selected' : '' ?>>DIRECTOR</option>
                            <option value="DECANO" <?= $cargo_admin == 'DECANO' ? 'selected' : '' ?>>DECANO</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>" id="email">
                        <span id="emailError" class="text-danger small"></span>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Estado *</label>
                        <select name="estado" class="form-control" required>
                            <option value="ac" <?= $estado == 'ac' ? 'selected' : '' ?>>ACTIVO</option>
                            <option value="in" <?= $estado == 'in' ? 'selected' : '' ?>>INACTIVO</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Fecha ingreso</label>
                        <input type="date" class="form-control" name="fecha_ingreso" value="<?= $fecha_ingreso ?>">
                    </div>
                </div>

                <div class="form-row mt-3">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-uc-primary btn-block"><i class="fas fa-save"></i> Guardar Cambios</button>
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
document.addEventListener('DOMContentLoaded', function() {
    var emailInput = document.getElementById('email');
    var emailError = document.getElementById('emailError');
    emailInput.addEventListener('blur', function() {
        var email = emailInput.value;
        var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && !pattern.test(email)) {
            emailError.textContent = 'Por favor, introduce un email válido';
        } else {
            emailError.textContent = '';
        }
    });
});
</script>
</body>
</html>