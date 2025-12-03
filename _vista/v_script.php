    <script src="../_complemento/js/jquery.min.js"></script>
    <script src="../_complemento/build/js/jquery-1.4.2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- jQuery -->
    <script src="../_complemento/vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../_complemento/vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FastClick -->
    <script src="../_complemento/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../_complemento/vendors/nprogress/nprogress.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="../_complemento/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="../_complemento/vendors/iCheck/icheck.min.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="../_complemento/vendors/moment/min/moment.min.js"></script>
    <script src="../_complemento/vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap-wysiwyg -->
    <script src="../_complemento/vendors/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js"></script>
    <script src="../_complemento/vendors/jquery.hotkeys/jquery.hotkeys.js"></script>
    <script src="../_complemento/vendors/google-code-prettify/src/prettify.js"></script>
    <!-- jQuery Tags Input -->
    <script src="../_complemento/vendors/jquery.tagsinput/src/jquery.tagsinput.js"></script>
    <!-- Switchery -->
    <script src="../_complemento/vendors/switchery/dist/switchery.min.js"></script>
    <!-- Select2 -->
    <link href="../_complemento/vendors/select2/dist/css/select2.min.css" rel="stylesheet">
    <script src="../_complemento/vendors/select2/dist/js/select2.full.min.js"></script>
    <!-- Parsley -->
    <script src="../_complemento/vendors/parsleyjs/dist/parsley.min.js"></script>
    <!-- Autosize -->
    <script src="../_complemento/vendors/autosize/dist/autosize.min.js"></script>
    <!-- jQuery autocomplete -->
    <script src="../_complemento/vendors/devbridge-autocomplete/dist/jquery.autocomplete.min.js"></script>
    <!-- starrr -->
    <script src="../_complemento/vendors/starrr/dist/starrr.js"></script>

    <!-- Datatables -->
    <script src="../_complemento/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../_complemento/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="../_complemento/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../_complemento/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="../_complemento/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../_complemento/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../_complemento/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../_complemento/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="../_complemento/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="../_complemento/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../_complemento/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="../_complemento/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="../_complemento/vendors/jszip/dist/jszip.min.js"></script>
    <script src="../_complemento/vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../_complemento/vendors/pdfmake/build/vfs_fonts.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="../_complemento/build/js/custom.min.js"></script>

    <script language="javascript" type="text/javascript">
        //-----------------------------------------------------------------------------   
        function soloNumeros(e) {
            var keynum = window.event ? window.event.keyCode : e.which;
            if ((keynum == 8) || (keynum == 46))
                return true;

            return /\d/.test(String.fromCharCode(keynum));
        }
        //-----------------------------------------------------------------------------   
        function soloLetras(e) {
            key = e.keyCode || e.which;
            tecla = String.fromCharCode(key).toString();
            letras = " áéíóúabcdefghijklmnñopqrstuvwxyzÁÉÍÓÚABCDEFGHIJKLMNÑOPQRSTUVWXYZ"; //Se define todo el abecedario que se quiere que se muestre.
            especiales = [8, 37, 39, 46, 6]; //Es la validación del KeyCodes, que teclas recibe el campo de texto.

            tecla_especial = false
            for (var i in especiales) {
                if (key == especiales[i]) {
                    tecla_especial = true;
                    break;
                }
            }

            if (letras.indexOf(tecla) == -1 && !tecla_especial) {
                //alert('Ingrese solo texto');
                return false;
            }
        }
        //-----------------------------------------------------------------------------  
    </script>


<!-- Select2 -->
<!-- Solicitante -->
<script>
$(document).ready(function() {

  // Inicializar Select2 para el campo Solicitante
  const $solicitante = $('#id_solicitante');

  if ($solicitante.length) {
    $solicitante.select2({
      placeholder: 'Seleccionar solicitante...',
      allowClear: true,
      width: '100%',
      minimumInputLength: 0,
      language: {
        noResults: function() { return "No se encontraron resultados"; },
        searching: function() { return "Buscando..."; },
      },
    });
  }

});
</script>

<!-- Proveedor -->
<script>
$(document).ready(function() {
  const $proveedor = $('#proveedor_orden');

  if ($proveedor.length) {
    $proveedor.select2({
      placeholder: 'Seleecionar proveedor...',
      allowClear: true,
      width: '100%',
      minimumInputLength: 0, 
      language: {
        noResults: function() { return "No se encontraron resultados"; },
        searching: function() { return "Buscando..."; }
      }
    });
  }
});
</script>

<!-- Centro de Costos -->
<script>
$(document).ready(function () {
  var $cc = $('#id_centro_costo');
  if ($cc.length) {
    $cc.select2({
      placeholder: 'Seleccionar centro de costos...',
      allowClear: true,
      width: '100%',
      minimumInputLength: 0,
      language: {
        noResults: function () { return 'No se encontraron resultados'; },
        searching: function () { return 'Buscando...'; }
      }
    });
  }
});
</script>

<!-- Personal encargado que encargado y el que recibe -->
<script>
$(document).ready(function () {
  function initSelect2($el, placeholder) {
    if ($el.length) {
      $el.select2({
        placeholder: placeholder,
        allowClear: true,           
        width: '100%',
        minimumInputLength: 0,
        language: {
          noResults: function () { return 'No se encontraron resultados'; },
          searching: function () { return 'Buscando...'; }
        }
      });
    }
  }

  initSelect2($('#id_personal_encargado'), 'Seleccionar personal encargado...');
  initSelect2($('#id_personal_recibe'), 'Seleccionar personal que recibe...');
});
</script>

<!-- Proveedor (Dashboard) -->
<script>
$(document).ready(function () {
  const $provDash = $('#proveedor');
  if ($provDash.length) {
    $provDash.select2({
      placeholder: 'Todos',
      allowClear: true,       
      width: '100%',
      minimumInputLength: 0,
      language: {
        noResults: function () { return 'No se encontraron resultados'; },
        searching: function () { return 'Buscando...'; }
      }
    });
  }
});
</script>

<!-- Almacén | Ubicación | Cliente destino -->
<script>
$(document).ready(function () {
  function initSelect2($el, placeholder) {
    if ($el.length) {
      $el.select2({
        placeholder: placeholder,
        allowClear: true,      
        width: '100%',
        minimumInputLength: 0,
        language: {
          noResults: function () { return 'No se encontraron resultados'; },
          searching: function () { return 'Buscando...'; }
        }
      });
    }
  }
  
  initSelect2($('select[name="id_almacen"]'),  'Seleccionar almacén...');
  initSelect2($('select[name="id_ubicacion"]'), 'Seleccionar ubicación...');
  //nitSelect2($('select[name="id_cliente_destino"]'), 'Seleccionar cliente destino...');
});
</script>

<!-- v_pedidos_nuevo: Tipo de pedido, Almacén y Unidad de Medida -->
<script>
$(document).ready(function () {
  function initSelect2($el, placeholder) {
    if ($el.length) {
      $el.select2({
        placeholder: placeholder,
        allowClear: true,   
        width: '100%',
        minimumInputLength: 0,
        language: {
          noResults: function () { return 'No se encontraron resultados'; },
          searching: function () { return 'Buscando...'; }
        }
      });
    }
  }

  // Fijos
  initSelect2($('select[name="tipo_pedido"]'), 'Seleccionar tipo de pedido...');
  initSelect2($('select[name="id_obra"]'), 'Seleccionar almacén...');

  // Unidad de Medida 
  function initUnidadMedida($scope) {
    $scope.find('select[name="unidad[]"]').each(function () {
      if (!$(this).data('select2')) {
        initSelect2($(this), 'Seleccionar unidad de medida...');
      }
    });
  }

  // Inicial existentes
  initUnidadMedida($(document));

  // Inicial las nuevas cuando se agrega material
  $(document).on('click', '#agregar-material', function () {
    setTimeout(function () {
      const $nuevoItem = $('#contenedor-materiales .material-item').last();
      initUnidadMedida($nuevoItem);
    }, 0);
  });
});
</script>

<!-- Centros de Costo Múltiples en Detalle de Pedidos - INDEPENDIENTES -->
<script>
$(document).ready(function () {
  // Función única para inicializar Select2 múltiple en centros de costo del detalle
  function initCentrosCostoDetalle($scope) {
    $scope.find('select.select2-centros-costo-detalle').each(function () {
      if (!$(this).data('select2')) {
        $(this).select2({
          placeholder: 'Seleccionar uno o más centros de costo...',
          allowClear: true,   
          width: '100%',
          minimumInputLength: 0,
          language: {
            noResults: function () { return 'No se encontraron resultados'; },
            searching: function () { return 'Buscando...'; }
          }
        });
      }
    });
  }

  // Inicializar centros de costo en los materiales existentes al cargar la página
  initCentrosCostoDetalle($(document));

  // Al agregar nuevo material - SIN preselección automática
  $(document).on('click', '#agregar-material', function () {
    setTimeout(function () {
      const $nuevoItem = $('#contenedor-materiales .material-item').last();
      
      // Inicializar Select2 en el nuevo item (VACÍO, sin preselección)
      initCentrosCostoDetalle($nuevoItem);
    }, 100);
  });
});
</script>
<!-- Personal Múltiple en Detalle de Pedidos -->
<script>
$(document).ready(function () {
  // Función para inicializar Select2 múltiple en personal del detalle
  function initPersonalDetalle($scope) {
    $scope.find('select.select2-personal-detalle').each(function () {
      if (!$(this).data('select2')) {
        $(this).select2({
          placeholder: 'Seleccionar personal...',
          allowClear: true,
          width: '100%',
          minimumInputLength: 0,
          language: {
            noResults: function () { return 'No se encontraron resultados'; },
            searching: function () { return 'Buscando...'; }
          }
        });
      }
    });
  }

  // Inicializar personal en los materiales existentes al cargar la página
  initPersonalDetalle($(document));

  // Al agregar nuevo material
  $(document).on('click', '#agregar-material', function () {
    setTimeout(function () {
      const $nuevoItem = $('#contenedor-materiales .material-item').last();
      initPersonalDetalle($nuevoItem);
    }, 100);
  });
});
</script>

<!--  (v_salidas_nuevo): Tipo de material + Origen/Destino -->
<script>
$(document).ready(function () {
  function initSelect2($el, placeholder) {
    if ($el.length) {
      $el.select2({
        placeholder: placeholder,
        allowClear: true,
        width: '100%',
        minimumInputLength: 0,
        language: {
          noResults: function () { return 'No se encontraron resultados'; },
          searching: function () { return 'Buscando...'; }
        }
      });
    }
  }

  // Tipo de material (no tiene id en el HTML, se usa selector por name)
  initSelect2($('select[name="id_material_tipo"]'), 'Seleccionar tipo de material...');

  // Origen
  initSelect2($('#id_almacen_origen'),   'Seleccionar almacén origen...');
  initSelect2($('#id_ubicacion_origen'), 'Seleccionar ubicación origen...');

  // Destino
  initSelect2($('#id_almacen_destino'),   'Seleccionar almacén destino...');
  initSelect2($('#id_ubicacion_destino'), 'Seleccionar ubicación destino...');
});
</script>


<!-- Script dinámico para manejar cuentas bancarias (VISTA_EDITAR) -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const tablaCuentas = document.getElementById("tabla-cuentas");
    const btnAgregar = document.getElementById("agregarCuenta");

    //Función para inicializar Select2 SOLO una vez por elemento
    function inicializarSelect2(context = document) {
        $(context).find('.select2_moneda').each(function() {
            if (!$(this).data('select2')) {
                $(this).select2({
                    placeholder: "Seleccione una moneda",
                    allowClear: true,
                    width: '100%'
                });
            }
        });
        $(context).find('.select2_banco').each(function() {
            if (!$(this).data('select2')) {
                $(this).select2({
                    placeholder: "Seleccione un banco",
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    }

    //Inicializa los Select2 existentes al cargar la página
    inicializarSelect2();

    //Evento para agregar nueva fila
    btnAgregar.addEventListener("click", function() {
        const nuevaFila = document.createElement("tr");
        nuevaFila.innerHTML = `
            <td>
                <select name="id_banco[]" class="form-control select2_banco" required>
                    <option value="">Seleccione un banco</option>
                    <?php if (isset($bancos) && is_array($bancos)) {
                      foreach ($bancos as $b) {
                        if ($b['est_banco'] == 1) { ?>
                        <option value="<?php echo $b['id_banco']; ?>"><?php echo $b['cod_banco']; ?></option>
                    <?php }}} ?>
                </select>
            </td>
            <td>
                <select name="id_moneda[]" class="form-control select2_moneda" required>
                    <option value="">Seleccione una moneda</option>
                    <?php if (isset($monedas) && is_array($monedas)) {
                    foreach ($monedas as $m) { ?>
                        <option value="<?php echo $m['id_moneda']; ?>"><?php echo $m['nom_moneda']; ?></option>
                    <?php }} ?>
                </select>
            </td>
            <td><input type="text" name="cta_corriente[]" class="form-control" required></td>
            <td><input type="text" name="cta_interbancaria[]" class="form-control" required></td>
            <td><button type="button" class="btn btn-danger btn-sm eliminar-fila">X</button></td>
        `;
        tablaCuentas.appendChild(nuevaFila);

        // Inicializa Select2 solo en la nueva fila
        inicializarSelect2(nuevaFila);
    });

    //Evento para eliminar filas
    tablaCuentas.addEventListener("click", function(e) {
        if (e.target.classList.contains("eliminar-fila")) {
            e.target.closest("tr").remove();
        }
    });
});
</script>

<!-- Script dinámico para manejar cuentas bancarias (MODAL CUENTAS)-->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const tablaCuentasModal = document.getElementById("tabla-cuentas-modal");
    const btnAgregarModal = document.getElementById("agregarCuentaModal");

    // Función para inicializar Select2 con configuración común
    function inicializarSelect2($scope, dropdownParent = null) {
        const opcionesBanco = {
            placeholder: "Seleccione un banco",
            allowClear: true,
            width: "100%"
        };
        const opcionesMoneda = {
            placeholder: "Seleccione una moneda",
            allowClear: true,
            width: "100%"
        };
        if (dropdownParent) {
            opcionesBanco.dropdownParent = dropdownParent;
            opcionesMoneda.dropdownParent = dropdownParent;
        }

        $scope.find('.select2_banco').select2(opcionesBanco);
        $scope.find('.select2_moneda').select2(opcionesMoneda);
    }

    // Acción para agregar nueva fila en el modal
    btnAgregarModal.addEventListener("click", function() {
        const nuevaFila = document.createElement("tr");
        nuevaFila.innerHTML = `
            <td>
                <select name="id_banco[]" class="form-control select2_banco" required>
                    <option value="">Seleccione un banco</option>
                    <?php if (isset($bancos) && is_array($bancos)) {
                    foreach ($bancos as $b) { if ($b['est_banco'] == 1) { ?>
                        <option value="<?php echo $b['id_banco']; ?>">
                            <?php echo $b['cod_banco']; ?>
                        </option>
                    <?php }} } ?>
                </select>
            </td>
            <td>
                <select name="id_moneda[]" class="form-control select2_moneda" required>
                    <option value="">Seleccione una moneda</option>
                    <?php if (isset($monedas) && is_array($monedas)) {
                    foreach ($monedas as $m) { ?>
                        <option value="<?php echo $m['id_moneda']; ?>"><?php echo $m['nom_moneda']; ?></option>
                    <?php }} ?>
                </select>
            </td>
            <td><input type="text" name="cta_corriente[]" class="form-control" required></td>
            <td><input type="text" name="cta_interbancaria[]" class="form-control" required></td>
            <td><button type="button" class="btn btn-danger btn-sm eliminar-fila-modal">X</button></td>
        `;
        tablaCuentasModal.appendChild(nuevaFila);

        // Inicializa Select2 solo en la nueva fila
        inicializarSelect2($(nuevaFila), $('#modalNuevoProveedor'));
    });

    // Acción para eliminar fila dentro del modal
    tablaCuentasModal.addEventListener("click", function(e) {
        if (e.target.classList.contains("eliminar-fila-modal")) {
            e.target.closest("tr").remove();
        }
    });

    // Inicialización inicial cuando se muestra el modal
    $('#modalNuevoProveedor').on('shown.bs.modal', function() {
        inicializarSelect2($('#modalNuevoProveedor'), $('#modalNuevoProveedor'));
    });
});
</script>

<script>
// ================================================================
// GESTIÓN DE CUENTAS BANCARIAS (MODA EDITAR PROVEEDOR)
// ================================================================
document.addEventListener("DOMContentLoaded", function () {
    const tablaCuentasModalEditar = document.getElementById("tabla-cuentas-modal-editar");
    const btnAgregarCuentaEditar = document.getElementById("agregarCuentaModal");

    //Inicializar Select2 con configuración común
    function inicializarSelect2Editar($scope, dropdownParent = null) {
        const opcionesBanco = {
            placeholder: "Seleccione un banco",
            allowClear: true,
            width: "100%"
        };
        const opcionesMoneda = {
            placeholder: "Seleccione una moneda",
            allowClear: true,
            width: "100%"
        };
        if (dropdownParent) {
            opcionesBanco.dropdownParent = dropdownParent;
            opcionesMoneda.dropdownParent = dropdownParent;
        }

        $scope.find('.select2_banco').select2(opcionesBanco);
        $scope.find('.select2_moneda').select2(opcionesMoneda);
    }

    //Evento: Agregar nueva fila de cuenta
    if (btnAgregarCuentaEditar) {
        btnAgregarCuentaEditar.addEventListener("click", function () {
            const nuevaFila = document.createElement("tr");
            nuevaFila.innerHTML = `
                <td>
                    <select name="id_banco[]" class="form-control select2_banco" required>
                        <option value="">Seleccione un banco</option>
                        <?php if (isset($bancos) && is_array($bancos)) {
                        foreach ($bancos as $b) { if ($b['est_banco'] == 1) { ?>
                            <option value="<?php echo $b['id_banco']; ?>">
                                <?php echo $b['cod_banco']; ?>
                            </option>
                        <?php }} } ?>
                    </select>
                </td>
                <td>
                    <select name="id_moneda[]" class="form-control select2_moneda" required>
                        <option value="">Seleccione una moneda</option>
                        <?php if (isset($monedas) && is_array($monedas)) {
                          foreach ($monedas as $m) { ?>
                            <option value="<?php echo $m['id_moneda']; ?>"><?php echo $m['nom_moneda']; ?></option>
                        <?php }} ?>
                    </select>
                </td>
                <td><input type="text" name="cta_corriente[]" class="form-control" required></td>
                <td><input type="text" name="cta_interbancaria[]" class="form-control" required></td>
                <td><button type="button" class="btn btn-danger btn-sm eliminar-fila-modal-editar">X</button></td>
            `;
            tablaCuentasModalEditar.appendChild(nuevaFila);

            // Re-inicializa Select2 solo en la nueva fila
            inicializarSelect2Editar($(nuevaFila), $('#modalNuevoProveedorEditar'));
        });
    }

    //Evento: Eliminar fila
    if (tablaCuentasModalEditar) {
        tablaCuentasModalEditar.addEventListener("click", function (e) {
            if (e.target.classList.contains("eliminar-fila-modal-editar")) {
                const filas = tablaCuentasModalEditar.querySelectorAll("tr");
                if (filas.length > 1) {
                    e.target.closest("tr").remove();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Advertencia',
                        text: 'Debe mantener al menos una fila de cuenta'
                    });
                }
            }
        });
    }

    //Cuando se abre el modal, inicializar Select2
    $('#modalNuevoProveedorEditar').on('shown.bs.modal', function () {
        inicializarSelect2Editar($('#modalNuevoProveedorEditar'), $('#modalNuevoProveedorEditar'));
    });
});
</script>

<!-- Personal (Nuevo Usuario) -->
<script>
$(document).ready(function () {
  const $personal = $('#id_personal');

  if ($personal.length) {
    $personal.select2({
      placeholder: 'Seleccionar personal...',
      allowClear: true,
      width: '100%',
      minimumInputLength: 0,
      language: {
        noResults: function () { return 'No se encontraron resultados'; },
        searching: function () { return 'Buscando...'; }
      }
    });
  }
});
</script>

<!-- Select2 para Dashboard - Proveedores y Centros de Costo MÚLTIPLES -->
<script>
$(document).ready(function () {
    // ⭐ PROVEEDORES MÚLTIPLES
    const $provDash = $('.select2-proveedores-dashboard');
    if ($provDash.length) {
        $provDash.select2({
            placeholder: 'Seleccione uno o más proveedores...',
            allowClear: true,
            width: '100%',
            multiple: true,  // ⭐ CLAVE: Habilita selección múltiple
            minimumInputLength: 0,
            language: {
                noResults: function () { return 'No se encontraron resultados'; },
                searching: function () { return 'Buscando...'; }
            }
        });
    }

    // ⭐ CENTROS DE COSTO MÚLTIPLES
    const $centroDash = $('.select2-centros-dashboard');
    if ($centroDash.length) {
        $centroDash.select2({
            placeholder: 'Seleccione uno o más centros de costo...',
            allowClear: true,
            width: '100%',
            multiple: true,  // ⭐ CLAVE: Habilita selección múltiple
            minimumInputLength: 0,
            language: {
                noResults: function () { return 'No se encontraron resultados'; },
                searching: function () { return 'Buscando...'; }
            }
        });
    }
});

</script>
<!-- Personal encargado y receptor (v_pedido_verificar.php) -->
<script>
$(document).ready(function () {
  function initSelect2($el, placeholder) {
    if ($el.length) {
      $el.select2({
        placeholder: placeholder,
        allowClear: true,           
        width: '100%',
        minimumInputLength: 0,
        language: {
          noResults: function () { return 'No se encontraron resultados'; },
          searching: function () { return 'Buscando...'; }
        }
      });
    }
  }

  // Inicializar Select2 para personal encargado y receptor
  initSelect2($('#personal_encargado_salida'), 'Seleccionar personal encargado...');
  initSelect2($('#personal_recibe_salida'), 'Seleccionar personal que recibe...');
});
</script>






