<?php
require_once("../_conexion/sesion.php");

if (!verificarPermisoEspecifico('editar_obras')) {
    require_once("../_modelo/m_auditoria.php");
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'OBRAS', 'EDITAR');
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

	<title>Editar Obra</title>

	<?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
	<div class="container body">
		<div class="main_container">
			<?php
			require_once("../_vista/v_menu.php");
			require_once("../_vista/v_menu_user.php");

			require_once("../_modelo/m_obras.php");

			//-------------------------------------------
			if (isset($_REQUEST['registrar'])) {
				$id_obra = $_REQUEST['id_obra'];
				$nom = strtoupper($_REQUEST['nom']);
				$est = isset($_REQUEST['est']) ? 1 : 0;

				$rpta = ActualizarObra($id_obra, $nom, $est);

				if ($rpta == "SI") {
				?>
					<script Language="JavaScript">
						location.href = 'obras_mostrar.php?actualizado=true';
					</script>
				<?php
				} else if ($rpta == "NO") {
				?>
					<script Language="JavaScript">
						location.href = 'obras_mostrar.php?error=true';
					</script>
				<?php
				}
			}
			//-------------------------------------------

			// Obtener ID de la obra desde GET
			$id_obra = isset($_GET['id_obra']) ? $_GET['id_obra'] : '';
			if ($id_obra == "") {
			?>
				<script Language="JavaScript">
					location.href = 'dashboard.php?error=true';
				</script>
			<?php
				exit;
			}

			// Obtener datos de la obra a editar
			$obra = ConsultarObra($id_obra);
			if (count($obra) > 0) {
				foreach ($obra as $value) {
					$nom = $value['nom_obra'];
					$est = ($value['est_obra'] == 1) ? "checked" : "";
				}
			} else {
			?>
				<script Language="JavaScript">
					location.href = 'dashboard.php?error=true';
				</script>
			<?php
				exit;
			}

			require_once("../_vista/v_obras_editar.php");
			require_once("../_vista/v_footer.php");
			?>
		</div>
	</div>

	<?php require_once("../_vista/v_script.php"); ?>
</body>

</html>