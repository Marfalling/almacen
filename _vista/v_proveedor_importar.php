<?php
//=======================================================================
// VISTA: v_proveedor_importar.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Importar Proveedores CSV</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Tu CSS de plantilla (x_panel, bordes, sombra, etc.) -->
    <link rel="stylesheet" href="../_assets/css/custom.css">
</head>
<body class="p-4">
    <div class="container">
        <h2>Importar Proveedores desde CSV</h2>
        <p class="text-muted">
            Selecciona el archivo <code>proveedores_limpio.csv</code> para cargar proveedores y sus cuentas.
        </p>

        <form action="../_controlador/proveedor_importar.php" method="post" enctype="multipart/form-data" class="mt-3">
            <div class="form-group">
                <label>Selecciona el archivo CSV:</label>
                <input type="file" name="archivo" accept=".csv" class="form-control-file" required>
            </div>
            <button type="submit" class="btn btn-success">Subir e Importar</button>
            <a href="../_controlador/proveedor_mostrar.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <!-- Scripts necesarios para modales -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




