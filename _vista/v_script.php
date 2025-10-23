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
<link href="../_complemento/vendors/select2/dist/css/select2.min.css" rel="stylesheet">
<script src="../_complemento/vendors/select2/dist/js/select2.full.min.js"></script>

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

<!-- Almacén y Ubicación (Uso de Material) -->
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

  // Usamos los name actuales para NO modificar la vista
  initSelect2($('select[name="id_almacen"]'),  'Seleccionar almacén...');
  initSelect2($('select[name="id_ubicacion"]'), 'Seleccionar ubicación...');
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




