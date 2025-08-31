<?php
// Preparar datos para la vista
$matriz_permisos = array();
$acciones_disponibles = array();

foreach ($modulos_acciones as $ma) {
    $matriz_permisos[$ma['nom_modulo']][$ma['nom_accion']] = $ma;
    if (!in_array($ma['nom_accion'], $acciones_disponibles)) {
        $acciones_disponibles[] = $ma['nom_accion'];
    }
}

// Ordenar las acciones para tener un orden consistente
sort($acciones_disponibles);

// Crear array de permisos actuales para facilitar la verificación
$permisos_actuales = array();
if (isset($rol['permisos'])) {
    foreach ($rol['permisos'] as $permiso) {
        $permisos_actuales[] = $permiso['id_modulo_accion'];
    }
}

// Mostrar mensaje de error si viene sin permisos
if (isset($_GET['sin_permisos'])) {
    echo '<script>
        Swal.fire({
            icon: "warning",
            title: "Permisos requeridos",
            text: "Debe seleccionar al menos un permiso para el rol",
            confirmButtonText: "Entendido"
        });
    </script>';
}
?>

<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3><i class="fa fa-user-shield"></i> Editar Rol de Usuario</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2><i class="fa fa-user-edit"></i> Información del Rol</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form class="form-horizontal form-label-left" method="POST" id="rolForm">
                            <input type="hidden" name="id_rol" value="<?php echo $rol['id_rol']; ?>">
                            
                            <!-- Datos básicos del rol -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Nombre del Rol <span class="required">*</span></label>
                                        <input type="text" name="nom_rol" class="form-control" 
                                               placeholder="Ejemplo: SUPERVISOR DE ALMACÉN" 
                                               value="<?php echo htmlspecialchars($rol['nom_rol']); ?>" required>
                                        <small class="form-text text-muted">Ingrese un nombre descriptivo para el rol</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Estado</label>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="est" <?php echo ($rol['est_rol'] == 1) ? 'checked' : ''; ?>> 
                                                <strong>Activo</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Tabla de permisos -->
                            <div class="form-group">
                                <label class="control-label">
                                    <h4><i class="fa fa-shield-alt"></i> Asignación de Permisos <span class="required">*</span></h4>
                                    <p class="text-muted">Seleccione los permisos que tendrá este rol en cada módulo del sistema</p>
                                </label>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="tablaPermisos">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="width: 25%; background: #2A3F5F; color: white;">
                                                    <i class="fa fa-cube"></i> MÓDULOS
                                                </th>
                                                <?php foreach ($acciones_disponibles as $accion) { ?>
                                                <th style="text-align: center; background: #2A3F5F; color: white; min-width: 120px;">
                                                    <div><?php echo strtoupper($accion); ?></div>
                                                    <button type="button" class="btn btn-outline-light btn-xs mt-1" 
                                                            onclick="toggleColumna('<?php echo $accion; ?>')" 
                                                            title="Seleccionar/Deseleccionar toda la columna">
                                                        <i class="fa fa-check-double"></i> Todo
                                                    </button>
                                                </th>
                                                <?php } ?>
                                                <th style="text-align: center; background: #28a745; color: white;">
                                                    <i class="fa fa-cog"></i> ACCIONES
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $contador = 0;
                                            foreach ($matriz_permisos as $modulo => $acciones) { 
                                                $contador++;
                                            ?>
                                            <tr class="<?php echo $contador % 2 == 0 ? 'table-light' : ''; ?>">
                                                <td style="background: #f8f9fa; font-weight: bold; color: #2A3F5F;">
                                                    <i class="fa fa-folder-open"></i> <?php echo $modulo; ?>
                                                </td>
                                                <?php foreach ($acciones_disponibles as $accion_std) { ?>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <?php if (isset($acciones[$accion_std])) { 
                                                        $permiso = $acciones[$accion_std];
                                                        $checked = in_array($permiso['id_modulo_accion'], $permisos_actuales) ? 'checked' : '';
                                                    ?>
                                                        <div class="form-check">
                                                            <input type="checkbox" 
                                                                   class="form-check-input permission-checkbox" 
                                                                   name="permisos[]" 
                                                                   value="<?php echo $permiso['id_modulo_accion']; ?>" 
                                                                   data-modulo="<?php echo $modulo; ?>" 
                                                                   data-accion="<?php echo $accion_std; ?>"
                                                                   id="perm_<?php echo $permiso['id_modulo_accion']; ?>"
                                                                   <?php echo $checked; ?>>
                                                            <label class="form-check-label" for="perm_<?php echo $permiso['id_modulo_accion']; ?>">
                                                                <i class="fa fa-check text-success"></i>
                                                            </label>
                                                        </div>
                                                    <?php } else { ?>
                                                        <span class="text-muted" title="Acción no disponible para este módulo">
                                                            <i class="fa fa-minus"></i>
                                                        </span>
                                                    <?php } ?>
                                                </td>
                                                <?php } ?>
                                                <td style="text-align: center;">
                                                    <button type="button" class="btn btn-success btn-xs" 
                                                            onclick="toggleFila('<?php echo $modulo; ?>')" 
                                                            title="Seleccionar/Deseleccionar toda la fila">
                                                        <i class="fa fa-check"></i> Fila
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="alert alert-info mt-3">
                                    <i class="fa fa-info-circle"></i> 
                                    <strong>Ayuda:</strong> Use los botones "Todo" para seleccionar columnas completas o "Fila" para seleccionar todos los permisos de un módulo.
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Botones de acción -->
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-info" onclick="seleccionarTodo()">
                                            <i class="fa fa-check-double"></i> Seleccionar Todo
                                        </button>
                                        <button type="button" class="btn btn-warning" onclick="limpiarSeleccion()">
                                            <i class="fa fa-times"></i> Limpiar Selección
                                        </button>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <a href="rol_usuario_mostrar.php" class="btn btn-secondary">
                                            <i class="fa fa-arrow-left"></i> Cancelar
                                        </a>
                                        <button type="reset" class="btn btn-outline-secondary" onclick="restaurarOriginal()">
                                            <i class="fa fa-undo"></i> Restaurar
                                        </button>
                                        <button type="submit" name="editar" value="1" class="btn btn-warning">
                                            <i class="fa fa-save"></i> Actualizar Rol
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.permission-checkbox {
    width: 25px;
    height: 25px;
    cursor: pointer;
}

.table th, .table td {
    vertical-align: middle;
}

.btn-xs {
    padding: 2px 6px;
    font-size: 11px;
    border-radius: 3px;
}

.form-check {
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-check-input {
    margin: 0;
}

.form-check-label {
    margin: 0 0 0 5px;
    font-size: 16px;
}

#tablaPermisos thead th {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>

<script>
// Guardar estado original para restaurar
let estadoOriginal = {};

// Función para alternar permisos de una columna
function toggleColumna(accion) {
    const checkboxes = document.querySelectorAll(`input[data-accion="${accion}"]`);
    const todosSeleccionados = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !todosSeleccionados;
    });
}

// Función para alternar permisos de una fila
function toggleFila(modulo) {
    const checkboxes = document.querySelectorAll(`input[data-modulo="${modulo}"]`);
    const todosSeleccionados = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !todosSeleccionados;
    });
}

// Función para seleccionar todos los permisos
function seleccionarTodo() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

// Función para limpiar selección
function limpiarSeleccion() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Función para restaurar al estado original
function restaurarOriginal() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        const id = checkbox.value;
        checkbox.checked = estadoOriginal[id] || false;
    });
    
    // Restaurar nombre del rol
    document.querySelector('input[name="nom_rol"]').value = "<?php echo addslashes($rol['nom_rol']); ?>";
    document.querySelector('input[name="est"]').checked = <?php echo $rol['est_rol'] == 1 ? 'true' : 'false'; ?>;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Guardar estado original
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        estadoOriginal[checkbox.value] = checkbox.checked;
    });
    
    // Validación del formulario
    document.getElementById('rolForm').addEventListener('submit', function(e) {
        const nombreRol = document.querySelector('input[name="nom_rol"]').value.trim();
        const permisosSeleccionados = document.querySelectorAll('.permission-checkbox:checked');

        if (nombreRol === '') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Campo requerido',
                text: 'Por favor ingrese el nombre del rol',
                confirmButtonText: 'Entendido'
            }).then(() => {
                document.querySelector('input[name="nom_rol"]').focus();
            });
            return;
        }

        if (permisosSeleccionados.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Permisos requeridos',
                text: 'Debe seleccionar al menos un permiso para el rol',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        // Confirmación antes de guardar
        e.preventDefault();
        Swal.fire({
            title: 'Confirmar actualización',
            text: `¿Está seguro de actualizar el rol "${nombreRol}" con ${permisosSeleccionados.length} permisos?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, actualizar rol',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Enviar el formulario
                document.getElementById('rolForm').submit();
            }
        });
    });
});
</script>