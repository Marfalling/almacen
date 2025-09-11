<?php
// sesion.php - Versión mejorada con mapeo completo
session_start();
date_default_timezone_set('America/Lima');
include("seguridad.php");

$id = $_SESSION['id'];
$id_personal = $_SESSION['id_personal'];
$usuario_sesion = $_SESSION['usuario_sesion'];
$cargo_sesion = $_SESSION['cargo_sesion'];
$area_sesion = $_SESSION['area_sesion'] ?? '';



/**
 * Función para verificar permiso específico (compatibilidad con código existente)
 * @param string $permiso_key Clave específica (ej: 'crear_usuarios')
 * @return bool True si tiene permiso
 */
function verificarPermisoEspecifico($permiso_key) {
    if (!isset($_SESSION['permisos'][0])) {
        return false;
    }
    return isset($_SESSION['permisos'][0][$permiso_key]) && $_SESSION['permisos'][0][$permiso_key] == 1;
}

/**
 * Función para redireccionar si no tiene permisos
 * @param string $permiso_key Clave del permiso requerido
 * @param string $modulo_nombre Nombre del módulo para auditoría
 * @param string $accion_nombre Nombre de la acción para auditoría
 */
function verificarYRedireccionar($permiso_key, $modulo_nombre = '', $accion_nombre = '') {
    global $id, $usuario_sesion;
    
    if (!verificarPermisoEspecifico($permiso_key)) {
        require_once("../_modelo/m_auditoria.php");
        $modulo = !empty($modulo_nombre) ? $modulo_nombre : 'SISTEMA';
        $accion = !empty($accion_nombre) ? $accion_nombre : strtoupper($permiso_key);
        
        GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', $modulo, $accion);
        header("location: bienvenido.php?permisos=true");
        exit;
    }
}

/**
 * Función para verificar acceso a un controlador específico
 * @param string $nombre_controlador Nombre del controlador (ej: 'usuario_mostrar')
 */
function verificarAccesoControlador($nombre_controlador) {
    // MAPEO COMPLETO DE CONTROLADORES A PERMISOS (basado en tus permisos reales)
    $mapeo_permisos = [
        // ==================== DASHBOARD ====================
        'dashboard' => 'ver_dashboard',
    
        // ==================== USUARIOS ====================
        'usuario_mostrar' => 'ver_usuarios',
        'usuario_nuevo' => 'crear_usuarios',
        'usuario_editar' => 'editar_usuarios',
        
        // ==================== PERSONAL ====================
        'personal_mostrar' => 'ver_personal',
        'personal_nuevo' => 'crear_personal',
        'personal_editar' => 'editar_personal',
        
        // ==================== CLIENTES ====================
        'cliente_mostrar' => 'ver_cliente',
        'cliente_nuevo' => 'crear_cliente',
        'cliente_editar' => 'editar_cliente',
        
        // ==================== PRODUCTOS ====================
        'producto_mostrar' => 'ver_producto',
        'producto_nuevo' => 'crear_producto',
        'producto_editar' => 'editar_producto',
        
        // ==================== ALMACÉN ====================
        'almacen_mostrar' => 'ver_almacen',
        'almacen_nuevo' => 'crear_almacen',
        'almacen_editar' => 'editar_almacen',
        
        // ==================== AUDITORÍA ====================
        'auditoria_mostrar' => 'ver_auditoria',
        
        // ==================== OBRAS ====================
        'obras_mostrar' => 'ver_obras',
        'obras_nuevo' => 'crear_obras',
        'obras_editar' => 'editar_obras',
        
        // ==================== USO DE MATERIAL ====================
        'uso_material_mostrar' => 'ver_uso de material',
        'uso_material_nuevo' => 'crear_uso de material',
        'uso_material_editar' => 'editar_uso de material',
        
        // ==================== PEDIDOS ====================
        'pedidos_mostrar' => 'ver_pedidos',
        'pedidos_nuevo' => 'crear_pedidos',
        'pedidos_editar' => 'editar_pedidos',
        
        // ==================== COMPRAS ====================
        'compras_mostrar' => 'ver_compras',
        'compras_nuevo' => 'crear_compras',
        'compras_editar' => 'editar_compras',
        
        // ==================== INGRESOS ====================
        'ingresos_mostrar' => 'ver_ingresos',
        'ingresos_nuevo' => 'crear_ingresos',
        'ingresos_editar' => 'editar_ingresos',
        
        // ==================== DEVOLUCIONES ====================
        'devoluciones_mostrar' => 'ver_devoluciones',
        'devoluciones_nuevo' => 'crear_devoluciones',
        'devoluciones_editar' => 'editar_devoluciones',
        
        // ==================== ALMACÉN ARCE ====================
        'almacen_arce_mostrar' => 'ver_almacen arce',
        'almacen_arce_nuevo' => 'crear_almacen arce',
        'almacen_arce_editar' => 'editar_almacen arce',
        
        // ==================== ALMACÉN CLIENTES ====================
        'almacen_clientes_mostrar' => 'ver_almacen clientes',
        'almacen_clientes_nuevo' => 'crear_almacen clientes',
        'almacen_clientes_editar' => 'editar_almacen clientes',
        
        // ==================== MÓDULOS ====================
        'modulo_mostrar' => 'ver_modulos',
        'modulo_nuevo' => 'crear_modulos',
        'modulo_editar' => 'editar_modulos',
        
        // ==================== ROL USUARIO ====================
        'rol_usuario_mostrar' => 'ver_rol de usuario',
        'rol_usuario_nuevo' => 'crear_rol de usuario',
        'rol_usuario_editar' => 'editar_rol de usuario',
        
        // ==================== ÁREA ====================
        'area_mostrar' => 'ver_area',
        'area_nuevo' => 'crear_area',
        'area_editar' => 'editar_area',
        
        // ==================== CARGO ====================
        'cargo_mostrar' => 'ver_cargo',
        'cargo_nuevo' => 'crear_cargo',
        'cargo_editar' => 'editar_cargo',
        
        // ==================== UBICACIÓN ====================
        'ubicacion_mostrar' => 'ver_ubicacion',
        'ubicacion_nuevo' => 'crear_ubicacion',
        'ubicacion_editar' => 'editar_ubicacion',
        
        // ==================== TIPO PRODUCTO ====================
        'tipo_producto_mostrar' => 'ver_tipo de producto',
        'tipo_producto_nuevo' => 'crear_tipo de producto', 
        'tipo_producto_editar' => 'editar_tipo de producto',
        
        // ==================== TIPO MATERIAL ====================
        'tipo_material_mostrar' => 'ver_tipo de material',
        'tipo_material_nuevo' => 'crear_tipo de material',
        'tipo_material_editar' => 'editar_tipo de material',
        
        // ==================== UNIDAD MEDIDA ====================
        'unidad_medida_mostrar' => 'ver_unidad de medida',
        'unidad_medida_nuevo' => 'crear_unidad de medida', 
        'unidad_medida_editar' => 'editar_unidad de medida',
        
        // ==================== PROVEEDOR ====================
        'proveedor_mostrar' => 'ver_proveedor',
        'proveedor_nuevo' => 'crear_proveedor',
        'proveedor_editar' => 'editar_proveedor',
        
        // ==================== MONEDA ====================
        'moneda_mostrar' => 'ver_moneda',
        'moneda_nuevo' => 'crear_moneda',
        'moneda_editar' => 'editar_moneda'
    ];
    
    if (isset($mapeo_permisos[$nombre_controlador])) {
        verificarYRedireccionar($mapeo_permisos[$nombre_controlador]);
    }
}

/**
 * Función flexible que intenta múltiples formatos de permisos
 * @param string $modulo Nombre del módulo
 * @param string $accion Nombre de la acción
 * @return bool True si tiene algún permiso relacionado
 */
function verificarPermisoFlexible($modulo, $accion) {
    if (!isset($_SESSION['permisos'][0])) {
        return false;
    }
    
    // Diferentes formatos que podrían existir
    $permisos_posibles = [
        $accion . '_' . $modulo,              // crear_usuarios
        'reg_' . $modulo,                     // reg_usuarios
        'edi_' . $modulo,                     // edi_usuarios  
        'ver_' . $modulo,                     // ver_usuarios
        str_replace(' ', '_', $accion . '_' . $modulo) // Para espacios: crear_uso_de_material
    ];
    
    foreach ($permisos_posibles as $permiso) {
        if (isset($_SESSION['permisos'][0][$permiso]) && $_SESSION['permisos'][0][$permiso] == 1) {
            return true;
        }
    }
    
    return false;
}
function tieneAccesoModulo($modulo) {
    if (!isset($_SESSION['permisos'][0])) {
        return false;
    }
    
    $modulo = strtolower($modulo);
    foreach ($_SESSION['permisos'][0] as $permiso => $valor) {
        if ($valor == 1) {
            // Verificar si el permiso contiene el nombre del módulo
            if (strpos($permiso, $modulo) !== false) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Función para verificar permiso específico por módulo y acción
 */
function verificarPermiso($modulo, $accion) {
    if (!isset($_SESSION['permisos'][0])) {
        return false;
    }
    
    // Crear la clave en diferentes formatos posibles
    $formatos = [
        $accion . '_' . $modulo,
        str_replace(' ', '_', $accion . '_' . $modulo),
        'ver_' . $modulo,
        'crear_' . $modulo,
        'editar_' . $modulo
    ];
    
    foreach ($formatos as $formato) {
        if (isset($_SESSION['permisos'][0][$formato]) && $_SESSION['permisos'][0][$formato] == 1) {
            return true;
        }
    }
    
    return false;
}

/**
 * Función para verificar permisos que pueden tener espacios o guiones
 */
function verificarPermisoConEspacios($permiso_buscado) {
    if (!isset($_SESSION['permisos'][0])) {
        return false;
    }
    
    // Buscar el permiso exacto primero
    if (isset($_SESSION['permisos'][0][$permiso_buscado]) && $_SESSION['permisos'][0][$permiso_buscado] == 1) {
        return true;
    }
    
    // Si no se encuentra, buscar variantes con espacios/guiones
    $variantes = [
        $permiso_buscado,
        str_replace('_', ' ', $permiso_buscado),   // convertir _ a espacios
        str_replace(' ', '_', $permiso_buscado)    // convertir espacios a _
    ];
    
    foreach ($variantes as $variante) {
        if (isset($_SESSION['permisos'][0][$variante]) && $_SESSION['permisos'][0][$variante] == 1) {
            return true;
        }
    }
    
    return false;
}
?>