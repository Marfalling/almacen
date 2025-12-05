<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('editar_proveedor')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PROVEEDOR', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//=======================================================================
// CONTROLADOR: proveedor_editar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editar Proveedor</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_proveedor.php");
            require_once("../_modelo/m_banco.php");
            require_once("../_modelo/m_moneda.php");
            
            $bancos = MostrarBanco();
            $monedas = MostrarMoneda();
            
            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_proveedor = $_REQUEST['id_proveedor'];
                $nom = strtoupper(trim($_REQUEST['nom']));
                $ruc = strtoupper(trim($_REQUEST['ruc']));
                $dir = strtoupper(trim($_REQUEST['dir']));
                $tel = strtoupper(trim($_REQUEST['tel']));
                $cont = strtoupper(trim($_REQUEST['cont']));
                $email = strtolower(trim($_REQUEST['email']));
                $est = isset($_REQUEST['est']) ? 1 : 0;

                //  OBTENER DATOS ANTES DE EDITAR
                $datos_antes = ObtenerProveedor($id_proveedor);
                $nom_anterior = $datos_antes['nom_proveedor'] ?? '';
                $ruc_anterior = $datos_antes['ruc_proveedor'] ?? '';
                $est_anterior = $datos_antes['est_proveedor'] ?? 0;
                
                // Contar cuentas antes
                $cuentas_antes = ObtenerCuentasProveedor($id_proveedor);
                $cant_cuentas_antes = count($cuentas_antes);

                //  ACTUALIZAR DATOS PRINCIPALES
                $rpta = ActualizarProveedor($id_proveedor, $nom, $ruc, $dir, $tel, $cont, $est, $email);

                if ($rpta == "SI") {
                    // Procesar cuentas bancarias
                    EliminarCuentasProveedor($id_proveedor);

                    $lista_bancos = $_POST['id_banco'] ?? [];
                    $lista_monedas = $_POST['id_moneda'] ?? [];
                    $lista_corrientes = $_POST['cta_corriente'] ?? [];
                    $lista_interbancarias = $_POST['cta_interbancaria'] ?? [];
                    
                    $cantidad_cuentas = count($lista_bancos);

                    for ($i = 0; $i < $cantidad_cuentas; $i++) {
                        $id_banco = strtoupper(trim($lista_bancos[$i]));
                        $moneda = intval($lista_monedas[$i]);
                        $cta_corriente = trim($lista_corrientes[$i]);
                        $cta_interbancaria = trim($lista_interbancarias[$i]);
                        GrabarCuentaProveedor($id_proveedor, $id_banco, $moneda, $cta_corriente, $cta_interbancaria);
                    }
                    
                    //  COMPARAR Y CONSTRUIR DESCRIPCIÓN
                    $cambios = [];
                    
                    if ($nom_anterior != $nom) {
                        $cambios[] = "Nombre: '$nom_anterior' → '$nom'";
                    }
                    if ($ruc_anterior != $ruc) {
                        $cambios[] = "RUC: '$ruc_anterior' → '$ruc'";
                    }
                    if ($est_anterior != $est) {
                        $estado_ant = ($est_anterior == 1) ? 'Activo' : 'Inactivo';
                        $estado_nvo = ($est == 1) ? 'Activo' : 'Inactivo';
                        $cambios[] = "Estado: $estado_ant → $estado_nvo";
                    }
                    if ($cant_cuentas_antes != $cantidad_cuentas) {
                        $cambios[] = "Cuentas: $cant_cuentas_antes → $cantidad_cuentas";
                    }
                    
                    if (empty($cambios)) {
                        $descripcion = "ID: $id_proveedor | Sin cambios";
                    } else {
                        $descripcion = "ID: $id_proveedor | " . implode(' | ', $cambios);
                    }
                    
                    //  AUDITORÍA: EDICIÓN EXITOSA
                    GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'PROVEEDOR', $descripcion);
                    ?>
                        <script Language="JavaScript">
                            location.href = 'proveedor_mostrar.php?actualizado=true';
                        </script>
                    <?php
                } else if ($rpta == "NO") {
                    //  AUDITORÍA: ERROR - YA EXISTE
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'PROVEEDOR', "ID: $id_proveedor | RUC '$ruc' ya existe");
                    ?>
                        <script Language="JavaScript">
                            location.href = 'proveedor_mostrar.php?existe=true';
                        </script>
                    <?php
                } else {
                    //  AUDITORÍA: ERROR GENERAL
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'PROVEEDOR', "ID: $id_proveedor | Error del sistema");
                    ?>
                        <script Language="JavaScript">
                            location.href = 'proveedor_mostrar.php?error=true';
                        </script>
                    <?php
                }
            }
            //-------------------------------------------

            // Obtener ID del proveedor desde GET
            $id_proveedor = isset($_GET['id_proveedor']) ? $_GET['id_proveedor'] : '';
            if ($id_proveedor == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del proveedor a editar
            $proveedor_data = ObtenerProveedor($id_proveedor);
            if ($proveedor_data) {
                $nom = $proveedor_data['nom_proveedor'];
                $ruc = $proveedor_data['ruc_proveedor'];
                $dir = $proveedor_data['dir_proveedor'];
                $tel = $proveedor_data['tel_proveedor'];
                $cont = $proveedor_data['cont_proveedor'];
                $email = $proveedor_data['mail_proveedor'];
                $est = ($proveedor_data['est_proveedor'] == 1) ? "checked" : "";

                // Aquí obtenemos las cuentas bancarias
                $cuentas = ObtenerCuentasProveedor($id_proveedor);
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_proveedor_editar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php
    require_once("../_vista/v_script.php");
    require_once("../_vista/v_alertas.php");
    ?>
</body>
</html>