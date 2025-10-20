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