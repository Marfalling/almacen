<?php 
//=======================================================================
// VISTA: v_pedidos_nuevo.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Nuevo Pedido</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Datos del Pedido <small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="pedidos_nuevo.php" method="post" enctype="multipart/form-data">
                            
                            <!-- Información básica del pedido -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Tipo de Pedido <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="tipo_pedido" class="form-control" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="MATERIAL">Pedido de Material</option>
                                        <option value="SERVICIO">Pedido de Servicio</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Obra <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <select name="id_obra" class="form-control" required>
                                        <option value="">Seleccionar obra</option>
                                        <?php foreach ($obras as $obra) { ?>
                                            <option value="<?php echo $obra['id_obra']; ?>">
                                                <?php echo $obra['nom_obra']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre del Pedido <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom_pedido" class="form-control" placeholder="Ingrese nombre del pedido" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Solicitante:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="solicitante" class="form-control" value="<?php echo $usuario_sesion; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Fecha de Necesidad <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="date" name="fecha_necesidad" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nº OT/LCL/LCA (Opcional):</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="num_ot" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Contacto <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="contacto" class="form-control" placeholder="Número de contacto" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Lugar de Entrega <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="lugar_entrega" class="form-control" placeholder="Dirección o lugar de entrega" required>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Sección de materiales/servicios -->
                            <div class="x_title">
                                <h4>Detalles del Pedido <small></small></h4>
                                <div class="clearfix"></div>
                            </div>

                            <div id="contenedor-materiales">
                                <div class="material-item border p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Descripción/Material <span class="text-danger">*</span>:</label>
                                            <input type="text" name="descripcion[]" class="form-control" placeholder="Descripción del material o servicio" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Cantidad <span class="text-danger">*</span>:</label>
                                            <input type="number" name="cantidad[]" class="form-control" step="0.01" min="0" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Unidad <span class="text-danger">*</span>:</label>
                                            <input type="text" name="unidad[]" class="form-control" placeholder="Ej: kg, m, und" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Observaciones:</label>
                                            <textarea name="observaciones[]" class="form-control" rows="1" placeholder="Observaciones o comentarios"></textarea>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <label>SST <span class="text-danger">*</span>:</label>
                                            <input type="text" name="sst[]" class="form-control" placeholder="Requisitos SST o 'No aplica'" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label>MA <span class="text-danger">*</span>:</label>
                                            <input type="text" name="ma[]" class="form-control" placeholder="Requisitos MA o 'No aplica'" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label>CA <span class="text-danger">*</span>:</label>
                                            <input type="text" name="ca[]" class="form-control" placeholder="Requisitos CA o 'No aplica'" required>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label>Adjuntar Archivos:</label>
                                            <input type="file" name="archivos_0[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png">
                                            
                                        </div>
                                        <div class="col-md-6 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm eliminar-material" style="display: none;">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
                                    <textarea name="aclaraciones" class="form-control" rows="4" ></textarea>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <button type="reset" class="btn btn-outline-danger btn-block">Limpiar</button>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" id="btn_registrar" class="btn btn-success btn-block">Finalizar Pedido</button>
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
    let contadorMateriales = 1;
    
    // Agregar nuevo material
    document.getElementById('agregar-material').addEventListener('click', function() {
        const contenedor = document.getElementById('contenedor-materiales');
        const nuevoMaterial = document.querySelector('.material-item').cloneNode(true);
        
        // Limpiar los valores del nuevo elemento
        const inputs = nuevoMaterial.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.value = '';
        });
        
        // Actualizar el name del input file
        const fileInput = nuevoMaterial.querySelector('input[type="file"]');
        fileInput.name = `archivos_${contadorMateriales}[]`;
        
        // Mostrar el botón eliminar
        const btnEliminar = nuevoMaterial.querySelector('.eliminar-material');
        btnEliminar.style.display = 'block';
        
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