<?php
include 'conn.php';

// Manejar acciones de CRUD para rectores
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $rector_cc = $_POST['rector_cc'];
        $conn->query("DELETE FROM rector WHERE rector_cc='$rector_cc'");
    } elseif (isset($_POST['edit'])) {
        // Manejar la edición
        // Obtener los datos del rector a editar
        $rector_cc = $_POST['rector_cc'];
        $result = $conn->query("SELECT * FROM rector WHERE rector_cc='$rector_cc'");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Mostrar el formulario de edición
            echo '<h3>Editar Rector</h3>';
            echo '<form method="post">';
            echo '<input type="hidden" name="rector_cc" value="' . $row['rector_cc'] . '">';
            echo '<div class="form-group">';
            echo '<label for="rector_nom_propio">Nombre Propio</label>';
            echo '<input type="text" class="form-control" id="rector_nom_propio" name="rector_nom_propio" value="' . $row['rector_nom_propio'] . '">';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label for="rector_sexo">Sexo</label>';
            echo '<select class="form-control" id="rector_sexo" name="rector_sexo">';
            echo '<option value="F" ' . ($row['rector_sexo'] == 'F' ? 'selected' : '') . '>F</option>';
            echo '<option value="M" ' . ($row['rector_sexo'] == 'M' ? 'selected' : '') . '>M</option>';
            echo '</select>';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label for="rector_resol_encargo">Resol Encargo</label>';
            echo '<input type="text" class="form-control" id="rector_resol_encargo" name="rector_resol_encargo" value="' . $row['rector_resol_encargo'] . '">';
            echo '</div>';
            
            echo '<div class="form-group">';
            echo '<label for="tipo_rector">Tipo Rector</label>';
            echo '<input type="text" class="form-control" id="tipo_rector" name="tipo_rector" value="' . $row['tipo_rector'] . '">';
            echo '</div>';
            echo '<button type="submit" name="update" class="btn btn-primary">Actualizar</button>';
            echo '</form>';
        } else {
            // Si no se encuentra el rector
            echo "<p>No se encontró el rector con CC: $rector_cc</p>";
        }
    } elseif (isset($_POST['update'])) {
        // Manejar la actualización
        $rector_cc = $_POST['rector_cc'];
        $rector_nombre = strtoupper($_POST['rector_nom_propio']);
        $rector_sexo = $_POST['rector_sexo'];
        $rector_resol_encargo = $_POST['rector_resol_encargo'];
        $rector_nom_propio = $_POST['rector_nom_propio'];
        $tipo_rector = $_POST['tipo_rector'];
        $conn->query("UPDATE rector SET rector_nombre='$rector_nombre', rector_sexo='$rector_sexo', rector_resol_encargo='$rector_resol_encargo', rector_nom_propio='$rector_nom_propio', tipo_rector='$tipo_rector' WHERE rector_cc='$rector_cc'");
    } elseif (isset($_POST['add'])) {
        // Agregar nuevos rectores
        $rector_cc = $_POST['rector_cc'];
        $rector_nombre = strtoupper($_POST['rector_nom_propio']);
        $rector_sexo = $_POST['rector_sexo'];
        $rector_resol_encargo = $_POST['rector_resol_encargo'];
        $rector_nom_propio = $_POST['rector_nom_propio'];
        $tipo_rector = $_POST['tipo_rector'];
        $conn->query("INSERT INTO rector (rector_cc, rector_nombre, rector_sexo, rector_resol_encargo, rector_nom_propio, tipo_rector) VALUES ('$rector_cc', '$rector_nombre', '$rector_sexo', '$rector_resol_encargo', '$rector_nom_propio', '$tipo_rector')");
    }
}

// Consultar y mostrar rectores
$result = $conn->query("SELECT * FROM rector");
?>

<h2>Gestión de Rectores</h2>
<table class="table">
    <thead>
        <tr>
            <th>CC</th>
            <th>Nombre Propio</th>
            <th>Sexo</th>
            <th>Resol Encargo</th>
            <th>Tipo Rector</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['rector_cc'] ?></td>
                <td><?= $row['rector_nom_propio'] ?></td>
                <td><?= $row['rector_sexo'] ?></td>
                <td><?= $row['rector_resol_encargo'] ?></td>
                <td><?= $row['tipo_rector'] ?></td>
                <td>
                    <!-- Formulario para editar/eliminar -->
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="rector_cc" value="<?= $row['rector_cc'] ?>">
<button type="submit" name="edit" class="btn btn-editar btn-sm">Editar</button>
                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Formulario para agregar nuevo registro -->
<h3>Agregar Nuevo Rector</h3>
<form method="post">
    <div class="form-group">
        <label for="rector_cc">CC</label>
        <input type="text" class="form-control" id="rector_cc" name="rector_cc">
    </div>
   <div class="form-group">
        <label for="rector_nom_propio">Nombre Propio</label>
        <input type="text" class="form-control" id="rector_nom_propio" name="rector_nom_propio">
    </div>
    <div class="form-group">
        <label for="rector_sexo">Sexo</label>
        <select class="form-control" id="rector_sexo" name="rector_sexo">
            <option value="F">F</option>
            <option value="M">M</option>
        </select>
    </div>
    <div class="form-group">
        <label for="rector_resol_encargo">Resol Encargo</label>
        <input type="text" class="form-control" id="rector_resol_encargo" name="rector_resol_encargo">
    </div>
   
    <div class="form-group">
        <label for="tipo_rector">Tipo Rector</label>
        <input type="text" class="form-control" id="tipo_rector" name="tipo_rector">
    </div>
<!--<button type="submit" name="add_revisa" class="btn btn-agregar">Agregar</button>-->
<button type="submit" name="add" class="btn btn-agregar">Agregar</button>

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