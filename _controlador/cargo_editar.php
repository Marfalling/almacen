<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('editar_cargo')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'CARGO', 'EDITAR');
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

	<title>Editar Cargo</title>

	<?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
	<div class="container body">
		<div class="main_container">
			<?php
			require_once("../_vista/v_menu.php");
			require_once("../_vista/v_menu_user.php");

			require_once("../_modelo/m_cargo.php");

			//-------------------------------------------
			if (isset($_REQUEST['registrar'])) {
				$id_cargo = $_REQUEST['id_cargo'];
				$nom = strtoupper($_REQUEST['nom']);
				$est = isset($_REQUEST['est']) ? 1 : 0;

				$rpta = EditarCargo($id_cargo, $nom, $est);

				if ($rpta == "SI") {
				?>
					<script Language="JavaScript">
						location.href = 'cargo_mostrar.php?actualizado=true';
					</script>
				<?php
				} else if ($rpta == "NO") {
				?>
					<script Language="JavaScript">
						location.href = 'cargo_mostrar.php?error=true';
					</script>
				<?php
				}
			}
			//-------------------------------------------

			// Obtener ID del cargo desde GET
			$id_cargo = isset($_GET['id_cargo']) ? $_GET['id_cargo'] : '';
			if ($id_cargo == "") {
			?>
				<script Language="JavaScript">
					location.href = 'dashboard.php?error=true';
				</script>
			<?php
				exit;
			}

			// Obtener datos del cargo a editar
			$cargo_data = ObtenerCargo($id_cargo);
			if ($cargo_data) {
				$nom = $cargo_data['nom_cargo'];
				$est = ($cargo_data['est_cargo'] == 1) ? "checked" : "";
			} else {
			?>
				<script Language="JavaScript">
					location.href = 'dashboard.php?error=true';
				</script>
			<?php
				exit;
			}

			require_once("../_vista/v_cargo_editar.php");
			require_once("../_vista/v_footer.php");
			?>
		</div>
	</div>

	<?php require_once("../_vista/v_script.php"); ?>
</body>

</html>