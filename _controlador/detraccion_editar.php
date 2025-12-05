<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php"); 

//=======================================================================
// CONTROLADOR: detraccion_editar.php 
//=======================================================================

// Verificar permisos
if (!verificarPermisoEspecifico('editar_detraccion')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'DETRACCION', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_detraccion.php");

// Obtener tipos de detracción para el formulario
$tipos_detraccion = ObtenerTiposDetraccion();

//-------------------------------------------
// OPERACIÓN DE EDICIÓN
//-------------------------------------------
if (isset($_POST['registrar'])) {
    $id_detraccion = intval($_POST['id_detraccion']);
    
    //  OBTENER DATOS ANTES DE EDITAR
    $detraccion_antes = ObtenerDetraccionPorId($id_detraccion);
    $nom_anterior = $detraccion_antes['nombre_detraccion'];
    $cod_anterior = $detraccion_antes['cod_detraccion'];
    $porcentaje_anterior = $detraccion_antes['porcentaje'];
    $estado_anterior = $detraccion_antes['est_detraccion'];
    $tipo_anterior = $detraccion_antes['id_detraccion_tipo'];
    
    // Obtener nuevos valores
    $nom_nuevo = strtoupper(trim($_POST['nom']));
    $cod_nuevo = strtoupper(trim($_POST['cod_detraccion']));
    $porcentaje_nuevo = floatval($_POST['porcentaje']);
    $estado_nuevo = isset($_POST['estado']) ? 1 : 0;
    $tipo_nuevo = intval($_POST['id_detraccion_tipo']);

    $rpta = EditarDetraccion($id_detraccion, $nom_nuevo, $cod_nuevo, $porcentaje_nuevo, $estado_nuevo, $tipo_nuevo);

    if ($rpta === "SI") {
        //  CONSTRUIR DESCRIPCIÓN CON CAMBIOS
        $cambios = [];
        
        if ($nom_anterior != $nom_nuevo) {
            $cambios[] = "Nombre: '$nom_anterior' → '$nom_nuevo'";
        }
        
        if ($cod_anterior != $cod_nuevo) {
            $cambios[] = "Código: '$cod_anterior' → '$cod_nuevo'";
        }
        
        if ($porcentaje_anterior != $porcentaje_nuevo) {
            $cambios[] = "Porcentaje: $porcentaje_anterior% → $porcentaje_nuevo%";
        }
        
        if ($estado_anterior != $estado_nuevo) {
            $estado_ant = ($estado_anterior == 1) ? 'Activo' : 'Inactivo';
            $estado_nue = ($estado_nuevo == 1) ? 'Activo' : 'Inactivo';
            $cambios[] = "Estado: $estado_ant → $estado_nue";
        }
        
        if ($tipo_anterior != $tipo_nuevo) {
            // Obtener nombres de los tipos para mejor legibilidad
            $nombre_tipo_ant = '';
            $nombre_tipo_nue = '';
            foreach ($tipos_detraccion as $tipo) {
                if ($tipo['id_detraccion_tipo'] == $tipo_anterior) {
                    $nombre_tipo_ant = $tipo['nombre_tipo_detraccion'];
                }
                if ($tipo['id_detraccion_tipo'] == $tipo_nuevo) {
                    $nombre_tipo_nue = $tipo['nombre_tipo_detraccion'];
                }
            }
            $cambios[] = "Tipo: '$nombre_tipo_ant' → '$nombre_tipo_nue'";
        }
        
        if (count($cambios) == 0) {
            $descripcion = "ID: $id_detraccion | Código: $cod_nuevo | Sin cambios";
        } else {
            $descripcion = "ID: $id_detraccion | Código: $cod_nuevo | " . implode(' | ', $cambios);
        }
        
        GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'DETRACCION', $descripcion);
        header("Location: detraccion_mostrar.php?actualizado=true");
        exit;
    } elseif ($rpta === "NO") {
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'DETRACCION', "ID: $id_detraccion - Código: $cod_nuevo - Detracción ya existe");
        header("Location: detraccion_mostrar.php?error=duplicado");
        exit;
    } else {
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'DETRACCION', "ID: $id_detraccion - Código: $cod_nuevo");
        header("Location: detraccion_mostrar.php?error=true");
        exit;
    }
}
//-------------------------------------------

// Obtener ID de la detracción desde GET
$id_detraccion = isset($_GET['id_detraccion']) ? intval($_GET['id_detraccion']) : 0;

if ($id_detraccion <= 0) {
    header("Location: detraccion_mostrar.php?error=true");
    exit;
}

// Obtener datos de la detracción a editar
$detraccion_data = ObtenerDetraccionPorId($id_detraccion);

if (!$detraccion_data) {
    header("Location: detraccion_mostrar.php?error=true");
    exit;
}

$nom                        = $detraccion_data['nombre_detraccion'];
$cod_detraccion             = $detraccion_data['cod_detraccion'];
$porcentaje                 = $detraccion_data['porcentaje'];
$estado                     = $detraccion_data['est_detraccion'];
$id_detraccion_tipo_actual  = $detraccion_data['id_detraccion_tipo'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editar Detracción</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_vista/v_detraccion_editar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>
    
    <?php require_once("../_vista/v_script.php"); ?>
</body>
</html>