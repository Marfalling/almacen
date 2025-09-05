<?php 
//=======================================================================
// VISTA: v_pedidos_editar.php
//=======================================================================
$pedido = $pedido_data[0]; // Datos del pedido principal
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Editar Pedido</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos del Pedido <small><?php echo $pedido['cod_pedido']; ?></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="pedidos_editar.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $pedido['id_pedido']; ?>">
                            
                            <!-- Información básica del pedido -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Código del Pedido:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo $pedido['cod_pedido']; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre del Pedido <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom_pedido" class="form-control" value="<?php echo $pedido['nom_pedido']; ?>" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha de Pedido:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i', strtotime($pedido['fec_pedido'])); ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha de Necesidad <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="date" name="fecha_necesidad" class="form-control" value="<?php echo $pedido['fec_req_pedido']; ?>" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nº OT/LCL/LCA:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="num_ot" class="form-control" value="<?php echo $pedido['ot_pedido']; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Contacto <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="contacto" class="form-control" value="<?php echo $pedido['cel_pedido']; ?>" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Lugar de Entrega <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="lugar_entrega" class="form-control" value="<?php echo $pedido['lug_pedido']; ?>" required>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Sección de materiales/servicios -->
                            <div class="x_title">
                                <h4>Detalles del Pedido <small></small></h4>
                                <div class="clearfix"></div>
                            </div>

                            <div id="contenedor-materiales">
                                <?php 
                                $contador_material = 0;
                                foreach ($pedido_detalle as $detalle) { 
                                    // Parsear comentarios para extraer unidad y observaciones
                                    $comentario = $detalle['com_pedido_detalle'];
                                    $unidad = '';
                                    $observaciones = '';
                                    
                                    if (preg_match('/Unidad:\s*([^|]*)\s*\|/', $comentario, $matches)) {
                                        $unidad = trim($matches[1]);
                                    }
                                    if (preg_match('/Obs:\s*(.*)$/', $comentario, $matches)) {
                                        $observaciones = trim($matches[1]);
                                    }
                                    
                                    // Parsear requisitos
                                    $requisitos = $detalle['req_pedido'];
                                    $sst = $ma = $ca = '';
                                    
                                    if (preg_match('/SST:\s*([^|]*)\s*\|/', $requisitos, $matches)) {
                                        $sst = trim($matches[1]);
                                    }
                                    if (preg_match('/MA:\s*([^|]*)\s*\|/', $requisitos, $matches)) {
                                        $ma = trim($matches[1]);
                                    }
                                    if (preg_match('/CA:\s*(.*)$/', $requisitos, $matches)) {
                                        $ca = trim($matches[1]);
                                    }
                                ?>
                                <div class="material-item border p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Descripción/Material <span class="text-danger">*</span>:</label>
                                            <input type="text" name="descripcion[]" class="form-control" value="<?php echo $detalle['prod_pedido_detalle']; ?>" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Cantidad <span class="text-danger">*</span>:</label>
                                            <input type="number" name="cantidad[]" class="form-control" step="0.01" min="0" value="<?php echo $detalle['cant_pedido_detalle']; ?>" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Unidad <span class="text-danger">*</span>:</label>
                                            <input type="text" name="unidad[]" class="form-control" value="<?php echo $unidad; ?>" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Observaciones:</label>
                                            <textarea name="observaciones[]" class="form-control" rows="1"><?php echo $observaciones; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <label>SST <span class="text-danger">*</span>:</label>
                                            <input type="text" name="sst[]" class="form-control" value="<?php echo $sst; ?>" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label>MA <span class="text-danger">*</span>:</label>
                                            <input type="text" name="ma[]" class="form-control" value="<?php echo $ma; ?>" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label>CA <span class="text-danger">*</span>:</label>
                                            <input type="text" name="ca[]" class="form-control" value="<?php echo $ca; ?>" required>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label>Adjuntar Nuevos Archivos:</label>
                                            <input type="file" name="archivos_<?php echo $contador_material; ?>[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png">
                                            <?php if (!empty($detalle['archivos'])) { ?>
                                                <small class="text-muted">
                                                    Archivos actuales: <?php echo $detalle['archivos']; ?>
                                                </small>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-end">
                                            <?php if ($contador_material > 0) { ?>
                                            <button type="button" class="btn btn-danger btn-sm eliminar-material">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                    $contador_material++;
                                } 
                                ?>
                            </div>

                            <div class="form-group">
                                <button type="button" id="agregar-material" class="btn btn-info btn-sm">
                                    <i class="fa fa-plus"></i> Agregar Material/Servicio
                                </button>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Aclaraciones -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Aclaraciones:</label>
                                <div class="col-md-9 col-sm-9">
                                    <textarea name="aclaraciones" class="form-control" rows="4"><?php echo $pedido['acl_pedido']; ?></textarea>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-6">
                                    <a href="pedidos_mostrar.php" class="btn btn-outline-secondary btn-block">Cancelar</a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="reset" class="btn btn-outline-danger btn-block">Limpiar</button>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="actualizar" id="btn_actualizar" class="btn btn-success btn-block">Actualizar Pedido</button>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 col-sm-12">
                                    <p><span class="text-danger">*</span> Los campos con (<span class="text-danger">*</span>) son obligatorios.</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    let contadorMateriales = <?php echo count($pedido_detalle); ?>;
    
    // Agregar nuevo material
    document.getElementById('agregar-material').addEventListener('click', function() {
        const contenedor = document.getElementById('contenedor-materiales');
        const nuevoMaterial = document.querySelector('.material-item').cloneNode(true);
        
        // Limpiar los valores del nuevo elemento
        const inputs = nuevoMaterial.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            if (input.type !== 'file') {
                input.value = '';
            }
        });
        
        // Actualizar el name del input file
        const fileInput = nuevoMaterial.querySelector('input[type="file"]');
        fileInput.name = `archivos_${contadorMateriales}[]`;
        
        // Mostrar el botón eliminar
        const btnEliminar = nuevoMaterial.querySelector('.eliminar-material');
        if (btnEliminar) {
            btnEliminar.style.display = 'block';
        } else {
            // Crear botón eliminar si no existe
            const colBotones = nuevoMaterial.querySelector('.col-md-6:last-child');
            colBotones.innerHTML = '<button type="button" class="btn btn-danger btn-sm eliminar-material"><i class="fa fa-trash"></i> Eliminar</button>';
        }
        
        // Limpiar texto de archivos actuales
        const archivoActual = nuevoMaterial.querySelector('.text-muted');
        if (archivoActual) {
            archivoActual.remove();
        }
        
        contenedor.appendChild(nuevoMaterial);
        contadorMateriales++;
        
        // Actualizar eventos de eliminar
        actualizarEventosEliminar();
    });
    
    // Función para actualizar eventos de eliminar
    function actualizarEventosEliminar() {
        document.querySelectorAll('.eliminar-material').forEach(btn => {
            btn.onclick = function() {
                if (document.querySelectorAll('.material-item').length > 1) {
                    this.closest('.material-item').remove();
                }
            };
        });
    }
    
    // Inicializar eventos
    actualizarEventosEliminar();
});
</script>
