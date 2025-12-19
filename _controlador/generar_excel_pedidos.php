<?php
// ==============================================================
// ARCHIVO: generar_excel_pedidos.php
// ==============================================================

// INICIAR SESION
include("../_conexion/sesion.php");

// Llamar a MODELO
require_once('../_modelo/m_pedidos.php');
require_once('../_modelo/m_compras.php');
require_once('../_modelo/m_moneda.php'); 

// ==============================================================
// OBTENER FILTROS
// ==============================================================

$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
$id_personal_filtro = isset($_GET['id_personal']) ? intval($_GET['id_personal']) : null;

// ==============================================================
// NOMBRE DEL ARCHIVO
// ==============================================================

$filename = "Reporte_Pedidos_" . date('Ymd_His') . ".xls";

// ==============================================================
// OBTENER DATOS
// ==============================================================

// Obtener pedidos con filtros
if ($fecha_inicio && $fecha_fin) {
    $pedidos = MostrarPedidosFecha($fecha_inicio, $fecha_fin, $id_personal_filtro);
} else {
    $pedidos = MostrarPedidos();
}

// ==============================================================
// PROCESAR DATOS PARA EL EXCEL
// ==============================================================

$consulta = array();

foreach ($pedidos as $pedido) {
    // Solo procesar pedidos activos (no anulados)
    if ($pedido['est_pedido'] == 0) {
        continue;
    }
    
    $id_pedido = $pedido['id_pedido'];
    
    // Código del pedido
    $codigo_pedido = $pedido['cod_pedido'];
    
    // Tipo de pedido
    $tipo_pedido = strtoupper($pedido['nom_producto_tipo']);
    
    // Almacén
    $almacen = $pedido['nom_almacen'];
    
    // Ubicación
    $ubicacion = $pedido['nom_ubicacion'];
    
    // Solicitante
    $solicitante = $pedido['nom_personal'];
    
    // Fecha del pedido
    $fecha_pedido = date('d/m/Y', strtotime($pedido['fec_pedido']));
    
    // Estado del pedido
    $estado_pedido = '';
    switch ($pedido['est_pedido']) {
        case 1: 
            $estado_pedido = !empty($pedido['id_personal_aprueba_tecnica']) ? 'APROBADO' : 'PENDIENTE';
            break;
        case 2: $estado_pedido = 'ATENDIDO'; break;
        case 3: $estado_pedido = 'APROBADO'; break;
        case 4: $estado_pedido = 'INGRESADO'; break;
        case 5: $estado_pedido = 'FINALIZADO'; break;
        default: $estado_pedido = 'DESCONOCIDO';
    }
    
    // Aprobación técnica del PEDIDO
    $aprobado_tecnica_pedido = !empty($pedido['nom_aprobado_tecnica']) ? $pedido['nom_aprobado_tecnica'] : '-';
    
    // Obtener órdenes de compra del pedido
    $ordenes_compra = ConsultarCompra($id_pedido);
    
    if (!empty($ordenes_compra)) {
        // Si hay órdenes de compra, crear una fila por cada orden
        foreach ($ordenes_compra as $compra) {
            // Solo incluir órdenes activas
            if ($compra['est_compra'] == 0) {
                continue;
            }
            
            // Generar código de orden (OC o OS)
            if ($pedido['id_producto_tipo'] == 1) {
                $cod_orden = 'OC' . str_pad($compra['id_compra'], 4, '0', STR_PAD_LEFT);
            } else {
                $cod_orden = 'OS' . str_pad($compra['id_compra'], 4, '0', STR_PAD_LEFT);
            }
            
            // Proveedor
            $proveedor = $compra['nom_proveedor'];
            
            // Fecha de registro de la orden
            $fecha_registro = date('d/m/Y H:i', strtotime($compra['fecha_reg_compra']));
            
            // Tipo de pago
            $es_contado = empty($compra['plaz_compra']) || $compra['plaz_compra'] == 0;
            $tipo_pago = $es_contado ? 'Contado' : 'Crédito (' . $compra['plaz_compra'] . ' días)';
            
            // Registrado por
            $registrado_por = $compra['nom_personal'];
            
            // Aprobado financiera por (de la COMPRA)
            $aprobado_financiera = !empty($compra['nom_aprobado_financiera']) ? $compra['nom_aprobado_financiera'] : '-';
            
            // 🔹 OBTENER MONEDA USANDO EL MODELO
            $moneda_nombre = '-';
            $simbolo_moneda = '';
            
            if (!empty($compra['id_moneda'])) {
                $moneda_data = ObtenerMoneda($compra['id_moneda']);
                if ($moneda_data) {
                    $moneda_nombre = $moneda_data['nom_moneda'];
                    
                    // Determinar símbolo según el nombre
                    if (stripos($moneda_nombre, 'sol') !== false || stripos($moneda_nombre, 'nuevos soles') !== false) {
                        $simbolo_moneda = 'S/.';
                    } elseif (stripos($moneda_nombre, 'dólar') !== false || stripos($moneda_nombre, 'dolar') !== false) {
                        $simbolo_moneda = 'US$';
                    } else {
                        $simbolo_moneda = '';
                    }
                }
            }
            
            // Calcular total de la orden
            $total_orden = 0;
            $detalles_compra = ConsultarCompraDetalle($compra['id_compra']);
            
            foreach ($detalles_compra as $det) {
                $cantidad = floatval($det['cant_compra_detalle']);
                $precio = floatval($det['prec_compra_detalle']);
                $igv_porcentaje = floatval($det['igv_compra_detalle']);
                
                $subtotal = $cantidad * $precio;
                $igv_monto = $subtotal * ($igv_porcentaje / 100);
                $total_orden += $subtotal + $igv_monto;
            }
            
            $total_formateado = $simbolo_moneda . ' ' . number_format($total_orden, 2);
            
            // Agregar al array
            $consulta[] = array(
                'codigo_pedido' => $codigo_pedido,
                'tipo_pedido' => $tipo_pedido,
                'almacen' => $almacen,
                'ubicacion' => $ubicacion,
                'solicitante' => $solicitante,
                'fecha_pedido' => $fecha_pedido,
                'estado_pedido' => $estado_pedido,
                'codigo_orden' => $cod_orden,
                'proveedor' => $proveedor,
                'fecha_registro' => $fecha_registro,
                'tipo_pago' => $tipo_pago,
                'moneda' => $moneda_nombre, 
                'registrado_por' => $registrado_por,
                'aprobado_financiera' => $aprobado_financiera,
                'total' => $total_formateado
            );
        }
    } else {
        // Si no hay órdenes de compra, mostrar datos del pedido
        $consulta[] = array(
            'codigo_pedido' => $codigo_pedido,
            'tipo_pedido' => $tipo_pedido,
            'almacen' => $almacen,
            'ubicacion' => $ubicacion,
            'solicitante' => $solicitante,
            'fecha_pedido' => $fecha_pedido,
            'estado_pedido' => $estado_pedido,
            'codigo_orden' => '-',
            'proveedor' => '-',
            'fecha_registro' => '-',
            'tipo_pago' => '-',
            'moneda' => '-',
            'registrado_por' => '-',
            'aprobado_financiera' => $aprobado_tecnica_pedido,
            'total' => '-'
        );
    }
}

// ==============================================================
// GENERAR ARCHIVO EXCEL
// ==============================================================

require('../_vista/v_excel_reporte_pedidos.php');

?>