<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php"); 

if (!verificarPermisoEspecifico('editar_banco')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'BANCO', 'EDITAR'); 
    header("location: bienvenido.php?permisos=true");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Banco</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_modelo/m_banco.php");

            //-------------------------------------------
            // OPERACIÓN DE EDICIÓN
            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_banco = $_REQUEST['id_banco'];
                
                //  OBTENER DATOS ANTES DE EDITAR
                $banco_antes = ObtenerBanco($id_banco);
                $cod_anterior = $banco_antes['cod_banco'];
                $nom_anterior = $banco_antes['nom_banco'];
                $est_anterior = $banco_antes['est_banco'];
                
                // Obtener nuevos valores
                $cod_nuevo = strtoupper(trim($_REQUEST['cod']));
                $nom_nuevo = strtoupper($_REQUEST['nom']);
                $est_nuevo = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = EditarBanco($id_banco, $cod_nuevo, $nom_nuevo, $est_nuevo);

                if ($rpta == "SI") {
                    //  CONSTRUIR DESCRIPCIÓN CON CAMBIOS
                    $cambios = [];
                    
                    if ($cod_anterior != $cod_nuevo) {
                        $cambios[] = "Código: '$cod_anterior' → '$cod_nuevo'";
                    }
                    
                    if ($nom_anterior != $nom_nuevo) {
                        $cambios[] = "Nombre: '$nom_anterior' → '$nom_nuevo'";
                    }
                    
                    if ($est_anterior != $est_nuevo) {
                        $estado_ant = ($est_anterior == 1) ? 'Activo' : 'Inactivo';
                        $estado_nue = ($est_nuevo == 1) ? 'Activo' : 'Inactivo';
                        $cambios[] = "Estado: $estado_ant → $estado_nue";
                    }
                    
                    if (count($cambios) == 0) {
                        $descripcion = "ID: $id_banco | Sin cambios";
                    } else {
                        $descripcion = "ID: $id_banco | " . implode(' | ', $cambios);
                    }
                    
                    GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'BANCO', $descripcion);
                ?>
                    <script Language="JavaScript">
                        location.href = 'banco_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'BANCO', "ID: $id_banco - Banco ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'banco_mostrar.php?error=true';
                    </script>
                <?php
                }
                exit;
            }
            //-------------------------------------------

            // Obtener ID del banco desde GET
            $id_banco = isset($_GET['id_banco']) ? $_GET['id_banco'] : '';
            if ($id_banco == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del banco a editar
            $banco_data = ObtenerBanco($id_banco);
            if ($banco_data) {
                $cod = $banco_data['cod_banco'];
                $nom = $banco_data['nom_banco'];
                $est = ($banco_data['est_banco'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_banco_editar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>
    <?php require_once("../_vista/v_script.php"); ?>
</body>
</html>
