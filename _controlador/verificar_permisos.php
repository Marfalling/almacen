<?php
// sesion.php - Archivo principal de sesión
session_start();
include("seguridad.php");

$id = $_SESSION['id'];
$usuario_sesion = $_SESSION['usuario_sesion'];
$cargo_sesion = $_SESSION['cargo_sesion'];
$area_sesion = $_SESSION['area_sesion'] ?? '';

/**
 * Función para verificar permisos específicos por módulo y acción
 */
function verificarPermiso($modulo, $accion) {
    $key = strtolower($accion) . '_' . strtolower($modulo);
    return isset($_SESSION['permisos'][0][$key]) && $_SESSION['permisos'][0][$key] == 1;
}

/**
 * Función para verificar si tiene permiso en un módulo (cualquier acción)
 */
function tieneAccesoModulo($modulo) {
    if (!isset($_SESSION['permisos'][0])) {
        return false;
    }
    
    $modulo_lower = strtolower($modulo);
    foreach ($_SESSION['permisos'][0] as $permiso => $valor) {
        if (strpos($permiso, '_' . $modulo_lower) !== false && $valor == 1) {
            return true;
        }
    }
    return false;
}

/**
 * Función para verificar permiso específico con compatibilidad hacia atrás
 * Esta función permite usar la sintaxis: $_SESSION['permisos'][0]['reg_usuario']
 */
function verificarPermisoEspecifico($permiso_key) {
    return isset($_SESSION['permisos'][0][$permiso_key]) && $_SESSION['permisos'][0][$permiso_key] == 1;
}

/**
 * Función para redireccionar si no tiene permisos por módulo y acción
 */
function verificarAccesoORedireccionar($modulo, $accion, $pagina_destino = 'dashboard.php') {
    global $id, $usuario_sesion;
    
    if (!verificarPermiso($modulo, $accion)) {
        require_once("../_modelo/m_auditoria.php");
        GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', strtoupper($modulo), strtoupper($accion));
        header("location: $pagina_destino?permisos=true");
        exit;
    }
}

/**
 * Función para verificar permiso específico y redireccionar
 */
function verificarPermisoEspecificoORedireccionar($permiso_key, $descripcion_accion = '', $pagina_destino = 'dashboard.php') {
    global $id, $usuario_sesion;
    
    if (!verificarPermisoEspecifico($permiso_key)) {
        require_once("../_modelo/m_auditoria.php");
        $accion_auditoria = !empty($descripcion_accion) ? $descripcion_accion : strtoupper($permiso_key);
        GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'SISTEMA', $accion_auditoria);
        header("location: $pagina_destino?permisos=true");
        exit;
    }
}
?>

<?php
// Ejemplo de uso en controladores:

// OPCIÓN 1: Verificación directa como en tu ejemplo original
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

// Para mantener compatibilidad con tu código existente
if (!verificarPermisoEspecifico('reg_usuario')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'USUARIO', 'NUEVO');
    header("location: dashboard.php?permisos=true");
    exit;
}

// OPCIÓN 2: Usando la nueva función con menos código
require_once("../_conexion/sesion.php");
verificarPermisoEspecificoORedireccionar('reg_usuario', 'NUEVO USUARIO');

// OPCIÓN 3: Verificación por módulo y acción
require_once("../_conexion/sesion.php");
verificarAccesoORedireccionar('usuario', 'crear');

// OPCIÓN 4: Verificación múltiple de permisos
require_once("../_conexion/sesion.php");
if (!verificarPermiso('usuario', 'crear') && !verificarPermiso('usuario', 'editar')) {
    verificarPermisoEspecificoORedireccionar('ver_usuario', 'ACCESO USUARIO');
}
?>

<?php
// autentificacion_usuario.php - Versión mejorada
session_start();
include("../_conexion/conexion.php");
require_once("../_modelo/m_auditoria.php");

$usu = mysqli_real_escape_string($con, $_REQUEST['usu']);
$pass = mysqli_real_escape_string($con, $_REQUEST['pass']);

// Consulta preparada para mayor seguridad
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
    $_SESSION['tiempo_login'] = time(); // Para control de expiración

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
    // Grabar intento de acceso fallido
    GrabarAuditoria(0, $usu, 'INTENTO DE ACCESO FALLIDO', 'SESIÓN', 'LOGIN');
    ?>
    <script Language="JavaScript">
        location.href = 'index.php?acceso=true';
    </script>
    <?php
}

mysqli_close($con);

/**
 * Función mejorada para obtener todos los permisos de un usuario
 */
function obtenerPermisosUsuario($id_usuario) {
    include("../_conexion/conexion.php");
    
    // Consulta preparada
    $sql = "SELECT DISTINCT
                m.nom_modulo,
                a.nom_accion,
                ma.id_modulo_accion,
                m.id_modulo,
                a.id_accion
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
    
    $permisos_formateados = array();
    
    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $modulo = strtolower($row['nom_modulo']);
        $accion = strtolower($row['nom_accion']);
        
        // Crear claves de permisos basadas en módulo_acción
        $key = $accion . '_' . $modulo;
        $permisos_formateados[0][$key] = 1;
        
        // Mapeo específico para compatibilidad hacia atrás
        $mapeo_permisos = [
            'usuario' => [
                'crear' => ['reg_usuario'],
                'editar' => ['ver_usuario', 'edi_usuario'],
                'eliminar' => ['eli_usuario'],
                'ver' => ['ver_usuario']
            ],
            'cliente' => [
                'crear' => ['reg_cliente'],
                'editar' => ['ver_cliente', 'edi_cliente'],
                'eliminar' => ['eli_cliente'],
                'ver' => ['ver_cliente']
            ],
            'almacen' => [
                'crear' => ['reg_almacen'],
                'editar' => ['ver_almacen', 'edi_almacen'],
                'eliminar' => ['eli_almacen'],
                'ver' => ['ver_almacen']
            ],
            'auditoria' => [
                'ver' => ['ver_auditoria'],
                'editar' => ['ver_auditoria']
            ],
            'procesos' => [
                'crear' => ['reg_procesos'],
                'editar' => ['ver_procesos', 'edi_procesos'],
                'eliminar' => ['eli_procesos'],
                'ver' => ['ver_procesos']
            ],
            'reportes' => [
                'ver' => ['ver_reportes'],
                'generar' => ['gen_reportes']
            ]
        ];
        
        // Aplicar mapeo de permisos
        if (isset($mapeo_permisos[$modulo][$accion])) {
            foreach ($mapeo_permisos[$modulo][$accion] as $permiso_legacy) {
                $permisos_formateados[0][$permiso_legacy] = 1;
            }
        }
    }
    
    // Permisos básicos si no hay permisos específicos
    if (empty($permisos_formateados)) {
        $permisos_formateados[0] = array('ver_dashboard' => 1);
    } else {
        // Asegurar acceso al dashboard
        $permisos_formateados[0]['ver_dashboard'] = 1;
    }
    
    mysqli_close($con);
    return $permisos_formateados;
}
?>

<?php
// Ejemplos de uso en diferentes controladores:

// ===== CONTROLADOR DE USUARIOS =====
// nuevo_usuario.php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

// Método 1: Como en tu ejemplo original
if (!isset($_SESSION['permisos'][0]['reg_usuario']) || $_SESSION['permisos'][0]['reg_usuario'] != 1) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'USUARIO', 'NUEVO');
    header("location: dashboard.php?permisos=true");
    exit;
}

// Método 2: Usando función helper (más limpio)
verificarPermisoEspecificoORedireccionar('reg_usuario', 'NUEVO USUARIO');

// ===== CONTROLADOR DE CLIENTES =====
// editar_cliente.php
require_once("../_conexion/sesion.php");

// Verificar permiso de edición de cliente
if (!verificarPermiso('cliente', 'editar')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'CLIENTE', 'EDITAR');
    header("location: dashboard.php?permisos=true");
    exit;
}

// ===== CONTROLADOR DE ALMACÉN =====
// almacen_lista.php
require_once("../_conexion/sesion.php");

// Verificar si tiene algún acceso al módulo almacén
if (!tieneAccesoModulo('almacen')) {
    verificarPermisoEspecificoORedireccionar('ver_almacen', 'ACCESO ALMACÉN');
}

// ===== CONTROLADOR DE AUDITORÍA =====
// auditoria.php
require_once("../_conexion/sesion.php");

// Verificar acceso a auditoría
verificarAccesoORedireccionar('auditoria', 'ver');

// ===== VERIFICACIONES MÚLTIPLES =====
// panel_administracion.php
require_once("../_conexion/sesion.php");

// Verificar si es administrador (tiene permisos de usuario)
$es_admin = verificarPermiso('usuario', 'crear') || 
            verificarPermiso('usuario', 'editar') || 
            verificarPermiso('usuario', 'eliminar');

if (!$es_admin) {
    verificarPermisoEspecificoORedireccionar('ver_dashboard', 'ACCESO ADMINISTRACIÓN');
}

// ===== EJEMPLO CON VERIFICACIÓN EN VISTA =====
// En tus archivos de vista (.php)
?>
<div class="container">
    <?php if (verificarPermiso('usuario', 'crear')): ?>
        <a href="nuevo_usuario.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </a>
    <?php endif; ?>
    
    <?php if (verificarPermisoEspecifico('reg_cliente')): ?>
        <a href="nuevo_cliente.php" class="btn btn-success">
            <i class="fas fa-user-plus"></i> Nuevo Cliente
        </a>
    <?php endif; ?>
    
    <?php if (tieneAccesoModulo('almacen')): ?>
        <div class="card">
            <h5>Gestión de Almacén</h5>
            <?php if (verificarPermiso('almacen', 'ver')): ?>
                <a href="lista_almacen.php">Ver Almacén</a>
            <?php endif; ?>
            
            <?php if (verificarPermiso('almacen', 'crear')): ?>
                <a href="nuevo_almacen.php">Crear Almacén</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// ===== VERIFICACIÓN CON AJAX =====
// ajax_verificar_permiso.php
require_once("../_conexion/sesion.php");

header('Content-Type: application/json');

$modulo = $_POST['modulo'] ?? '';
$accion = $_POST['accion'] ?? '';

$tiene_permiso = verificarPermiso($modulo, $accion);

echo json_encode([
    'success' => true,
    'tiene_permiso' => $tiene_permiso,
    'usuario' => $usuario_sesion
]);
?>

<?php
// ===== MIDDLEWARE DE PERMISOS =====
// middleware_permisos.php
function middleware_permisos($permisos_requeridos) {
    global $id, $usuario_sesion;
    
    if (!is_array($permisos_requeridos)) {
        $permisos_requeridos = [$permisos_requeridos];
    }
    
    $tiene_alguno = false;
    foreach ($permisos_requeridos as $permiso) {
        if (strpos($permiso, '_') !== false) {
            // Es un permiso específico (ej: reg_usuario)
            if (verificarPermisoEspecifico($permiso)) {
                $tiene_alguno = true;
                break;
            }
        } else {
            // Es un módulo (ej: usuario)
            if (tieneAccesoModulo($permiso)) {
                $tiene_alguno = true;
                break;
            }
        }
    }
    
    if (!$tiene_alguno) {
        require_once("../_modelo/m_auditoria.php");
        GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'SISTEMA', 'PERMISOS INSUFICIENTES');
        header("location: dashboard.php?permisos=true");
        exit;
    }
    
    return true;
}

// Uso del middleware:
// require_once("../_conexion/sesion.php");
// require_once("middleware_permisos.php");
// middleware_permisos(['reg_usuario', 'edi_usuario']); // Requiere al menos uno
?>

<?php
// ===== CLASE PARA GESTIÓN DE PERMISOS (OPCIONAL) =====
class PermisosManager {
    
    public static function verificar($modulo, $accion) {
        return verificarPermiso($modulo, $accion);
    }
    
    public static function verificarEspecifico($permiso_key) {
        return verificarPermisoEspecifico($permiso_key);
    }
    
    public static function requiere($permiso, $descripcion = '') {
        global $id, $usuario_sesion;
        
        $tiene_permiso = false;
        
        if (strpos($permiso, '_') !== false) {
            $tiene_permiso = self::verificarEspecifico($permiso);
        } else {
            $tiene_permiso = tieneAccesoModulo($permiso);
        }
        
        if (!$tiene_permiso) {
            require_once("../_modelo/m_auditoria.php");
            $accion_auditoria = !empty($descripcion) ? $descripcion : strtoupper($permiso);
            GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'SISTEMA', $accion_auditoria);
            header("location: dashboard.php?permisos=true");
            exit;
        }
        
        return true;
    }
    
    public static function tieneAlgunoDe($permisos) {
        foreach ($permisos as $permiso) {
            if (strpos($permiso, '_') !== false) {
                if (self::verificarEspecifico($permiso)) return true;
            } else {
                if (tieneAccesoModulo($permiso)) return true;
            }
        }
        return false;
    }
}

// Uso de la clase:
// require_once("../_conexion/sesion.php");
// PermisosManager::requiere('reg_usuario', 'CREAR USUARIO');
// 
// if (PermisosManager::tieneAlgunoDe(['reg_usuario', 'edi_usuario'])) {
//     // El usuario puede crear o editar usuarios
// }
?>