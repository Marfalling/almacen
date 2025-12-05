<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php"); 

//=======================================================================
// CONTROLADOR: centro_costo_editar.php 
//=======================================================================

require_once("../_modelo/m_centro_costo.php");

// Verificar permiso
if (!verificarPermisoEspecifico('editar_centro de costo')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'CENTRO DE COSTO', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

//-------------------------------------------
// OPERACIÓN DE EDICIÓN
//-------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_centro = intval($_POST['id_centro_costo']);
    
    //  OBTENER DATOS ANTES DE EDITAR
    $centro_antes = ObtenerCentroCostoPorId($id_centro);
    $nom_anterior = $centro_antes['nom_area'];
    $est_anterior = $centro_antes['act_area'];
    
    // Obtener nuevos valores
    $nom_nuevo = strtoupper(trim($_POST['nom_centro_costo']));
    $est_nuevo = isset($_POST['est']) ? 1 : 0;

    $rpta = EditarCentroCosto($id_centro, $nom_nuevo);
    
    if ($rpta == "SI") {
        CambiarEstadoCentroCosto($id_centro, $est_nuevo);
        
        //  CONSTRUIR DESCRIPCIÓN CON CAMBIOS
        $cambios = [];
        
        if ($nom_anterior != $nom_nuevo) {
            $cambios[] = "Nombre: '$nom_anterior' → '$nom_nuevo'";
        }
        
        if ($est_anterior != $est_nuevo) {
            $estado_ant = ($est_anterior == 1) ? 'Activo' : 'Inactivo';
            $estado_nue = ($est_nuevo == 1) ? 'Activo' : 'Inactivo';
            $cambios[] = "Estado: $estado_ant → $estado_nue";
        }
        
        if (count($cambios) == 0) {
            $descripcion = "ID: $id_centro | Sin cambios";
        } else {
            $descripcion = "ID: $id_centro | " . implode(' | ', $cambios);
        }
        
        GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'CENTRO DE COSTO', $descripcion);
        header("Location: centro_costo_mostrar.php?actualizado=true");
    } else {
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'CENTRO DE COSTO', "ID: $id_centro - Centro de costo ya existe");
        header("Location: centro_costo_mostrar.php?error=true");
    }
    exit;
}
//-------------------------------------------

// Obtener ID del centro de costo desde GET
$id_centro = isset($_GET['id_centro_costo']) ? intval($_GET['id_centro_costo']) : 0;

if ($id_centro == 0) {
?>
    <script Language="JavaScript">
        location.href = 'dashboard.php?error=true';
    </script>
<?php
    exit;
}

// Obtener datos del centro de costo a editar
$centro = ObtenerCentroCostoPorId($id_centro);

if (!$centro) {
?>
    <script Language="JavaScript">
        location.href = 'dashboard.php?error=true';
    </script>
<?php
    exit;
}

$est = ($centro['act_area'] == 1) ? 'checked' : '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editar Centro de Costo</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">

            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_vista/v_centro_costo_editar.php");
            require_once("../_vista/v_footer.php");
            ?>

        </div>
    </div>
    
    <?php require_once("../_vista/v_script.php"); ?>
</body>
</html>