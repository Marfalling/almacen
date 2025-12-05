<?php 
header('Content-Type: application/json');
//llamada a funciones
require_once("_modelo/m_pedido.php");

//recibir dede el app
$id_pedido = $_REQUEST['id_pedido'];

$pedido = MostrarPedidoID($id_pedido);

//respuesta al app
echo json_encode($pedido);
?>