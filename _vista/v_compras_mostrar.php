<?php 
//=======================================================================
// VISTA: v_compras_mostrar.php (Versión fusionada)
//=======================================================================

//=======================================================================
// Cargar bancos y monedas activas para los select del modal
//=======================================================================
require_once("../_modelo/m_banco.php");
require_once("../_modelo/m_moneda.php");

$bancos = MostrarBanco(); 
$monedas = MostrarMoneda();
?>

<script>
// Aprobar por el área técnica
/*function AprobarCompraTecnica(id_compra) {
    Swal.fire({
        title: '¿Deseas aprobar técnicamente esta compra?',
        text: "Esta acción no se puede deshacer.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'compras_aprobar_tecnica.php',
                type: 'POST',
                data: { id_compra: id_compra },
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire('¡Aprobado!', response.mensaje, 'success')
                        .then(() => { location.reload(); });
                    } else {
                        Swal.fire('Error', response.mensaje, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        }
    });
}*/

// Aprobar por el área financiera
function AprobarCompraFinanciera(id_compra) {
    Swal.fire({
        title: '¿Deseas aprobar financieramente esta compra?',
        text: "Esta acción no se puede deshacer.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'compras_aprobar_financiera.php',
                type: 'POST',
                data: { id_compra: id_compra },
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire('¡Aprobado!', response.mensaje, 'success')
                        .then(() => { location.reload(); });
                    } else {
                        Swal.fire('Error', response.mensaje, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        }
    });
}

//==============================================
//ANTIGUA FUNCION ANULACION OC Y PEDIDO
//==============================================

/*function AnularCompra(id_compra, id_pedido) {
    Swal.fire({
        title: '¿Qué deseas anular?',
        text: "Selecciona una opción:",
        icon: 'warning',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonColor: '#d33',
        denyButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Solo O/C',
        denyButtonText: 'O/C y Pedido',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Anular solo la orden de compra
            $.ajax({
                url: 'compras_anular.php',
                type: 'POST',
                data: { id_compra: id_compra },
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire('¡Anulado!', response.mensaje, 'success')
                        .then(() => { location.reload(); });
                    } else {
                        Swal.fire('Error', response.mensaje, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        } else if (result.isDenied) {
            // Anular la orden y también el pedido
            $.ajax({
                url: 'compras_pedido_anular.php',
                type: 'POST',
                data: { id_compra: id_compra, id_pedido: id_pedido },
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire('¡Anulado!', response.mensaje, 'success')
                        .then(() => { location.reload(); });
                    } else {
                        Swal.fire('Error', response.mensaje, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        }
    });
}*/

//==============================================
//NUEVA FUNCION ANULACION OC Y PEDIDO
//==============================================

function AnularCompra(id_compra, id_pedido) {
    // Primero validar si el pedido tiene otras OC o salidas
    $.ajax({
        url: 'compras_validar_anulacion.php',
        type: 'POST',
        data: { 
            id_compra: id_compra, 
            id_pedido: id_pedido 
        },
        dataType: 'json',
        success: function(validacion) {
                
            if (validacion.error) {
                Swal.fire('Error', validacion.mensaje, 'error');
                return;
            }

            // CASO 1: Pedido tiene otras OC o salidas → SOLO ANULAR OC
            if (validacion.tiene_otras_oc || validacion.tiene_salidas) {
                    
                let mensaje_restriccion = "No se puede anular el pedido completo porque:\n\n";
                    
                if (validacion.tiene_otras_oc) {
                    mensaje_restriccion += `• Tiene ${validacion.total_otras_oc} orden(es) de compra adicional(es)\n`;
                }
                    
                if (validacion.tiene_salidas) {
                    mensaje_restriccion += `• Tiene ${validacion.total_salidas} orden(es) de salida registrada(s)\n`;
                }
                    
                mensaje_restriccion += "\n¿Deseas anular solo esta Orden de Compra?";

                Swal.fire({
                    title: '¿Seguro que deseas anular esta O/C?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, anular O/C',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        anularSoloOC(id_compra);
                    }
                });

            } 
            // CASO 2: Pedido sin restricciones → MOSTRAR AMBAS OPCIONES
            else {
                    
                Swal.fire({
                    title: '¿Qué deseas anular?',
                    text: "Selecciona una opción:",
                    icon: 'warning',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonColor: '#d33',
                    denyButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Solo O/C',
                    denyButtonText: 'O/C y Pedido',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        anularSoloOC(id_compra);
                    } else if (result.isDenied) {
                        anularOCyPedido(id_compra, id_pedido);
                    }
                });
            }
        },
        error: function() {
            Swal.fire('Error', 'No se pudo validar la anulación. Intente nuevamente.', 'error');
        }
    });
}

// Anular solo OC
function anularSoloOC(id_compra) {
    $.ajax({
        url: 'compras_anular.php',
        type: 'POST',
        data: { id_compra: id_compra },
        dataType: 'json',
        success: function(response) {
            if (response.tipo_mensaje === 'success') {
                Swal.fire('¡Anulado!', response.mensaje, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.mensaje, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
        }
    });
}

// Anular OC y Pedido
function anularOCyPedido(id_compra, id_pedido) {
    $.ajax({
        url: 'compras_pedido_anular.php',
        type: 'POST',
        data: { 
            id_compra: id_compra, 
            id_pedido: id_pedido 
        },
        dataType: 'json',
        success: function(response) {
            if (response.tipo_mensaje === 'success') {
                Swal.fire('¡Anulado!', response.mensaje, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.mensaje, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
        }
    });
}

function SubirDocumento(id_compra) {
    Swal.fire({
        title: 'Subir documento',
        html: '<input type="file" id="documento" class="swal2-file">',
        showCancelButton: true,
        confirmButtonText: 'Subir'
    }).then((result) => {
        if (result.isConfirmed) {
            const archivo = document.getElementById('documento').files[0];
            if (!archivo) {
                Swal.fire('Error', 'Debes seleccionar un archivo', 'error');
                return;
            }

            let formData = new FormData();
            formData.append("entidad", "compras");
            formData.append("id_entidad", id_compra);
            formData.append("documento", archivo);

            $.ajax({
                url: 'compras_subir_documentos.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire('¡Éxito!', response.mensaje, 'success')
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Error', response.mensaje, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        }
    });
}

function EliminarDocumento(id_doc) {
    Swal.fire({
        title: '¿Eliminar documento?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'compras_eliminar_documento.php',
                type: 'POST',
                data: { id_doc: id_doc },
                dataType: 'json',
                success: function(response) {
                    if (response.tipo_mensaje === 'success') {
                        Swal.fire('Eliminado', response.mensaje, 'success')
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Error', response.mensaje, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        }
    });
}
</script>

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Compras</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <h2>Listado de Compras</h2>
                            </div>
                            <div class="col-sm-8 text-right">
                                <a href="generar_excel.php" class="btn btn-success btn-sm">
                                    <i class="fa fa-file-excel-o"></i> Excel General
                                </a>
                                <a href="generar_excel.php?moneda=1" class="btn btn-success btn-sm">
                                    <i class="fa fa-file-excel-o"></i> Pendiente Soles
                                </a>
                                <a href="generar_excel.php?moneda=2" class="btn btn-success btn-sm">
                                    <i class="fa fa-file-excel-o"></i> Pendiente Dólares
                                </a>
                                <button type="button" class="btn btn-primary btn-sm" onclick="abrirModalMasivo()">
                                    <i class="fa fa-cloud-upload"></i> Vouchers Masivo
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="x_content">
                        <!-- Filtro de fechas -->
                        <form method="get" action="compras_mostrar.php" class="form-inline mb-3">
                            <div class="form-group mr-3">
                                <label for="fecha_inicio" class="mr-2 font-weight-bold">Desde:</label>
                                <input type="date" id="fecha_inicio" name="fecha_inicio"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                            </div>

                            <div class="form-group mr-3">
                                <label for="fecha_fin" class="mr-2 font-weight-bold">Hasta:</label>
                                <input type="date" id="fecha_fin" name="fecha_fin"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($fecha_fin); ?>">
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Consultar
                            </button>
                       </form>

                        <!-- Auto-abrir modal desde URL -->
                        <?php if (isset($_GET['abrir_modal']) && !empty($_GET['abrir_modal'])): ?>
                        <script>
                        window.addEventListener('load', function() {
                            // Esperar a que jQuery esté disponible
                            (function esperarTodo() {
                                if (typeof jQuery === 'undefined' || typeof abrirModalEditarOrden === 'undefined') {
                                    setTimeout(esperarTodo, 200);
                                    return;
                                }
                                
                                const idCompraAbrir = <?php echo intval($_GET['abrir_modal']); ?>;
                                console.log('=== TODO LISTO - Abriendo modal para orden:', idCompraAbrir, '===');
                                
                                setTimeout(function() {
                                    abrirModalEditarOrden(idCompraAbrir);
                                    
                                    // Limpiar URL sin usar jQuery
                                    setTimeout(function() {
                                        const url = new URL(window.location);
                                        url.searchParams.delete('abrir_modal');
                                        window.history.replaceState({}, document.title, url.pathname + (url.search || ''));
                                    }, 1000);
                                }, 1000);
                            })();
                        });
                        </script>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-sm-12">

                                

                                <div class="card-box table-responsive">
                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Código Orden</th>
                                                <th>Código Pedido</th>
                                                <th>Proveedor</th>
                                                <th>Fecha Registro</th>
                                                <th>Tipo Pago</th>
                                                <th>Registrado Por</th>
                                                <!--<th>Aprob. Técnica Por</th>-->
                                                <th>Aprob. Financiera Por</th>
                                                <th>Estado</th>
                                                <th>Docs</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php 
                                            $contador = 1;
                                            foreach($compras as $compra) { 
                                                $tiene_tecnica = !empty($compra['id_personal_aprueba_tecnica']);
                                                $tiene_financiera = !empty($compra['id_personal_aprueba_financiera']);
                                                
                                                // Determinar si es al contado o crédito
                                                $es_contado = empty($compra['plaz_compra']) || $compra['plaz_compra'] == 0;
                                                
                                                // Calcular clase para sombrado SOLO si es crédito y está activa
                                                $clase_fila = '';
                                                if (!$es_contado && $compra['est_compra'] == 1) {
                                                    $fecha_vencimiento = date('Y-m-d', strtotime($compra['fec_compra'] . ' + ' . $compra['plaz_compra'] . ' days'));
                                                    $dias_restantes = (strtotime($fecha_vencimiento) - strtotime(date('Y-m-d'))) / 86400;
                                                    
                                                    if ($dias_restantes <= 0) {
                                                        $clase_fila = 'table-danger'; // Vencido o vence hoy
                                                    } elseif ($dias_restantes <= 3) {
                                                        $clase_fila = 'table-warning'; // Por vencer (3 días o menos)
                                                    }
                                                }
                                            ?>
                                                <tr class="<?php echo $clase_fila; ?>">
                                                    <td><?php echo $contador; ?></td>
                                                    <td><?php echo 'C00' . $compra['id_compra']; ?></td>
                                                    <td>
                                                        <a class="btn btn-sm btn-outline-secondary" target="_blank" 
                                                        href="pedido_pdf.php?id=<?php echo $compra['id_pedido']; ?>">
                                                            <?php echo $compra['cod_pedido']; ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo $compra['nom_proveedor']; ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($compra['fecha_reg_compra'])); ?></td>
                                                    
                                                    <!-- COLUMNA TIPO DE PAGO -->
                                                    <td class="text-center">
                                                        <?php if ($es_contado) { ?>
                                                            <span class="badge badge-success">Contado</span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-info">Crédito (<?php echo $compra['plaz_compra']; ?> días)</span>
                                                            <?php
                                                            // Mostrar fecha de vencimiento si NO está anulada
                                                            if ($compra['est_compra'] != 0) {
                                                                $fecha_vencimiento = date('d/m/Y', strtotime($compra['fec_compra'] . ' + ' . $compra['plaz_compra'] . ' days'));
                                                                $dias_restantes = (strtotime($compra['fec_compra'] . ' + ' . $compra['plaz_compra'] . ' days') - strtotime(date('Y-m-d'))) / 86400;
                                                                
                                                                // Agregar color según urgencia
                                                                $clase_vencimiento = '';
                                                                if ($dias_restantes <= 0) {
                                                                    $clase_vencimiento = 'text-danger font-weight-bold';
                                                                } elseif ($dias_restantes <= 3) {
                                                                    $clase_vencimiento = 'text-warning font-weight-bold';
                                                                }
                                                                
                                                                echo '<br><small class="' . $clase_vencimiento . ' text-muted">Vence: ' . $fecha_vencimiento . '</small>';
                                                            }
                                                            ?>
                                                        <?php } ?>
                                                    </td>
                                                    
                                                    <td><?php echo $compra['nom_registrado']; ?></td>
                                                    <!--<td>
                                                        <?php 
                                                        if ($tiene_tecnica) {
                                                            echo $compra['nom_aprobado_tecnica'];
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>-->
                                                    <td>
                                                        <?php 
                                                        if ($tiene_financiera) {
                                                            echo $compra['nom_aprobado_financiera'];
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>

                                                    <?php
                                                    $estado_final = '';
                                                    $badge_class = '';

                                                    if ($compra['est_compra'] == 2 && $compra['pagado'] == 1) {
                                                        $estado_final = 'PAGADO';
                                                        $badge_class = 'badge-primary';

                                                    } elseif ($compra['est_compra'] == 3 && $compra['pagado'] == 1) {
                                                        $estado_final = 'CERRADO';
                                                        $badge_class = 'badge-dark';

                                                    } elseif ($compra['est_compra'] == 2) {
                                                        $estado_final = 'APROBADO';
                                                        $badge_class = 'badge-info';

                                                    } elseif ($compra['est_compra'] == 3) {
                                                        $estado_final = 'INGRESADO';
                                                        $badge_class = 'badge-success';

                                                    } elseif ($compra['est_compra'] == 1) {

                                                        if ($tiene_financiera && !$tiene_tecnica) {
                                                            $estado_final = 'APROBADO FINANCIERA';
                                                            $badge_class = 'badge-info';

                                                        } elseif ($tiene_tecnica && !$tiene_financiera) {
                                                            $estado_final = 'APROBADO TÉCNICO';
                                                            $badge_class = 'badge-info';

                                                        } else {
                                                            $estado_final = 'PENDIENTE';
                                                            $badge_class = 'badge-warning';
                                                        }

                                                    } else {
                                                        $estado_final = 'ANULADO';
                                                        $badge_class = 'badge-danger';
                                                    }
                                                    ?>
                                                    <td>
                                                        <center>
                                                            <span class="badge <?php echo $badge_class; ?> badge_size">
                                                                <?php echo $estado_final; ?>
                                                            </span>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <?php if ($compra['est_compra'] == 0) { ?>
                                                            <button type="button" 
                                                                    class="btn btn-sm" 
                                                                    style="background-color:#b0b0b0; color:white; border:none; cursor:default;"
                                                                    title="Bloqueado"
                                                                    disabled>
                                                                <i class="fa fa-folder-open"></i>
                                                            </button>
                                                        <?php } else { ?>
                                                            <button type="button" 
                                                                    class="btn btn-info btn-sm" 
                                                                    data-toggle="modal" 
                                                                    data-target="#modalDocumentos<?php echo $compra['id_compra']; ?>">
                                                                <i class="fa fa-folder-open"></i>
                                                            </button>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <?php 
                                                            // Verificar si tiene aprobaciones
                                                            //$tiene_aprobacion = !empty($compra['id_personal_aprueba_tecnica']) || !empty($compra['id_personal_aprueba_financiera']);
                                                            $tiene_aprobacion = !empty($compra['id_personal_aprueba_financiera']);

                                                                ?>
                                                                <button class="btn btn-info btn-sm btn-ver-detalle-compra"
                                                                        data-toggle="tooltip" 
                                                                        title="Ver Detalles de la Orden"
                                                                        data-id-compra="<?php echo $compra['id_compra']; ?>">
                                                                    <i class="fa fa-eye"></i>
                                                                </button>
                                                                
                                                                <?php
                                                                // LÓGICA CORREGIDA: Solo cuando está PENDIENTE (1) se permiten acciones
                                                                if ($compra['est_compra'] == 1) { 
                                                                ?>
                                                                <!-- Compra PENDIENTE: botones habilitados según aprobaciones -->
                                                                <!--<a href="#"
                                                                <?php if ($tiene_tecnica) { ?>
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    title="Ya aprobado técnica"
                                                                    tabindex="-1" aria-disabled="true"
                                                                <?php } else { ?>
                                                                    onclick="AprobarCompraTecnica(<?php echo $compra['id_compra']; ?>)"
                                                                    class="btn btn-success btn-sm"
                                                                    title="Aprobar Técnica"
                                                                <?php } ?>>
                                                                    <i class="fa fa-check"></i>
                                                                </a>-->

                                                                <a href="#"
                                                                <?php if ($tiene_financiera) { ?>
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    title="Ya aprobado financiera"
                                                                    data-toggle="tooltip"
                                                                    tabindex="-1" aria-disabled="true"
                                                                <?php } else { ?>
                                                                    onclick="AprobarCompraFinanciera(<?php echo $compra['id_compra']; ?>)"
                                                                    class="btn btn-primary btn-sm"
                                                                    data-toggle="tooltip"
                                                                    title="Aprobar Financiera"
                                                                <?php } ?>>
                                                                    <i class="fa fa-check"></i>
                                                                </a>

                                                                <!-- Botón anular - DESHABILITAR SI TIENE APROBACIÓN -->
                                                                <?php if ($tiene_aprobacion) { ?>
                                                                    <a href="#"
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    title="No se puede anular: tiene aprobación iniciada"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-times"></i>
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <a href="#"
                                                                    onclick="AnularCompra(<?php echo $compra['id_compra']; ?>, <?php echo $compra['id_pedido']; ?>)"
                                                                    class="btn btn-danger btn-sm"
                                                                    data-toggle="tooltip"
                                                                    title="Anular">
                                                                        <i class="fa fa-times"></i>
                                                                    </a>
                                                                <?php } ?>

                                                                <!-- PDF -->
                                                                <a href="compras_pdf.php?id=<?php echo $compra['id_compra']; ?>"
                                                                class="btn btn-secondary btn-sm"
                                                                data-toggle="tooltip"
                                                                title="Generar PDF"
                                                                target="_blank">
                                                                    <i class="fa fa-file-pdf-o"></i>
                                                                </a>

                                                                <!-- Botón Editar - SOLO SI NO TIENE APROBACIONES -->
                                                                <?php if (!$tiene_aprobacion) { ?>
                                                                    <a href="#" 
                                                                    class="btn btn-warning btn-sm"
                                                                    data-toggle="tooltip"
                                                                    onclick="abrirModalEditarOrden(<?php echo $compra['id_compra']; ?>)"
                                                                    title="Editar Orden">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <a href="#" 
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    title="No se puede editar - Tiene aprobación iniciada"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                <?php } ?>

                                                                <!-- Botón Pagos -->
                                                                <?php if ($tiene_financiera) { ?>
                                                                    <a href="comprobante_registrar.php?id_compra=<?php echo $compra['id_compra']; ?>"
                                                                    class="btn btn-warning btn-sm"
                                                                    data-toggle="tooltip"
                                                                    title="Registrar/Ver Pagos">
                                                                        <i class="fa fa-money"></i>
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <a href="#"
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    title="Requiere aprobación financiera"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-money"></i>
                                                                    </a>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <!-- Compra NO pendiente (anulada, aprobada o pagada): todos deshabilitados -->
                                                                <!--<a href="#" class="btn btn-outline-secondary btn-sm disabled" title="Aprobar Técnica" tabindex="-1" aria-disabled="true">
                                                                    <i class="fa fa-check"></i>
                                                                </a>-->
                                                                <a href="#" class="btn btn-outline-secondary btn-sm disabled" title="Aprobar Financiera" tabindex="-1" aria-disabled="true">
                                                                    <i class="fa fa-check"></i>
                                                                </a>

                                                                <!-- Botón anular -->
                                                                <a href="#" class="btn btn-outline-secondary btn-sm disabled"
                                                                title="<?php 
                                                                        if ($compra['est_compra'] == 0) echo 'Ya anulada'; 
                                                                        elseif ($compra['est_compra'] == 4) echo 'No se puede anular: compra pagada'; 
                                                                        else echo 'No se puede anular: compra aprobada'; 
                                                                ?>"
                                                                tabindex="-1" aria-disabled="true">
                                                                    <i class="fa fa-times"></i>
                                                                </a>

                                                                <!-- PDF -->
                                                                <a href="compras_pdf.php?id=<?php echo $compra['id_compra']; ?>"
                                                                class="btn btn-secondary btn-sm"
                                                                title="Generar PDF"
                                                                target="_blank">
                                                                    <i class="fa fa-file-pdf-o"></i>
                                                                </a>

                                                                <!-- Botón Editar - DESHABILITADO -->
                                                                <a href="#" 
                                                                class="btn btn-outline-secondary btn-sm disabled"
                                                                title="<?php 
                                                                    if ($compra['est_compra'] == 0) echo 'No se puede editar - Orden anulada';
                                                                    elseif ($compra['est_compra'] == 4) echo 'No se puede editar - Orden pagada';
                                                                    else echo 'No se puede editar - Orden aprobada';
                                                                ?>"
                                                                tabindex="-1" aria-disabled="true">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>

                                                                <!-- Botón Pagos -->
                                                                <?php if ($compra['est_compra'] == 2 || $compra['est_compra'] == 3) { ?>
                                                                    <a href="comprobante_registrar.php?id_compra=<?php echo $compra['id_compra']; ?>"
                                                                    class="btn btn-warning btn-sm"
                                                                    title="Registrar/Ver Pagos">
                                                                        <i class="fa fa-money"></i>
                                                                    </a>
                                                                <?php } elseif ($compra['est_compra'] == 4) { ?>
                                                                    <!--debe poder verse la vista pero no registrar nuevos pagos-->
                                                                    <a href="comprobante_registrar.php?id_compra=<?php echo $compra['id_compra']; ?>"
                                                                    class="btn btn-warning btn-sm"
                                                                    title="Compra pagada"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-money"></i>
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <a href="#"
                                                                    class="btn btn-outline-secondary btn-sm disabled"
                                                                    title="No disponible"
                                                                    tabindex="-1" aria-disabled="true">
                                                                        <i class="fa fa-money"></i>
                                                                    </a>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php 
                                                $contador++;
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
        </div>
    </div>
</div>
<!-- /page content -->

<!-- Modales de documentos -->
<?php foreach($compras as $compra) { 
    $documentos = MostrarDocumentos('compras', $compra['id_compra']); ?>
<div class="modal fade" id="modalDocumentos<?php echo $compra['id_compra']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalDocumentosLabel<?php echo $compra['id_compra']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Documentos de la Compra #<?php echo $compra['id_compra']; ?></h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($documentos)) { ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Documento</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach($documentos as $doc) { ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><a href="../uploads/compras/<?php echo $doc['documento']; ?>" target="_blank"><i class="fa fa-file"></i> <?php echo $doc['documento']; ?></a></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($doc['fec_subida'])); ?></td>
                                <td>
                                    <?php if ($compra['est_compra'] != 0) { ?>
                                        <button class="btn btn-sm btn-outline-danger" onclick="EliminarDocumento(<?php echo $doc['id_doc']; ?>)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    <?php } else { ?>
                                        <span class="text-muted">Bloqueado</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="alert alert-info"><i class="fa fa-info-circle"></i> No hay documentos registrados.</div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <?php if ($compra['est_compra'] != 0) { ?>
                    <button class="btn btn-primary" onclick="SubirDocumento(<?php echo $compra['id_compra']; ?>)">
                        <i class="fa fa-upload"></i> Subir Documento
                    </button>
                <?php } ?>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<!-- MODAL SIMPLE -->
<div id="modalSubidaMasivo" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.5); z-index:1040; justify-content:center; align-items:center;">
    
    <div style="background:white; width:85%; max-width:950px; border-radius:10px; 
                display:flex; flex-direction:column; max-height:90vh; overflow:hidden;">

        <!-- CABECERA -->
        <div style="background:#667eea; color:white; padding:10px 20px; 
                    display:flex; justify-content:space-between; align-items:center;">
            <h5 style="margin:0;">Subida Masiva de Vouchers</h5>
            <button onclick="cerrarModalMasivo()" 
                    style="background:none; border:none; color:white; font-size:20px;">&times;</button>
        </div>

        <!-- CONTENIDO SCROLLEABLE -->
        <div style="padding:20px; overflow-y:auto; flex:1;">

            <!-- CONTENEDOR PRINCIPAL -->
            <div style="display:flex; flex-wrap:wrap; gap:20px; align-items:stretch;">
                
                <!-- INSTRUCCIONES -->
                <div style="flex:1; min-width:300px; background:#f1f3f5; border-radius:8px; padding:15px;">
                    <strong>Instrucciones:</strong>
                    <ol style="margin:5px 0 0 20px; font-size:14px; color:#333;">
                        <li>Archivos deben llamarse como la serie-número del comprobante</li>
                        <li>Ej: <code>F001-1234.pdf</code>, <code>B002-5678.jpg</code></li>
                        <li>Formatos: PDF, JPG, JPEG, PNG</li>
                        <li>Máx. 5MB por archivo</li>
                    </ol>
                </div>

                <!-- ZONA DE CARGA -->
                <div style="flex:1; min-width:300px; display:flex; align-items:center; justify-content:center;">
                    <div id="dropZone" onclick="document.getElementById('inputArchivos').click()"
                        style="border:2px dashed #667eea; border-radius:8px; padding:25px; text-align:center;
                               background:#f9f9fc; cursor:pointer; transition:0.3s; width:100%;">
                        <i class="fa fa-cloud-upload" style="font-size:40px; color:#667eea; margin-bottom:8px;"></i>
                        <h5 style="color:#667eea; font-size:16px; margin-bottom:5px;">Haz clic para seleccionar archivos</h5>
                        <p style="color:#6c757d; font-size:13px; margin:0;">(Puedes seleccionar varios a la vez)</p>
                        <input type="file" id="inputArchivos" multiple style="display:none" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>
            </div>

            <!-- LISTA CON SCROLL -->
            <div id="listaArchivos" 
                 style="margin-top:25px; max-height:250px; overflow-y:auto; border:1px solid #e0e0e0; border-radius:6px; padding:10px;">
            </div>

            <!-- OPCIONES DE CORREO -->
            <div style="margin-top:20px; background:#f8f9fa; padding:15px; border-radius:8px;">
                <h6><i class="fa fa-envelope"></i> Notificaciones:</h6>
                <label><input type="checkbox" id="enviarProveedor" checked> Enviar al Proveedor</label><br>
                <label><input type="checkbox" id="enviarContabilidad" checked> Enviar a Contabilidad</label><br>
                <label><input type="checkbox" id="enviarTesoreria" checked> Enviar a Tesorería</label>
            </div>
        </div>

        <!-- PIE FIJO -->
        <div style="padding:10px 20px; text-align:right; border-top:1px solid #ddd; background:#fff;">
            <button class="btn btn-secondary" onclick="cerrarModalMasivo()">Cancelar</button>
            <button class="btn btn-primary" id="btnProcesarMasivo" onclick="procesarArchivos()">Procesar</button>
        </div>
    </div>
</div>

<!-- Modal Editar Orden de Compra -->
<div class="modal fade" id="modalEditarOrden" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #fff3cd; padding: 10px 15px;">
                <h5 class="modal-title">
                    <i class="fa fa-edit text-warning"></i> 
                    Editar Orden de Compra <span id="orden-numero"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto; padding: 20px;">
                <div id="loading-editar" class="text-center" style="padding: 40px;">
                    <i class="fa fa-spinner fa-spin fa-3x text-warning"></i>
                    <p class="mt-2">Cargando datos de la orden...</p>
                </div>
                
                <div id="contenido-editar-orden" style="display: none;">
                    <form id="form-editar-orden-modal">
                        <input type="hidden" name="id_compra" id="edit_id_compra">
                        <input type="hidden" name="actualizar_orden_modal" value="1">
                        
                        <!-- Información General -->
                        <div class="card mb-3">
                            <div class="card-header" style="background-color: #e3f2fd; padding: 8px 12px;">
                                <h6 class="mb-0">
                                    <i class="fa fa-info-circle text-primary"></i> 
                                    Información General
                                </h6>
                            </div>
                            <div class="card-body" style="padding: 12px;">
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label style="font-size: 11px; font-weight: bold;">Fecha: <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control form-control-sm" name="fecha_orden" id="edit_fecha_orden" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="mb-1" style="font-size:11px;font-weight:bold;">
                                            Proveedor: <span class="text-danger">*</span>
                                        </label>

                                        <div class="d-flex align-items-center">
                                            <select id="edit_proveedor_orden" name="proveedor_orden"
                                                    class="form-control form-control-sm flex-grow-1"
                                                    style="font-size:12px;" required>
                                                <option value="">Seleccionar proveedor...</option>
                                            </select>

                                            <button type="button"
                                                    class="btn btn-info btn-sm ml-1"
                                                    id="btn-agregar-proveedor-modal-editar"
                                                    title="Agregar Proveedor"
                                                    style="padding: 4px 8px;">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label style="font-size: 11px; font-weight: bold;">Moneda: <span class="text-danger">*</span></label>
                                        <select class="form-control form-control-sm" name="moneda_orden" id="edit_moneda_orden" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="1">Soles (S/.)</option>
                                            <option value="2">Dólares (US$)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label style="font-size: 11px; font-weight: bold;">Plazo de Entrega (días):</label>
                                        <input type="number" class="form-control form-control-sm" name="plazo_entrega" id="edit_plazo_entrega" min="0" placeholder="0 = Contado">
                                        <small class="text-muted">Dejar vacío o 0 para contado</small>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label style="font-size: 11px; font-weight: bold;">Dirección de Envío:</label>
                                        <textarea class="form-control form-control-sm" name="direccion_envio" id="edit_direccion_envio" rows="2" style="resize: none;"></textarea>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label style="font-size: 11px; font-weight: bold;">Observaciones:</label>
                                        <textarea class="form-control form-control-sm" name="observaciones_orden" id="edit_observaciones_orden" rows="2" style="resize: none;"></textarea>
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label style="font-size: 11px; font-weight: bold;">Tipo de Porte:</label>
                                        <input type="text" class="form-control form-control-sm" name="tipo_porte" id="edit_tipo_porte">
                                    </div>
                                </div>
                                
                                <!-- SECCIÓN DE DETRACCIÓN, RETENCIÓN Y PERCEPCIÓN -->
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <div class="card" style="border: 1px solid #dee2e6;">
                                            <div class="card-header" style="background-color: #f8f9fa; padding: 8px 12px;">
                                                <h6 class="mb-0" style="font-size: 13px;">
                                                    <i class="fa fa-percent text-info"></i> Detracción, Retención y Percepción (Opcional)
                                                </h6>
                                            </div>
                                            <div class="card-body" style="padding: 12px;" id="edit_contenedor_detracciones">
                                                <!-- Se cargará dinámicamente -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                        <!-- Items de la orden -->
                        <div class="card">
                            <div class="card-header" style="background-color: #e8f5e8; padding: 8px 12px;">
                                <h6 class="mb-0">
                                    <i class="fa fa-list-alt text-success"></i> 
                                    Productos de la Orden
                                </h6>
                            </div>
                            <div class="card-body" style="padding: 12px;" id="edit_items_container">
                                <!-- Se cargará dinámicamente -->
                            </div>
                        </div>
                        
                        <!-- Total -->
                        <div id="edit_total_orden" class="mt-3">
                            <!-- Se calculará dinámicamente -->
                        </div>
                    </form>
                </div>
                
                <div id="error-editar-orden" style="display: none;" class="text-center">
                    <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="text-warning">Error al cargar datos</h5>
                    <p class="text-muted">No se pudieron cargar los datos de la orden.</p>
                </div>
            </div>
            <div class="modal-footer" style="padding: 15px; background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="btn-guardar-edicion-orden">
                    <i class="fa fa-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Detalles de Orden de Compra -->
<div class="modal fade" id="modalDetalleOrdenCompra" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #f8f9fa; padding: 15px;">
                <h5 class="modal-title" id="modalDetalleOrdenCompraLabel">
                    <i class="fa fa-file-text-o text-primary"></i> 
                    Detalles de Orden de Compra
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <div id="loading-detalle-compra" class="text-center" style="padding: 40px;">
                    <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-2">Cargando detalles...</p>
                </div>
                
                <div id="contenido-detalle-compra-mostrar" style="display: none;">
                    <!-- Contenido del detalle -->
                </div>
                
                <div id="error-detalle-compra-mostrar" style="display: none;" class="text-center">
                    <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="text-warning">Error al cargar detalles</h5>
                    <p class="text-muted">No se pudieron cargar los detalles de la orden.</p>
                </div>
            </div>
            <div class="modal-footer" style="padding: 15px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
                <a id="btn-descargar-pdf-compra" href="#" target="_blank" class="btn btn-primary">
                    <i class="fa fa-file-pdf-o"></i> Descargar PDF
                </a>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA AGREGAR PROVEEDOR (MODAL EDICIÓN) -->
<div class="modal fade" id="modalNuevoProveedorEditar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #2a3f54; padding: 15px;">
                <h5 class="modal-title" style="color: white;">
                    <i class="fa fa-user-plus"></i> 
                    Agregar Nuevo Proveedor
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto; padding: 20px;">
                <form id="form-nuevo-proveedor-modal-editar" class="form-horizontal form-label-left">
                    
                    <!-- Nombre -->
                    <div class="form-group row">
                        <label class="control-label col-md-3 col-sm-3">Nombre <span class="text-danger">*</span>:</label>
                        <div class="col-md-9 col-sm-9">
                            <input type="text" name="nom_proveedor" class="form-control" placeholder="Nombre del proveedor" required>
                        </div>
                    </div>

                    <!-- RUC -->
                    <div class="form-group row">
                        <label class="control-label col-md-3 col-sm-3">RUC <span class="text-danger">*</span>:</label>
                        <div class="col-md-9 col-sm-9">
                            <input type="text" name="ruc_proveedor" class="form-control" placeholder="RUC del proveedor" maxlength="11" pattern="[0-9]{11}" title="Ingrese exactamente 11 dígitos numéricos" required>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="form-group row">
                        <label class="control-label col-md-3 col-sm-3">Dirección <span class="text-danger">*</span>:</label>
                        <div class="col-md-9 col-sm-9">
                            <textarea name="dir_proveedor" class="form-control" rows="3" placeholder="Dirección del proveedor" required></textarea>
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="form-group row">
                        <label class="control-label col-md-3 col-sm-3">Teléfono <span class="text-danger">*</span>:</label>
                        <div class="col-md-9 col-sm-9">
                            <input type="text" name="tel_proveedor" class="form-control" placeholder="Teléfono del proveedor" required>
                        </div>
                    </div>

                    <!-- Contacto -->
                    <div class="form-group row">
                        <label class="control-label col-md-3 col-sm-3">Contacto <span class="text-danger">*</span>:</label>
                        <div class="col-md-9 col-sm-9">
                            <input type="text" name="cont_proveedor" class="form-control" placeholder="Persona de contacto" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group row">
                        <label class="control-label col-md-3 col-sm-3">Email:</label>
                        <div class="col-md-9 col-sm-9">
                            <input type="email" name="email_proveedor" class="form-control" placeholder="Correo electrónico">
                        </div>
                    </div>

                    <!-- Cuentas Bancarias -->
                    <div class="x_panel" style="margin-top: 20px;">
                        <div class="x_title">
                            <h2 style="font-size: 16px;">Cuentas Bancarias (Opcional)</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table-bordered" style="font-size: 12px;">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th>Banco</th>
                                        <th>Moneda</th>
                                        <th>Cuenta Corriente</th>
                                        <th>Cuenta Interbancaria</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-cuentas-modal-editar">
                                    <tr>
                                        <td>
                                            <select name="id_banco[]" class="form-control select2_banco" required>
                                                <option value="">Seleccione un banco</option>
                                                <?php foreach ($bancos as $b) { ?>
                                                    <?php if ($b['est_banco'] == 1) { // Solo bancos activos ?>
                                                        <option value="<?php echo $b['id_banco']; ?>">
                                                            <?php echo $b['cod_banco']; ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="id_moneda[]" class="form-control select2_moneda" required>
                                                <option value="">Seleccione una moneda</option>
                                                <?php foreach ($monedas as $m) { ?>
                                                    <option value="<?php echo $m['id_moneda']; ?>"><?php echo $m['nom_moneda']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="cta_corriente[]" class="form-control" required></td>
                                        <td><input type="text" name="cta_interbancaria[]" class="form-control" required></td>
                                        <td><button type="button" class="btn btn-danger btn-sm eliminar-fila-modal">X</button></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success btn-sm" id="agregarCuentaModal">
                                <i class="fa fa-plus"></i> Agregar Cuenta
                            </button>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer" style="padding: 15px; background-color: #f8f9fa;">
                <button type="button" class="btn btn-outline-danger" data-dismiss="modal">
                    <i></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btn-guardar-proveedor-modal-editar">
                    <i></i> Registrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>

// Variables globales
let archivosSeleccionados = [];
let datosAnalisis = null; // Almacena resultado del análisis

// ============================================================
//  FUNCIONES DE SUBIDA MASIVA
// ============================================================

// INICIALIZAR SOLO UNA VEZ
function inicializarModalMasivo() {
    const inputArchivos = document.getElementById("inputArchivos");
    
    if (!inputArchivos) {
        console.error(" Input de archivos no encontrado");
        return;
    }
    
    inputArchivos.removeEventListener("change", manejarCambioArchivos);
    inputArchivos.addEventListener("change", manejarCambioArchivos);
    
    console.log(" Modal masivo inicializado");
}

function abrirModalMasivo() {
    const modal = document.getElementById("modalSubidaMasivo");
    if (modal) {
        modal.style.display = "flex";
        
        setTimeout(() => {
            inicializarModalMasivo();
        }, 200);
    } else {
        console.error(" Modal no encontrado");
    }
}

function cerrarModalMasivo() {
    const modal = document.getElementById("modalSubidaMasivo");
    if (modal) {
        modal.style.display = "none";
        archivosSeleccionados = [];
        datosAnalisis = null;
        document.getElementById("listaArchivos").innerHTML = "";
    }
}

function manejarCambioArchivos(e) {
    console.log(" ¡¡¡CAMBIO DETECTADO!!!");
    const nuevosArchivos = Array.from(e.target.files);
    if (nuevosArchivos.length === 0) return;

    nuevosArchivos.forEach((archivo) => {
        const nombre = archivo.name.trim();
        const tamañoMB = archivo.size / (1024 * 1024);

        if (tamañoMB > 5) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo demasiado grande',
                text: `El archivo "${nombre}" pesa ${tamañoMB.toFixed(2)} MB. El máximo permitido es 5 MB.`
            });
            return;
        }

        const regexNombre = /^[A-Z0-9]{4}-\d{2,8}\.[A-Za-z0-9]+$/i;
        if (!regexNombre.test(nombre)) {
            Swal.fire({
                icon: 'warning',
                title: 'Formato inválido',
                text: `El archivo "${nombre}" no cumple el formato "SERIE-NUMERO", por ejemplo: "F001-00012345.pdf"`
            });
            return;
        }

        if (!archivosSeleccionados.find(a => a.name === nombre)) {
            archivosSeleccionados.push(archivo);
            console.log(" Archivo agregado:", nombre);
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Archivo duplicado',
                text: `El archivo "${nombre}" ya fue agregado.`
            });
        }
    });

    console.log("Total en memoria:", archivosSeleccionados.length);
    actualizarListaArchivos();
    e.target.value = "";
}

function actualizarListaArchivos() {
    const listaArchivos = document.getElementById("listaArchivos");
    
    if (archivosSeleccionados.length === 0) {
        listaArchivos.innerHTML = "";
        return;
    }

    let html = `
        <h6><i class="fa fa-list"></i> Archivos seleccionados (${archivosSeleccionados.length}):</h6>
        <table class="table table-sm table-bordered" style="margin-top:10px;">
            <thead style="background:#667eea; color:white;">
                <tr>
                    <th width="5%">#</th>
                    <th>Nombre</th>
                    <th width="15%">Tamaño</th>
                    <th width="10%">Acción</th>
                </tr>
            </thead>
            <tbody>
    `;

    archivosSeleccionados.forEach((archivo, index) => {
        html += `
            <tr>
                <td>${index + 1}</td>
                <td><i class="fa fa-file-o"></i> ${archivo.name}</td>
                <td>${(archivo.size / 1024).toFixed(1)} KB</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger" onclick="eliminarArchivo(${index})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    html += `</tbody></table>`;
    listaArchivos.innerHTML = html;
}

function eliminarArchivo(index) {
    archivosSeleccionados.splice(index, 1);
    actualizarListaArchivos();
}

// NUEVO FLUJO: FASE 1 - ANÁLISIS
async function procesarArchivos() {
    if (archivosSeleccionados.length === 0) {
        Swal.fire('Advertencia', 'Selecciona al menos un archivo', 'warning');
        return;
    }

    // 1️⃣ FASE DE ANÁLISIS
    Swal.fire({
        title: 'Analizando archivos...',
        html: '',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    const formData = new FormData();
    archivosSeleccionados.forEach(archivo => {
        formData.append('archivos[]', archivo);
    });

    try {
        const response = await fetch('../_controlador/comprobante_analizar_masivo.php', {
            method: 'POST',
            body: formData
        });

        datosAnalisis = await response.json();
        
        Swal.close();

        if (!datosAnalisis.success) {
            Swal.fire('Error', datosAnalisis.mensaje || 'Error al analizar archivos', 'error');
            return;
        }

        // 2️⃣ ¿HAY CONFLICTOS?
        if (datosAnalisis.conflictos.length > 0) {
            //await mostrarResumenAnalisis();
            await resolverConflictos();
        } else {
            // 3️⃣ NO HAY CONFLICTOS → REGISTRAR DIRECTAMENTE
            await registrarArchivosMasivo();
        }

    } catch (error) {
        Swal.close();
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
    }
}

// NUEVO: Mostrar resumen del análisis
/*async function mostrarResumenAnalisis() {
    const { correctos, conflictos, errores } = datosAnalisis;

    return Swal.fire({
        icon: 'info',
        title: 'Análisis completado',
        html: `
            <div style="text-align:center; margin:12px 0;">
                <span class="badge badge-success" style="font-size:13px; padding:6px 12px;">
                    ✅ ${correctos.length} correctos
                </span>
                <span class="badge badge-warning" style="font-size:13px; padding:6px 12px;">
                    ⚠️ ${conflictos.length} conflictos
                </span>
                <span class="badge badge-danger" style="font-size:13px; padding:6px 12px;">
                    ❌ ${errores.length} errores
                </span>
            </div>
            <p style="margin-top:12px; font-size:13px;">
                ${conflictos.length > 0 ? 'Debes resolver los conflictos antes de continuar' : 'Procederemos con el registro'}
            </p>
        `,
        confirmButtonText: conflictos.length > 0 ? 'Resolver conflictos' : 'Continuar'
    });
}*/

// NUEVO FLUJO: FASE 2 - RESOLUCIÓN DE CONFLICTOS
async function resolverConflictos() {
    let indice = 0;
    const conflictos = datosAnalisis.conflictos;

    console.log(`🔍 Iniciando resolución de ${conflictos.length} conflictos`);

    async function mostrarSiguienteConflicto() {
        if (indice >= conflictos.length) {
            console.log("✅ Todos los conflictos resueltos");
            // ✅ Todos los conflictos resueltos → REGISTRAR
            await registrarArchivosMasivo();
            return;
        }

        const conflicto = conflictos[indice];
        console.log(`📋 Mostrando conflicto ${indice + 1}/${conflictos.length}:`, conflicto.archivo);
        
        await mostrarModalConflicto(
            conflicto.archivo,
            conflicto.serie,
            conflicto.numero,
            conflicto.opciones,
            indice + 1,
            conflictos.length
        );
    }

    // Callback para siguiente conflicto
    window.resolverConflictoCallback = async (id_comprobante_seleccionado) => {
        console.log(`✅ Resolviendo conflicto ${indice + 1} con ID:`, id_comprobante_seleccionado);
        
        // Mover de conflictos a correctos
        const conflictoResuelto = conflictos[indice];
        const opcionSeleccionada = conflictoResuelto.opciones.find(
            op => op.id_comprobante == id_comprobante_seleccionado
        );

        if (!opcionSeleccionada) {
            console.error("❌ Opción no encontrada");
            return;
        }

        datosAnalisis.correctos.push({
            archivo: conflictoResuelto.archivo,
            serie: conflictoResuelto.serie,
            numero: conflictoResuelto.numero,
            id_comprobante: id_comprobante_seleccionado,
            nom_proveedor: opcionSeleccionada.nom_proveedor,
            ruc_proveedor: opcionSeleccionada.ruc_proveedor,
            archivo_temporal: conflictoResuelto.archivo_temporal,
            extension: conflictoResuelto.extension
        });

        console.log(`✅ Conflicto ${indice + 1} resuelto. Correctos:`, datosAnalisis.correctos.length);

        indice++;
        await mostrarSiguienteConflicto();
    };

    await mostrarSiguienteConflicto();
}

// ACTUALIZAR: Modal de conflicto con contador con SweetAlert2
async function mostrarModalConflicto(archivo, serie, numero, opciones, actual, total) {
    let htmlOpciones = "";
    
    opciones.forEach(op => {
        htmlOpciones += `
        <div class="form-check">
            <input class="form-check-input" type="radio" name="conflictoProveedor" value="${op.id_comprobante}" id="opt_${op.id_comprobante}">
            <label class="form-check-label" for="opt_${op.id_comprobante}">
                <strong>${op.nom_proveedor}</strong><br>
                <small class="text-muted">RUC: ${op.ruc_proveedor}</small>
            </label>
        </div>`;
    });

    const result = await Swal.fire({
        title: 'Información',
        html: `
            <div style="text-align:left;">
                <p class="mb-1">Se encontraron varios comprobantes con la serie y número: <strong>${serie}-${numero}</strong></p>
                <br>
                <p>Seleccione a qué proveedor pertenece el archivo:</p>
                ${htmlOpciones}
            </div>
        `,
        width: '400px',
        showCancelButton: true,
        confirmButtonText: 'Asignar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'swal2-small',
            title: 'swal2-title-warning'
        },
        preConfirm: () => {
            const seleccionado = document.querySelector('input[name="conflictoProveedor"]:checked');
            if (!seleccionado) {
                Swal.showValidationMessage('Debes seleccionar un proveedor');
                return false;
            }
            return seleccionado.value;
        }
    });

    if (result.isConfirmed) {
        if (window.resolverConflictoCallback) {
            await window.resolverConflictoCallback(result.value);
        }
    } else {
        Swal.fire('Proceso cancelado', 'No se registró ningún archivo', 'info');
    }
}

// ACTUALIZAR: Resolver conflicto (sin registrar)
function resolverConflicto() {
    let id_comprobante = document.querySelector('input[name="conflictoProveedor"]:checked');
    
    if (!id_comprobante) {
        Swal.fire({
            icon: 'warning',
            title: 'Selección requerida',
            text: 'Debes seleccionar un proveedor',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    id_comprobante = id_comprobante.value;
    
    $("#modalConflicto").modal("hide");
    
    // Llamar callback para continuar
    if (window.resolverConflictoCallback) {
        window.resolverConflictoCallback(id_comprobante);
    }
}

// NUEVO FLUJO: FASE 3 - REGISTRO MASIVO
async function registrarArchivosMasivo() {
    const { correctos, errores } = datosAnalisis;

    if (correctos.length === 0) {
        mostrarResultadoFinal(0, errores);
        return;
    }

    Swal.fire({
        title: 'Registrando archivos...',
        html: `Procesando ${correctos.length} archivo(s)`,
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    // Preparar datos para envío
    const archivos_a_registrar = correctos.map(item => ({
        archivo: item.archivo,
        id_comprobante: item.id_comprobante,
        archivo_temporal: item.archivo_temporal,
        extension: item.extension
    }));

    const payload = {
        archivos_a_registrar: archivos_a_registrar,
        enviar_proveedor: document.getElementById('enviarProveedor').checked ? 1 : 0,
        enviar_contabilidad: document.getElementById('enviarContabilidad').checked ? 1 : 0,
        enviar_tesoreria: document.getElementById('enviarTesoreria').checked ? 1 : 0
    };

    try {
        const response = await fetch('../_controlador/comprobante_subida_masiva.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const resultado = await response.json();
        
        Swal.close();

        if (resultado.success) {
            // Combinar errores de análisis + errores de registro
            const todosLosErrores = [...errores, ...resultado.errores];
            mostrarResultadoFinal(resultado.exitosos, todosLosErrores);
        } else {
            Swal.fire('Error', resultado.mensaje || 'Error al registrar archivos', 'error');
        }

    } catch (error) {
        Swal.close();
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
    }
}

// NUEVO FLUJO: FASE 4 - RESULTADO FINAL
function mostrarResultadoFinal(exitosos, errores) {
    if (errores.length === 0) {
        // ✅ TODO EXITOSO
        Swal.fire({
            icon: 'success',
            title: '¡Proceso completado!',
            html: `<b>${exitosos}</b> archivo(s) procesado(s) exitosamente`,
            confirmButtonText: 'Aceptar'
        }).then(() => {
            cerrarModalMasivo();
            location.reload();
        });
    } else {
        // ⚠️ HAY ERRORES
        let htmlErrores = '<div style="max-height:350px; overflow-y:auto; text-align:left; margin-top:10px;">';
        
        errores.forEach((error) => {
            htmlErrores += `
                <div style="
                    display:flex; 
                    align-items:center; 
                    gap:8px;
                    padding:6px 10px; 
                    margin-bottom:4px;
                    background:#f8f9fa;
                    border-radius:4px;
                    border-left:3px solid #dc3545;
                    transition: all 0.2s;
                " onmouseover="this.style.background='#e9ecef'" onmouseout="this.style.background='#f8f9fa'">
                    <i class="fa fa-file-o" style="color:#dc3545; font-size:12px;"></i>
                    <span style="font-size:12px; color:#495057; flex:1; min-width:0;">
                        ${error.archivo}
                    </span>
                    <span 
                        style="
                            background:#dc3545;
                            color:white;
                            width:18px;
                            height:18px;
                            border-radius:50%;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-size:10px;
                            cursor:help;
                            flex-shrink:0;
                        "
                        title="${error.motivo}"
                        data-toggle="tooltip"
                        data-placement="left"
                    >
                        <i class="fa fa-info"></i>
                    </span>
                </div>
            `;
        });
        
        htmlErrores += '</div>';

        Swal.fire({
            icon: exitosos > 0 ? 'warning' : 'error',
            title: exitosos > 0 ? 'Proceso con errores' : 'Proceso fallido',
            html: `
                <div style="text-align:center; margin:8px 0 12px 0;">
                    ${exitosos > 0 ? `
                        <span class="badge badge-success" style="font-size:12px; padding:4px 10px;">
                            ${exitosos} exitosos
                        </span>
                    ` : ''}
                    <span class="badge badge-danger" style="font-size:12px; padding:4px 10px;">
                        ${errores.length} fallidos
                    </span>
                </div>
                ${htmlErrores}
            `,
            width: '500px',
            confirmButtonText: 'Cerrar',
            confirmButtonColor: '#6c757d',
            didOpen: () => {
                $('[data-toggle="tooltip"]').tooltip();
            },
            willClose: () => {
                $('[data-toggle="tooltip"]').tooltip('dispose');
            }
        }).then(() => {
            if (exitosos > 0) {
                cerrarModalMasivo();
                location.reload();
            }
        });
    }
}


// ============================================================================
// FUNCIÓN PARA VER DETALLES DE ORDEN DE COMPRA
// ============================================================================
function mostrarDetalleOrdenCompra(id_compra) {
    $('#modalDetalleOrdenCompra').modal('show');
    
    document.getElementById('loading-detalle-compra').style.display = 'block';
    document.getElementById('contenido-detalle-compra-mostrar').style.display = 'none';
    document.getElementById('error-detalle-compra-mostrar').style.display = 'none';
    
    document.getElementById('btn-descargar-pdf-compra').href = 'compras_pdf.php?id=' + id_compra;
    
    const formData = new FormData();
    formData.append('accion', 'obtener_detalle');
    formData.append('id_compra', id_compra);
    
    fetch('compra_detalles.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading-detalle-compra').style.display = 'none';
        if (data.success) {
            mostrarContenidoDetalleCompra(data.compra, data.detalles);
        } else {
            mostrarErrorDetalleCompra(data.message || 'Error desconocido');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loading-detalle-compra').style.display = 'none';
        mostrarErrorDetalleCompra('Error de conexión');
    });
}

function mostrarContenidoDetalleCompra(compra, detalles) {
    const titulo = document.getElementById('modalDetalleOrdenCompraLabel');
    
    const esServicio = compra.id_producto_tipo == 2;
    const tipoOrden = esServicio ? 'Servicio' : 'Compra';
    
    titulo.innerHTML = `<i class="fa fa-file-text-o text-primary"></i> Orden de ${tipoOrden} - C00${compra.id_compra}`;
    
    const contenido = document.getElementById('contenido-detalle-compra-mostrar');
    const fechaFormateada = new Date(compra.fec_compra).toLocaleDateString('es-PE');
    const estadoCompra = parseInt(compra.est_compra);
    
    let estadoTexto = 'Desconocido';
    let estadoClase = 'secondary';
    
    switch(estadoCompra) {
        case 0: estadoTexto = 'Anulada'; estadoClase = 'danger'; break;
        case 1: estadoTexto = 'Pendiente'; estadoClase = 'warning'; break;
        case 2: estadoTexto = 'Aprobada'; estadoClase = 'success'; break;
        case 3: estadoTexto = 'Cerrada'; estadoClase = 'info'; break;
        case 4: estadoTexto = 'Pagada'; estadoClase = 'primary'; break;
    }
    
    const badgeTipo = esServicio 
        ? '<span class="badge badge-primary ml-2">ORDEN DE SERVICIO</span>'
        : '<span class="badge badge-info ml-2">ORDEN DE MATERIAL</span>';
    
    let html = `
        <div class="card mb-3">
            <div class="card-header" style="background-color: #e3f2fd; padding: 10px 15px;">
                <h6 class="mb-0">
                    <i class="fa fa-info-circle text-primary"></i> Información General
                    ${badgeTipo}
                </h6>
            </div>
            <div class="card-body" style="padding: 15px;">
                <div class="row">
                    <div class="col-md-6">
                        <p style="margin: 5px 0; font-size: 13px;"><strong>N° Orden:</strong> C00${compra.id_compra}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Proveedor:</strong> ${compra.nom_proveedor || 'No especificado'}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>RUC:</strong> ${compra.ruc_proveedor || 'No especificado'}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Moneda:</strong> ${compra.nom_moneda || 'No especificada'}</p>
                    </div>
                    <div class="col-md-6">
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Fecha Orden:</strong> ${fechaFormateada}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Estado:</strong> <span class="badge badge-${estadoClase}">${estadoTexto}</span></p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Creado por:</strong> ${compra.nom_personal || 'No especificado'}</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>Plazo Entrega:</strong> ${compra.plaz_compra || 'No especificado'}</p>
                    </div>
                </div>`;
    
    let tieneAfectacion = false;
    
    if (compra.nombre_detraccion && compra.porcentaje_detraccion) {
        tieneAfectacion = true;
        html += `
            <div class="alert alert-warning" style="margin-top: 15px; padding: 10px;">
                <i class="fa fa-exclamation-triangle"></i> 
                <strong>Detracción Aplicada:</strong> ${compra.nombre_detraccion} 
                <span class="badge badge-warning">${compra.porcentaje_detraccion}%</span>
            </div>`;
    }
    
    if (compra.nombre_retencion && compra.porcentaje_retencion) {
        tieneAfectacion = true;
        html += `
            <div class="alert alert-info" style="margin-top: 15px; padding: 10px;">
                <i class="fa fa-info-circle"></i> 
                <strong>Retención Aplicada:</strong> ${compra.nombre_retencion} 
                <span class="badge badge-info">${compra.porcentaje_retencion}%</span>
            </div>`;
    }
    
    if (compra.nombre_percepcion && compra.porcentaje_percepcion) {
        tieneAfectacion = true;
        html += `
            <div class="alert alert-success" style="margin-top: 15px; padding: 10px;">
                <i class="fa fa-plus-circle"></i> 
                <strong>Percepción Aplicada:</strong> ${compra.nombre_percepcion} 
                <span class="badge badge-success">${compra.porcentaje_percepcion}%</span>
            </div>`;
    }
    
    if (!tieneAfectacion) {
        html += `
            <div class="alert alert-secondary" style="margin-top: 15px; padding: 10px;">
                <i class="fa fa-info-circle"></i> 
                <strong>Sin afectaciones:</strong> Esta orden no tiene detracción, retención ni percepción aplicada.
            </div>`;
    }
    
    if (compra.denv_compra || compra.obs_compra || compra.port_compra) {
        html += `<div class="row mt-3"><div class="col-md-12"><div class="border-top pt-2">`;
        if (compra.denv_compra) html += `<p style="margin: 5px 0; font-size: 13px;"><strong>Dirección de Envío:</strong> ${compra.denv_compra}</p>`;
        if (compra.obs_compra) html += `<p style="margin: 5px 0; font-size: 13px;"><strong>Observaciones:</strong> ${compra.obs_compra}</p>`;
        if (compra.port_compra) html += `<p style="margin: 5px 0; font-size: 13px;"><strong>Tipo de Porte:</strong> ${compra.port_compra}</p>`;
        html += `</div></div></div>`;
    }
    
    html += `</div></div>`;
    
    html += `
        <div class="card">
            <div class="card-header" style="background-color: #e8f5e8; padding: 10px 15px;">
                <h6 class="mb-0"><i class="fa fa-list-alt text-success"></i> ${esServicio ? 'Servicios' : 'Productos'} de la Orden</h6>
            </div>
            <div class="card-body" style="padding: 15px;">
                <div class="table-responsive">
                    <table class="table table-striped table-sm" style="font-size: 12px;">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th style="width: 8%;">#</th>
                                <th style="width: 15%;">Código</th>
                                <th style="width: 35%;">Descripción</th>
                                <th style="width: 10%;">Cantidad</th>
                                <th style="width: 12%;">Precio Unit.</th>
                                <th style="width: 10%;">IGV (%)</th>
                                <th style="width: 10%;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>`;
    
    let subtotalGeneral = 0;
    let totalIgv = 0;
    const simboloMoneda = compra.sim_moneda || (compra.id_moneda == 1 ? 'S/.' : 'US$');
    
    detalles.forEach((detalle, index) => {
        const cantidad = parseFloat(detalle.cant_compra_detalle);
        const precioUnit = parseFloat(detalle.prec_compra_detalle);
        const igvPorcentaje = parseFloat(detalle.igv_compra_detalle || 18);
        
        const subtotal = cantidad * precioUnit;
        const montoIgv = subtotal * (igvPorcentaje / 100);
        
        subtotalGeneral += subtotal;
        totalIgv += montoIgv;
        
        html += `<tr>
                    <td style="font-weight: bold;">${index + 1}</td>
                    <td>${detalle.cod_material || 'N/A'}</td>
                    <td>${detalle.nom_producto}</td>
                    <td class="text-center">${cantidad.toFixed(2)}</td>
                    <td class="text-right">${simboloMoneda} ${precioUnit.toFixed(2)}</td>
                    <td class="text-center">${igvPorcentaje}%</td>
                    <td class="text-right" style="font-weight: bold;">${simboloMoneda} ${subtotal.toFixed(2)}</td>
                </tr>`;
    });
    
    html += `</tbody></table></div><div class="row mt-3"><div class="col-md-12">`;
    
    const totalConIgv = subtotalGeneral + totalIgv;
    
    let tipoAfectacion = null;
    let porcentaje = 0;
    let nombreConcepto = '';
    let montoAfectacion = 0;
    
    if (compra.porcentaje_detraccion && parseFloat(compra.porcentaje_detraccion) > 0) {
        tipoAfectacion = 'DETRACCION';
        porcentaje = parseFloat(compra.porcentaje_detraccion);
        nombreConcepto = compra.nombre_detraccion;
        montoAfectacion = (totalConIgv * porcentaje) / 100;
    } else if (compra.porcentaje_retencion && parseFloat(compra.porcentaje_retencion) > 0) {
        tipoAfectacion = 'RETENCION';
        porcentaje = parseFloat(compra.porcentaje_retencion);
        nombreConcepto = compra.nombre_retencion;
        montoAfectacion = (totalConIgv * porcentaje) / 100;
    } else if (compra.porcentaje_percepcion && parseFloat(compra.porcentaje_percepcion) > 0) {
        tipoAfectacion = 'PERCEPCION';
        porcentaje = parseFloat(compra.porcentaje_percepcion);
        nombreConcepto = compra.nombre_percepcion;
        montoAfectacion = (totalConIgv * porcentaje) / 100;
    }
    
    let totalFinal = 0;
    
    if (tipoAfectacion === 'DETRACCION') {
        totalFinal = totalConIgv - montoAfectacion;
    } else if (tipoAfectacion === 'RETENCION') {
        totalFinal = totalConIgv - montoAfectacion;
    } else if (tipoAfectacion === 'PERCEPCION') {
        totalFinal = totalConIgv + montoAfectacion;
    } else {
        totalFinal = totalConIgv;
    }
    
    html += `<div class="alert alert-light" style="margin-bottom: 10px; padding: 10px;">
                <div style="font-size: 14px; text-align: center; margin-bottom: 5px;">
                    <i class="fa fa-calculator text-secondary"></i> <strong>SUBTOTAL:</strong> ${simboloMoneda} ${subtotalGeneral.toFixed(2)}
                </div>
                <div style="font-size: 13px; text-align: center; margin-bottom: 5px;">
                    <i class="fa fa-percent text-secondary"></i> <strong>IGV TOTAL:</strong> ${simboloMoneda} ${totalIgv.toFixed(2)}
                </div>
                <div style="font-size: 14px; text-align: center; font-weight: bold; padding: 5px; background-color: #e3f2fd; border-radius: 4px; margin-bottom: 5px;">
                    <i class="fa fa-calculator text-primary"></i> <strong>TOTAL CON IGV:</strong> ${simboloMoneda} ${totalConIgv.toFixed(2)}
                </div>`;
    
    if (tipoAfectacion === 'DETRACCION') {
        html += `<div style="font-size: 13px; text-align: center; color: #ffc107; margin-bottom: 5px;">
                    <i class="fa fa-minus-circle"></i> <strong>Detracción ${nombreConcepto} (${porcentaje}%):</strong> -${simboloMoneda} ${montoAfectacion.toFixed(2)}
                 </div>`;
    }
    
    if (tipoAfectacion === 'RETENCION') {
        html += `<div style="font-size: 13px; text-align: center; color: #2196f3; margin-bottom: 5px;">
                    <i class="fa fa-minus-circle"></i> <strong>Retención ${nombreConcepto} (${porcentaje}%):</strong> -${simboloMoneda} ${montoAfectacion.toFixed(2)}
                 </div>`;
    }
    
    if (tipoAfectacion === 'PERCEPCION') {
        html += `<div style="font-size: 13px; text-align: center; color: #4caf50; margin-bottom: 5px;">
                    <i class="fa fa-plus-circle"></i> <strong>Percepción ${nombreConcepto} (${porcentaje}%):</strong> +${simboloMoneda} ${montoAfectacion.toFixed(2)}
                 </div>`;
    }
    
    html += `</div>
             <div class="alert alert-success text-center" style="font-size: 18px; font-weight: bold; margin: 0; padding: 15px;">
                <i class="fa fa-money"></i> TOTAL A PAGAR: ${simboloMoneda} ${totalFinal.toFixed(2)}
             </div></div></div></div></div>`;
    
    contenido.innerHTML = html;
    contenido.style.display = 'block';
}

function mostrarErrorDetalleCompra(mensaje) {
    const errorDiv = document.getElementById('error-detalle-compra-mostrar');
    errorDiv.querySelector('p').textContent = mensaje;
    errorDiv.style.display = 'block';
}

document.addEventListener('click', function(event) {
    const btnVerDetalle = event.target.closest('.btn-ver-detalle-compra');
    if (btnVerDetalle) {
        event.preventDefault();
        event.stopPropagation();
        const idCompra = btnVerDetalle.getAttribute('data-id-compra');
        mostrarDetalleOrdenCompra(idCompra);
    }
});

// ============================================================================
// FUNCIONES PARA EDITAR ORDEN EN MODAL (CON SOPORTE SERVICIOS)
// ============================================================================

let esOrdenServicioGlobal = false;

function abrirModalEditarOrden(id_compra) {
    $('#modalEditarOrden').modal('show');
    
    document.getElementById('loading-editar').style.display = 'block';
    document.getElementById('contenido-editar-orden').style.display = 'none';
    document.getElementById('error-editar-orden').style.display = 'none';
    
    fetch('compras_obtener_datos_edicion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id_compra=' + id_compra
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading-editar').style.display = 'none';
        
        if (data.success) {
            esOrdenServicioGlobal = (data.orden.id_producto_tipo == 2);
            
            cargarDatosOrdenModal(data.orden, data.detalles, data.proveedores, data.detracciones);
            document.getElementById('contenido-editar-orden').style.display = 'block';
        } else {
            document.getElementById('error-editar-orden').style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loading-editar').style.display = 'none';
        document.getElementById('error-editar-orden').style.display = 'block';
    });
}

function cargarDatosOrdenModal(orden, detalles, proveedores, detracciones) {
    const tipoOrden = esOrdenServicioGlobal ? 'Servicio' : 'Compra';
    const badgeTipo = esOrdenServicioGlobal 
        ? '<span class="badge badge-primary ml-2">SERVICIO</span>'
        : '<span class="badge badge-info ml-2">MATERIAL</span>';
    
    document.getElementById('orden-numero').innerHTML = `C00${orden.id_compra} ${badgeTipo}`;
    document.getElementById('edit_id_compra').value = orden.id_compra;
    document.getElementById('edit_fecha_orden').value = orden.fec_compra.split(' ')[0];
    document.getElementById('edit_moneda_orden').value = orden.id_moneda;
    document.getElementById('edit_plazo_entrega').value = orden.plaz_compra || '';
    document.getElementById('edit_direccion_envio').value = orden.denv_compra || '';
    document.getElementById('edit_observaciones_orden').value = orden.obs_compra || '';
    document.getElementById('edit_tipo_porte').value = orden.port_compra || '';

    let inputEliminados = document.getElementById('edit_items_eliminados');
    if (!inputEliminados) {
        inputEliminados = document.createElement('input');
        inputEliminados.type = 'hidden';
        inputEliminados.name = 'items_eliminados';
        inputEliminados.id = 'edit_items_eliminados';
        inputEliminados.value = '';
        document.getElementById('form-editar-orden-modal').appendChild(inputEliminados);
    } else {
        inputEliminados.value = '';
    }
    
    const selectProveedor = document.getElementById('edit_proveedor_orden');
    selectProveedor.innerHTML = '<option value="">Seleccionar...</option>';
    proveedores.forEach(prov => {
        const option = document.createElement('option');
        option.value = prov.id_proveedor;
        option.textContent = prov.nom_proveedor;
        option.selected = (prov.id_proveedor == orden.id_proveedor);
        selectProveedor.appendChild(option);
    });
    
    const contenedorDetracciones = document.getElementById('edit_contenedor_detracciones');
    contenedorDetracciones.innerHTML = '';
    
    if (detracciones && detracciones.length > 0) {
        const detracciones_tipo = {};
        detracciones.forEach(det => {
            const tipo = det.nom_detraccion_tipo.toUpperCase();
            if (!detracciones_tipo[tipo]) {
                detracciones_tipo[tipo] = [];
            }
            detracciones_tipo[tipo].push(det);
        });
        
        let html = '';
        
        if (detracciones_tipo['DETRACCION']) {
            html += '<div class="mb-3"><label style="font-size: 12px; font-weight: bold;">Detracción:</label>';
            html += '<div style="padding: 8px; background-color: #fff3cd; border-radius: 4px; border: 1px solid #ffc107;">';
            
            detracciones_tipo['DETRACCION'].forEach(det => {
                const checked = (orden.id_detraccion == det.id_detraccion) ? 'checked' : '';
                html += `
                    <div class="form-check" style="margin-bottom: 5px;">
                        <input class="form-check-input edit-detraccion-checkbox" 
                               type="checkbox" 
                               name="id_detraccion" 
                               value="${det.id_detraccion}" 
                               data-porcentaje="${det.porcentaje}" 
                               data-nombre="${det.nombre_detraccion}"
                               id="edit_detraccion_${det.id_detraccion}" 
                               ${checked}>
                        <label class="form-check-label" 
                               for="edit_detraccion_${det.id_detraccion}" 
                               style="font-size: 12px; cursor: pointer;">
                            ${det.nombre_detraccion} <strong>(${det.porcentaje}%)</strong>
                        </label>
                    </div>
                `;
            });
            
            html += '</div><small class="form-text text-muted">Se aplica sobre el subtotal antes de IGV</small></div>';
        }
        
        if (detracciones_tipo['RETENCION']) {
            html += '<div class="mb-3"><label style="font-size: 12px; font-weight: bold;">Retención:</label>';
            html += '<div style="padding: 8px; background-color: #e7f3ff; border-radius: 4px; border: 1px solid #2196f3;">';
            
            detracciones_tipo['RETENCION'].forEach(det => {
                const checked = (orden.id_retencion == det.id_detraccion) ? 'checked' : '';
                html += `
                    <div class="form-check" style="margin-bottom: 5px;">
                        <input class="form-check-input edit-retencion-checkbox" 
                               type="checkbox" 
                               name="id_retencion" 
                               value="${det.id_detraccion}" 
                               data-porcentaje="${det.porcentaje}" 
                               data-nombre="${det.nombre_detraccion}"
                               id="edit_retencion_${det.id_detraccion}" 
                               ${checked}>
                        <label class="form-check-label" 
                               for="edit_retencion_${det.id_detraccion}" 
                               style="font-size: 12px; cursor: pointer;">
                            ${det.nombre_detraccion} <strong>(${det.porcentaje}%)</strong>
                        </label>
                    </div>
                `;
            });
            
            html += '</div><small class="form-text text-muted">Se aplica sobre el total después de IGV</small></div>';
        }
        
        if (detracciones_tipo['PERCEPCION']) {
            html += '<div class="mb-2"><label style="font-size: 12px; font-weight: bold;">Percepción:</label>';
            html += '<div style="padding: 8px; background-color: #e8f5e9; border-radius: 4px; border: 1px solid #4caf50;">';
            
            detracciones_tipo['PERCEPCION'].forEach(det => {
                const checked = (orden.id_percepcion == det.id_detraccion) ? 'checked' : '';
                html += `
                    <div class="form-check" style="margin-bottom: 5px;">
                        <input class="form-check-input edit-percepcion-checkbox" 
                               type="checkbox" 
                               name="id_percepcion" 
                               value="${det.id_detraccion}" 
                               data-porcentaje="${det.porcentaje}" 
                               data-nombre="${det.nombre_detraccion}"
                               id="edit_percepcion_${det.id_detraccion}" 
                               ${checked}>
                        <label class="form-check-label" 
                               for="edit_percepcion_${det.id_detraccion}" 
                               style="font-size: 12px; cursor: pointer;">
                            ${det.nombre_detraccion} <strong>(${det.porcentaje}%)</strong>
                        </label>
                    </div>
                `;
            });
            
            html += '</div><small class="form-text text-muted">Se aplica sobre el total después de IGV</small></div>';
        }
        
        contenedorDetracciones.innerHTML = html;
        
    } else {
        contenedorDetracciones.innerHTML = '<p class="text-muted mb-0" style="font-size: 11px;"><i class="fa fa-info-circle"></i> No hay detracciones configuradas</p>';
    }
    
    document.querySelectorAll('.edit-detraccion-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                document.querySelectorAll('.edit-detraccion-checkbox').forEach(otherCb => {
                    if (otherCb !== this) otherCb.checked = false;
                });
                document.querySelectorAll('.edit-retencion-checkbox').forEach(cb => cb.checked = false);
                document.querySelectorAll('.edit-percepcion-checkbox').forEach(cb => cb.checked = false);
            }
            calcularTotalOrdenModal();
        });
    });
    
    document.querySelectorAll('.edit-retencion-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                document.querySelectorAll('.edit-retencion-checkbox').forEach(otherCb => {
                    if (otherCb !== this) otherCb.checked = false;
                });
                document.querySelectorAll('.edit-detraccion-checkbox').forEach(cb => cb.checked = false);
                document.querySelectorAll('.edit-percepcion-checkbox').forEach(cb => cb.checked = false);
            }
            calcularTotalOrdenModal();
        });
    });
    
    document.querySelectorAll('.edit-percepcion-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                document.querySelectorAll('.edit-percepcion-checkbox').forEach(otherCb => {
                    if (otherCb !== this) otherCb.checked = false;
                });
                document.querySelectorAll('.edit-detraccion-checkbox').forEach(cb => cb.checked = false);
                document.querySelectorAll('.edit-retencion-checkbox').forEach(cb => cb.checked = false);
            }
            calcularTotalOrdenModal();
        });
    });
    
    const itemsContainer = document.getElementById('edit_items_container');
    itemsContainer.innerHTML = '';

    const simboloMoneda = orden.id_moneda == 1 ? 'S/.' : 'US$';

    detalles.forEach((item, index) => {
        const subtotal = parseFloat(item.cant_compra_detalle) * parseFloat(item.prec_compra_detalle);
        const igvPorcentaje = parseFloat(item.igv_compra_detalle) || 18;
        const montoIgv = subtotal * (igvPorcentaje / 100);
        const total = subtotal + montoIgv;
        
        const badgeItem = esOrdenServicioGlobal 
            ? '<span class="badge badge-primary badge-sm ml-1">SERVICIO</span>'
            : '<span class="badge badge-info badge-sm ml-1">MATERIAL</span>';
        
        itemsContainer.innerHTML += `
            <div class="alert alert-light p-2 mb-2" id="edit_item_${item.id_compra_detalle}">
                <input type="hidden" name="items_orden[${item.id_compra_detalle}][id_compra_detalle]" value="${item.id_compra_detalle}">
                <input type="hidden" name="items_orden[${item.id_compra_detalle}][id_producto]" value="${item.id_producto}">
                <input type="hidden" name="items_orden[${item.id_compra_detalle}][es_nuevo]" value="0">
                
                <div class="row align-items-center mb-2">
                    <div class="col-md-11">
                        <div style="font-size: 12px;">
                            <strong>Descripción:</strong> ${item.nom_producto}
                            ${badgeItem}
                        </div>
                    </div>
                    <div class="col-md-1 text-right">
                        <button type="button" class="btn btn-danger btn-sm btn-remover-item" 
                                data-id-detalle="${item.id_compra_detalle}"
                                title="Eliminar item">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-2">
                        <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">Cantidad:</label>
                        <input type="number" 
                            class="form-control form-control-sm edit-cantidad-item" 
                            name="items_orden[${item.id_compra_detalle}][cantidad]"
                            data-id-detalle="${item.id_compra_detalle}"
                            value="${item.cant_compra_detalle}"
                            min="0.01" 
                            step="0.01"
                            style="font-size: 12px;"
                            required>
                    </div>
                    
                    <div class="col-md-2">
                        <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">Precio Unit.:</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text edit-simbolo-moneda" style="font-size: 11px; background-color: #f8f9fa; border: 1px solid #ced4da;">
                                    ${simboloMoneda}
                                </span>
                            </div>
                            <input type="number" 
                                class="form-control form-control-sm edit-precio-item" 
                                name="items_orden[${item.id_compra_detalle}][precio_unitario]"
                                data-id-detalle="${item.id_compra_detalle}"
                                value="${item.prec_compra_detalle}"
                                step="0.01" 
                                min="0"
                                style="font-size: 11px;"
                                required>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">IGV (%):</label>
                        <input type="number" 
                            class="form-control form-control-sm edit-igv-item" 
                            name="items_orden[${item.id_compra_detalle}][igv]"
                            data-id-detalle="${item.id_compra_detalle}"
                            value="${igvPorcentaje}"
                            min="0" 
                            max="100"
                            step="0.01"
                            style="font-size: 12px;"
                            required>
                    </div>
                    
                    <div class="col-md-3">
                        <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block;">Homologación:</label>
                        ${item.hom_compra_detalle ? `
                            <div class="mb-1">
                                <a href="../_archivos/homologaciones/${item.hom_compra_detalle}" target="_blank" 
                                class="text-success" style="font-size: 11px;">
                                    <i class="fa fa-file-pdf-o"></i> Ver archivo actual
                                </a>
                            </div>
                        ` : ''}
                        <input type="file" 
                            class="form-control-file" 
                            name="homologacion[${item.id_compra_detalle}]"
                            accept=".pdf,.jpg,.jpeg,.png"
                            style="font-size: 11px; padding-top: 4px;">
                        <small class="text-muted" style="font-size: 10px;">PDF, JPG, PNG</small>
                    </div>
                    
                    <div class="col-md-3 text-right">
                        <label style="font-size: 11px; font-weight: bold; margin-bottom: 4px; display: block; visibility: hidden;">-</label>
                        <div class="edit-calculo-item" id="edit_calculo_${item.id_compra_detalle}" 
                            style="font-size: 11px; line-height: 1.4;">
                            <div class="edit-subtotal-text">Subtotal: ${simboloMoneda} ${subtotal.toFixed(2)}</div>
                            <div class="edit-igv-text">IGV: ${simboloMoneda} ${montoIgv.toFixed(2)}</div>
                            <div class="edit-total-text" style="font-weight: bold; color: #28a745;">Total: ${simboloMoneda} ${total.toFixed(2)}</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    document.querySelectorAll('.edit-cantidad-item, .edit-precio-item, .edit-igv-item').forEach(input => {
        input.addEventListener('input', function() {
            const idDetalle = this.getAttribute('data-id-detalle');
            const itemDiv = document.getElementById('edit_item_' + idDetalle);
            
            const cantidad = parseFloat(itemDiv.querySelector('.edit-cantidad-item').value) || 0;
            const precio = parseFloat(itemDiv.querySelector('.edit-precio-item').value) || 0;
            const igvPorcentaje = parseFloat(itemDiv.querySelector('.edit-igv-item').value) || 0;
            
            const subtotal = cantidad * precio;
            const montoIgv = subtotal * (igvPorcentaje / 100);
            const total = subtotal + montoIgv;
            
            const simbolo = document.getElementById('edit_moneda_orden').value == 1 ? 'S/.' : 'US$';
            const calculoDiv = document.getElementById('edit_calculo_' + idDetalle);
            
            calculoDiv.querySelector('.edit-subtotal-text').textContent = `Subtotal: ${simbolo} ${subtotal.toFixed(2)}`;
            calculoDiv.querySelector('.edit-igv-text').textContent = `IGV: ${simbolo} ${montoIgv.toFixed(2)}`;
            calculoDiv.querySelector('.edit-total-text').textContent = `Total: ${simbolo} ${total.toFixed(2)}`;
            
            calcularTotalOrdenModal();
        });
    });
    
    document.querySelectorAll('.btn-remover-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const idDetalle = this.getAttribute('data-id-detalle');
            Swal.fire({
                title: '¿Eliminar item?',
                text: 'Este item se eliminará de la orden',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const inputEliminados = document.getElementById('edit_items_eliminados');
                    let eliminados = inputEliminados.value ? inputEliminados.value.split(',') : [];
                    
                    if (!idDetalle.toString().startsWith('nuevo-')) {
                        eliminados.push(idDetalle);
                        inputEliminados.value = eliminados.join(',');
                    }
                    
                    document.getElementById('edit_item_' + idDetalle).remove();
                    calcularTotalOrdenModal();
                }
            });
        });
    });
    
    document.getElementById('edit_moneda_orden').addEventListener('change', function() {
        const simbolo = this.value == 1 ? 'S/.' : 'US$';
        document.querySelectorAll('.edit-simbolo-moneda').forEach(el => {
            el.textContent = simbolo;
        });
        calcularTotalOrdenModal();
    });
    
    calcularTotalOrdenModal();
}

function calcularTotalOrdenModal() {
    const items = document.querySelectorAll('[id^="edit_item_"]');
    let subtotalGeneral = 0;
    let totalIgv = 0;
    
    items.forEach(item => {
        const cantidadInput = item.querySelector('.edit-cantidad-item');
        const precioInput = item.querySelector('.edit-precio-item');
        const igvInput = item.querySelector('.edit-igv-item');
        
        if (cantidadInput && precioInput && igvInput) {
            const cantidad = parseFloat(cantidadInput.value) || 0;
            const precio = parseFloat(precioInput.value) || 0;
            const igvPorcentaje = parseFloat(igvInput.value) || 0;
            
            const subtotal = cantidad * precio;
            const montoIgv = subtotal * (igvPorcentaje / 100);
            
            subtotalGeneral += subtotal;
            totalIgv += montoIgv;
        }
    });
    
    const totalConIgv = subtotalGeneral + totalIgv;
    
    let tipoAfectacion = null;
    let porcentaje = 0;
    let nombreConcepto = '';
    let montoAfectacion = 0;
    
    const checkboxDetraccion = document.querySelector('.edit-detraccion-checkbox:checked');
    const checkboxRetencion = document.querySelector('.edit-retencion-checkbox:checked');
    const checkboxPercepcion = document.querySelector('.edit-percepcion-checkbox:checked');
    
    if (checkboxDetraccion) {
        tipoAfectacion = 'DETRACCION';
        porcentaje = parseFloat(checkboxDetraccion.getAttribute('data-porcentaje')) || 0;
        nombreConcepto = checkboxDetraccion.getAttribute('data-nombre') || '';
        montoAfectacion = (totalConIgv * porcentaje) / 100;
    } else if (checkboxRetencion) {
        tipoAfectacion = 'RETENCION';
        porcentaje = parseFloat(checkboxRetencion.getAttribute('data-porcentaje')) || 0;
        nombreConcepto = checkboxRetencion.getAttribute('data-nombre') || '';
        montoAfectacion = (totalConIgv * porcentaje) / 100;
    } else if (checkboxPercepcion) {
        tipoAfectacion = 'PERCEPCION';
        porcentaje = parseFloat(checkboxPercepcion.getAttribute('data-porcentaje')) || 0;
        nombreConcepto = checkboxPercepcion.getAttribute('data-nombre') || '';
        montoAfectacion = (totalConIgv * porcentaje) / 100;
    }
    
    let totalFinal = 0;
    
    if (tipoAfectacion === 'DETRACCION') {
        totalFinal = totalConIgv - montoAfectacion;
    } else if (tipoAfectacion === 'RETENCION') {
        totalFinal = totalConIgv - montoAfectacion;
    } else if (tipoAfectacion === 'PERCEPCION') {
        totalFinal = totalConIgv + montoAfectacion;
    } else {
        totalFinal = totalConIgv;
    }
    
    const simboloMoneda = document.getElementById('edit_moneda_orden').value == 1 ? 'S/.' : 'US$';
    
    let html = `
        <div style="font-size: 15px; padding: 10px 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px;">
            <div class="mb-2">
                <i class="fa fa-calculator text-secondary"></i>
                <strong class="text-secondary"> Subtotal:</strong>
                <span class="text-dark">${simboloMoneda} ${subtotalGeneral.toFixed(2)}</span>
            </div>
            <div class="mb-2">
                <i class="fa fa-percent text-secondary"></i>
                <strong class="text-secondary"> IGV Total:</strong>
                <span class="text-dark">${simboloMoneda} ${totalIgv.toFixed(2)}</span>
            </div>
            <div class="mb-2" style="font-weight: bold; font-size: 16px; padding: 5px; background-color: #e3f2fd; border-radius: 4px;">
                <i class="fa fa-calculator text-primary"></i>
                <strong class="text-primary"> Total con IGV:</strong>
                <span class="text-primary">${simboloMoneda} ${totalConIgv.toFixed(2)}</span>
            </div>`;
    
    if (tipoAfectacion === 'DETRACCION') {
        html += `
            <div class="mb-2">
                <i class="fa fa-minus-circle text-warning"></i>
                <strong class="text-warning"> Detracción ${nombreConcepto} (${porcentaje}%):</strong>
                <span class="text-warning">-${simboloMoneda} ${montoAfectacion.toFixed(2)}</span>
            </div>`;
    }
    
    if (tipoAfectacion === 'RETENCION') {
        html += `
            <div class="mb-2">
                <i class="fa fa-minus-circle text-info"></i>
                <strong class="text-info"> Retención ${nombreConcepto} (${porcentaje}%):</strong>
                <span class="text-info">-${simboloMoneda} ${montoAfectacion.toFixed(2)}</span>
            </div>`;
    }
    
    if (tipoAfectacion === 'PERCEPCION') {
        html += `
            <div class="mb-2">
                <i class="fa fa-plus-circle text-success"></i>
                <strong class="text-success"> Percepción ${nombreConcepto} (${porcentaje}%):</strong>
                <span class="text-success">+${simboloMoneda} ${montoAfectacion.toFixed(2)}</span>
            </div>`;
    }
    
    html += `
            <div style="font-size: 18px; font-weight: bold; padding: 10px; background-color: #28a745; color: white; border-radius: 6px; text-align: center; margin-top: 10px;">
                <i class="fa fa-money"></i> 
                TOTAL A PAGAR: ${simboloMoneda} ${totalFinal.toFixed(2)}
            </div>
        </div>`;
    
    document.getElementById('edit_total_orden').innerHTML = html;
}

document.getElementById('btn-guardar-edicion-orden').addEventListener('click', function() {
    const form = document.getElementById('form-editar-orden-modal');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const itemsContainer = document.getElementById('edit_items_container');
    const items = itemsContainer.querySelectorAll('[id^="edit_item_"]');
    
    if (items.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Sin items',
            text: 'Debe mantener al menos un item en la orden'
        });
        return;
    }
    
    const erroresValidacion = validarCantidadesModal(items);
    
    if (erroresValidacion.length > 0) {
        const tipoOrden = esOrdenServicioGlobal ? 'servicio' : 'material';
        
        let mensajeHTML = '<div style="text-align: left; padding: 10px;">' +
                        `<p style="margin-bottom: 10px;"><strong>No se puede guardar la orden de ${tipoOrden}:</strong></p>` +
                        '<ul style="color: #dc3545; font-size: 13px; margin-left: 20px;">';
        
        erroresValidacion.forEach(error => {
            mensajeHTML += `<li style="margin-bottom: 8px;">${error}</li>`;
        });
        
        mensajeHTML += '</ul></div>';
        
        Swal.fire({
            icon: 'error',
            title: 'Cantidad No Permitida',
            html: mensajeHTML,
            confirmButtonColor: '#d33',
            confirmButtonText: '<i class="fa fa-times"></i> Entendido',
            allowOutsideClick: false
        });
        
        return;
    }
    
    const btnGuardar = this;
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';
    
    const formData = new FormData(form);
    
    const inputsFile = form.querySelectorAll('input[type="file"]');
    inputsFile.forEach(input => {
        if (input.files.length > 0) {
            formData.append(input.name, input.files[0]);
        }
    });
    
    fetch('compras_actualizar_orden.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                $('#modalEditarOrden').modal('hide');
                location.reload();
            });
        } else {
            const esValidacion = data.tipo === 'validacion';
            
            Swal.fire({
                icon: esValidacion ? 'warning' : 'error',
                title: esValidacion ? 'Validación de datos' : 'Error del sistema',
                html: data.message,
                confirmButtonColor: esValidacion ? '#ffc107' : '#d33',
                confirmButtonText: 'Entendido',
                allowOutsideClick: false,
                customClass: {
                    htmlContainer: 'text-left'
                }
            });
            
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fa fa-save"></i> Guardar Cambios';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor. Por favor, verifica tu conexión a internet.',
            confirmButtonColor: '#d33'
        });
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = '<i class="fa fa-save"></i> Guardar Cambios';
    });
});

function validarCantidadesModal(itemsOrden) {
    const errores = [];
    
    itemsOrden.forEach(itemElement => {
        const cantidadInput = itemElement.querySelector('.edit-cantidad-item');
        
        if (!cantidadInput) return;
        
        const cantidadNueva = parseFloat(cantidadInput.value) || 0;
        
        if (cantidadNueva <= 0) {
            const descripcion = itemElement.querySelector('strong').nextSibling.textContent.trim();
            errores.push(`<strong>${descripcion}:</strong> La cantidad debe ser mayor a 0`);
        }
    });
    
    return errores;
}

document.getElementById('modalEditarOrden').addEventListener('hidden.bs.modal', function () {
    document.getElementById('form-editar-orden-modal').reset();
    document.getElementById('edit_items_container').innerHTML = '';
    document.getElementById('edit_total_orden').innerHTML = '';
    esOrdenServicioGlobal = false;
});

// ============================================================================
// CONFIGURACIÓN MODAL PROVEEDOR (MODAL EDICIÓN)
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    configurarModalProveedorEditar();
});

function configurarModalProveedorEditar() {
    const tablaCuentasModalEditar = document.getElementById("tabla-cuentas-modal-editar");
    //const btnAgregarModalEditar = document.getElementById("agregarCuentaModalEditar");
    
    /*
    if (btnAgregarModalEditar) {
        btnAgregarModalEditar.addEventListener("click", function() {
            const nuevaFila = document.createElement("tr");
            nuevaFila.innerHTML = `
                <td><input type="text" name="banco[]" class="form-control form-control-sm"></td>
                <td>
                    <select name="id_moneda[]" class="form-control form-control-sm">
                        <option value="">-- Moneda --</option>
                        <option value="1">Soles (S/.)</option>
                        <option value="2">Dólares (US$)</option>
                    </select>
                </td>
                <td><input type="text" name="cta_corriente[]" class="form-control form-control-sm"></td>
                <td><input type="text" name="cta_interbancaria[]" class="form-control form-control-sm"></td>
                <td><button type="button" class="btn btn-danger btn-sm eliminar-fila-modal-editar">X</button></td>
            `;
            tablaCuentasModalEditar.appendChild(nuevaFila);
        });
    }*/
    
    if (tablaCuentasModalEditar) {
        tablaCuentasModalEditar.addEventListener("click", function(e) {
            if (e.target.classList.contains("eliminar-fila-modal-editar")) {
                const filas = tablaCuentasModalEditar.querySelectorAll("tr");
                if (filas.length > 1) {
                    e.target.closest("tr").remove();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Advertencia',
                        text: 'Debe mantener al menos una fila'
                    });
                }
            }
        });
    }
    
    const btnAgregarProveedorEditar = document.getElementById('btn-agregar-proveedor-modal-editar');
    if (btnAgregarProveedorEditar) {
        btnAgregarProveedorEditar.addEventListener('click', () => $('#modalNuevoProveedorEditar').modal('show'));
    }
    
    const btnGuardarProveedorModalEditar = document.getElementById('btn-guardar-proveedor-modal-editar');
    if (btnGuardarProveedorModalEditar) {
        btnGuardarProveedorModalEditar.addEventListener('click', guardarProveedorModalEditar);
    }
    
    $('#modalNuevoProveedorEditar').on('hidden.bs.modal', limpiarFormularioProveedorEditar);
}

function guardarProveedorModalEditar() {
    const form = document.getElementById('form-nuevo-proveedor-modal-editar');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const rucInput = form.querySelector('input[name="ruc_proveedor"]');
    const ruc = rucInput.value.trim();
    
    if (ruc.length !== 11 || !/^\d+$/.test(ruc)) {
        Swal.fire({
            icon: 'error',
            title: 'RUC inválido',
            text: 'El RUC debe contener exactamente 11 dígitos numéricos'
        });
        rucInput.focus();
        return;
    }
    
    const formData = new FormData(form);
    formData.append('registrar_ajax', '1');
    
    const btnGuardar = document.getElementById('btn-guardar-proveedor-modal-editar');
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';
    
    fetch('proveedor_nuevo_directo.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // ✅ ACTUALIZAR SELECT DE PROVEEDOR EN MODAL EDITAR
            const selectProveedor = document.getElementById('edit_proveedor_orden');
            const newOption = new Option(data.nombre_proveedor, data.id_proveedor, true, true);
            selectProveedor.add(newOption);
            
            $('#modalNuevoProveedorEditar').modal('hide');
            form.reset();
            
            Swal.fire({
                icon: 'success',
                title: '¡Proveedor agregado!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor.'
        });
    })
    .finally(() => {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = '<i class="fa fa-save"></i> Registrar';
    });
}

function limpiarFormularioProveedorEditar() {
    const form = document.getElementById('form-nuevo-proveedor-modal-editar');
    if (form) {
        form.reset();
        const tablaCuentas = document.getElementById('tabla-cuentas-modal-editar');
        if (tablaCuentas) {
            const filas = tablaCuentas.querySelectorAll('tr');
            for (let i = filas.length - 1; i > 0; i--) {
                filas[i].remove();
            }
        }
    }
}

function descargarExcel() {
    Swal.fire({
        title: "Generando Excel...",
        text: "Por favor espera",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    // Simular espera corta
    setTimeout(() => {
        window.location.href = "generar_excel.php";

        Swal.close();
        Swal.fire(
            "¡Listo!",
            "Excel generado correctamente y estados actualizados.",
            "success"
        );
    }, 800);
}

// ============================================================================
// AUTO-ABRIR MODAL DESDE URL (CUANDO VIENE DE UNA ALERTA)
// ============================================================================
<?php if (isset($_GET['abrir_detalle']) && !empty($_GET['abrir_detalle'])): ?>
window.addEventListener('DOMContentLoaded', function() {
    const idCompraAbrir = <?php echo intval($_GET['abrir_detalle']); ?>;
    
    setTimeout(function() {
        mostrarDetalleOrdenCompra(idCompraAbrir);
        
        // Limpiar URL después de abrir
        setTimeout(function() {
            const url = new URL(window.location);
            url.searchParams.delete('abrir_detalle');
            window.history.replaceState({}, document.title, url.pathname + url.search);
        }, 1000);
    }, 500);
});
<?php endif; ?>
</script>