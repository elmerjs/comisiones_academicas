<?php
include 'conn.php';

// Manejar acciones de CRUD para revisa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_revisa'])) {
        // Eliminar revisa
        $revisa_cc = $_POST['revisa_cc'];
        $conn->query("DELETE FROM revisa WHERE revisa_cc='$revisa_cc'");
    } elseif (isset($_POST['edit_revisa'])) {
        // Obtener datos del revisa a editar
        $revisa_cc = $_POST['revisa_cc'];
        $result = $conn->query("SELECT * FROM revisa WHERE revisa_cc='$revisa_cc'");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Mostrar formulario de edición
            ?>
            <h3>Editar Revisa</h3>
            <form method="post">
                <input type="hidden" name="revisa_cc" value="<?= $row['revisa_cc'] ?>">
               <div class="form-group">
                    <label for="revisa_nom_propio">Nombre Propio</label>
                    <input type="text" class="form-control" id="revisa_nom_propio" name="revisa_nom_propio" value="<?= $row['revisa_nom_propio'] ?>">
                </div>
                <div class="form-group">
                    <label for="revisa_sexo">Sexo</label>
                    <select class="form-control" id="revisa_sexo" name="revisa_sexo">
                        <option value="F" <?= ($row['revisa_sexo'] == 'F' ? 'selected' : '') ?>>F</option>
                        <option value="M" <?= ($row['revisa_sexo'] == 'M' ? 'selected' : '') ?>>M</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="revisa_resol_encargo">Resolución de Encargo</label>
                    <input type="text" class="form-control" id="revisa_resol_encargo" name="revisa_resol_encargo" value="<?= $row['revisa_resol_encargo'] ?>">
                </div>
                
                <div class="form-group">
                    <label for="revisa_tipo">Tipo Revisa</label>
                    <input type="text" class="form-control" id="revisa_tipo" name="revisa_tipo" value="<?= $row['revisa_tipo_revisa'] ?>">
                </div>
                <button type="submit" name="update_revisa" class="btn btn-primary">Actualizar</button>
            </form>
            <?php
        } else {
            // Si no se encuentra el revisa
            echo "<p>No se encontró el revisa con CC: $revisa_cc</p>";
        }
    } elseif (isset($_POST['update_revisa'])) {
        // Actualizar datos del revisa
        $revisa_cc = $_POST['revisa_cc'];
        //$revisa_nombre = $_POST['revisa_nombre'];
      $revisa_nombre = strtoupper($_POST['revisa_nom_propio']);

        $revisa_sexo = $_POST['revisa_sexo'];
        $revisa_resol_encargo = $_POST['revisa_resol_encargo'];
        $revisa_nom_propio = $_POST['revisa_nom_propio'];
        $revisa_tipo = $_POST['revisa_tipo'];
        $conn->query("UPDATE revisa SET revisa_nombre='$revisa_nombre', revisa_sexo='$revisa_sexo', revisa_resol_encargo='$revisa_resol_encargo', revisa_nom_propio='$revisa_nom_propio', revisa_tipo_revisa='$revisa_tipo' WHERE revisa_cc='$revisa_cc'");
    } elseif (isset($_POST['add_revisa'])) {
        // Agregar nuevo revisa
        $revisa_cc = $_POST['revisa_cc'];
      $revisa_nombre = strtoupper($_POST['revisa_nom_propio']);
        $revisa_sexo = $_POST['revisa_sexo'];
        $revisa_resol_encargo = $_POST['revisa_resol_encargo'];
        $revisa_nom_propio = $_POST['revisa_nom_propio'];
        $revisa_tipo = $_POST['revisa_tipo'];
        $conn->query("INSERT INTO revisa (revisa_cc, revisa_nombre, revisa_sexo, revisa_resol_encargo, revisa_nom_propio, revisa_tipo_revisa) VALUES ('$revisa_cc', '$revisa_nombre', '$revisa_sexo', '$revisa_resol_encargo', '$revisa_nom_propio', '$revisa_tipo')");
    }
}

// Consultar y mostrar revisa
$result = $conn->query("SELECT * FROM revisa");
?>

<h2>Gestión de Revisa</h2>
<table class="table">
    <thead>
        <tr>
            <th>CC</th>
            <th>Nombre</th>
            <th>Sexo</th>
            <th>Resolución de Encargo</th>
            <th>Tipo Revisa</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['revisa_cc'] ?></td>
                <td><?= $row['revisa_nom_propio'] ?></td>
                <td><?= $row['revisa_sexo'] ?></td>
                <td><?= $row['revisa_resol_encargo'] ?></td>
                <td><?= $row['revisa_tipo_revisa'] ?></td>
                <td>
                    <!-- Formulario para editar/eliminar -->
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="revisa_cc" value="<?= $row['revisa_cc'] ?>">
<button type="submit" name="edit" class="btn btn-editar btn-sm">Editar</button>
                        <button type="submit" name="delete_revisa" class="btn btn-danger btn-sm">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Formulario para agregar nuevo registro -->
<h3>Agregar Nuevo Revisa</h3>
<form method="post">
    <div class="form-group">
        <label for="revisa_cc">CC</label>
        <input type="text" class="form-control" id="revisa_cc" name="revisa_cc">
    </div>
    <div class="form-group">
        <label for="revisa_nom_propio">Nombre Propio</label>
        <input type="text" class="form-control" id="revisa_nom_propio" name="revisa_nom_propio">
    </div>
    <div class="form-group">
        <label for="revisa_sexo">Sexo</label>
        <select class="form-control" id="revisa_sexo" name="revisa_sexo">
            <option value="F">F</option>
            <option value="M">M</option>
        </select>
    </div>
    <div class="form-group">
        <label for="revisa_resol_encargo">Resolución de Encargo</label>
        <input type="text" class="form-control" id="revisa_resol_encargo" name="revisa_resol_encargo">
    </div>
   
    <div class="form-group">
        <label for="revisa_tipo">Tipo Revisa</label>
        <input type="text" class="form-control" id="revisa_tipo" name="revisa_tipo">
    </div>
<button type="submit" name="add_revisa" class="btn btn-agregar">Agregar</button>
</form>
 <style>
        /* Estilo personalizado para el botón "Editar" */
        .btn-editar {
            background-color: #1a1a4b !important; /* Azul oscuro */
            color: white !important; /* Letra blanca */
        }

        .btn-editar:hover {
            background-color: #000033 !important; /* Un azul más oscuro para el hover */
        }
        /* Estilo personalizado para el botón "Agregar" */
        .btn-agregar {
            background-color: #800020   !important; /* Azul oscuro */
            color: white !important; /* Letra blanca */
        }
 
        .btn-agregar:hover {
            background-color: #66001a s   !important; /* Un azul más oscuro para el hover */
        }
    </style>