<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('importar_proveedor')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PROVEEDOR', 'IMPORTAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: proveedor_importar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Importar Proveedores</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_modelo/m_proveedor.php");

            //-------------------------------------------
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
                    ?>
                        <script>
                            location.href = 'proveedor_mostrar.php?actualizado=true';
                        </script>
                    <?php
                } else {
                    ?>
                        <script>
                            location.href = 'proveedor_mostrar.php?error=true';
                        </script>
                    <?php
                }
            } else {
                ?>
                    <script>
                        location.href = 'proveedor_mostrar.php?error=true';
                    </script>
                <?php
            }
            //-------------------------------------------
            ?>
        </div>
    </div>

    <?php
    require_once("../_vista/v_script.php");
    require_once("../_vista/v_alertas.php");
    ?>
</body>
</html>

