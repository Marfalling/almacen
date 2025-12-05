<?php 
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

if (!verificarPermisoEspecifico('crear_personal')) {
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
                    //  AUDITORÍA: ERROR POR CAMPOS INCOMPLETOS
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'PERSONAL', "Campos incompletos | Nombre: '$nom' | DNI: '$dni'");
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
                    // AUDITORÍA: REGISTRO EXITOSO
                    $estado_texto = ($est == 1) ? 'Activo' : 'Inactivo';
                    
                    //  OBTENER NOMBRES DE ÁREA Y CARGO
                    $area_data = ObtenerArea($id_area);
                    $cargo_data = ObtenerCargo($id_cargo);
                    $nom_area = !empty($area_data) ? $area_data[0]['nom_area'] : '';
                    $nom_cargo = isset($cargo_data['nom_cargo']) ? $cargo_data['nom_cargo'] : '';
                    
                    $descripcion = "Nombre: '$nom' | DNI: '$dni' | Área: '$nom_area' | Cargo: '$nom_cargo' | Estado: $estado_texto";
                    GrabarAuditoria($id, $usuario_sesion, 'REGISTRAR', 'PERSONAL', $descripcion);
                ?>
                    <script Language="JavaScript">
                        location.href = 'personal_mostrar.php?registrado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                    
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'PERSONAL', "Nombre: '$nom' | DNI: '$dni' - Ya existe");
                ?>
                    <script Language="JavaScript">
                        location.href = 'personal_mostrar.php?existe=true';
                    </script>
                <?php
                } else {
                    
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL REGISTRAR', 'PERSONAL', "Nombre: '$nom' | DNI: '$dni' - Error del sistema");
                ?>
                    <script Language="JavaScript">
                        location.href = 'personal_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            //  USAR FUNCIONES EXISTENTES EN LUGAR DE CREAR NUEVAS
            $areas = MostrarAreasActivas();
            $cargos = MostrarCargosActivos();

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