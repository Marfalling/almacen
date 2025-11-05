<?php 
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('crear_personal')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'PERSONAL', 'CREAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}
//=======================================================================
// CONTROLADOR: personal_nuevo.php
//=======================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Nuevo Personal</title>
    
    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            
            require_once("../_modelo/m_personal.php");
            require_once("../_modelo/m_area.php");
            require_once("../_modelo/m_cargo.php");
            require_once("../_conexion/conexion.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_area   = $_REQUEST['id_area'];
                $id_cargo  = $_REQUEST['id_cargo'];
                $nom       = strtoupper(trim($_REQUEST['nom']));
                $dni       = trim($_REQUEST['dni']);
                $email     = trim($_REQUEST['email']);
                $tel       = trim($_REQUEST['tel']);
                $est       = isset($_REQUEST['est']) ? 1 : 0;

                // Validación de campos obligatorios
                if (empty($id_area) || empty($id_cargo) || empty($nom) || empty($dni)) {
                ?>
                    <script Language="JavaScript">
                        location.href = 'personal_mostrar.php?incompleto=true';
                    </script>
                <?php
                    exit();
                }

                // Registrar personal
                $rpta = GrabarPersonal($id_area, $id_cargo, $nom, $dni, $email, $tel, $est);

                if ($rpta == "SI") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'personal_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'personal_mostrar.php?existe=true';
                    </script>
                <?php
                } else {
                ?>
                    <script Language="JavaScript">
                        location.href = 'personal_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            // Obtener listas necesarias para la vista (áreas y cargos)
            function ObtenerAreas()
            {
                include("../_conexion/conexion.php");

                $sql = "SELECT id_area, nom_area 
                        FROM {$bd_complemento}.area 
                        WHERE act_area = 1 
                        ORDER BY nom_area";

                $res = $con->query($sql);

                $areas = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

                mysqli_close($con);
                return $areas;
            }

            function ObtenerCargos()
            {
                include("../_conexion/conexion.php");

                $sql = "SELECT id_cargo, nom_cargo 
                        FROM {$bd_complemento}.cargo 
                        WHERE act_cargo = 1 
                        ORDER BY nom_cargo";

                $res = $con->query($sql);

                $cargos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

                mysqli_close($con);
                return $cargos;
            }

            $areas = ObtenerAreas();
            $cargos = ObtenerCargos();

            require_once("../_vista/v_personal_nuevo.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php
    require_once("../_vista/v_script.php");
    require_once("../_vista/v_alertas.php");
    ?>
</body>
</html>
