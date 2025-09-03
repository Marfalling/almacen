<?php
// Función auxiliar para obtener el ID de acción cuando no está disponible en los datos
function obtenerIdAccion($nombre_accion) {
    $mapeo_acciones = array(
        'Ver' => 1,
        'Crear' => 2,
        'Editar' => 3
    );
    return isset($mapeo_acciones[$nombre_accion]) ? $mapeo_acciones[$nombre_accion] : 0;
}

// Preparar datos para la vista
$matriz_permisos = array();
$acciones_disponibles = array();

foreach ($modulos_acciones as $ma) {
    $matriz_permisos[$ma['nom_modulo']][$ma['nom_accion']] = $ma;
    if (!in_array($ma['nom_accion'], $acciones_disponibles)) {
        $acciones_disponibles[] = $ma['nom_accion'];
    }
}

// Ordenar las acciones según el ID (Ver=1, Crear=2, Editar=3)
// Crear un array con el orden correcto basado en los IDs de la base de datos
$orden_acciones = array();

// Obtener las acciones con sus IDs para ordenarlas correctamente
foreach ($modulos_acciones as $ma) {
    if (!isset($orden_acciones[$ma['nom_accion']])) {
        $orden_acciones[$ma['nom_accion']] = $ma['id_accion'];
    }
}

// Ordenar por ID de acción
uasort($orden_acciones, function($a, $b) {
    return $a - $b;
});

// Crear array ordenado de acciones
$acciones_disponibles = array_keys($orden_acciones);

// Mostrar mensaje de error si viene sin permisos
if (isset($_GET['sin_permisos'])) {
    echo '<script>alert("Debe seleccionar al menos un permiso para el rol");</script>';
}
?>

<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3><i class="fa fa-user-shield"></i> Nuevo Rol de Usuario</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2><i class="fa fa-user-plus"></i> Información del Rol</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form class="form-horizontal form-label-left" method="POST" id="rolForm">
                            
                            <!-- Datos básicos del rol -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Nombre del Rol <span class="required">*</span></label>
                                        <input type="text" name="nom_rol" class="form-control" 
                                               placeholder="Ejemplo: SUPERVISOR DE ALMACÉN" required>
                                        <small class="form-text text-muted">Ingrese un nombre descriptivo para el rol</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Estado</label>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="est" checked> 
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
                                                    ?>
                                                        <div class="form-check">
                                                            <input type="checkbox" 
                                                                   class="form-check-input permission-checkbox" 
                                                                   name="permisos[]" 
                                                                   value="<?php echo $permiso['id_modulo_accion']; ?>" 
                                                                   data-modulo="<?php echo $modulo; ?>" 
                                                                   data-accion="<?php echo $accion_std; ?>"
                                                                   data-id-accion="<?php echo $permiso['id_accion']; ?>"
                                                                   id="perm_<?php echo $permiso['id_modulo_accion']; ?>"
                                                                   onchange="manejarSeleccionPermisos(this)">
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
                                
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Botones de acción -->
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-8">
                                        <button type="button" class="btn btn-info" onclick="seleccionarTodo()">
                                            <i class="fa fa-check-double"></i> Seleccionar Todo
                                        </button>
                                        <button type="button" class="btn btn-warning" onclick="limpiarSeleccion()">
                                            <i class="fa fa-times"></i> Limpiar Selección
                                        </button>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <button type="reset" class="btn btn-secondary" onclick="limpiarFormulario()">
                                            <i class="fa fa-undo"></i> Limpiar
                                        </button>
                                        <button type="submit" name="registrar" value="1" class="btn btn-success">
                                            <i class="fa fa-save"></i> Crear Rol
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
// Función para manejar la selección automática de permisos
function manejarSeleccionPermisos(checkbox) {
    const modulo = checkbox.getAttribute('data-modulo');
    const accionId = parseInt(checkbox.getAttribute('data-id-accion'));
    
    // Si se selecciona "Ver" (ID=1), automáticamente seleccionar "Crear" (ID=2)
    if (accionId === 1 && checkbox.checked) {
        const checkboxCrear = document.querySelector(`input[data-modulo="${modulo}"][data-id-accion="2"]`);
        if (checkboxCrear && !checkboxCrear.checked) {
            checkboxCrear.checked = true;
        }
    }
    
    // Si se deselecciona "Crear" (ID=2), automáticamente deseleccionar "Ver" (ID=1)
    if (accionId === 2 && !checkbox.checked) {
        const checkboxVer = document.querySelector(`input[data-modulo="${modulo}"][data-id-accion="1"]`);
        if (checkboxVer && checkboxVer.checked) {
            checkboxVer.checked = false;
        }
    }
    
    // Si se deselecciona "Editar" (ID=3), no hacer nada automáticamente
    // Si se selecciona "Editar" (ID=3), no hacer nada automáticamente
    
    actualizarResumen();
}

// Función para alternar permisos de una columna
function toggleColumna(accion) {
    const checkboxes = document.querySelectorAll(`input[data-accion="${accion}"]`);
    const todosSeleccionados = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        if (!todosSeleccionados) {
            checkbox.checked = true;
            // Aplicar lógica de dependencias
            manejarSeleccionPermisos(checkbox);
        } else {
            checkbox.checked = false;
            // Aplicar lógica de dependencias
            manejarSeleccionPermisos(checkbox);
        }
    });
    
    actualizarResumen();
}

// Función para alternar permisos de una fila
function toggleFila(modulo) {
    const checkboxes = document.querySelectorAll(`input[data-modulo="${modulo}"]`);
    const todosSeleccionados = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !todosSeleccionados;
        // No aplicar lógica de dependencias aquí para evitar conflictos
    });
    
    actualizarResumen();
}

// Función para seleccionar todos los permisos
function seleccionarTodo() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    actualizarResumen();
}

// Función para limpiar selección
function limpiarSeleccion() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    actualizarResumen();
}

// Función para limpiar formulario
function limpiarFormulario() {
    document.getElementById('rolForm').reset();
    document.querySelector('input[name="est"]').checked = true;
    actualizarResumen();
}

// Función para actualizar resumen (placeholder)
function actualizarResumen() {
    // Esta función puede ser implementada para mostrar un resumen de permisos seleccionados
    const seleccionados = document.querySelectorAll('.permission-checkbox:checked').length;
    console.log(`Permisos seleccionados: ${seleccionados}`);
}

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    actualizarResumen();
});
</script>