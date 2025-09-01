<?php
require_once("../_conexion/sesion.php");

?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Editar Almacén</title>

	<?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
	<div class="container body">
		<div class="main_container">
			<?php
			require_once("../_vista/v_menu.php");
			require_once("../_vista/v_menu_user.php");

			require_once("../_modelo/m_almacen.php");
			require_once("../_modelo/m_clientes.php");
			require_once("../_modelo/m_obras.php");

			//-------------------------------------------
			if (isset($_REQUEST['registrar'])) {
				$id_almacen = $_REQUEST['id_almacen'];
				$id_cliente = $_REQUEST['id_cliente'];
				$id_obra = $_REQUEST['id_obra'];
				$nom = strtoupper($_REQUEST['nom']);
				$est = isset($_REQUEST['est']) ? 1 : 0;

				$rpta = ActualizarAlmacen($id_almacen, $id_cliente, $id_obra, $nom, $est);

				if ($rpta == "SI") {
				?>
					<script Language="JavaScript">
						location.href = 'almacen_mostrar.php?actualizado=true';
					</script>
				<?php
				} else if ($rpta == "NO") {
				?>
					<script Language="JavaScript">
						location.href = 'almacen_mostrar.php?error=true';
					</script>
				<?php
				}
			}
			//-------------------------------------------

			// Obtener ID del almacén desde GET
			$id_almacen = isset($_GET['id_almacen']) ? $_GET['id_almacen'] : '';
			if ($id_almacen == "") {
			?>
				<script Language="JavaScript">
					location.href = 'dashboard.php?error=true';
				</script>
			<?php
				exit;
			}

			// Obtener datos del almacén a editar
			$almacen = ConsultarAlmacen($id_almacen);
			if (count($almacen) > 0) {
				foreach ($almacen as $value) {
					$id_cliente = $value['id_cliente'];
					$id_obra = $value['id_obra'];
					$nom = $value['nom_almacen'];
					$est = ($value['est_almacen'] == 1) ? "checked" : "";
				}
			} else {
			?>
				<script Language="JavaScript">
					location.href = 'dashboard.php?error=true';
				</script>
			<?php
				exit;
			}

			// Obtener listas para los selects (solo activos)
			$listaClientes = MostrarClientesActivos();
			$listaObras = MostrarObrasActivas();

			require_once("../_vista/v_almacen_editar.php");
			require_once("../_vista/v_footer.php");
			?>
		</div>
	</div>

	<?php require_once("../_vista/v_script.php"); ?>
</body>

</html>