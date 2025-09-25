<?php
session_start();
require_once("../_conexion/sesion.php");
//require_once("../_modelo/m_auditoria.php");

//GrabarAuditoria($id, $usuario_sesion, 'CIERRE DE SESIÓN', 'SESIÓN', $usuario_sesion);

session_unset();
session_destroy();
header("location: ../_controlador/index.php");
?>