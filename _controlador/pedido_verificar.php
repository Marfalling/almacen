<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('ver_pedidos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PEDIDOS', 'VERIFICAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

// CARGAR MODELOS PRIMERO (antes de cualquier HTML)
require_once("../_modelo/m_pedidos.php");
require_once("../_modelo/m_obras.php");
require_once("../_modelo/m_proveedor.php");
require_once("../_modelo/m_moneda.php");
require_once("../_modelo/m_compras.php");
require_once("../_modelo/m_detraccion.php");

$id_pedido = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$id_compra_editar = isset($_REQUEST['id_compra']) ? intval($_REQUEST['id_compra']) : 0;
$modo_editar = ($id_compra_editar > 0);

$alerta = null;

// ============================================================================
// PROCESAR FORMULARIOS (ANTES DE CUALQUIER HTML)
// ============================================================================

// VERIFICAR ITEM
if (isset($_REQUEST['verificar_item'])) {
    $id_pedido_detalle = $_REQUEST['id_pedido_detalle'];
    $new_cant_fin = $_REQUEST['fin_cant_pedido_detalle'];
    $rpta = verificarItem($id_pedido_detalle, $new_cant_fin);

    if ($rpta == "SI") {
        header("Location: pedido_verificar.php?id=$id_pedido&success=verificado");
        exit;
    } else {
        $alerta = [
            "icon" => "error",
            "title" => "Error",
            "text" => "Error al verificar: $rpta"
        ];
    }
}

// ACTUALIZAR ORDEN
if (isset($_REQUEST['actualizar_orden'])) {
    $id_compra = $_REQUEST['id_compra'];
    $proveedor_sel = $_REQUEST['proveedor_orden'];
    $moneda_sel = $_REQUEST['moneda_orden'];
    $observacion = $_REQUEST['observaciones_orden'];
    $direccion = $_REQUEST['direccion_envio'];
    $plazo_entrega = $_REQUEST['plazo_entrega'];
    $porte = $_REQUEST['tipo_porte'];
    $fecha_orden = $_REQUEST['fecha_orden'];
    $items = $_REQUEST['items_orden'] ?? [];
    
    // NUEVO: Capturar la detracción
    $id_detraccion = isset($_REQUEST['id_detraccion']) ? intval($_REQUEST['id_detraccion']) : null;

    if (empty($proveedor_sel) || empty($moneda_sel) || empty($fecha_orden)) {
        $alerta = [
            "icon" => "error",
            "title" => "Error",
            "text" => "Complete todos los campos obligatorios (Fecha, Proveedor y Moneda)"
        ];
    } elseif (empty($items)) {
        $alerta = [
            "icon" => "error",
            "title" => "Error",
            "text" => "Debe tener al menos un item en la orden"
        ];
    } else {
        $rpta = ActualizarOrdenCompra(
            $id_compra,
            $proveedor_sel,
            $moneda_sel,
            $observacion,
            $direccion,
            $plazo_entrega,
            $porte,
            $fecha_orden,
            $items,
            $id_detraccion  
        );

        if ($rpta == "SI") {
            header("Location: pedido_verificar.php?id=$id_pedido&success=actualizado");
            exit;
        } else {
            $alerta = [
                "icon" => "error",
                "title" => "Error",
                "text" => "Error al actualizar orden: $rpta"
            ];
        }
    }
}

/// CREAR ORDEN
if (isset($_REQUEST['crear_orden'])) {
    $id_pedido = $_REQUEST['id'];
    $proveedor = $_REQUEST['proveedor_orden'];
    $moneda = $_REQUEST['moneda_orden'];
    $id_personal = $_SESSION['id_personal'];
    $observacion = $_REQUEST['observaciones_orden'];
    $direccion = $_REQUEST['direccion_envio'];
    $plazo_entrega = $_REQUEST['plazo_entrega'];
    $porte = $_REQUEST['tipo_porte'];
    $fecha_orden = $_REQUEST['fecha_orden'];
    $items = $_REQUEST['items_orden'];
    
    // NUEVO: Capturar la detracción
    $id_detraccion = isset($_REQUEST['id_detraccion']) ? intval($_REQUEST['id_detraccion']) : null;

    $rpta = CrearOrdenCompra($id_pedido, $proveedor, $moneda, $id_personal, $observacion, $direccion, $plazo_entrega, $porte, $fecha_orden, $items, $id_detraccion);

    error_log("Respuesta CrearOrdenCompra: " . $rpta);
    error_log("ID Pedido: " . $id_pedido);
    
    if ($rpta == "SI") {
        include("../_conexion/conexion.php");
        $check = mysqli_query($con, "SELECT * FROM compra WHERE id_pedido = $id_pedido ORDER BY id_compra DESC LIMIT 1");
        $ultima_orden = mysqli_fetch_assoc($check);
        error_log("Última orden creada ID: " . ($ultima_orden ? $ultima_orden['id_compra'] : 'NINGUNA'));
        mysqli_close($con);
        
        header("Location: pedido_verificar.php?id=$id_pedido&success=creado");
        exit;
    } else {
        $alerta = [
            "icon" => "error",
            "title" => "Error",
            "text" => "Error al crear orden: $rpta"
        ];
    }
}

// FINALIZAR VERIFICACIÓN
if (isset($_REQUEST['finalizar_verificacion'])) {
    $id_pedido = $_REQUEST['id'];
    
    $resultado = FinalizarPedido($id_pedido);
    
    if ($resultado['success']) {
        header("Location: pedidos_mostrar.php?success=finalizado");
        exit;
    } else {
        $alerta = [
            "icon" => $resultado['tipo'],
            "title" => "Error",
            "text" => $resultado['mensaje']
        ];
    }
}

// MOSTRAR ALERTAS DE SUCCESS (después del redirect)
if (isset($_GET['success'])) {
    switch($_GET['success']) {
        case 'verificado':
            $alerta = [
                "icon" => "success",
                "title" => "¡Éxito!",
                "text" => "Item verificado correctamente",
                "timer" => 1500
            ];
            break;
        case 'creado':
            $alerta = [
                "icon" => "success",
                "title" => "¡Orden Creada!",
                "text" => "La orden de compra se ha creado exitosamente",
                "timer" => 2000
            ];
            break;
        case 'actualizado':
            $alerta = [
                "icon" => "success",
                "title" => "¡Orden Actualizada!",
                "text" => "La orden de compra se ha actualizado exitosamente",
                "timer" => 2000
            ];
            break;
    }
}

// ============================================================================
// CARGAR DATOS DEL PEDIDO
// ============================================================================

$tiene_salida_activa = false; // Inicializar variable

if ($id_pedido > 0) {
    $pedido_data = ConsultarPedido($id_pedido);
    
    if (empty($pedido_data)) {
        $pedido_data = ConsultarPedidoAnulado($id_pedido);
    }
    
    if (!empty($pedido_data)) {
        $pedido_detalle = ConsultarPedidoDetalle($id_pedido);
        $pedido_compra = ConsultarCompra($id_pedido);
        $proveedor = MostrarProveedores();
        $moneda = MostrarMoneda();
        $obras = MostrarObras();

        //NUEVO: Verificar si ya tiene salida activa
        require_once("../_modelo/m_salidas.php");
        $tiene_salida_activa = TieneSalidaActivaPedido($id_pedido);

        $orden_data = null;
        $orden_detalle = null;
        if ($modo_editar) {
            $orden_data = ObtenerOrdenPorId($id_compra_editar);
            $orden_detalle = ObtenerDetalleOrden($id_compra_editar);
        }

        $pedido = $pedido_data[0];
    } else {
        header("Location: pedidos_mostrar.php?error=pedido_no_encontrado");
        exit;
    }
} else {
    header("Location: pedidos_mostrar.php?error=id_invalido");
    exit;
}

// ============================================================================
// AHORA SÍ, INICIAR EL HTML
// ============================================================================
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Verificar Pedido</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_vista/v_pedido_verificar.php"); 
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php
    require_once("../_vista/v_script.php");
    require_once("../_vista/v_alertas.php");
    ?>

    <?php if ($alerta): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerta = <?php echo json_encode($alerta, JSON_UNESCAPED_UNICODE); ?>;

        Swal.fire({
            icon: alerta.icon,
            title: alerta.title,
            text: alerta.text,
            showConfirmButton: !alerta.timer,
            timer: alerta.timer || null,
            allowOutsideClick: false
        });
    });
    </script>
    <?php endif; ?>
</body>
</html>