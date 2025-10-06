<?php
//=======================================================================
// VISTA: v_centro_costo_nuevo.php
//=======================================================================
?>
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Nuevo Centro de Costo</h3>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="x_panel">
            <div class="x_title">
                <h2>Registrar Centro de Costo</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <form action="centro_costo_guardar.php" method="POST">
                    <div class="form-group col-md-6">
                        <label>Nombre del Centro de Costo *</label>
                        <input type="text" name="nom_area" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <button type="submit" class="btn btn-success">Guardar</button>
                        <a href="centro_costo_mostrar.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
