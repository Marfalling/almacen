<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_ingreso.php");

// Verificar si se recibió el ID de compra
if (!isset($_GET['id_compra'])) {
    header("location: ingresos_mostrar.php");
    exit;
}

$id_compra = intval($_GET['id_compra']);

// Verificar que la compra existe y está aprobada
$compra = ObtenerDetalleCompra($id_compra);
if (!$compra) {
    header("location: ingresos_mostrar.php?error=" . urlencode("Compra no encontrada"));
    exit;
}

// Obtener productos pendientes de ingreso
$productos_pendientes = ObtenerProductosPendientesIngreso($id_compra);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Verificar Ingreso - Orden #<?php echo $id_compra; ?></title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            
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
    // JavaScript para manejo de ingresos con AJAX
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
        const checkboxesSeleccionados = document.querySelectorAll('.producto-checkbox:checked');
        
        if (checkboxesSeleccionados.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Selección requerida',
                text: 'Debe marcar al menos un producto para ingresar',
            });
            return false;
        }
        
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
                text: 'Todos los productos seleccionados deben tener una cantidad válida mayor a 0',
            });
            return false;
        }
        
        // Confirmación antes de procesar
        Swal.fire({
            title: '¿Confirmar ingreso?',
            text: `¿Está seguro de que desea agregar ${checkboxesSeleccionados.length} producto(s) al stock?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, procesar ingreso',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                enviarFormularioAjax();
            }
        });
    }

    function enviarFormularioAjax() {
        // Mostrar loading
        Swal.fire({
            title: 'Procesando ingreso...',
            text: 'Por favor espere mientras se procesan los productos',
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
                        title: '¡Ingreso exitoso!',
                        text: response.mensaje,
                        confirmButtonColor: '#28a745',
                    }).then(() => {
                        window.location.reload();
                    });
                } else if (response.tipo_mensaje === 'warning') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ingreso parcial',
                        text: response.mensaje,
                        confirmButtonColor: '#ffc107',
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en el ingreso',
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