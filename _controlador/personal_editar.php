<?php
//=======================================================================
// CONTROLADOR: personal_editar.php
//=======================================================================
require_once("../_conexion/conexion.php");
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_personal.php");
require_once("../_modelo/m_area.php");
require_once("../_modelo/m_cargo.php");
require_once("../_modelo/m_auditoria.php");

// Validar permisos
if (!verificarPermisoEspecifico('editar_personal')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PERSONAL', 'EDITAR');
    header("location: dashboard.php?permisos=false");
    exit;
}

// Obtener ID del personal desde GET
$id_personal = isset($_GET['id_personal']) ? intval($_GET['id_personal']) : 0;
if ($id_personal <= 0) {
    header("location: personal_mostrar.php?error=true");
    exit;
}

// Si se envía el formulario
if (isset($_POST['registrar'])) {
    $nom      = strtoupper(trim($_POST['nom'] ?? ''));
    $dni      = trim($_POST['dni'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $cel      = trim($_POST['cel'] ?? '');
    $est      = isset($_POST['est']) ? 1 : 0;
    $id_area  = intval($_POST['id_area'] ?? 0);
    $id_cargo = intval($_POST['id_cargo'] ?? 0);

    // Validaciones mínimas
    if ($nom === '' || $dni === '' || $id_area === 0 || $id_cargo === 0) {
        //  AUDITORÍA: ERROR POR CAMPOS INCOMPLETOS
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'PERSONAL', "ID: $id_personal | Campos incompletos");
        
        echo "<script>alert('Error: Complete todos los campos obligatorios.'); history.back();</script>";
        exit;
    }

    //  OBTENER DATOS ANTES DE EDITAR (usando función existente)
    $datos_antes = ConsultarPersonal($id_personal);
    $nom_anterior = $datos_antes['nom_personal'] ?? '';
    $dni_anterior = $datos_antes['dni_personal'] ?? '';
    $email_anterior = $datos_antes['email_personal'] ?? '';
    $cel_anterior = $datos_antes['cel_personal'] ?? '';
    $area_anterior = $datos_antes['id_area'] ?? 0;
    $cargo_anterior = $datos_antes['id_cargo'] ?? 0;
    $est_anterior = $datos_antes['act_personal'] ?? 0;
    
    //  OBTENER NOMBRES DE ÁREA Y CARGO (ANTES)
    $area_antes_data = ObtenerArea($area_anterior);
    $cargo_antes_data = ObtenerCargo($cargo_anterior);
    $nom_area_anterior = !empty($area_antes_data) ? $area_antes_data[0]['nom_area'] : '';
    $nom_cargo_anterior = isset($cargo_antes_data['nom_cargo']) ? $cargo_antes_data['nom_cargo'] : '';

    //  EJECUTAR EDICIÓN (usando función existente)
    $rpta = EditarPersonal($id_personal, $id_area, $id_cargo, $nom, $dni, $email, $cel, $est);

    if ($rpta === "SI") {
        //  COMPARAR Y CONSTRUIR DESCRIPCIÓN
        $cambios = [];
        
        if ($nom_anterior != $nom) {
            $cambios[] = "Nombre: '$nom_anterior' → '$nom'";
        }
        if ($dni_anterior != $dni) {
            $cambios[] = "DNI: '$dni_anterior' → '$dni'";
        }
        if ($email_anterior != $email) {
            $cambios[] = "Email: '$email_anterior' → '$email'";
        }
        if ($cel_anterior != $cel) {
            $cambios[] = "Celular: '$cel_anterior' → '$cel'";
        }
        
        // 🔹 OBTENER NOMBRES DE ÁREA Y CARGO (DESPUÉS)
        if ($area_anterior != $id_area) {
            $area_nueva_data = ObtenerArea($id_area);
            $nom_area_nueva = !empty($area_nueva_data) ? $area_nueva_data[0]['nom_area'] : '';
            $cambios[] = "Área: '$nom_area_anterior' → '$nom_area_nueva'";
        }
        
        if ($cargo_anterior != $id_cargo) {
            $cargo_nuevo_data = ObtenerCargo($id_cargo);
            $nom_cargo_nuevo = isset($cargo_nuevo_data['nom_cargo']) ? $cargo_nuevo_data['nom_cargo'] : '';
            $cambios[] = "Cargo: '$nom_cargo_anterior' → '$nom_cargo_nuevo'";
        }
        
        if ($est_anterior != $est) {
            $estado_ant = ($est_anterior == 1) ? 'Activo' : 'Inactivo';
            $estado_nvo = ($est == 1) ? 'Activo' : 'Inactivo';
            $cambios[] = "Estado: $estado_ant → $estado_nvo";
        }
        
        if (empty($cambios)) {
            $descripcion = "ID: $id_personal | Sin cambios";
        } else {
            $descripcion = "ID: $id_personal | " . implode(' | ', $cambios);
        }
        
        //  AUDITORÍA: EDICIÓN EXITOSA
        GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'PERSONAL', $descripcion);
        
        echo "<script>location.href='personal_mostrar.php?actualizado=true';</script>";
        exit;
    } elseif ($rpta === "NO") {
        //  AUDITORÍA: ERROR - YA EXISTE DNI
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'PERSONAL', "ID: $id_personal | DNI '$dni' ya existe");
        
        echo "<script>alert('Error: Ya existe un personal con este DNI.'); history.back();</script>";
        exit;
    } else {
        //  AUDITORÍA: ERROR GENERAL
        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'PERSONAL', "ID: $id_personal | Error del sistema");
        
        echo "<script>alert('Error al actualizar el personal.'); history.back();</script>";
        exit;
    }
}

// Obtener datos actuales del personal
$personal_data = ConsultarPersonal($id_personal);
if (!$personal_data) {
    header("location: personal_mostrar.php?error=true");
    exit;
}

// Asignar valores con fallback
$nom      = $personal_data['nom_personal'] ?? '';
$dni      = $personal_data['dni_personal'] ?? '';
$email    = $personal_data['email_personal'] ?? '';
$cel      = $personal_data['cel_personal'] ?? '';
$est      = ($personal_data['act_personal'] ?? 0) ? "checked" : "";
$id_area  = $personal_data['id_area'] ?? '';
$id_cargo = $personal_data['id_cargo'] ?? '';

// Listas para select (usando funciones existentes)
$areas  = MostrarAreasActivas();
$cargos = MostrarCargosActivos();

// Cargar la vista
require_once("../_vista/v_personal_editar.php");
?>