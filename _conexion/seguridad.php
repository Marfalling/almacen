<?php
// seguridad.php - Verificación de autenticación
if (!isset($_SESSION['autentificado']) || $_SESSION['autentificado'] !== TRUE) {
    header("location: index.php");
    exit();
}

// Verificar que la sesión no haya expirado (opcional - 8 horas)
if (isset($_SESSION['tiempo_login'])) {
    $tiempo_transcurrido = time() - $_SESSION['tiempo_login'];
    $tiempo_limite = 8 * 60 * 60; // 8 horas en segundos
    
    if ($tiempo_transcurrido > $tiempo_limite) {
        session_unset();
        session_destroy();
        header("location: index.php?sesion_expirada=true");
        exit();
    }
}

// Actualizar tiempo de última actividad
$_SESSION['tiempo_login'] = time();
?>