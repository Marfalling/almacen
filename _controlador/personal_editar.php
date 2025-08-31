<?php

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Editar Personal</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");

            require_once("../_modelo/m_personal.php");

            //-------------------------------------------
            if (isset($_REQUEST['registrar'])) {
                $id_personal = $_REQUEST['id_personal'];
                $id_area = $_REQUEST['id_area'];
                $id_cargo = $_REQUEST['id_cargo'];
                $nom = strtoupper($_REQUEST['nom']);
                $ape = strtoupper($_REQUEST['ape']);
                $dni = $_REQUEST['dni'];
                $email = $_REQUEST['email'];
                $tel = $_REQUEST['tel'];
                $est = isset($_REQUEST['est']) ? 1 : 0;

                $rpta = EditarPersonal($id_personal, $id_area, $id_cargo, $nom, $ape, $dni, $email, $tel, $est);

                if ($rpta == "SI") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'personal_mostrar.php?actualizado=true';
                    </script>
                <?php
                } else if ($rpta == "NO") {
                ?>
                    <script Language="JavaScript">
                        location.href = 'personal_mostrar.php?error=true';
                    </script>
                <?php
                }
            }
            //-------------------------------------------

            // Obtener ID del personal desde GET
            $id_personal = isset($_GET['id_personal']) ? $_GET['id_personal'] : '';
            if ($id_personal == "") {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            // Obtener datos del personal a editar
            $personal_data = ObtenerPersonal($id_personal);
            if ($personal_data) {
                $id_area = $personal_data['id_area'];
                $id_cargo = $personal_data['id_cargo'];
                $nom = $personal_data['nom_personal'];
                $ape = $personal_data['ape_personal'];
                $dni = $personal_data['dni_personal'];
                $email = $personal_data['email_personal'];
                $tel = $personal_data['tel_personal'];
                $est = ($personal_data['est_personal'] == 1) ? "checked" : "";
            } else {
            ?>
                <script Language="JavaScript">
                    location.href = 'dashboard.php?error=true';
                </script>
            <?php
                exit;
            }

            require_once("../_vista/v_personal_editar.php");
            require_once("../_vista/v_footer.php");
            ?>
        </div>
    </div>

    <?php require_once("../_vista/v_script.php"); ?>
</body>

</html>