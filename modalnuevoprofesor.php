<style>
    /* Estilos específicos para el modal (complementan los globales) */
    .modal-uc .modal-content {
        border-radius: 24px;
        border: none;
        box-shadow: 0 20px 35px -10px rgba(0,0,0,0.2);
    }
    .modal-uc .modal-header {
        background: linear-gradient(135deg, var(--azul-oscuro, #002A9E), var(--morado, #4C19AF));
        color: white;
        border-radius: 24px 24px 0 0;
        padding: 1rem 1.5rem;
        border-bottom: none;
    }
    .modal-uc .modal-header .close {
        color: white;
        opacity: 0.8;
        text-shadow: none;
    }
    .modal-uc .modal-header .close:hover {
        opacity: 1;
    }
    .modal-uc .modal-body {
        padding: 2rem 1.5rem;
    }
    .modal-uc .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
    }
    .modal-uc .form-group label {
        font-weight: 600;
        font-size: 0.8rem;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }
    .modal-uc .form-control, .modal-uc .custom-select {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .modal-uc .form-control:focus, .modal-uc .custom-select:focus {
        border-color: var(--azul-cielo, #16A8E1);
        box-shadow: 0 0 0 3px rgba(22,168,225,0.1);
    }
    .btn-uc-modal {
        border-radius: 40px;
        padding: 8px 24px;
        font-weight: 600;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .btn-uc-modal-primary {
        background: var(--verde, #249337);
        color: white;
        border: none;
    }
    .btn-uc-modal-primary:hover {
        background: #1a6e2c;
        transform: translateY(-1px);
    }
    .btn-uc-modal-secondary {
        background: #e9ecef;
        color: #1e293b;
        border: none;
    }
    .btn-uc-modal-secondary:hover {
        background: #dee2e6;
    }
</style>

<!-- Modal Bootstrap 4 (estructura moderna) -->
<div class="modal fade" id="myModalb" tabindex="-1" role="dialog" aria-labelledby="myModalbLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content modal-uc">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalbLabel">
                    <i class="fas fa-user-plus"></i> Crear Nuevo Profesor
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="resolucionForm" method="post" action="inserttercero.php">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="documento">Documento *</label>
                                <input type="text" id="documento" class="form-control" name="documento" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sexo">Sexo *</label>
                                <select id="sexo" name="sexo" class="form-control" required>
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
                                <label for="nombre1">Primer Nombre *</label>
                                <input type="text" id="nombre1" class="form-control" name="nombre1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre2">Segundo Nombre</label>
                                <input type="text" id="nombre2" class="form-control" name="nombre2">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido1">Primer Apellido *</label>
                                <input type="text" id="apellido1" class="form-control" name="apellido1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido2">Segundo Apellido</label>
                                <input type="text" id="apellido2" class="form-control" name="apellido2">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="vincul">Vinculación *</label>
                                <select id="vincul" name="vincul" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <option value="PLANTA">PLANTA</option>
                                    <option value="OCASIONAL">OCASIONAL</option>
                                    <option value="HORA CATEDRA">HORA CATEDRA</option>
                                    <option value="DOCENTES ENCARGO ADM.">DOCENTES ENCARGO ADM.</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="vinculacion">Dedicación *</label>
                                <select id="vinculacion" name="vinculacion" class="form-control" required>
                                    <option value="TC">Tiempo Completo</option>
                                    <option value="MT">Medio Tiempo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="escalafon">Escalafón *</label>
                                <select id="escalafon" name="escalafon" class="form-control" required>
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
                                <label for="depto">Departamento *</label>
                                <select id="depto" name="depto" class="form-control" required>
                                    <?php
                                    // Conexión ya está disponible por el include de headerz.php
                                    $query_departamentos = "SELECT d.PK_DEPTO, d.NOMBRE_DEPTO_CORT, f.NOMBREC_FAC
                                                            FROM deparmanentos d
                                                            INNER JOIN facultad f ON d.FK_FAC = f.PK_FAC 
                                                            ORDER BY d.NOMBRE_DEPTO_CORT ASC";
                                    $resultado_departamentos = $conn->query($query_departamentos);
                                    if ($resultado_departamentos->num_rows > 0) {
                                        while ($fila = $resultado_departamentos->fetch_assoc()) {
                                            echo '<option value="' . $fila['PK_DEPTO'] . '">' 
                                                 . htmlspecialchars($fila['NOMBRE_DEPTO_CORT']) . ' - ' 
                                                 . htmlspecialchars($fila['NOMBREC_FAC']) . '</option>';
                                        }
                                    } else {
                                        echo '<option value="">No hay departamentos disponibles</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cargo">Cargo administrativo</label>
                                <select id="cargo" name="cargo" class="form-control">
                                    <option value="">Ninguno</option>
                                    <option value="COORDINADORPS">COORDINADOR Posgrado</option>
                                    <option value="COORDINADORPR">COORDINADOR Pregrado</option>
                                    <option value="JEFE">JEFE DE DEPTO</option>
                                    <option value="DIRECTOR">DIRECTOR</option>
                                    <option value="DECANO">DECANO</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" class="form-control" name="email">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="estado">Estado *</label>
                                <select id="estado" name="estado" class="form-control" required>
                                    <option value="ac">ACTIVO</option>
                                    <option value="in">INACTIVO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fecha_ingreso">Fecha ingreso *</label>
                                <input type="date" id="fecha_ingreso" class="form-control" name="fecha_ingreso" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-uc-modal-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-uc-modal-primary">
                        <i class="fas fa-save"></i> Guardar Profesor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Abrir modal con Bootstrap (ya no necesita JS manual, pero mantenemos compatibilidad)
$(document).ready(function() {
    $('#openModalBtnb').click(function() {
        $('#myModalb').modal('show');
    });
});
</script>