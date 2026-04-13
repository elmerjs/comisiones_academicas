<?php
include 'conn.php';

// Manejo de acciones (sin cambios, pero mejorado con consultas preparadas)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_revisa'])) {
        $revisa_cc = $_POST['revisa_cc'];
        $stmt = $conn->prepare("DELETE FROM revisa WHERE revisa_cc = ?");
        $stmt->bind_param("s", $revisa_cc);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['add_revisa'])) {
        $revisa_cc = $_POST['revisa_cc'];
        $revisa_nom_propio = strtoupper(trim($_POST['revisa_nom_propio']));
        $revisa_sexo = $_POST['revisa_sexo'];
        $revisa_resol_encargo = $_POST['revisa_resol_encargo'];
        $revisa_tipo = $_POST['revisa_tipo'];
        $stmt = $conn->prepare("INSERT INTO revisa (revisa_cc, revisa_nombre, revisa_nom_propio, revisa_sexo, revisa_resol_encargo, revisa_tipo_revisa) VALUES (?, ?, ?, ?, ?, ?)");
        $revisa_nombre = $revisa_nom_propio;
        $stmt->bind_param("ssssss", $revisa_cc, $revisa_nombre, $revisa_nom_propio, $revisa_sexo, $revisa_resol_encargo, $revisa_tipo);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update_revisa'])) {
        $revisa_cc = $_POST['revisa_cc'];
        $revisa_nom_propio = strtoupper(trim($_POST['revisa_nom_propio']));
        $revisa_sexo = $_POST['revisa_sexo'];
        $revisa_resol_encargo = $_POST['revisa_resol_encargo'];
        $revisa_tipo = $_POST['revisa_tipo'];
        $stmt = $conn->prepare("UPDATE revisa SET revisa_nombre = ?, revisa_nom_propio = ?, revisa_sexo = ?, revisa_resol_encargo = ?, revisa_tipo_revisa = ? WHERE revisa_cc = ?");
        $revisa_nombre = $revisa_nom_propio;
        $stmt->bind_param("ssssss", $revisa_nombre, $revisa_nom_propio, $revisa_sexo, $revisa_resol_encargo, $revisa_tipo, $revisa_cc);
        $stmt->execute();
        $stmt->close();
    }
}

$result = $conn->query("SELECT * FROM revisa ORDER BY revisa_nom_propio ASC");
?>

<style>
    .table-revisores th {
        background-color: var(--azul-oscuro, #002A9E);
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 12px 10px;
        border: none;
    }
    .table-revisores td {
        padding: 10px 8px;
        vertical-align: middle;
        border-bottom: 1px solid var(--gris-border, #E9EEF3);
    }
    .btn-edit {
        background: #e9ecef;
        color: #1e293b;
        border: none;
        border-radius: 30px;
        padding: 4px 12px;
        font-size: 0.7rem;
        font-weight: 600;
        transition: 0.2s;
    }
    .btn-edit:hover {
        background: var(--azul-cielo, #16A8E1);
        color: white;
    }
    .btn-delete {
        background: #fee2e2;
        color: var(--rojo, #E52724);
        border: none;
        border-radius: 30px;
        padding: 4px 12px;
        font-size: 0.7rem;
        font-weight: 600;
        transition: 0.2s;
    }
    .btn-delete:hover {
        background: var(--rojo, #E52724);
        color: white;
    }
    .card-form {
        background: #F8FAFE;
        border-radius: 20px;
        padding: 1.2rem;
        margin-top: 1.5rem;
        border: 1px solid var(--gris-border);
    }
</style>

<div class="table-responsive">
    <table class="table table-hover table-revisores">
        <thead>
            <tr>
                <th>CC</th>
                <th>Nombre Propio</th>
                <th>Sexo</th>
                <th>Resolución de Encargo</th>
                <th>Tipo Revisor</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['revisa_cc']) ?></td>
                        <td><?= htmlspecialchars($row['revisa_nom_propio']) ?></td>
                        <td><?= htmlspecialchars($row['revisa_sexo']) ?></td>
                        <td><?= htmlspecialchars($row['revisa_resol_encargo']) ?></td>
                        <td><?= htmlspecialchars($row['revisa_tipo_revisa']) ?></td>
                        <td>
                            <!-- Botón de editar con datos en JSON para evitar problemas de escapado -->
                            <button type="button" class="btn-edit btn-editar-revisor"
                                    data-json='<?= json_encode([
                                        'cc' => $row['revisa_cc'],
                                        'nombre' => $row['revisa_nom_propio'],
                                        'sexo' => $row['revisa_sexo'],
                                        'resol' => $row['revisa_resol_encargo'],
                                        'tipo' => $row['revisa_tipo_revisa']
                                    ]) ?>'>
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <form method="post" style="display: inline-block;" onsubmit="return confirm('¿Eliminar este revisor?')">
                                <input type="hidden" name="revisa_cc" value="<?= $row['revisa_cc'] ?>">
                                <button type="submit" name="delete_revisa" class="btn-delete"><i class="fas fa-trash-alt"></i> Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No hay revisores registrados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para editar revisor -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 24px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #002A9E, #4C19AF); color: white; border-radius: 24px 24px 0 0;">
                <h5 class="modal-title" id="editModalLabel"><i class="fas fa-user-edit"></i> Editar Revisor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="revisa_cc" id="edit_cc">
                    <div class="form-group">
                        <label>Nombre Propio</label>
                        <input type="text" class="form-control" name="revisa_nom_propio" id="edit_nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Sexo</label>
                        <select class="form-control" name="revisa_sexo" id="edit_sexo">
                            <option value="F">Femenino</option>
                            <option value="M">Masculino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Resolución de Encargo</label>
                        <input type="text" class="form-control" name="revisa_resol_encargo" id="edit_resol">
                    </div>
                    <div class="form-group">
                        <label>Tipo Revisor</label>
                        <input type="text" class="form-control" name="revisa_tipo" id="edit_tipo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="update_revisa" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulario para agregar nuevo revisor -->
<div class="card-form">
    <h6 class="mb-3" style="color: var(--azul-oscuro); font-weight: 700;"><i class="fas fa-plus-circle"></i> Agregar Nuevo Revisor</h6>
    <form method="post">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label>CC</label>
                <input type="text" class="form-control" name="revisa_cc" required>
            </div>
            <div class="form-group col-md-4">
                <label>Nombre Propio</label>
                <input type="text" class="form-control" name="revisa_nom_propio" required>
            </div>
            <div class="form-group col-md-2">
                <label>Sexo</label>
                <select class="form-control" name="revisa_sexo">
                    <option value="F">Femenino</option>
                    <option value="M">Masculino</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label>Resolución Encargo</label>
                <input type="text" class="form-control" name="revisa_resol_encargo">
            </div>
            <div class="form-group col-md-3">
                <label>Tipo Revisor</label>
                <input type="text" class="form-control" name="revisa_tipo">
            </div>
            <div class="form-group col-md-2 d-flex align-items-end">
                <button type="submit" name="add_revisa" class="btn btn-primary" style="background: var(--verde); border-radius: 30px;"><i class="fas fa-save"></i> Agregar</button>
            </div>
        </div>
    </form>
</div>

<script>
// Aseguramos que el DOM esté listo y usamos delegación de eventos
document.addEventListener('DOMContentLoaded', function() {
    // Delegación: escucha clicks en cualquier elemento con clase .btn-editar-revisor
    document.body.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-editar-revisor');
        if (!btn) return;
        
        // Obtener el JSON del atributo data-json
        var data = btn.getAttribute('data-json');
        if (!data) {
            console.error('No se encontró data-json en el botón');
            alert('Error: No se pudieron cargar los datos del revisor.');
            return;
        }
        
        try {
            var revisor = JSON.parse(data);
            document.getElementById('edit_cc').value = revisor.cc;
            document.getElementById('edit_nombre').value = revisor.nombre;
            document.getElementById('edit_sexo').value = revisor.sexo;
            document.getElementById('edit_resol').value = revisor.resol;
            document.getElementById('edit_tipo').value = revisor.tipo;
            
            // Abrir el modal usando Bootstrap (asegurar que jQuery y Bootstrap estén disponibles)
            if (typeof $ !== 'undefined' && $.fn.modal) {
                $('#editModal').modal('show');
            } else if (typeof bootstrap !== 'undefined') {
                // Fallback con Bootstrap nativo (si se usa Bootstrap 5)
                var modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.show();
            } else {
                console.error('No se pudo abrir el modal');
                alert('Error al abrir el modal');
            }
        } catch (err) {
            console.error('Error al parsear JSON:', err);
            alert('Error en los datos del revisor.');
        }
    });
});
</script>