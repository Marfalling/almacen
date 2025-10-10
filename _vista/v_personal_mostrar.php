<?php 
//=======================================================================
// VISTA: v_personal_mostrar.php
// Descripción: Muestra el listado del personal desde la BD arceperucomplemento
//=======================================================================

require_once("../_conexion/conexion_complemento.php");
require_once("../_modelo/m_personal.php");

// Obtenemos la lista del personal
$personal = MostrarPersonal();

?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Personal <small></small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <!-- --------------------------------------- -->
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-10">
                                <h2>Listado de Personal</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                                <a href="personal_nuevo.php" class="btn btn-outline-info btn-sm btn-block">Nuevo Personal</a> 
                            </div>
                        </div>
                    </div>

                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>DNI</th>
                                                <th>Nombres</th>
                                                <th>Área</th>
                                                <th>Cargo</th>
                                                <th>Email</th>
                                                <th>Teléfono</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            if (!empty($personal)) {
                                                $c = 0;
                                                foreach ($personal as $value) {
                                                    $c++;
                                                    $id_personal   = $value['id_personal'];
                                                    $nom_personal  = $value['nom_personal'];
                                                    $dni_personal  = $value['dni_personal'];
                                                    $email_personal = $value['email_personal'];
                                                    $cel_personal  = $value['cel_personal'];
                                                    $nom_area      = $value['nom_area'] ?? '-';
                                                    $nom_cargo     = $value['nom_cargo'] ?? '-';
                                                    $act_personal  = $value['act_personal'];

                                                    $estado = ($act_personal == 1) ? "ACTIVO" : "INACTIVO";
                                            ?>
                                                    <tr>
                                                        <td><?php echo $c; ?></td>
                                                        <td><?php echo htmlspecialchars($dni_personal); ?></td>
                                                        <td><?php echo htmlspecialchars($nom_personal); ?></td>
                                                        <td><?php echo htmlspecialchars($nom_area); ?></td>
                                                        <td><?php echo htmlspecialchars($nom_cargo); ?></td>
                                                        <td><?php echo htmlspecialchars($email_personal); ?></td>
                                                        <td><?php echo htmlspecialchars($cel_personal); ?></td>
                                                        <td>
                                                            <?php if ($act_personal == 1) { ?>
                                                                <span class="badge badge-success"><?php echo $estado; ?></span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-danger"><?php echo $estado; ?></span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <a class="btn btn-warning btn-xs" href="personal_editar.php?id_personal=<?php echo $id_personal; ?>">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                            <?php
                                                }
                                            } else {
                                                echo '<tr><td colspan="10" class="text-center text-muted">No hay registros de personal</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- --------------------------------------- -->
        </div>
    </div>
</div>
<!-- /page content -->
