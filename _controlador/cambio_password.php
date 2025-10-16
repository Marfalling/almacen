<?php
require_once("../_conexion/sesion.php");

// Verificar sesión activa
if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    header("location: ../login.php");
    exit;
}

require_once("../_modelo/m_usuario.php");

$mensaje = "";

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_SESSION['id'];
    $actual = trim($_POST['password_actual'] ?? '');
    $nueva = trim($_POST['password_nueva'] ?? '');
    $confirmar = trim($_POST['password_confirmar'] ?? '');

    if ($nueva === "" || $confirmar === "" || $actual === "") {
        $mensaje = "<div class='alert alert-danger'>Todos los campos son obligatorios.</div>";
    } elseif ($nueva !== $confirmar) {
        $mensaje = "<div class='alert alert-danger'>Las contraseñas nuevas no coinciden.</div>";
    } else {
        if (CambiarPassword($id, $actual, $nueva)) {
            $mensaje = "<div class='alert alert-success'>Contraseña actualizada correctamente.</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>La contraseña actual no es correcta.</div>";
        }
    }
}

//=================
// ESTRUCTURA EXACTA como tus otros módulos
//=================
require_once("../_vista/v_estilo.php");
?>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">

            <?php 
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            ?>

            <?php require_once("../_vista/v_cambio_password.php"); ?>

            <?php 
            require_once("../_vista/v_footer.php");
            require_once("../_vista/v_script.php");
            require_once("../_vista/v_alertas.php");
            ?>
        </div>
    </div>
</body>
</html>

