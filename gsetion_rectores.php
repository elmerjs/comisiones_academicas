<?php
include 'conn.php';

// Procesar formulario de agregar/modificar/eliminar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_rector'])) {
        // Eliminar rector
        $rector_cc = $_POST['rector_cc'];
        $conn->query("DELETE FROM rector WHERE rector_cc='$rector_cc'");
    } elseif (isset($_POST['edit_rector'])) {
        // Obtener datos del rector a editar
        $rector_cc = $_POST['rector_cc'];
        $result = $conn->query("SELECT * FROM rector WHERE rector_cc='$rector_cc'");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Mostrar formulario de edición
            ?>
            <h3>Editar Rector</h3>
            <form method="post">
                <input type="hidden" name="rector_cc" value="<?= $row['rector_cc'] ?>">
                <div class="form-group">
                    <label for="rector_nombre">Nombre</label>
                    <input type="text" class="form-control" id="rector_nombre" name="rector_nombre" value="<?= $row['rector_nom_propio'] ?>">
                </div>
                <div class="form-group">
                    <label for="rector_sexo">Sexo</label>
                    <select class="form-control" id="rector_sexo" name="rector_sexo">
                        <option value="F" <?= ($row['rector_sexo'] == 'F' ? 'selected' : '') ?>>F</option>
                        <option value="M" <?= ($row['rector_sexo'] == 'M' ? 'selected' : '') ?>>M</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rector_resol_encargo">Resol Encargo</label>
                    <input type="text" class="form-control" id="rector_resol_encargo" name="rector_resol_encargo" value="<?= $row['rector_resol_encargo'] ?>">
                </div>
                <div class="form-group">
                    <label for="tipo_rector">Tipo Rector</label>
                    <input type="text" class="form-control" id="tipo_rector" name="tipo_rector" value="<?= $row['tipo_rector'] ?>">
                </div>
                <button type="submit" name="update_rector" class="btn btn-primary">Actualizar</button>
            </form>
            <?php
        } else {
            // Si no se encuentra el rector
            echo "<p>No se encontró el rector con CC: $rector_cc</p>";
        }
    } elseif (isset($_POST['update_rector'])) {
        // Actualizar datos del rector
        $rector_cc = $_POST['rector_cc'];
        $rector_nombre = $_POST['rector_nombre'];
        $rector_nombre_completo = strtoupper($rector_nombre);
        $rector_sexo = $_POST['rector_sexo'];
        $rector_resol_encargo = $_POST['rector_resol_encargo'];
        $tipo_rector = $_POST['tipo_rector'];
        $conn->query("UPDATE rector SET rector_nombre='$rector_nombre_completo', rector_nom_propio='$rector_nombre', rector_sexo='$rector_sexo', rector_resol_encargo='$rector_resol_encargo', tipo_rector='$tipo_rector' WHERE rector_cc='$rector_cc'");
    } elseif (isset($_POST['add_rector'])) {
        // Agregar nuevo rector
        $rector_cc = $_POST['rector_cc'];
        $rector_nombre = $_POST['rector_nombre'];
        $rector_nombre_completo = strtoupper($rector_nombre);
        $rector_sexo = $_POST['rector_sexo'];
        $rector_resol_encargo = $_POST['rector_resol_encargo'];
        $tipo_rector = $_POST['tipo_rector'];
        $conn->query("INSERT INTO rector (rector_cc, rector_nombre, rector_sexo, rector_resol_encargo, rector_nom_propio, tipo_rector) VALUES ('$rector_cc', '$rector_nombre_completo', '$rector_sexo', '$rector_resol_encargo', '$rector_nombre', '$tipo_rector')");
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
            <th>Nombre</th>
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
                        <button type="submit" name="edit_rector" class="btn btn-warning btn-sm">Editar</button>
                        <button type="submit" name="delete_rector" class="btn btn-danger btn-sm">Eliminar</button>
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
        <label for="rector_nombre">Nombre</label>
        <input type="text" class="form-control" id="rector_nombre" name="rector_nombre">
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
    <button type="submit" name="add_rector" class="btn btn-primary">Agregar</button>
</form>
