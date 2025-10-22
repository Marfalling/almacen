<?php 
//=======================================================================
// VISTA: v_centro_costo_nuevo.php 
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Centro de Costo <small>Nuevo Registro</small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Registrar nuevo Centro de Costo</h2>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <form class="form-horizontal form-label-left" method="POST" action="../_controlador/centro_costo_nuevo.php">

                            <!-- Nombre del Centro -->
                            <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3">
                                    Nombre del Centro <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-9 col-sm-9">
                                    <input type="text" name="nom_centro_costo" required class="form-control" 
                                           placeholder="Ingrese nombre del centro de costo" maxlength="100">
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <!-- Botones Cancelar y Guardar alineados al final -->
                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 offset-md-8">
                                    <a href="centro_costo_mostrar.php" class="btn btn-outline-danger btn-block">
                                        Cancelar
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <button type="submit" name="registrar" class="btn btn-success btn-block actualizar-btn">
                                        Guardar
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
<!-- /page content -->

<!-- ====== ESTILOS DEL BOTÓN ====== -->
<style>
/* Botón Guardar - consistencia con Editar Personal y Cliente */
.actualizar-btn {
    background-color: #26B99A;
    border-color: #26B99A;
    font-weight: bold;
    font-size: 13px;
}

.actualizar-btn:hover {
    background-color: #1e9e83;
    border-color: #1e9e83;
}

/* Botón Cancelar - outline-danger */
.btn-outline-danger {
    border: 1px solid #d9534f;
    color: #d9534f;
    font-weight: bold;
}

.btn-outline-danger:hover {
    background-color: #f2dede;
    border-color: #d43f3a;
}
</style>



