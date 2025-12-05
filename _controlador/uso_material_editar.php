<?php
//=======================================================================
// uso_material_editar.php - CONTROLADOR CON AUDITORÍA
//=======================================================================
require_once("../_conexion/sesion.php");
require_once("../_modelo/m_auditoria.php");

// VERIFICACIÓN DE PERMISOS
if (!verificarPermisoEspecifico('editar_uso de material')) {
    GrabarAuditoria($id, $usuario_sesion, 'ERROR DE ACCESO', 'USO_MATERIAL', 'EDITAR');
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

    <title>Editar Uso de Material</title>

    <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <?php
            require_once("../_vista/v_menu.php");
            require_once("../_vista/v_menu_user.php");
            require_once("../_modelo/m_producto.php");
            require_once("../_modelo/m_uso_material.php");
            require_once("../_modelo/m_almacen.php");
            require_once("../_modelo/m_ubicacion.php");
            require_once("../_modelo/m_personal.php");
            
            // Cargar datos necesarios para el formulario
            $ubicaciones = MostrarUbicacionesActivas();
            $personal = MostrarPersonal();
            
            // Crear directorio de archivos si no existe
            if (!file_exists("../_archivos/uso_material/")) {
                mkdir("../_archivos/uso_material/", 0777, true);
            }

            $id_uso_material = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

            //=======================================================================
            // CONTROLADOR
            //=======================================================================
            if (isset($_REQUEST['actualizar'])) {
                $id_ubicacion = intval($_REQUEST['id_ubicacion']);
                $id_solicitante = intval($_REQUEST['id_solicitante']);

                //  OBTENER DATOS ANTES DE EDITAR
                $uso_actual = ConsultarUsoMaterial($id_uso_material);
                $detalles_actuales = ConsultarUsoMaterialDetalle($id_uso_material);
                
                $ubicacion_anterior = !empty($uso_actual) ? $uso_actual[0]['id_ubicacion'] : 0;
                $solicitante_anterior = !empty($uso_actual) ? $uso_actual[0]['id_solicitante'] : 0;

                // Procesar materiales
                $materiales = array();
                if (isset($_REQUEST['id_producto']) && is_array($_REQUEST['id_producto'])) {
                    for ($i = 0; $i < count($_REQUEST['id_producto']); $i++) {
                        if (!empty($_REQUEST['id_producto'][$i])) {
                            $materiales[] = array(
                                'id_producto' => $_REQUEST['id_producto'][$i],
                                'cantidad' => $_REQUEST['cantidad'][$i],
                                'observaciones' => $_REQUEST['observaciones'][$i],
                                'id_detalle' => isset($_REQUEST['id_detalle'][$i]) ? $_REQUEST['id_detalle'][$i] : 0
                            );
                        }
                    }
                }

                // Procesar archivos
                $archivos_subidos = array();
                foreach ($_FILES as $key => $file) {
                    if (strpos($key, 'archivos_') === 0 && !empty($file['name'][0])) {
                        $index = str_replace('archivos_', '', $key);
                        $archivos_subidos[$index] = $file;
                    }
                }

                // Validar que hay materiales
                if (empty($materiales)) {
                    //  AUDITORÍA: ERROR - SIN MATERIALES
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'USO_MATERIAL', "ID: $id_uso_material | Sin materiales agregados");
                    ?>
                    <script Language="JavaScript">
                        alert('Debe agregar al menos un material.');
                    </script>
                    <?php
                } else {
                    //  EJECUTAR ACTUALIZACIÓN
                    $rpta = ActualizarUsoMaterial($id_uso_material, $id_ubicacion, $id_solicitante, 
                                                $materiales, $archivos_subidos);

                    if ($rpta == "SI") {
                        //  COMPARAR Y CONSTRUIR DESCRIPCIÓN
                        $cambios = [];

                        // Comparar ubicación
                        if ($ubicacion_anterior != $id_ubicacion) {
                            $ubi_ant_data = ObtenerUbicacion($ubicacion_anterior);
                            $ubi_nva_data = ObtenerUbicacion($id_ubicacion);
                            $nom_ubi_ant = $ubi_ant_data ? $ubi_ant_data['nom_ubicacion'] : '';
                            $nom_ubi_nva = $ubi_nva_data ? $ubi_nva_data['nom_ubicacion'] : '';
                            $cambios[] = "Ubicación: '$nom_ubi_ant' → '$nom_ubi_nva'";
                        }

                        // Comparar solicitante
                        if ($solicitante_anterior != $id_solicitante) {
                            $sol_ant_data = ObtenerPersonal($solicitante_anterior);
                            $sol_nvo_data = ObtenerPersonal($id_solicitante);
                            $nom_sol_ant = $sol_ant_data ? $sol_ant_data['nom_personal'] : '';
                            $nom_sol_nvo = $sol_nvo_data ? $sol_nvo_data['nom_personal'] : '';
                            $cambios[] = "Solicitante: '$nom_sol_ant' → '$nom_sol_nvo'";
                        }

                        // 🔹 COMPARAR MATERIALES (DETALLADO)
                        $cambios_materiales = [];

                        // Crear índice de materiales anteriores
                        $materiales_antes_index = [];
                        foreach ($detalles_actuales as $det) {
                            $id_prod = intval($det['id_producto']);
                            $materiales_antes_index[$id_prod] = [
                                'descripcion' => $det['nom_producto'],
                                'cantidad' => floatval($det['cant_uso_material_detalle'])
                            ];
                        }

                        // Comparar con materiales nuevos
                        foreach ($materiales as $mat) {
                            $id_prod = intval($mat['id_producto']);
                            
                            
                            $producto_data = ObtenerProductoPorId($id_prod);
                            $nom_producto = $producto_data ? $producto_data['nom_producto'] : "ID:$id_prod";
                            
                            $desc_corta = (strlen($nom_producto) > 30) 
                                ? substr($nom_producto, 0, 30) . '...' 
                                : $nom_producto;
                            
                            if (isset($materiales_antes_index[$id_prod])) {
                                // Producto existente - comparar cantidad
                                $cantidad_anterior = $materiales_antes_index[$id_prod]['cantidad'];
                                $cantidad_nueva = floatval($mat['cantidad']);
                                
                                if ($cantidad_anterior != $cantidad_nueva) {
                                    $cambios_materiales[] = "$desc_corta: $cantidad_anterior → $cantidad_nueva";
                                }
                                
                                unset($materiales_antes_index[$id_prod]);
                            } else {
                                // Producto nuevo
                                $cambios_materiales[] = "NUEVO: $desc_corta (Cant: {$mat['cantidad']})";
                            }
                        }

                        // Detectar productos eliminados
                        foreach ($materiales_antes_index as $id_prod => $datos) {
                            $desc_corta = (strlen($datos['descripcion']) > 30) 
                                ? substr($datos['descripcion'], 0, 30) . '...' 
                                : $datos['descripcion'];
                            $cambios_materiales[] = "ELIMINADO: $desc_corta (Cant: {$datos['cantidad']})";
                        }

                        // Agregar cambios de materiales
                        if (!empty($cambios_materiales)) {
                            $cambios[] = "Materiales: " . implode(' | ', $cambios_materiales);
                        }

                        if (empty($cambios)) {
                            $descripcion = "ID: $id_uso_material | Sin cambios";
                        } else {
                            $descripcion = "ID: $id_uso_material | " . implode(' | ', $cambios);
                        }

                        GrabarAuditoria($id, $usuario_sesion, 'EDITAR', 'USO_MATERIAL', $descripcion);
                        ?>
                        <script Language="JavaScript">
                            location.href = 'uso_material_mostrar.php?actualizado=true';
                        </script>
                        <?php
                    } else {
                        //  AUDITORÍA: ERROR AL EDITAR
                        GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'USO_MATERIAL', "ID: $id_uso_material | Error: " . $rpta);
                        ?>
                        <script Language="JavaScript">
                            alert('Error al actualizar el uso de material: <?php echo addslashes($rpta); ?>');
                        </script>
                        <?php
                    }
                }
            }
            //-------------------------------------------

            if ($id_uso_material > 0) {
                // Cargar datos del uso de material
                $uso_material_data = ConsultarUsoMaterial($id_uso_material);
                $uso_material_detalle = ConsultarUsoMaterialDetalle($id_uso_material);
                
                if (!empty($uso_material_data)) {
                    require_once("../_vista/v_uso_material_editar.php");
                } else {
                    //  AUDITORÍA: USO DE MATERIAL NO ENCONTRADO
                    GrabarAuditoria($id, $usuario_sesion, 'ERROR AL EDITAR', 'USO_MATERIAL', "ID: $id_uso_material | No encontrado");
                    echo "<script>alert('Uso de material no encontrado'); location.href='uso_material_mostrar.php';</script>";
                }
            } else {
                echo "<script>alert('ID de uso de material no válido'); location.href='uso_material_mostrar.php';</script>";
            }

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