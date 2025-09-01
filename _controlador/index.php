<?php
// index.php - Controlador principal de login

// Procesar formulario de login si se envió
if ($_POST) {
    require_once("../_conexion/autentificacion_usuario.php");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | ARCE PERU</title>
    <link rel="stylesheet" href="../_complemento/css/estilo_login.css">
    <link rel="shortcut icon" href="../_complemento/images/icon.png" type="image/x-icon">
</head>
<body>
    <hgroup>
        <img src="../_complemento/images/logoarcee.png" />
        <h2>Iniciar Sesión</h2>
    </hgroup>
    
    <form method="post" action="">
        <div class="group">
            <input type="text" name="usu" required="required">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Usuario</label>
        </div>
        
        <div class="group">
            <input type="password" name="pass" required="required">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Contraseña</label>
        </div>

        <button type="submit" class="button buttonBlue">
            Ingresar
            <div class="ripples buttonRipples">
                <span class="ripplesCircle"></span>
            </div>
        </button>
    </form>

    <footer>
        <p>ARCE PERU &copy; <?php echo date('Y'); ?></p>
    </footer>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script src="../_complemento/js/script_login.js"></script>
    
    <?php 
    // Mostrar alertas si existen parámetros en la URL
    if (isset($_GET['acceso']) && $_GET['acceso'] == 'true') {
        echo '<script>alert("Usuario o contraseña incorrectos");</script>';
    }
    if (isset($_GET['error']) && $_GET['error'] == 'true') {
        echo '<script>alert("Error en el sistema");</script>';
    }
    ?>
</body>
</html>