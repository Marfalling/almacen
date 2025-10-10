<?php
//=======================================================================
// CONTROLADOR: personal_editar.php
//=======================================================================
require_once("../_conexion/conexion_complemento.php");
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
        echo "<script>alert('Error: Complete todos los campos obligatorios.'); history.back();</script>";
        exit;
    }

    // LLAMADA CORRECTA A LA FUNCIÓN: ORDEN DE PARÁMETROS EXACTO
    $rpta = EditarPersonal($id_personal, $id_area, $id_cargo, $nom, $dni, $email, $cel, $est);

    if ($rpta === "SI") {
        GrabarAuditoria($id, $usuario_sesion, 'ACTUALIZÓ PERSONAL', 'PERSONAL', "ID: $id_personal");
        echo "<script>location.href='personal_mostrar.php?actualizado=true';</script>";
        exit;
    } elseif ($rpta === "NO") {
        echo "<script>alert('Error: Ya existe un personal con este DNI.'); history.back();</script>";
        exit;
    } else {
        echo "<script>alert('Error al actualizar el personal.'); history.back();</script>";
        exit;
    }
}

// Obtener datos actuales del personal
$personal_data = ConsultarPersonal($id_personal); //  Usa ConsultarPersonal para traer los datos
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

// Listas para select
$areas  = MostrarAreasActivas();
$cargos = MostrarCargosActivos();

// Grabar auditoría de ingreso a la edición
GrabarAuditoria($id, $usuario_sesion, 'INGRESO', 'PERSONAL', 'EDITAR');

// Cargar la vista
require_once("../_vista/v_personal_editar.php");
?>
