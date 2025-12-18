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
            u.id_personal,
            p.nom_personal,
            c.nom_cargo,
            a.nom_area
        FROM usuario u
        INNER JOIN {$bd_complemento}.personal p ON u.id_personal = p.id_personal
        INNER JOIN {$bd_complemento}.cargo c ON p.id_cargo = c.id_cargo
        INNER JOIN {$bd_complemento}.area a ON p.id_area = a.id_area
        WHERE u.usu_usuario = ? 
        AND u.con_usuario = ? 
        AND u.est_usuario = 1 
        AND p.act_personal = 1";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ss", $usu, $pass);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_array($resultado);

if ($row != null) {
    $id_usuario = $row['id_usuario'];
    $id_personal = $row['id_personal'];
    $nom_usuario = $row['nom_personal'];
    $nom_cargo = $row['nom_cargo'];
    $nom_area = $row['nom_area'];
    
    $_SESSION['id'] = $id_usuario;
    $_SESSION['id_personal']  = $id_personal;
    $_SESSION['usuario_sesion'] = $nom_usuario;
    $_SESSION['cargo_sesion'] = $nom_cargo;
    $_SESSION['area_sesion'] = $nom_area;
    $_SESSION['autentificado'] = TRUE;
    $_SESSION['tiempo_login'] = time();
    
    // Actualizar último acceso
    date_default_timezone_set('America/Lima');
    $fecha_actual = date('Y-m-d H:i:s');
    $sql_update = "UPDATE usuario SET fec_ultimo_acceso = ? WHERE id_usuario = ?";
    $stmt_update = mysqli_prepare($con, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "si", $fecha_actual, $id_usuario);
    mysqli_stmt_execute($stmt_update);

    // Grabar auditoría
    GrabarAuditoria($id_usuario, $nom_usuario, 'INICIO DE SESIÓN', 'SESIÓN', $nom_usuario);
    
    // Obtener permisos del usuario
    $_SESSION['permisos'] = obtenerPermisosUsuario($id_usuario);

    header("location: bienvenido.php");
} else {
    GrabarAuditoria(null, $usu, 'INTENTO DE ACCESO FALLIDO', 'SESIÓN', 'LOGIN');
    ?>
    <script Language="JavaScript">
        location.href = 'index.php?acceso=true';
    </script>
    <?php
}

mysqli_close($con);

/**
 * Función COMPLETA Y ACTUALIZADA con todos los módulos
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
        'acceso_sistema' => 1
    );
    
    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $modulo = strtolower(trim($row['nom_modulo']));
        $accion = strtolower(trim($row['nom_accion']));
        
        // Crear permiso en formato moderno
        $key = $accion . '_' . $modulo;
        $permisos_formateados[0][$key] = 1;
        
        // MAPEO COMPLETO Y ACTUALIZADO PARA TODOS LOS MÓDULOS
        switch($modulo) {
            case 'dashboard':
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_dashboard'] = 1;
                }
                break;

            case 'uso de material':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_uso de material'] = 1;
                    $permisos_formateados[0]['reg_uso de material'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_uso de material'] = 1;
                    $permisos_formateados[0]['edi_uso de material'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_uso de material'] = 1;
                }
                if($accion == 'ver todo') {
                    $permisos_formateados[0]['ver todo_uso de material'] = 1;
                }
                if($accion == 'anular') {  
                    $permisos_formateados[0]['anular_uso de material'] = 1;
                }
                break;

            case 'pedidos':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_pedidos'] = 1;
                    $permisos_formateados[0]['reg_pedidos'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_pedidos'] = 1;
                    $permisos_formateados[0]['edi_pedidos'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_pedidos'] = 1;
                }
                if($accion == 'ver todo') {
                    $permisos_formateados[0]['ver todo_pedidos'] = 1;
                }
                if($accion == 'anular') {
                    $permisos_formateados[0]['anular_pedidos'] = 1;
                }
                if($accion == 'aprobar') {
                    $permisos_formateados[0]['aprobar_pedidos'] = 1;
                }
                if($accion == 'verificar') {
                    $permisos_formateados[0]['verificar_pedidos'] = 1;
                }
                break;

            case 'compras':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_compras'] = 1;
                    $permisos_formateados[0]['reg_compras'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_compras'] = 1;
                    $permisos_formateados[0]['edi_compras'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_compras'] = 1;
                }
                if($accion == 'ver todo') {
                    $permisos_formateados[0]['ver todo_compras'] = 1;
                }
                if($accion == 'anular') {
                    $permisos_formateados[0]['anular_compras'] = 1;
                }
                if($accion == 'aprobar') {
                    $permisos_formateados[0]['aprobar_compras'] = 1;
                }
                break;

            // ********** INGRESOS - ACTUALIZADO CON VERIFICAR **********
            case 'ingresos':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_ingresos'] = 1;
                    $permisos_formateados[0]['reg_ingresos'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_ingresos'] = 1;
                    $permisos_formateados[0]['edi_ingresos'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_ingresos'] = 1;
                }
                if($accion == 'anular') {  
                    $permisos_formateados[0]['anular_ingresos'] = 1;
                }
                // *** VERIFICAR INGRESOS ***
                if($accion == 'verificar') {
                    $permisos_formateados[0]['verificar_ingresos'] = 1;
                }
                break;

            case 'salidas':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_salidas'] = 1;
                    $permisos_formateados[0]['reg_salidas'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_salidas'] = 1;
                    $permisos_formateados[0]['edi_salidas'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_salidas'] = 1;
                }
                if($accion == 'ver todo') {
                    $permisos_formateados[0]['ver todo_salidas'] = 1;
                }
                if($accion == 'anular') {
                    $permisos_formateados[0]['anular_salidas'] = 1;
                }
                if($accion == 'aprobar') {
                    $permisos_formateados[0]['aprobar_salidas'] = 1;
                }
                if($accion == 'recepcionar') { 
                    $permisos_formateados[0]['recepcionar_salidas'] = 1;
                }
                break;

            case 'devoluciones':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_devoluciones'] = 1;
                    $permisos_formateados[0]['reg_devoluciones'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_devoluciones'] = 1;
                    $permisos_formateados[0]['edi_devoluciones'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_devoluciones'] = 1;
                }
                if($accion == 'anular') { 
                    $permisos_formateados[0]['anular_devoluciones'] = 1;
                }
                break;

            case 'movimientos':
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_movimientos'] = 1;
                }
                if($accion == 'ver todo') {
                    $permisos_formateados[0]['ver todo_movimientos'] = 1;
                }
                if ($accion == 'crear') {
                    $permisos_formateados[0]['crear_movimientos'] = 1;
                }
                if ($accion == 'editar') {
                    $permisos_formateados[0]['editar_movimientos'] = 1;
                    $permisos_formateados[0]['edi_movimientos'] = 1;
                }
                break;

            case 'almacen arce':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_almacen arce'] = 1;
                    $permisos_formateados[0]['reg_almacen arce'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_almacen arce'] = 1;
                    $permisos_formateados[0]['edi_almacen arce'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_almacen arce'] = 1;
                }
                break;

            case 'almacen clientes':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_almacen clientes'] = 1;
                    $permisos_formateados[0]['reg_almacen clientes'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_almacen clientes'] = 1;
                    $permisos_formateados[0]['edi_almacen clientes'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_almacen clientes'] = 1;
                }
                break;

            case 'personal':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_personal'] = 1;
                    $permisos_formateados[0]['reg_personal'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_personal'] = 1;
                    $permisos_formateados[0]['edi_personal'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_personal'] = 1;
                }
                break;

            case 'usuarios':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_usuarios'] = 1;
                    $permisos_formateados[0]['reg_usuarios'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_usuarios'] = 1;
                    $permisos_formateados[0]['edi_usuarios'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_usuarios'] = 1;
                }
                break;

            case 'modulos':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_modulos'] = 1;
                    $permisos_formateados[0]['reg_modulos'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_modulos'] = 1;
                    $permisos_formateados[0]['edi_modulos'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_modulos'] = 1;
                }
                break;

            case 'rol de usuario':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_rol de usuario'] = 1;
                    $permisos_formateados[0]['reg_rol de usuario'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_rol de usuario'] = 1;
                    $permisos_formateados[0]['edi_rol de usuario'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_rol de usuario'] = 1;
                }
                break;

            case 'area':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_area'] = 1;
                    $permisos_formateados[0]['reg_area'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_area'] = 1;
                    $permisos_formateados[0]['edi_area'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_area'] = 1;
                }
                break;

            case 'cargo':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_cargo'] = 1;
                    $permisos_formateados[0]['reg_cargo'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_cargo'] = 1;
                    $permisos_formateados[0]['edi_cargo'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_cargo'] = 1;
                }
                break;

            case 'cliente':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_cliente'] = 1;
                    $permisos_formateados[0]['reg_cliente'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_cliente'] = 1;
                    $permisos_formateados[0]['edi_cliente'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_cliente'] = 1;
                }
                break;

            case 'obras':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_obras'] = 1;
                    $permisos_formateados[0]['reg_obras'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_obras'] = 1;
                    $permisos_formateados[0]['edi_obras'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_obras'] = 1;
                }
                break;

            case 'almacen':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_almacen'] = 1;
                    $permisos_formateados[0]['reg_almacen'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_almacen'] = 1;
                    $permisos_formateados[0]['edi_almacen'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_almacen'] = 1;
                }
                break;

            case 'ubicacion':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_ubicacion'] = 1;
                    $permisos_formateados[0]['reg_ubicacion'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_ubicacion'] = 1;
                    $permisos_formateados[0]['edi_ubicacion'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_ubicacion'] = 1;
                }
                break;

            case 'producto':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_producto'] = 1;
                    $permisos_formateados[0]['reg_producto'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_producto'] = 1;
                    $permisos_formateados[0]['edi_producto'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_producto'] = 1;
                }
                break;

            case 'tipo de producto':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_tipo de producto'] = 1;
                    $permisos_formateados[0]['reg_tipo de producto'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_tipo de producto'] = 1;
                    $permisos_formateados[0]['edi_tipo de producto'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_tipo de producto'] = 1;
                }
                break;

            case 'tipo de material':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_tipo de material'] = 1;
                    $permisos_formateados[0]['reg_tipo de material'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_tipo de material'] = 1;
                    $permisos_formateados[0]['edi_tipo de material'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_tipo de material'] = 1;
                }
                break;

            case 'unidad de medida':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_unidad de medida'] = 1;
                    $permisos_formateados[0]['reg_unidad de medida'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_unidad de medida'] = 1;
                    $permisos_formateados[0]['edi_unidad de medida'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_unidad de medida'] = 1;
                }
                break;

            case 'proveedor':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_proveedor'] = 1;
                    $permisos_formateados[0]['reg_proveedor'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_proveedor'] = 1;
                    $permisos_formateados[0]['edi_proveedor'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_proveedor'] = 1;
                }
                if($accion == 'importar') {
                    $permisos_formateados[0]['importar_proveedor'] = 1;
                }
                break;

            case 'moneda':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_moneda'] = 1;
                    $permisos_formateados[0]['reg_moneda'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_moneda'] = 1;
                    $permisos_formateados[0]['edi_moneda'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_moneda'] = 1;
                }
                break;

            case 'detraccion':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_detraccion'] = 1;
                    $permisos_formateados[0]['reg_detraccion'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_detraccion'] = 1;
                    $permisos_formateados[0]['edi_detraccion'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_detraccion'] = 1;
                }
                break;

            case 'centro de costo':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_centro de costo'] = 1;
                    $permisos_formateados[0]['reg_centro de costo'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_centro de costo'] = 1;
                    $permisos_formateados[0]['edi_centro de costo'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_centro de costo'] = 1;
                }
                break;
            
            case 'banco':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_banco'] = 1;
                    $permisos_formateados[0]['reg_banco'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_banco'] = 1;
                    $permisos_formateados[0]['edi_banco'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_banco'] = 1;
                }
                break;

            case 'tipo de documento':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_tipo de documento'] = 1;
                    $permisos_formateados[0]['reg_tipo de documento'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_tipo de documento'] = 1;
                    $permisos_formateados[0]['edi_tipo de documento'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_tipo de documento'] = 1;
                }
                break;

            case 'medio de pago':
                if($accion == 'crear') {
                    $permisos_formateados[0]['crear_medio de pago'] = 1;
                    $permisos_formateados[0]['reg_medio de pago'] = 1;
                }
                if($accion == 'editar') {
                    $permisos_formateados[0]['editar_medio de pago'] = 1;
                    $permisos_formateados[0]['edi_medio de pago'] = 1;
                }
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_medio de pago'] = 1;
                }
                break;

            case 'auditoria':
                if($accion == 'ver') {
                    $permisos_formateados[0]['ver_auditoria'] = 1;
                }
                break;
        }
    }
    
    mysqli_close($con);
    return $permisos_formateados;
}
?>