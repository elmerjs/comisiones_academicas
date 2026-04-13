<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_vice'])) {
        $vice_cc = $_POST['vice_cc'];
        $stmt = $conn->prepare("DELETE FROM vicerrector WHERE vice_cc = ?");
        $stmt->bind_param("s", $vice_cc);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['add_vice'])) {
        $vice_cc = $_POST['vice_cc'];
        $vice_nom_propio = strtoupper(trim($_POST['vice_nom_propio']));
        $vice_sexo = $_POST['vice_sexo'];
        $vice_resol_encargo = $_POST['vice_resol_encargo'];
        $tipo_vice = $_POST['tipo_vice'];
        $stmt = $conn->prepare("INSERT INTO vicerrector (vice_cc, vice_nombre, vice_nom_propio, vice_sexo, vice_resol_encargo, tipo_vice) VALUES (?, ?, ?, ?, ?, ?)");
        $vice_nombre = $vice_nom_propio;
        $stmt->bind_param("ssssss", $vice_cc, $vice_nombre, $vice_nom_propio, $vice_sexo, $vice_resol_encargo, $tipo_vice);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update_vice'])) {
        $vice_cc = $_POST['vice_cc'];
        $vice_nom_propio = strtoupper(trim($_POST['vice_nom_propio']));
        $vice_sexo = $_POST['vice_sexo'];
        $vice_resol_encargo = $_POST['vice_resol_encargo'];
        $tipo_vice = $_POST['tipo_vice'];
        $stmt = $conn->prepare("UPDATE vicerrector SET vice_nombre = ?, vice_nom_propio = ?, vice_sexo = ?, vice_resol_encargo = ?, tipo_vice = ? WHERE vice_cc = ?");
        $vice_nombre = $vice_nom_propio;
        $stmt->bind_param("ssssss", $vice_nombre, $vice_nom_propio, $vice_sexo, $vice_resol_encargo, $tipo_vice, $vice_cc);
        $stmt->execute();
        $stmt->close();
    }
}

$result = $conn->query("SELECT * FROM vicerrector ORDER BY vice_nom_propio ASC");
?>

<style>
    .table-vicerrectores th {
        background-color: var(--azul-oscuro, #002A9E);
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 12px 10px;
        border: none;
    }
    .table-vicerrectores td {
        padding: 10px 8px;
        vertical-align: middle;
        border-bottom: 1px solid var(--gris-border, #E9EEF3);
    }
    .btn-edit-vice {
        background: #e9ecef;
        color: #1e293b;
        border: none;
        border-radius: 30px;
        padding: 4px 12px;
        font-size: 0.7rem;
        font-weight: 600;
        transition: 0.2s;
    }
    .btn-edit-vice:hover {
        background: var(--azul-cielo, #16A8E1);
        color: white;
    }
    .btn-delete-vice {
        background: #fee2e2;
        color: var(--rojo, #E52724);
        border: none;
        border-radius: 30px;
        padding: 4px 12px;
        font-size: 0.7rem;
        font-weight: 600;
        transition: 0.2s;
    }
    .btn-delete-vice:hover {
        background: var(--rojo, #E52724);
        color: white;
    }
    .card-form-vice {
        background: #F8FAFE;
        border-radius: 20px;
        padding: 1.2rem;
        margin-top: 1.5rem;
        border: 1px solid var(--gris-border);
    }
</style>

<div class="table-responsive">
    <table class="table table-hover table-vicerrectores">
        <thead>
            <tr>
                <th>CC</th>
                <th>Nombre Propio</th>
                <th>Sexo</th>
                <th>Resolución Encargo</th>
                <th>Tipo Vicerrector</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['vice_cc']) ?></td>
                        <td><?= htmlspecialchars($row['vice_nom_propio']) ?></td>
                        <td><?= htmlspecialchars($row['vice_sexo']) ?></td>
                        <td><?= htmlspecialchars($row['vice_resol_encargo']) ?></td>
                        <td><?= htmlspecialchars($row['tipo_vice']) ?></td>
                        <td>
                            <button type="button" class="btn-edit-vice"
                                    data-json='<?= json_encode([
                                        'cc' => $row['vice_cc'],
                                        'nombre' => $row['vice_nom_propio'],
                                        'sexo' => $row['vice_sexo'],
                                        'resol' => $row['vice_resol_encargo'],
                                        'tipo' => $row['tipo_vice']
                                    ]) ?>'>
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <form method="post" style="display: inline-block;" onsubmit="return confirm('¿Eliminar este vicerrector?')">
                                <input type="hidden" name="vice_cc" value="<?= $row['vice_cc'] ?>">
                                <button type="submit" name="delete_vice" class="btn-delete-vice"><i class="fas fa-trash-alt"></i> Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No hay vicerrectores registrados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal de edición -->
<div class="modal fade" id="editModalVice" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 24px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #002A9E, #4C19AF); color: white; border-radius: 24px 24px 0 0;">
                <h5 class="modal-title"><i class="fas fa-user-edit"></i> Editar Vicerrector</h5>
                <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="vice_cc" id="edit_vice_cc">
                    <div class="form-group">
                        <label>Nombre Propio</label>
                        <input type="text" class="form-control" name="vice_nom_propio" id="edit_vice_nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Sexo</label>
                        <select class="form-control" name="vice_sexo" id="edit_vice_sexo">
                            <option value="F">Femenino</option>
                            <option value="M">Masculino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Resolución Encargo</label>
                        <input type="text" class="form-control" name="vice_resol_encargo" id="edit_vice_resol">
                    </div>
                    <div class="form-group">
                        <label>Tipo Vicerrector</label>
                        <input type="text" class="form-control" name="tipo_vice" id="edit_vice_tipo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="update_vice" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card-form-vice">
    <h6 class="mb-3" style="color: var(--azul-oscuro); font-weight: 700;"><i class="fas fa-plus-circle"></i> Agregar Nuevo Vicerrector</h6>
    <form method="post">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label>CC</label>
                <input type="text" class="form-control" name="vice_cc" required>
            </div>
            <div class="form-group col-md-4">
                <label>Nombre Propio</label>
                <input type="text" class="form-control" name="vice_nom_propio" required>
            </div>
            <div class="form-group col-md-2">
                <label>Sexo</label>
                <select class="form-control" name="vice_sexo">
                    <option value="F">Femenino</option>
                    <option value="M">Masculino</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label>Resolución Encargo</label>
                <input type="text" class="form-control" name="vice_resol_encargo">
            </div>
            <div class="form-group col-md-3">
                <label>Tipo Vicerrector</label>
                <input type="text" class="form-control" name="tipo_vice">
            </div>
            <div class="form-group col-md-2 d-flex align-items-end">
                <button type="submit" name="add_vice" class="btn btn-primary" style="background: var(--verde); border-radius: 30px;"><i class="fas fa-save"></i> Agregar</button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-edit-vice');
        if (!btn) return;

        var data = btn.getAttribute('data-json');
        if (!data) {
            alert('Error al cargar los datos.');
            return;
        }

        try {
            var vice = JSON.parse(data);
            document.getElementById('edit_vice_cc').value = vice.cc;
            document.getElementById('edit_vice_nombre').value = vice.nombre;
            document.getElementById('edit_vice_sexo').value = vice.sexo;
            document.getElementById('edit_vice_resol').value = vice.resol;
            document.getElementById('edit_vice_tipo').value = vice.tipo;

            if (typeof $ !== 'undefined' && $.fn.modal) {
                $('#editModalVice').modal('show');
            } else if (typeof bootstrap !== 'undefined') {
                var modal = new bootstrap.Modal(document.getElementById('editModalVice'));
                modal.show();
            } else {
                alert('No se pudo abrir el modal.');
            }
        } catch (err) {
            console.error(err);
            alert('Error en los datos.');
        }
    });
});
</script>