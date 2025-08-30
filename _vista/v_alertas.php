<script src="../_complemento/js/sweetalert2.min.js"></script>
<?php
    if (isset($_GET['registrado']) && $_GET['registrado'] == 'true') {
        echo '
        <script>
            Swal.fire({
                icon: "success",
                title: "Registro completado exitosamente",
                showConfirmButton: false,
                timer: 2500
            });
            history.replaceState(null, null, window.location.pathname);
        </script>';
    } elseif (isset($_GET['existe']) && $_GET['existe'] == 'true') {
        echo '<script>
            Swal.fire({
                icon: "info",
                title: "El registro ya existe",
                showConfirmButton: false,
                timer: 2500
            });
            history.replaceState(null, null, window.location.pathname);
        </script>';
    } elseif (isset($_GET['error']) && $_GET['error'] == 'true') {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Ocurrió un error en la operación",
                showConfirmButton: false,
                timer: 2500
            });
            history.replaceState(null, null, window.location.pathname);
        </script>';
    } elseif (isset($_GET['actualizado']) && $_GET['actualizado'] == 'true') {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "El registro ha sido actualizado exitosamente",
                showConfirmButton: false,
                timer: 2500
            });
            history.replaceState(null, null, window.location.pathname);
        </script>';
    } elseif (isset($_GET['permisos']) && $_GET['permisos'] == 'true') {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "No tiene permisos para acceder a esta opción",
                showConfirmButton: false,
                timer: 2500
            });
            history.replaceState(null, null, window.location.pathname);
        </script>';
    } elseif (isset($_GET['acceso']) && $_GET['acceso'] == 'true') {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Datos de acceso incorrectos",
                showConfirmButton: false,
                timer: 2500
            });
            history.replaceState(null, null, window.location.pathname);
        </script>';
    } elseif (isset($_GET['anulado']) && $_GET['anulado'] == 'true') {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "La muestra ha sido anulada exitosamente",
                showConfirmButton: false,
                timer: 2500
            });
            history.replaceState(null, null, window.location.pathname);
        </script>';
    } elseif (isset($_GET['eliminado']) && $_GET['eliminado'] == 'true') {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "El rango ha sido anulado exitosamente",
                showConfirmButton: false,
                timer: 2500
            });
            history.replaceState(null, null, window.location.pathname);
        </script>';
    } elseif (isset($_GET['password_incorrecta']) && $_GET['password_incorrecta'] == 'true') {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "La contraseña es incorrecta",
                showConfirmButton: false,
                timer: 2500
            }).then(() => {
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.delete(`password_incorrecta`);
                const newUrl = window.location.pathname + `?` + urlParams.toString();
                window.location.href = newUrl;
            });
        </script>';
    }
    ?>