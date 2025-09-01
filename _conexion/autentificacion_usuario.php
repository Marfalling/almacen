<?php
// autentificacion_usuario.php
session_start();
include("../_conexion/conexion.php");
require_once("../_modelo/m_auditoria.php");

$usu = mysqli_real_escape_string($con, $_REQUEST['usu']);
$pass = mysqli_real_escape_string($con, $_REQUEST['pass']);

// Consulta para autenticación
$sql = "SELECT 
            u.id_usuario,
            u.usu_usuario,
            p.nom_personal,
            p.ape_personal,
            c.nom_cargo,
            a.nom_area
        FROM usuario u
        INNER JOIN personal p ON u.id_personal = p.id_personal
        INNER JOIN cargo c ON p.id_cargo = c.id_cargo
        INNER JOIN area a ON p.id_area = a.id_area
        WHERE u.usu_usuario = ? 
        AND u.con_usuario = ? 
        AND u.est_usuario = 1 
        AND p.est_personal = 1";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ss", $usu, $pass);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_array($resultado);

if ($row != null) {
    $id_usuario = $row['id_usuario'];
    $nom_usuario = $row['nom_personal'] . ' ' . $row['ape_personal'];
    $nom_cargo = $row['nom_cargo'];
    $nom_area = $row['nom_area'];
    
    $_SESSION['id'] = $id_usuario;
    $_SESSION['usuario_sesion'] = $nom_usuario;
    $_SESSION['cargo_sesion'] = $nom_cargo;
    $_SESSION['area_sesion'] = $nom_area;
    $_SESSION['autentificado'] = TRUE;
    $_SESSION['tiempo_login'] = time();

    // Actualizar último acceso
    $fecha_actual = date('Y-m-d H:i:s');
    $sql_update = "UPDATE usuario SET fec_ultimo_acceso = ? WHERE id_usuario = ?";
    $stmt_update = mysqli_prepare($con, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "si", $fecha_actual, $id_usuario);
    mysqli_stmt_execute($stmt_update);

    // Grabar auditoría
    GrabarAuditoria($id_usuario, $nom_usuario, 'INICIO DE SESIÓN', 'SESIÓN', $nom_usuario);
    
    // Obtener permisos del usuario
    $_SESSION['permisos'] = obtenerPermisosUsuario($id_usuario);

    header("location: dashboard.php");
} else {
    GrabarAuditoria(0, $usu, 'INTENTO DE ACCESO FALLIDO', 'SESIÓN', 'LOGIN');
    ?>
    <script Language="JavaScript">
        location.href = 'index.php?acceso=true';
    </script>
    <?php
}

mysqli_close($con);

/**
 * Función ADAPTADA a tu estructura de BD específica
 */
function obtenerPermisosUsuario($id_usuario) {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT DISTINCT
                m.nom_modulo,
                a.nom_accion
            FROM usuario_rol ur
            INNER JOIN rol r ON ur.id_rol = r.id_rol
            INNER JOIN permiso p ON r.id_rol = p.id_rol
            INNER JOIN modulo_accion ma ON p.id_modulo_accion = ma.id_modulo_accion
            INNER JOIN modulo m ON ma.id_modulo = m.id_modulo
            INNER JOIN accion a ON ma.id_accion = a.id_accion
            WHERE ur.id_usuario = ? 
            AND ur.est_usuario_rol = 1 
            AND r.est_rol = 1 
            AND p.est_permiso = 1 
            AND ma.est_modulo_accion = 1 
            AND m.est_modulo = 1 
            AND a.est_accion = 1";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    // Inicializar con permisos básicos SIEMPRE
    $permisos_formateados[0] = array(
        'ver_dashboard' => 1,
        'acceso_sistema' => 1
    );
    
    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $modulo = strtolower(trim($row['nom_modulo']));
        $accion = strtolower(trim($row['nom_accion']));
        
        // Crear permiso en formato moderno
        $key = $accion . '_' . $modulo;
        $permisos_formateados[0][$key] = 1;
        
        // MAPEO COMPLETO para todos los módulos
        switch($modulo) {
            case 'cliente':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_cliente'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_cliente'] = 1;
                    $permisos_formateados[0]['edi_cliente'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_cliente'] = 1;
                }
                break;
                
            case 'almacen':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_almacen'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_almacen'] = 1;
                    $permisos_formateados[0]['edi_almacen'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_almacen'] = 1;
                }
                break;
                
            case 'auditoria':
                $permisos_formateados[0]['ver_auditoria'] = 1;
                break;
                
            case 'procesos':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_procesos'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_procesos'] = 1;
                    $permisos_formateados[0]['edi_procesos'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_procesos'] = 1;
                }
                break;
                
            case 'usuario':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_usuario'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_usuario'] = 1;
                    $permisos_formateados[0]['edi_usuario'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_usuario'] = 1;
                }
                break;
                
            case 'personal':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_personal'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_personal'] = 1;
                    $permisos_formateados[0]['edi_personal'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_personal'] = 1;
                }
                break;
                
            case 'obra':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_obra'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_obra'] = 1;
                    $permisos_formateados[0]['edi_obra'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_obra'] = 1;
                }
                break;
                
            case 'producto':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_producto'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_producto'] = 1;
                    $permisos_formateados[0]['edi_producto'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_producto'] = 1;
                }
                break;
                
            case 'proveedor':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_proveedor'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_proveedor'] = 1;
                    $permisos_formateados[0]['edi_proveedor'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_proveedor'] = 1;
                }
                break;
                
            // MÓDULO "MODULO" - CORREGIDO PARA FUNCIONALIDAD COMPLETA
            case 'modulo':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_modulo'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_modulo'] = 1;
                    $permisos_formateados[0]['edi_modulo'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_modulo'] = 1;
                }
                break;
                
            case 'area':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_area'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_area'] = 1;
                    $permisos_formateados[0]['edi_area'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_area'] = 1;
                }
                break;
                
            case 'cargo':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_cargo'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_cargo'] = 1;
                    $permisos_formateados[0]['edi_cargo'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_cargo'] = 1;
                }
                break;
                
            case 'ubicacion':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_ubicacion'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_ubicacion'] = 1;
                    $permisos_formateados[0]['edi_ubicacion'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_ubicacion'] = 1;
                }
                break;
                
            case 'material_tipo':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_material_tipo'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_material_tipo'] = 1;
                    $permisos_formateados[0]['edi_material_tipo'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_material_tipo'] = 1;
                }
                break;
                
            case 'producto_tipo':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_producto_tipo'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_producto_tipo'] = 1;
                    $permisos_formateados[0]['edi_producto_tipo'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_producto_tipo'] = 1;
                }
                break;
                
            case 'unidad_medida':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_unidad_medida'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_unidad_medida'] = 1;
                    $permisos_formateados[0]['edi_unidad_medida'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_unidad_medida'] = 1;
                }
                break;
                
            case 'compra':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_compra'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_compra'] = 1;
                    $permisos_formateados[0]['edi_compra'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_compra'] = 1;
                }
                break;
                
            case 'moneda':
                if($accion == 'crear') {
                    $permisos_formateados[0]['reg_moneda'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['ver_moneda'] = 1;
                    $permisos_formateados[0]['edi_moneda'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_moneda'] = 1;
                }
                break;
        }
    }
    
    mysqli_close($con);
    return $permisos_formateados;
}
?>