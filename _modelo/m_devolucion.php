<?php
//=======================================================================
// MODELO: m_devolucion.php
//=======================================================================

function GrabarDevolucion($id_almacen, $id_ubicacion, $id_personal, $obs_devolucion, $materiales) 
{
    include("../_conexion/conexion.php");

    // Insertar devolución principal
    $sql = "INSERT INTO devolucion (
                id_almacen, id_ubicacion, id_personal, 
                obs_devolucion, fec_devolucion, est_devolucion
            ) VALUES (
                $id_almacen, $id_ubicacion, $id_personal, 
                '$obs_devolucion', NOW(), 1
            )";

    if (mysqli_query($con, $sql)) {
        $id_devolucion = mysqli_insert_id($con);
        
        // Insertar detalles y generar movimientos
        foreach ($materiales as $material) {
            $id_producto = intval($material['id_producto']);
            $cantidad = floatval($material['cantidad']);
            $detalle = mysqli_real_escape_string($con, $material['detalle']);

            // Insertar detalle de devolución
            $sql_detalle = "INSERT INTO devolucion_detalle (
                                id_devolucion, id_producto, cant_devolucion_detalle, 
                                det_devolucion_detalle, est_devolucion_detalle
                            ) VALUES (
                                $id_devolucion, $id_producto, $cantidad, 
                                '$detalle', 1
                            )";
            
            if (mysqli_query($con, $sql_detalle)) {
                // Movimiento de SALIDA (resta stock)
                $sql_mov = "INSERT INTO movimiento (
                                id_personal, id_orden, id_producto, id_almacen, 
                                id_ubicacion, tipo_orden, tipo_movimiento, 
                                cant_movimiento, fec_movimiento, est_movimiento
                            ) VALUES (
                                $id_personal, $id_devolucion, $id_producto, $id_almacen, 
                                $id_ubicacion, 3, 2, 
                                $cantidad, NOW(), 1
                            )";
                mysqli_query($con, $sql_mov);
            }
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        $error = mysqli_error($con);
        mysqli_close($con);
        return "ERROR: " . $error;
    }
}

//-----------------------------------------------------------------------
function MostrarDevoluciones()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT d.*, 
                   a.nom_almacen, 
                   u.nom_ubicacion, 
                   p.nom_personal, p.ape_personal
            FROM devolucion d
            INNER JOIN almacen a ON d.id_almacen = a.id_almacen
            INNER JOIN ubicacion u ON d.id_ubicacion = u.id_ubicacion
            INNER JOIN personal p ON d.id_personal = p.id_personal
            WHERE d.est_devolucion = 1
            ORDER BY d.fec_devolucion DESC";

    $res = mysqli_query($con, $sql);
    $resultado = array();

    while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function ConsultarDevolucion($id_devolucion)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT d.*, 
                   a.nom_almacen, 
                   u.nom_ubicacion, 
                   p.nom_personal, p.ape_personal
            FROM devolucion d
            INNER JOIN almacen a ON d.id_almacen = a.id_almacen
            INNER JOIN ubicacion u ON d.id_ubicacion = u.id_ubicacion
            INNER JOIN personal p ON d.id_personal = p.id_personal
            WHERE d.id_devolucion = $id_devolucion";

    $res = mysqli_query($con, $sql);
    $resultado = array();

    while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function ConsultarDevolucionDetalle($id_devolucion)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT dd.*, 
                   pr.nom_producto, 
                   um.nom_unidad_medida
            FROM devolucion_detalle dd
            INNER JOIN producto pr ON dd.id_producto = pr.id_producto
            INNER JOIN unidad_medida um ON pr.id_unidad_medida = um.id_unidad_medida
            WHERE dd.id_devolucion = $id_devolucion 
              AND dd.est_devolucion_detalle = 1
            ORDER BY dd.id_devolucion_detalle";

    $res = mysqli_query($con, $sql);
    $resultado = array();

    while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------

function MostrarMaterialesActivos() {
    global $con;
    $sql = "SELECT * FROM producto 
            WHERE est_producto = 1 
              AND id_producto_tipo = 1";
    $result = mysqli_query($con, $sql);

    $materiales = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $materiales[] = $row;
    }
    return $materiales;
}



?>