<?php
// sesion.php - VERSIÓN FINAL CORREGIDA
session_start();
include("seguridad.php");

$id = $_SESSION['id'];
$usuario_sesion = $_SESSION['usuario_sesion'];
$cargo_sesion = $_SESSION['cargo_sesion'];
$area_sesion = $_SESSION['area_sesion'] ?? '';

function esAdministrador() {
    return verificarPermiso('usuario', 'crear') || 
           verificarPermiso('usuario', 'editar') || 
           verificarPermiso('usuario', 'eliminar');
}

function puedeGestionarInventario() {
    return tieneAccesoModulo('almacen') || 
           tieneAccesoModulo('producto') || 
           verificarPermiso('procesos', 'ver');
}

function puedeVerReportes() {
    return verificarPermiso('auditoria', 'ver') ||
           verificarPermiso('procesos', 'ver') ||
           esAdministrador();
}

/**
 * Función para verificar permiso específico
 */
function verificarPermisoEspecifico($permiso_key) {
    if (!isset($_SESSION['permisos'][0][$permiso_key])) {
        return false;
    }
    return $_SESSION['permisos'][0][$permiso_key] == 1;
}

/**
 * Función para verificar permisos por módulo y acción
 */
function verificarPermiso($modulo, $accion) {
    $key = strtolower($accion) . '_' . strtolower($modulo);
    return verificarPermisoEspecifico($key);
}

/**
 * Función CORREGIDA para verificar si tiene acceso a un módulo
 * Maneja tanto singular como plural de los nombres de módulos
 */
function tieneAccesoModulo($modulo) {
    if (!isset($_SESSION['permisos'][0])) {
        return false;
    }
    
    $modulo_lower = strtolower($modulo);
    
    // Mapeo de nombres de módulos para compatibilidad
    $mapeo_modulos = [
        'modulo' => ['modulo', 'modulos'],
        'modulos' => ['modulo', 'modulos'],
        'producto' => ['producto'],
        'usuario' => ['usuario', 'usuarios'],
        'usuarios' => ['usuario', 'usuarios'],
        'tipo_producto' => ['tipo de producto'],
        'producto_tipo' => ['tipo de producto'],
        'tipo_material' => ['tipo de material'],
        'material_tipo' => ['tipo de material'],
        'unidad_medida' => ['unidad de medida'],
        'rol_usuario' => ['rol de usuario'],
        'usuario_rol' => ['rol de usuario'],
        'uso_material' => ['uso de material'],
        'almacen_arce' => ['almacen arce'],
        'almacen_clientes' => ['almacen clientes']
    ];
    
    // Obtener variantes del módulo a buscar
    $variantes = isset($mapeo_modulos[$modulo_lower]) ? $mapeo_modulos[$modulo_lower] : [$modulo_lower];
    
    foreach ($_SESSION['permisos'][0] as $permiso => $valor) {
        if ($valor == 1) {
            foreach ($variantes as $variante) {
                if (strpos($permiso, '_' . $variante) !== false) {
                    return true;
                }
            }
        }
    }
    return false;
}

/**
 * Función SEGURA para verificar permisos sin bucles infinitos
 */
function verificarPermisoSeguro($permiso_key, $descripcion = '') {
    global $id, $usuario_sesion;
    
    // Obtener el archivo actual
    $archivo_actual = basename($_SERVER['PHP_SELF']);
    
    // NO verificar permisos en dashboard.php para evitar bucles
    if ($archivo_actual === 'dashboard.php') {
        return true;
    }
    
    if (!verificarPermisoEspecifico($permiso_key)) {
        require_once("../_modelo/m_auditoria.php");
        $accion_auditoria = !empty($descripcion) ? $descripcion : strtoupper($permiso_key);
        GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'SISTEMA', $accion_auditoria);
        header("location: dashboard.php?permisos=true&modulo=" . urlencode($permiso_key));
        exit;
    }
    return true;
}

/**
 * Función de depuración para ver permisos disponibles
 */
function debugPermisos() {
    if (isset($_SESSION['permisos'][0])) {
        echo "<pre>";
        print_r($_SESSION['permisos'][0]);
        echo "</pre>";
    }
}

/**
 * Función para verificar acceso directo con mapeo mejorado
 */
function verificarAccesoModulo($nombre_modulo) {
    // Mapeo directo para casos específicos
    $mapeos_directos = [
        'modulos' => 'ver_modulos',
        'modulo' => 'ver_modulos',
        'rol_usuario' => 'ver_rol de usuario',
        'tipo_producto' => 'ver_tipo de producto',
        'tipo_material' => 'ver_tipo de material',
        'unidad_medida' => 'ver_unidad de medida',
        'almacen_arce' => 'ver_almacen arce',
        'almacen_clientes' => 'ver_almacen clientes'
    ];
    
    $modulo_key = strtolower($nombre_modulo);
    
    // Verificar mapeo directo primero
    if (isset($mapeos_directos[$modulo_key])) {
        return verificarPermisoEspecifico($mapeos_directos[$modulo_key]);
    }
    
    // Verificar con tieneAccesoModulo si no hay mapeo directo
    return tieneAccesoModulo($nombre_modulo);
}
?>