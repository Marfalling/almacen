<?php
//=======================================================================
// VISTA: v_proveedor_mostrar.php
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Proveedor</h3>
        </div>
    </div>
    <div class="clearfix"></div>

    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <div class="row">
                        <div class="col-sm-8">
                            <h2>Listado de Proveedor</h2>
                        </div>
                        <div class="col-sm-4 text-right">
                            <a href="proveedor_nuevo.php" class="btn btn-outline-info btn-sm">Nuevo proveedor</a>
                            <!-- BOTÓN MODAL -->
                            <button class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#modalImportar">
                                Importar CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="x_content">
                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>RUC</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th>Contacto</th>
                                <th>Email</th>
                                <th>Cuentas Bancarias</th>
                                <th>Estado</th>
                                <th>Editar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $c=0; 
                            foreach ($proveedor as $value) { 
                                $c++;
                                $id_proveedor = $value['id_proveedor'];
                                $cuentas = ObtenerCuentasProveedor($id_proveedor); 
                            ?>
                                <tr>
                                    <td><?= $c; ?></td>
                                    <td><?= $value['nom_proveedor']; ?></td>
                                    <td><?= $value['ruc_proveedor']; ?></td>
                                    <td><?= $value['dir_proveedor']; ?></td>
                                    <td><?= $value['tel_proveedor']; ?></td>
                                    <td><?= $value['cont_proveedor']; ?></td>
                                    <td><?= $value['mail_proveedor']; ?></td>
                                    <td>
                                        <?php if (!empty($cuentas)) { ?>
                                            <button class="btn btn-info btn-sm" 
                                                    data-toggle="modal" 
                                                    data-target="#modalCuentas<?= $id_proveedor; ?>">
                                                Ver cuentas
                                            </button>
                                            <!-- Modal cuentas -->
                                            <div class="modal fade" id="modalCuentas<?= $id_proveedor; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Cuentas bancarias - <?= $value['nom_proveedor']; ?></h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Banco</th>
                                                                        <th>Moneda</th>
                                                                        <th>Nro. Cuenta</th>
                                                                        <th>CCI</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($cuentas as $cta) { ?>
                                                                        <tr>
                                                                            <td>
                                                                                <?= !empty($cta['nom_banco']) 
                                                                                    ? htmlspecialchars($cta['nom_banco'], ENT_QUOTES, 'UTF-8') 
                                                                                    : '<span class="text-muted">—</span>'; ?>
                                                                            </td>
                                                                            <td><?= $cta['nom_moneda']; ?></td>
                                                                            <td><?= $cta['nro_cuenta_corriente']; ?></td>
                                                                            <td><?= $cta['nro_cuenta_interbancaria']; ?></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <span class="text-muted">Sin cuentas</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <center>
                                            <?php if ($value['est_proveedor'] == 1) { ?>
                                                <span class="badge badge-success badge_size">ACTIVO</span>
                                            <?php } else { ?>
                                                <span class="badge badge-danger badge_size">INACTIVO</span>
                                            <?php } ?>
                                        </center>
                                    </td>
                                    <td>
                                        <a class="btn btn-warning btn-sm" href="proveedor_editar.php?id_proveedor=<?= $id_proveedor; ?>">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL IMPORTACIÓN CSV -->
<div class="modal fade" id="modalImportar" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Importar proveedores desde CSV</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="../_controlador/proveedor_importar.php" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-group">
            <label>Selecciona el archivo CSV:</label>
            <input type="file" name="archivo" accept=".csv" class="form-control-file" required>
          </div>
          <p class="text-muted">Formato esperado: Nombre, RUC, Dirección, Teléfono, Contacto, Estado, Email, Banco, Moneda, Cuenta, CCI, EstadoCuenta</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Subir e Importar</button>
        </div>
      </form>
    </div>
  </div>
</div>

