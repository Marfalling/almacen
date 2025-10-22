<?php 

?>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3> Nuevo Módulo</h3>
            </div>
        </div>
        
        <div class="clearfix"></div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2><i class="fa fa-cube"></i> Módulo <small>Sistema de permisos</small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form class="form-horizontal form-label-left" action="modulo_nuevo.php" method="post" id="moduloForm">
                            
                            <!-- Datos básicos del módulo -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Nombre del Módulo <span class="text-danger">*</span>:</label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom_modulo" class="form-control" 
                                           placeholder="Nombre del módulo" 
                                           required="required" 
                                           value="<?php echo isset($_POST['nom_modulo']) ? htmlspecialchars($_POST['nom_modulo']) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">Estado:</label>
                                <div class="col-md-9 col-sm-9">
                                    <div class="">
                                        <label>
                                            <input type="checkbox" name="est" class="js-switch" 
                                                   <?php echo (!isset($_POST['registrar']) || isset($_POST['est'])) ? 'checked' : ''; ?>> 
                                            Activo
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Solo los módulos activos estarán disponibles para asignar permisos
                                    </small>
                                </div>
                            </div>
                            
                            <div class="ln_solid"></div>

                            <!-- Sección de selección de acciones -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">
                                    <h4><i class="fa fa-cogs"></i> Acciones Disponibles <span class="text-danger">*</span></h4>
                                </label>
                                <div class="col-md-9 col-sm-9">
                                    <p class="text-muted">Seleccione las acciones que estarán disponibles para este módulo</p>
                                    
                                    <div class="row">
                                        <?php if (!empty($acciones_disponibles)) { ?>
                                            <?php foreach ($acciones_disponibles as $accion) { ?>
                                            <div class="col-md-6 col-sm-12 mb-3">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                        <div>
                                                            <i class="fa fa-cog fa-lg text-info mr-2"></i>
                                                            <strong><?php echo strtoupper($accion['nom_accion']); ?></strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                Permitir <?php echo strtolower($accion['nom_accion']); ?> en este módulo
                                                            </small>
                                                        </div>
                                                        <div>
                                                            <label>
                                                                <input type="checkbox" 
                                                                       name="acciones[]" 
                                                                       value="<?php echo $accion['id_accion']; ?>" 
                                                                       class="js-switch"
                                                                       <?php echo (isset($_POST['acciones']) && in_array($accion['id_accion'], $_POST['acciones'])) ? 'checked' : ''; ?>>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <div class="col-md-12">
                                                <div class="alert alert-warning">
                                                    <i class="fa fa-exclamation-triangle"></i> 
                                                    No hay acciones disponibles. Debe crear acciones primero.
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                </div>
                            </div>
                            
                            <div class="ln_solid"></div>
                            
                            
                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <a href="modulo_mostrar.php" class="btn btn-outline-danger btn-block">
                                        Cancelar
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" id="btn_registrar" class="btn btn-success btn-block">
                                        <i></i> Registrar
                                    </button>
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


<script>

// Validación del formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('moduloForm');
    
    form.addEventListener('submit', function(e) {
        const accionesSeleccionadas = document.querySelectorAll('input[name="acciones[]"]:checked');
        
        if (accionesSeleccionadas.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Acciones requeridas',
                text: 'Debe seleccionar al menos una acción para el módulo',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#3085d6'
            });
            return false;
        }
    });
});
</script>

<!-- /page content -->