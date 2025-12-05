<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");
require_once("../_modelo/m_proveedor.php");

header('Content-Type: application/json');

if (!verificarPermisoEspecifico('crear_proveedor')) {
    //  AUDITORÍA: ERROR DE ACCESO
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PROVEEDOR', 'REGISTRAR AJAX');
    
    echo json_encode([
        'success' => false,
        'message' => 'No tiene permisos para crear proveedores'
    ]);
    exit;
}

if (isset($_POST['registrar_ajax'])) {
    $nom = strtoupper(trim($_POST['nom_proveedor']));
    $ruc = strtoupper(trim($_POST['ruc_proveedor']));
    $dir = strtoupper(trim($_POST['dir_proveedor']));
    $tel = strtoupper(trim($_POST['tel_proveedor']));
    $cont = strtoupper(trim($_POST['cont_proveedor']));
    $est = 1;
    $email = !empty($_POST['email_proveedor']) ? strtolower(trim($_POST['email_proveedor'])) : '';
    
    // Validar RUC
    if (strlen($ruc) != 11 || !ctype_digit($ruc)) {
        //  AUDITORÍA: ERROR DE VALIDACIÓN
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'PROVEEDOR', "RUC inválido: '$ruc' | Nombre: '$nom'");
        
        echo json_encode([
            'success' => false,
            'message' => 'El RUC debe tener exactamente 11 dígitos numéricos'
        ]);
        exit;
    }
    
    // Registrar proveedor
    $rpta = GrabarProveedor($nom, $ruc, $dir, $tel, $cont, $est, $email);
    
    if ($rpta == "NO") {
        //  AUDITORÍA: ERROR - YA EXISTE
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'PROVEEDOR', "Nombre: '$nom' | RUC: '$ruc' - Ya existe (AJAX)");
        
        echo json_encode([
            'success' => false,
            'message' => 'Ya existe un proveedor con ese nombre o RUC'
        ]);
        exit;
    }
    
    if ($rpta == "ERROR") {
        //  AUDITORÍA: ERROR DEL SISTEMA
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'PROVEEDOR', "Nombre: '$nom' | RUC: '$ruc' - Error del sistema (AJAX)");
        
        echo json_encode([
            'success' => false,
            'message' => 'Error al registrar el proveedor'
        ]);
        exit;
    }
    
    $id_proveedor = $rpta;
    
    // Registrar cuentas bancarias si existen
    $cantidad_cuentas = 0;
    
    if (
        isset($_POST['id_banco'], $_POST['id_moneda'], $_POST['cta_corriente'], $_POST['cta_interbancaria']) &&
        is_array($_POST['id_banco'])
    ) {
        for ($i = 0; $i < count($_POST['id_banco']); $i++) {
            $id_banco = !empty($_POST['id_banco'][$i]) ? (int)$_POST['id_banco'][$i] : null;
            $id_moneda = !empty($_POST['id_moneda'][$i]) ? (int)$_POST['id_moneda'][$i] : null;
            $cta_corriente = !empty($_POST['cta_corriente'][$i]) ? trim($_POST['cta_corriente'][$i]) : '';
            $cta_interbancaria = !empty($_POST['cta_interbancaria'][$i]) ? trim($_POST['cta_interbancaria'][$i]) : '';

            // Solo registrar si hay datos válidos
            if (!empty($id_banco) && !empty($id_moneda) && !empty($cta_corriente)) {
                GrabarCuentaProveedor($id_proveedor, $id_banco, $id_moneda, $cta_corriente, $cta_interbancaria);
                $cantidad_cuentas++;
            }
        }
    }
    
    //  AUDITORÍA: REGISTRO EXITOSO
    $estado_texto = ($est == 1) ? 'Activo' : 'Inactivo';
    $descripcion = "Nombre: '$nom' | RUC: '$ruc' | Estado: $estado_texto | $cantidad_cuentas cuenta(s) bancaria(s) (AJAX)";
    GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'PROVEEDOR', $descripcion);
    
    echo json_encode([
        'success' => true,
        'message' => 'Proveedor registrado correctamente',
        'id_proveedor' => $id_proveedor,
        'nombre_proveedor' => $nom
    ]);
    
} else {
    // AUDITORÍA: SOLICITUD INVÁLIDA
    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'PROVEEDOR', "Solicitud inválida - Parámetro 'registrar_ajax' no recibido");
    
    echo json_encode([
        'success' => false,
        'message' => 'Solicitud inválida'
    ]);
}
?>