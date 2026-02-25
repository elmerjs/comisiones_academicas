<?php
include 'conn.php';

// Manejar acciones de CRUD para vicerrectores
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_vice'])) {
        // Eliminar vicerrector
        $vice_cc = $_POST['vice_cc'];
        $conn->query("DELETE FROM vicerrector WHERE vice_cc='$vice_cc'");
    } elseif (isset($_POST['edit_vice'])) {
        // Obtener datos del vicerrector a editar
        $vice_cc = $_POST['vice_cc'];
        $result = $conn->query("SELECT * FROM vicerrector WHERE vice_cc='$vice_cc'");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Mostrar formulario de edición
            ?>
            <h3>Editar Vicerrector</h3>
            <form method="post">
                <input type="hidden" name="vice_cc" value="<?= $row['vice_cc'] ?>">
                <div class="form-group">
                    <label for="vice_nom_propio">Nombre Propio Vicerrector</label>
                    <input type="text" class="form-control" id="vice_nom_propio" name="vice_nom_propio" value="<?= $row['vice_nom_propio'] ?>">
                </div>
                <div class="form-group">
                    <label for="vice_sexo">Sexo</label>
                    <select class="form-control" id="vice_sexo" name="vice_sexo">
                        <option value="F" <?= ($row['vice_sexo'] == 'F' ? 'selected' : '') ?>>F</option>
                        <option value="M" <?= ($row['vice_sexo'] == 'M' ? 'selected' : '') ?>>M</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="vice_resol_encargo">Resol Vicerrector Encargo</label>
                    <input type="text" class="form-control" id="vice_resol_encargo" name="vice_resol_encargo" value="<?= $row['vice_resol_encargo'] ?>">
                </div>
                <div class="form-group">
                    <label for="tipo_vice">Tipo Vicerrector</label>
                    <input type="text" class="form-control" id="tipo_vice" name="tipo_vice" value="<?= $row['tipo_vice'] ?>">
                </div>
                <button type="submit" name="update_vice" class="btn btn-primary">Actualizar</button>
            </form>
            <?php
        } else {
            // Si no se encuentra el vicerrector
            echo "<p>No se encontró el vicerrector con CC: $vice_cc</p>";
        }
    } elseif (isset($_POST['update_vice'])) {
        // Actualizar datos del vicerrector
        $vice_cc = $_POST['vice_cc'];
        $vice_nom_propio = $_POST['vice_nom_propio'];
        $vice_nombre = strtoupper($vice_nom_propio);
        $vice_sexo = $_POST['vice_sexo'];
        $vice_resol_encargo = $_POST['vice_resol_encargo'];
        $tipo_vice = $_POST['tipo_vice'];
        $conn->query("UPDATE vicerrector SET vice_nombre='$vice_nombre', vice_nom_propio='$vice_nom_propio', vice_sexo='$vice_sexo', vice_resol_encargo='$vice_resol_encargo', tipo_vice='$tipo_vice' WHERE vice_cc='$vice_cc'");
    } elseif (isset($_POST['add_vice'])) {
        // Agregar nuevo vicerrector
        $vice_cc = $_POST['vice_cc'];
        $vice_nom_propio = $_POST['vice_nom_propio'];
        $vice_nombre = strtoupper($vice_nom_propio);
        $vice_sexo = $_POST['vice_sexo'];
        $vice_resol_encargo = $_POST['vice_resol_encargo'];
        $tipo_vice = $_POST['tipo_vice'];
        $conn->query("INSERT INTO vicerrector (vice_cc, vice_nombre, vice_sexo, vice_resol_encargo, vice_nom_propio, tipo_vice) VALUES ('$vice_cc', '$vice_nombre', '$vice_sexo', '$vice_resol_encargo', '$vice_nom_propio', '$tipo_vice')");
    }
}

// Consultar y mostrar vicerrectores
$result = $conn->query("SELECT * FROM vicerrector");
?>

<h2>Gestión de Vicerrectores</h2>
<table class="table">
    <thead>
        <tr>
            <th>CC</th>
            <th>Nombre Propio Vicerrector</th>
            <th>Sexo</th>
            <th>Resol Vicerrector Encargo</th>
            <th>Tipo Vicerrector</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['vice_cc'] ?></td>
                <td><?= $row['vice_nom_propio'] ?></td>
                <td><?= $row['vice_sexo'] ?></td>
                <td><?= $row['vice_resol_encargo'] ?></td>
                <td><?= $row['tipo_vice'] ?></td>
                <td>
                    <!-- Formulario para editar/eliminar -->
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="vice_cc" value="<?= $row['vice_cc'] ?>">
<button type="submit" name="edit_vice" class="btn btn-editar btn-sm">Editar</button>
                        <button type="submit" name="delete_vice" class="btn btn-danger btn-sm">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Formulario para agregar nuevo registro -->
<h3>Agregar Nuevo Vicerrector</h3>
<form method="post">
    <div class="form-group">
        <label for="vice_cc">CC</label>
        <input type="text" class="form-control" id="vice_cc" name="vice_cc">
    </div>
    <div class="form-group">
        <label for="vice_nom_propio">Nombre Propio Vicerrector</label>
        <input type="text" class="form-control" id="vice_nom_propio" name="vice_nom_propio">
    </div>
    <div class="form-group">
        <label for="vice_sexo">Sexo</label>
        <select class="form-control" id="vice_sexo" name="vice_sexo">
            <option value="F">F</option>
            <option value="M">M</option>
        </select>
    </div>
    <div class="form-group">
        <label for="vice_resol_encargo">Resol Vicerrector Encargo</label>
        <input type="text" class="form-control" id="vice_resol_encargo" name="vice_resol_encargo">
    </div>
    <div class="form-group">
        <label for="tipo_vice">Tipo Vicerrector</label>
        <input type="text" class="form-control" id="tipo_vice" name="tipo_vice">
    </div>
<button type="submit" name="add_vice" class="btn btn-agregar">Agregar</button>
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
