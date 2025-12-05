<?php
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php"); 

if (!verificarPermisoEspecifico('editar_area')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'AREA', 'EDITAR');
    header("location: bienvenido.php?permisos=true");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Editar Area</title>

	<?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
	<div class="container body">
		<div class="main_container">
			<?php
			require_once("../_vista/v_menu.php");
			require_once("../_vista/v_menu_user.php");

			require_once("../_modelo/m_area.php");

			//-------------------------------------------
			// OPERACIÓN DE EDICIÓN
			//-------------------------------------------
			if (isset($_REQUEST['registrar'])) {
				$id_area = $_REQUEST['id_area'];
				$nom = strtoupper($_REQUEST['nom']); 
				$est = isset($_REQUEST['est']) ? 1 : 0;

				$rpta = EditarArea($id_area, $nom, $est);

				if ($rpta == "SI") {
					//  AUDITORÍA: EDICIÓN EXITOSA
					GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'AREA', "ID: $id_area - $nom");
				?>
					<script Language="JavaScript">
						location.href = 'area_mostrar.php?actualizado=true';
					</script>
				<?php
				} else if ($rpta == "NO") {
					//  AUDITORÍA: ERROR AL EDITAR
					GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'AREA', "ID: $id_area - $nom");
				?>
					<script Language="JavaScript">
						location.href = 'area_mostrar.php?error=true';
					</script>
				<?php
				}
			}
			//-------------------------------------------

			// Obtener ID del área desde GET
			$id_area = isset($_GET['id_area']) ? $_GET['id_area'] : '';
			if ($id_area == "") {
			?>
				<script Language="JavaScript">
					location.href = 'dashboard.php?error=true';
				</script>
			<?php
				exit;
			}

			// Obtener datos del área a editar
			$area_data = ObtenerArea($id_area);
			if ($area_data) {
				$nom = $area_data[0]['nom_area'];
				$est = ($area_data[0]['act_area'] == 1) ? "checked" : "";
			} else {
			?>
				<script Language="JavaScript">
					//location.href = 'dashboard.php?error=true'; 
				</script>
			<?php
				exit;
			}

			require_once("../_vista/v_area_editar.php");
			require_once("../_vista/v_footer.php");
			?>
		</div>
	</div>

	<?php require_once("../_vista/v_script.php"); ?>
</body>

</html>