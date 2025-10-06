<?php 
//=======================================================================
// VISTA: v_centro_costo_mostrar.php 
//=======================================================================
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Centro de Costos</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <?php if (isset($_GET['registrado'])): ?>
            <script>
                Swal.fire({ icon: 'success', title: 'Centro de Costo registrado correctamente', showConfirmButton: false, timer: 2000 });
            </script>
        <?php elseif (isset($_GET['actualizado'])): ?>
            <script>
                Swal.fire({ icon: 'success', title: 'Centro de Costo actualizado correctamente', showConfirmButton: false, timer: 2000 });
            </script>
        <?php elseif (isset($_GET['error'])): ?>
            <script>
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo completar la acción', showConfirmButton: true });
            </script>
        <?php endif; ?>

        <div class="x_panel">
            <div class="x_title">
                <div class="row">
                    <div class="col-sm-10">
                        <h2>Listado de Centros de Costos</h2>
                    </div>
                    <div class="col-sm-2">
                        <?php if (verificarPermisoEspecifico('crear_centro_costo')): ?>
                            <a href="centro_costo_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nuevo Centro</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="x_content">
                <div class="card-box table-responsive">
                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Editar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $c = 0;
                            if (!empty($centros)) {
                                foreach ($centros as $value) {
                                    $c++;
                                    $id = $value['id_centro_costo'];
                                    $nom = $value['nom_centro_costo'];
                                    $estado = $value['est_centro_costo'];
                            ?>
                                <tr>
                                    <td><?php echo $c; ?></td>
                                    <td><?php echo $nom; ?></td>
                                    <td class="text-center">
                                        <?php if (verificarPermisoEspecifico('editar_centro_costo')): ?>
                                            <button 
                                                class="btn btn-sm <?php echo ($estado == 1) ? 'btn-success' : 'btn-secondary'; ?>" 
                                                onclick="cambiarEstado(<?php echo $id; ?>, <?php echo $estado; ?>)">
                                                <?php echo ($estado == 1) ? 'Activo' : 'Inactivo'; ?>
                                            </button>
                                        <?php else: ?>
                                            <span class="badge badge-size <?php echo ($estado == 1) ? 'badge-success' : 'badge-secondary'; ?>">
                                                <?php echo ($estado == 1) ? 'Activo' : 'Inactivo'; ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (verificarPermisoEspecifico('editar_centro_costo')): ?>
                                            <a class="btn btn-warning btn-sm" href="centro_costo_editar.php?id_centro_costo=<?php echo $id; ?>">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="4" class="text-center">No hay registros disponibles</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para cambiar estado -->
<script>
function cambiarEstado(id, estadoActual) {
    let nuevoEstado = (estadoActual === 1) ? 0 : 1;
    let accion = (estadoActual === 1) ? 'desactivar' : 'activar';
    
    Swal.fire({
        title: `¿Deseas ${accion} este Centro de Costo?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, ' + accion
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `centro_costo_mostrar.php?accion=estado&id_centro_costo=${id}&estado=${nuevoEstado}`;
        }
    });
}
</script>
