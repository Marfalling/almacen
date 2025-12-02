<?php 
//=======================================================================
// VISTA: v_detraccion_mostrar.php 
//=======================================================================

// ========================================================================
// VERIFICAR PERMISOS AL INICIO
// ========================================================================
$tiene_permiso_crear = verificarPermisoEspecifico('crear_detraccion');
$tiene_permiso_editar = verificarPermisoEspecifico('editar_detraccion');
?>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Detracción / Retención / Percepción <small></small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <?php if (isset($_GET['registrado'])): ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Registro exitoso',
                    text: 'Se registró correctamente.',
                    showConfirmButton: false,
                    timer: 2000
                });
            </script>
        <?php elseif (isset($_GET['actualizado'])): ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Actualización exitosa',
                    text: 'Se actualizó correctamente.',
                    showConfirmButton: false,
                    timer: 2000
                });
            </script>
        <?php elseif (isset($_GET['error']) && $_GET['error'] == 'duplicado'): ?>
            <script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Registro duplicado',
                    text: 'Ya existe un registro con ese código o nombre.',
                    showConfirmButton: true
                });
            </script>
        <?php elseif (isset($_GET['error'])): ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo completar la acción.',
                    showConfirmButton: true
                });
            </script>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-10">
                                <h2>Listado de Detracciones / Retenciones / Percepciones</h2>
                            </div>
                            <div class="col-sm-2">
                                <!-- ============================================ -->
                                <!-- BOTÓN NUEVO REGISTRO -->
                                <!-- ============================================ -->
                                <?php if (!$tiene_permiso_crear) { ?>
                                    <a href="#" 
                                       class="btn btn-outline-danger btn-sm btn-block disabled"
                                       title="No tienes permiso para crear detracciones/retenciones/percepciones"
                                       tabindex="-1" 
                                       aria-disabled="true">
                                        <i class="fa fa-plus"></i> Nuevo Registro
                                    </a>
                                <?php } else { ?>
                                    <a href="detraccion_nuevo.php" 
                                       class="btn btn-outline-info btn-sm btn-block"
                                       title="Crear nuevo registro">
                                        <i class="fa fa-plus"></i> Nuevo Registro
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Código</th>
                                                <th>Tipo</th>
                                                <th>Nombre</th>
                                                <th>Porcentaje (%)</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $c = 0;
                                            if (!empty($detraccion)) {
                                                foreach ($detraccion as $value) {
                                                    $c++;
                                                    $id         = $value['id_detraccion'];
                                                    $nom        = $value['nombre_detraccion'];
                                                    $cod        = !empty($value['cod_detraccion']) ? $value['cod_detraccion'] : '-';
                                                    $porcentaje = $value['porcentaje'];
                                                    $estado     = isset($value['est_detraccion']) ? $value['est_detraccion'] : 1;
                                                    $tipo       = $value['nom_detraccion_tipo'];

                                                    // Badge color por tipo
                                                    $tipo_upper = strtoupper($tipo);
                                                    $badge_tipo = 'badge-warning';
                                                    if ($tipo_upper == 'RETENCION') $badge_tipo = 'badge-info';
                                                    if ($tipo_upper == 'PERCEPCION') $badge_tipo = 'badge-success';
                                            ?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
                                                    <td><?php echo htmlspecialchars($cod); ?></td> 
                                                    <td class="text-center">
                                                        <span class="badge <?php echo $badge_tipo; ?> badge_size">
                                                            <?php echo $tipo_upper; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($nom); ?></td>
                                                    <td class="text-right"><?php echo number_format($porcentaje, 2); ?> %</td>
                                                    <td>
                                                        <center>
                                                            <?php if ($estado == 1) { ?>
                                                                <span class="badge badge-success badge_size">ACTIVO</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-danger badge_size">INACTIVO</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td class="text-center">
                                                        <!-- ============================================ -->
                                                        <!-- BOTÓN EDITAR -->
                                                        <!-- ============================================ -->
                                                        <?php if (!$tiene_permiso_editar) { ?>
                                                            <a href="#" 
                                                               class="btn btn-outline-danger btn-sm disabled"
                                                               title="No tienes permiso para editar detracciones/retenciones/percepciones"
                                                               tabindex="-1" 
                                                               aria-disabled="true">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a class="btn btn-warning btn-sm" 
                                                               href="detraccion_editar.php?id_detraccion=<?php echo $id; ?>" 
                                                               title="Editar registro">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php
                                                }
                                            } else {
                                            ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No hay registros disponibles</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->