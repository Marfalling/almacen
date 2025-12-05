<?php
//=======================================================================
// CONTROLADOR: obras_editar.php 
//=======================================================================
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

// Verificar permisos
if (!verificarPermisoEspecifico('editar_obras')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'OBRAS', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_obras.php");

//-------------------------------------------
// OPERACIÓN DE EDICIÓN
//-------------------------------------------
if (isset($_POST['registrar'])) {
    $id_obra = intval($_POST['id_obra']);
    
    //  OBTENER DATOS ANTES DE EDITAR
    $obra_antes = ConsultarObra($id_obra);
    $nom_anterior = $obra_antes['nom_subestacion'];
    $est_anterior = $obra_antes['act_subestacion'];
    
    // Obtener nuevos valores
    $nom_nuevo = strtoupper(trim($_POST['nom']));
    $est_nuevo = isset($_POST['est']) ? 1 : 0;

    $rpta = ActualizarObra($id_obra, $nom_nuevo, $est_nuevo);

    if ($rpta == "SI") {
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
            $descripcion = "ID: $id_obra | Sin cambios";
        } else {
            $descripcion = "ID: $id_obra | " . implode(' | ', $cambios);
        }
        
        GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'OBRAS', $descripcion);
        header("Location: obras_mostrar.php?actualizado=true");
        exit;
    } elseif ($rpta == "NO") {
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'OBRAS', "ID: $id_obra - Obra ya existe");
        header("Location: obras_mostrar.php?existe=true");
        exit;
    } else {
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'OBRAS', "ID: $id_obra");
        header("Location: obras_mostrar.php?error=true");
        exit;
    }
}
//-------------------------------------------

// Obtener obra a editar
$id_obra = isset($_GET['id_obra']) ? intval($_GET['id_obra']) : 0;

// Consultar obra/subestacion por ID
$obra = ConsultarObra($id_obra);
if (!$obra) {
    header("Location: obras_mostrar.php?error=true");
    exit;
}

$nom = $obra['nom_subestacion'];
$est = ($obra['act_subestacion'] == 1) ? "checked" : "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Obra</title>
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <?php
        require_once("../_vista/v_menu.php");
        require_once("../_vista/v_menu_user.php");
        require_once("../_vista/v_obras_editar.php");
        require_once("../_vista/v_footer.php"); 
        ?>
    </div>
</div>
<?php require_once("../_vista/v_script.php"); ?>
</body>
</html>