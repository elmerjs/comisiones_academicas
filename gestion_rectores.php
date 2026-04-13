<?php
include 'conn.php';

// Manejo de acciones (usando prepared statements)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Eliminar
    if (isset($_POST['delete_rector'])) {
        $rector_cc = $_POST['rector_cc'];
        $stmt = $conn->prepare("DELETE FROM rector WHERE rector_cc = ?");
        $stmt->bind_param("s", $rector_cc);
        $stmt->execute();
        $stmt->close();
    }
    // Agregar
    elseif (isset($_POST['add_rector'])) {
        $rector_cc = $_POST['rector_cc'];
        $rector_nom_propio = strtoupper(trim($_POST['rector_nom_propio']));
        $rector_sexo = $_POST['rector_sexo'];
        $rector_resol_encargo = $_POST['rector_resol_encargo'];
        $tipo_rector = $_POST['tipo_rector'];
        $stmt = $conn->prepare("INSERT INTO rector (rector_cc, rector_nombre, rector_nom_propio, rector_sexo, rector_resol_encargo, tipo_rector) VALUES (?, ?, ?, ?, ?, ?)");
        $rector_nombre = $rector_nom_propio;
        $stmt->bind_param("ssssss", $rector_cc, $rector_nombre, $rector_nom_propio, $rector_sexo, $rector_resol_encargo, $tipo_rector);
        $stmt->execute();
        $stmt->close();
    }
    // Actualizar (vía modal)
    elseif (isset($_POST['update_rector'])) {
        $rector_cc = $_POST['rector_cc'];
        $rector_nom_propio = strtoupper(trim($_POST['rector_nom_propio']));
        $rector_sexo = $_POST['rector_sexo'];
        $rector_resol_encargo = $_POST['rector_resol_encargo'];
        $tipo_rector = $_POST['tipo_rector'];
        $stmt = $conn->prepare("UPDATE rector SET rector_nombre = ?, rector_nom_propio = ?, rector_sexo = ?, rector_resol_encargo = ?, tipo_rector = ? WHERE rector_cc = ?");
        $rector_nombre = $rector_nom_propio;
        $stmt->bind_param("ssssss", $rector_nombre, $rector_nom_propio, $rector_sexo, $rector_resol_encargo, $tipo_rector, $rector_cc);
        $stmt->execute();
        $stmt->close();
    }
}

// Obtener todos los rectores
$result = $conn->query("SELECT * FROM rector ORDER BY rector_nom_propio ASC");
?>

<style>
    .table-rectores th {
        background-color: var(--azul-oscuro, #002A9E);
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 12px 10px;
        border: none;
    }
    .table-rectores td {
        padding: 10px 8px;
        vertical-align: middle;
        border-bottom: 1px solid var(--gris-border, #E9EEF3);
    }
    .btn-edit-rector {
        background: #e9ecef;
        color: #1e293b;
        border: none;
        border-radius: 30px;
        padding: 4px 12px;
        font-size: 0.7rem;
        font-weight: 600;
        transition: 0.2s;
    }
    .btn-edit-rector:hover {
        background: var(--azul-cielo, #16A8E1);
        color: white;
    }
    .btn-delete-rector {
        background: #fee2e2;
        color: var(--rojo, #E52724);
        border: none;
        border-radius: 30px;
        padding: 4px 12px;
        font-size: 0.7rem;
        font-weight: 600;
        transition: 0.2s;
    }
    .btn-delete-rector:hover {
        background: var(--rojo, #E52724);
        color: white;
    }
    .card-form-rector {
        background: #F8FAFE;
        border-radius: 20px;
        padding: 1.2rem;
        margin-top: 1.5rem;
        border: 1px solid var(--gris-border);
    }
</style>

<div class="table-responsive">
    <table class="table table-hover table-rectores">
        <thead>
            <tr>
                <th>CC</th>
                <th>Nombre Propio</th>
                <th>Sexo</th>
                <th>Resolución Encargo</th>
                <th>Tipo Rector</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['rector_cc']) ?></td>
                        <td><?= htmlspecialchars($row['rector_nom_propio']) ?></td>
                        <td><?= htmlspecialchars($row['rector_sexo']) ?></td>
                        <td><?= htmlspecialchars($row['rector_resol_encargo']) ?></td>
                        <td><?= htmlspecialchars($row['tipo_rector']) ?></td>
                        <td>
                            <button type="button" class="btn-edit-rector"
                                    data-json='<?= json_encode([
                                        'cc' => $row['rector_cc'],
                                        'nombre' => $row['rector_nom_propio'],
                                        'sexo' => $row['rector_sexo'],
                                        'resol' => $row['rector_resol_encargo'],
                                        'tipo' => $row['tipo_rector']
                                    ]) ?>'>
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <form method="post" style="display: inline-block;" onsubmit="return confirm('¿Eliminar este rector?')">
                                <input type="hidden" name="rector_cc" value="<?= $row['rector_cc'] ?>">
                                <button type="submit" name="delete_rector" class="btn-delete-rector"><i class="fas fa-trash-alt"></i> Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No hay rectores registrados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para editar rector -->
<div class="modal fade" id="editModalRector" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 24px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #002A9E, #4C19AF); color: white; border-radius: 24px 24px 0 0;">
                <h5 class="modal-title"><i class="fas fa-user-edit"></i> Editar Rector</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">&times;</button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="rector_cc" id="edit_rector_cc">
                    <div class="form-group">
                        <label>Nombre Propio</label>
                        <input type="text" class="form-control" name="rector_nom_propio" id="edit_rector_nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Sexo</label>
                        <select class="form-control" name="rector_sexo" id="edit_rector_sexo">
                            <option value="F">Femenino</option>
                            <option value="M">Masculino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Resolución de Encargo</label>
                        <input type="text" class="form-control" name="rector_resol_encargo" id="edit_rector_resol">
                    </div>
                    <div class="form-group">
                        <label>Tipo Rector</label>
                        <input type="text" class="form-control" name="tipo_rector" id="edit_rector_tipo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="update_rector" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulario para agregar nuevo rector -->
<div class="card-form-rector">
    <h6 class="mb-3" style="color: var(--azul-oscuro); font-weight: 700;"><i class="fas fa-plus-circle"></i> Agregar Nuevo Rector</h6>
    <form method="post">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label>CC</label>
                <input type="text" class="form-control" name="rector_cc" required>
            </div>
            <div class="form-group col-md-4">
                <label>Nombre Propio</label>
                <input type="text" class="form-control" name="rector_nom_propio" required>
            </div>
            <div class="form-group col-md-2">
                <label>Sexo</label>
                <select class="form-control" name="rector_sexo">
                    <option value="F">Femenino</option>
                    <option value="M">Masculino</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label>Resolución Encargo</label>
                <input type="text" class="form-control" name="rector_resol_encargo">
            </div>
            <div class="form-group col-md-3">
                <label>Tipo Rector</label>
                <input type="text" class="form-control" name="tipo_rector">
            </div>
            <div class="form-group col-md-2 d-flex align-items-end">
                <button type="submit" name="add_rector" class="btn btn-primary" style="background: var(--verde); border-radius: 30px;"><i class="fas fa-save"></i> Agregar</button>
            </div>
        </div>
    </form>
</div>

<script>
// Delegación de eventos para el botón Editar (funciona incluso dentro de pestañas)
document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-edit-rector');
        if (!btn) return;

        var data = btn.getAttribute('data-json');
        if (!data) {
            alert('Error al cargar los datos del rector.');
            return;
        }

        try {
            var rector = JSON.parse(data);
            document.getElementById('edit_rector_cc').value = rector.cc;
            document.getElementById('edit_rector_nombre').value = rector.nombre;
            document.getElementById('edit_rector_sexo').value = rector.sexo;
            document.getElementById('edit_rector_resol').value = rector.resol;
            document.getElementById('edit_rector_tipo').value = rector.tipo;

            // Abrir modal usando jQuery o Bootstrap nativo
            if (typeof $ !== 'undefined' && $.fn.modal) {
                $('#editModalRector').modal('show');
            } else if (typeof bootstrap !== 'undefined') {
                var modal = new bootstrap.Modal(document.getElementById('editModalRector'));
                modal.show();
            } else {
                alert('No se pudo abrir el modal. Verifique que Bootstrap esté cargado.');
            }
        } catch (err) {
            console.error('Error al parsear JSON:', err);
            alert('Error en los datos del rector.');
        }
    });
});
</script>