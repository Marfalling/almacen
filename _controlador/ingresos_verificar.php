<?php
//=======================================================================
// INGRESOS - VER (ingresos_verificar.php)
//=======================================================================
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('ver_ingresos')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'INGRESOS', 'VER');
    header("location: bienvenido.php?permisos=true");
    exit;
}

require_once("../_modelo/m_ingreso.php");
require_once("../_modelo/m_documentos.php");

// Verificar si se recibió el ID de compra
if (!isset($_GET['id_compra'])) {
    header("location: ingresos_mostrar.php");
    exit;
}

$id_compra = intval($_GET['id_compra']);

// Verificar que la compra existe y está aprobada
$comprax = ObtenerDetalleCompra($id_compra);
if (!$comprax) {
    header("location: ingresos_mostrar.php?error=" . urlencode("Compra no encontrada"));
    exit;
}

// ============================================
//  DETERMINAR SI ES SERVICIO   
// ============================================
$tipo_producto = ObtenerTipoProductoPedidoPorCompra($id_compra);

$es_servicio = $tipo_producto['es_servicio'];
$nombre_tipo_pedido = $tipo_producto['nom_producto_tipo'];

// ============================================
// OBTENER DATOS (MISMO FLUJO PARA AMBOS TIPOS)
// ============================================
$productos_pendientes = ObtenerProductosPendientesIngreso($id_compra);
$documentos_ingreso = MostrarDocumentos('ingresos', $id_compra);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>
        <?php echo $es_servicio ? 'Verificar Servicio' : 'Verificar Ingreso'; ?> - Orden #<?php echo $id_compra; ?>
    </title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            
            // 🆕 PASAR VARIABLE A LA VISTA
            require_once("../_vista/v_ingresos_verificar.php");
            
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php
    require_once("../_vista/v_script.php");
    require_once("../_vista/v_alertas.php");
    ?>

    <script>
    // 🆕 VARIABLE GLOBAL SOLO PARA UI
    const ES_SERVICIO = <?php echo $es_servicio ? 'true' : 'false'; ?>;
    const TIPO_ORDEN = '<?php echo $es_servicio ? 'servicio' : 'compra'; ?>';
    
    // ============================================
    // FUNCIONES PARA MANEJO DE PRODUCTOS/SERVICIOS
    // ============================================
    function toggleProducto(checkbox) {
        const productId = checkbox.dataset.producto;
        const cantidadInput = document.querySelector(`input[name="cantidades[${productId}]"]`);
        
        if (checkbox.checked) {
            cantidadInput.value = checkbox.dataset.pendiente;
            cantidadInput.style.backgroundColor = '#fff';
            cantidadInput.focus();
        } else {
            cantidadInput.value = '';
            cantidadInput.style.backgroundColor = '#f8f9fa';
        }
    }

    function validarCantidad(input, maxCantidad) {
        const value = parseFloat(input.value);
        
        if (value > maxCantidad) {
            Swal.fire({
                icon: 'warning',
                title: 'Cantidad inválida',
                text: `La cantidad ingresada no puede ser mayor a la cantidad pendiente (${maxCantidad})`,
            });
            input.value = maxCantidad;
        }
        
        if (value <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Cantidad inválida', 
                text: 'La cantidad debe ser mayor a 0',
            });
            input.value = 0.01;
        }
        
        // Auto-marcar el checkbox si hay cantidad
        const productId = input.name.match(/\[(\d+)\]/)[1];
        const checkbox = document.querySelector(`input[data-producto="${productId}"]`);
        checkbox.checked = value > 0;
    }

    function procesarIngreso() {
        // VALIDACIÓN 1: Verificar que hay documentos
        const contenedorDocs = document.getElementById('contenedor-documentos');
        const hayDocumentos = contenedorDocs.querySelector('table') !== null;
        
        if (!hayDocumentos) {
            const textoTipo = ES_SERVICIO ? 'VALIDACIÓN DE SERVICIO' : 'INGRESO';
            const textoDocumento = ES_SERVICIO ? 'acta de conformidad, informe de servicio u otro documento' : 'guía de remisión, factura, etc.';
            
            Swal.fire({
                icon: 'error',
                title: 'Documentos requeridos',
                html: `<p><strong>NO PUEDE PROCESAR ${textoTipo} SIN DOCUMENTOS.</strong></p>` +
                      `<p>Debe adjuntar al menos un documento (${textoDocumento}) antes de continuar.</p>` +
                      '<p>Por favor, use el botón <strong>"Subir Documento"</strong> en la sección correspondiente.</p>',
                confirmButtonColor: '#dc3545',
            });
            
            // Scroll a la sección de documentos
            document.querySelector('#lista-documentos-ingreso').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            
            return false;
        }
        
        // VALIDACIÓN 2: Productos/Servicios seleccionados
        const checkboxesSeleccionados = document.querySelectorAll('.producto-checkbox:checked');
        const textoItem = ES_SERVICIO ? 'servicio' : 'producto';
        
        if (checkboxesSeleccionados.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Selección requerida',
                text: `Debe marcar al menos un ${textoItem} para procesar`,
            });
            return false;
        }
        
        // VALIDACIÓN 3: Cantidades válidas
        let cantidadesValidas = true;
        
        checkboxesSeleccionados.forEach(checkbox => {
            const productId = checkbox.dataset.producto;
            const cantidadInput = document.querySelector(`input[name="cantidades[${productId}]"]`);
            const cantidad = parseFloat(cantidadInput.value);
            
            if (!cantidad || cantidad <= 0) {
                cantidadesValidas = false;
            }
        });
        
        if (!cantidadesValidas) {
            Swal.fire({
                icon: 'error',
                title: 'Cantidades inválidas',
                text: `Todos los ${textoItem}s seleccionados deben tener una cantidad válida mayor a 0`,
            });
            return false;
        }
        
        // CONFIRMACIÓN
        const textoAccion = ES_SERVICIO ? 'validar' : 'agregar al stock';
        const textoItemPlural = ES_SERVICIO ? 'servicio(s)' : 'producto(s)';
        const tituloConfirmacion = ES_SERVICIO ? '¿Confirmar validación de servicio?' : '¿Confirmar ingreso?';
        
        Swal.fire({
            title: tituloConfirmacion,
            text: `¿Está seguro de que desea ${textoAccion} ${checkboxesSeleccionados.length} ${textoItemPlural}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: ES_SERVICIO ? 'Sí, validar servicio' : 'Sí, procesar ingreso',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                enviarFormularioAjax();
            }
        });
    }

    function enviarFormularioAjax() {
        const textoAccion = ES_SERVICIO ? 'validación' : 'ingreso';
        
        // Mostrar loading
        Swal.fire({
            title: `Procesando ${textoAccion}...`,
            text: ES_SERVICIO ? 'Validando servicios prestados' : 'Agregando productos al stock',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Recopilar datos del formulario
        const formData = new FormData(document.getElementById('form-ingreso'));
        formData.append('id_compra', <?php echo $id_compra; ?>);
        // 🆕 NO ENVIAR 'es_servicio' - el backend usa la misma función para ambos
        
        $.ajax({
            url: 'ingresos_procesar.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.tipo_mensaje === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: ES_SERVICIO ? '¡Validación exitosa!' : '¡Ingreso exitoso!',
                        text: response.mensaje,
                        confirmButtonColor: '#28a745',
                    }).then(() => {
                        window.location.reload();
                    });
                } else if (response.tipo_mensaje === 'warning') {
                    Swal.fire({
                        icon: 'warning',
                        title: ES_SERVICIO ? 'Validación parcial' : 'Ingreso parcial',
                        text: response.mensaje,
                        confirmButtonColor: '#ffc107',
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: ES_SERVICIO ? 'Error en la validación' : 'Error en el ingreso',
                        text: response.mensaje,
                        confirmButtonColor: '#dc3545',
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                console.error('Response:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor. Por favor, intente nuevamente.',
                    confirmButtonColor: '#dc3545',
                });
            }
        });
    }
    </script>
</body>
</html>