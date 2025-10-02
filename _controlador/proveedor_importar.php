<?php
//=======================================================================
// CONTROLADOR: proveedor_importar.php
//=======================================================================
require_once("../_modelo/m_proveedor.php");

if (isset($_FILES['archivo']['tmp_name'])) {
    $archivo = $_FILES['archivo']['tmp_name'];

    // Detectar delimitador automáticamente
    $firstLine = file($archivo)[0];
    $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

    if (($handle = fopen($archivo, "r")) !== FALSE) {
        fgetcsv($handle, 1000, $delimiter); // Omitir encabezado

        while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {

            // Detectar codificación y convertir solo si no es UTF-8
            $data = array_map(function($value) {
                $encoding = mb_detect_encoding($value, ['UTF-8','ISO-8859-1','Windows-1252'], true);
                if ($encoding !== 'UTF-8') {
                    return mb_convert_encoding($value, 'UTF-8', $encoding);
                }
                return $value;
            }, $data);

            $data = array_map('trim', $data);

            // === DATOS DEL PROVEEDOR ===
            $nom   = $data[0];
            $ruc   = $data[1];
            $dir   = $data[2];
            $tel   = $data[3];
            $cont  = $data[4];
            $est   = (int)$data[5];
            $mail  = $data[6];

            // === DATOS DE LA CUENTA ===
            $banco = $data[7];
            $mon   = (int)$data[8];
            $cta   = $data[9];
            $cci   = $data[10];
            $est_cta = (int)$data[11];

            // Verificar proveedor por RUC
            $prov = BuscarProveedorPorRuc($ruc);

            if (!$prov) {
                $id_proveedor = GrabarProveedor($nom, $ruc, $dir, $tel, $cont, $est, $mail);
            } else {
                $id_proveedor = $prov['id_proveedor'];
            }

            // Grabar cuenta si no existe
            if ($id_proveedor && $id_proveedor != "ERROR" && $id_proveedor != "NO") {
                if (!CuentaProveedorExiste($id_proveedor, $cta, $cci)) {
                    GrabarCuentaProveedorConEstado($id_proveedor, $banco, $mon, $cta, $cci, $est_cta);
                }
            }
        }

        fclose($handle);

        // 🔹 Modal igual al de edición de proveedor
        echo "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <title>Carga Exitosa</title>
            <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css'>
            <link rel='stylesheet' href='../_assets/css/custom.css'>
        </head>
        <body>
            <div class='modal fade' id='importSuccessModal' tabindex='-1' role='dialog' aria-labelledby='importSuccessModalLabel' aria-hidden='true'>
              <div class='modal-dialog modal-dialog-centered' role='document'>
                <div class='modal-content x_panel border-success shadow'>
                  <div class='modal-header bg-success text-white'>
                    <h5 class='modal-title' id='importSuccessModalLabel'>¡Carga de Datos Exitosa!</h5>
                    <button type='button' class='close text-white' data-dismiss='modal' aria-label='Cerrar'>
                      <span aria-hidden='true'>&times;</span>
                    </button>
                  </div>
                  <div class='modal-body'>
                    La carga del archivo CSV se ha realizado correctamente.
                  </div>
                  <div class='modal-footer'>
                    <a href='proveedor_mostrar.php' class='btn btn-success'>Aceptar</a>
                  </div>
                </div>
              </div>
            </div>

            <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
            <script src='https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js'></script>
            <script>
              $(document).ready(function(){
                  $('#importSuccessModal').modal('show');
              });
            </script>
        </body>
        </html>
        ";

    } else {
        echo "Error al abrir el archivo.";
    }
} else {
    echo "No se subió ningún archivo.";
}
?>
