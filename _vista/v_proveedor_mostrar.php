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
                        <div class="col-sm-10">
                            <h2>Listado de Proveedor</h2>
                        </div>
                        <div class="col-sm-2">
                            <a href="proveedor_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nuevo proveedor</a>
                        </div>
                    </div>
                </div>
                <div class="x_content">
                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th><th>Nombre</th><th>RUC</th><th>Dirección</th>
                                <th>Teléfono</th><th>Contacto</th><th>Email</th>
                                <th>Cuentas Bancarias</th><th>Estado</th><th>Editar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $c=0; foreach ($proveedor as $value) { $c++;
                                $id_proveedor = $value['id_proveedor'];
                                $cuentas = ObtenerCuentasProveedor($id_proveedor); ?>
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
                                            <ul>
                                            <?php foreach ($cuentas as $cta) { ?>
                                                <li>
                                                    <b><?= $cta['banco_proveedor']; ?></b> - <?= $cta['nom_moneda']; ?><br>
                                                    CC: <?= $cta['nro_cuenta_corriente']; ?><br>
                                                    CCI: <?= $cta['nro_cuenta_interbancaria']; ?>
                                                </li>
                                            <?php } ?>
                                            </ul>
                                        <?php } else { echo "<span class='text-muted'>Sin cuentas</span>"; } ?>
                                    </td>
                                    <td><?= ($value['est_proveedor']==1) ? "ACTIVO":"INACTIVO"; ?></td>
                                    <td><a class="btn btn-warning btn-sm" href="proveedor_editar.php?id_proveedor=<?= $id_proveedor; ?>"><i class="fa fa-edit"></i></a></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

