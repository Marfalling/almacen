<?php
$id_pedido = $_GET['id_pedido'];
//llamada a funciones
require_once("_modelo/m_vigilante.php");
$vigilante = MostrarVigilante3($id_pedido);
//respuesta al app
echo json_encode($vigilante);
?>