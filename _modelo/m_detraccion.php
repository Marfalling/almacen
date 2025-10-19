<?php
//=======================================================================
// MODELO: m_detraccion.php
//=======================================================================

function GrabarDetraccion($nombre, $porcentaje, $id_detraccion_tipo) {
    include("../_conexion/conexion.php");
    
    // Validar que no exista duplicado
    $nombre_escaped = mysqli_real_escape_string($con, $nombre);
    $sql_check = "SELECT id_detraccion FROM detraccion 
                  WHERE nombre_detraccion = '$nombre_escaped' 
                  AND est_detraccion = 1";
    $result = mysqli_query($con, $sql_check);
    
    if (mysqli_num_rows($result) > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar
    $id_detraccion_tipo = intval($id_detraccion_tipo);
    $porcentaje = floatval($porcentaje);
    
    $sql = "INSERT INTO detraccion (nombre_detraccion, porcentaje, id_detraccion_tipo, est_detraccion) 
            VALUES ('$nombre_escaped', $porcentaje, $id_detraccion_tipo, 1)";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

function ObtenerDetracciones() {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT d.*, dt.nom_detraccion_tipo 
            FROM detraccion d
            INNER JOIN detraccion_tipo dt ON d.id_detraccion_tipo = dt.id_detraccion_tipo
            WHERE d.est_detraccion = 1
            ORDER BY dt.nom_detraccion_tipo, d.nombre_detraccion";
    
    $resultado = mysqli_query($con, $sql);
    $detracciones = array();
    
    while ($row = mysqli_fetch_assoc($resultado)) {
        $detracciones[] = $row;
    }
    
    mysqli_close($con);
    return $detracciones;
}

function ObtenerDetraccionesPorTipo($tipo_nombre) {
    include("../_conexion/conexion.php");
    
    // Convertir nombre a mayúsculas para evitar problemas
    $tipo_nombre = strtoupper(mysqli_real_escape_string($con, $tipo_nombre));
    
    $sql = "SELECT d.* 
            FROM detraccion d
            INNER JOIN detraccion_tipo dt ON d.id_detraccion_tipo = dt.id_detraccion_tipo
            WHERE UPPER(dt.nom_detraccion_tipo) = '$tipo_nombre'
            AND d.est_detraccion = 1
            ORDER BY d.nombre_detraccion";
    
    $resultado = mysqli_query($con, $sql);
    $detracciones = array();
    
    while ($row = mysqli_fetch_assoc($resultado)) {
        $detracciones[] = $row;
    }
    
    mysqli_close($con);
    return $detracciones;
}

function ObtenerTiposDetraccion() {
    include("../_conexion/conexion.php");
    
    $sql = "SELECT * FROM detraccion_tipo 
            WHERE est_detraccion_tipo = 1
            ORDER BY id_detraccion_tipo";
    
    $resultado = mysqli_query($con, $sql);
    $tipos = array();
    
    while ($row = mysqli_fetch_assoc($resultado)) {
        $tipos[] = $row;
    }
    
    mysqli_close($con);
    return $tipos;
}

function ObtenerDetraccionPorId($id_detraccion) {
    include("../_conexion/conexion.php");
    
    $id_detraccion = intval($id_detraccion);
    $sql = "SELECT d.*, dt.nom_detraccion_tipo 
            FROM detraccion d
            INNER JOIN detraccion_tipo dt ON d.id_detraccion_tipo = dt.id_detraccion_tipo
            WHERE d.id_detraccion = $id_detraccion";
    
    $resultado = mysqli_query($con, $sql);
    $detraccion = mysqli_fetch_assoc($resultado);
    
    mysqli_close($con);
    return $detraccion;
}

function EditarDetraccion($id_detraccion, $nombre, $porcentaje, $estado, $id_detraccion_tipo) {
    include("../_conexion/conexion.php");
    
    $id_detraccion = intval($id_detraccion);
    $nombre_escaped = mysqli_real_escape_string($con, $nombre);
    $porcentaje = floatval($porcentaje);
    $estado = intval($estado);
    $id_detraccion_tipo = intval($id_detraccion_tipo);
    
    // Validar que no exista duplicado (excluyendo el registro actual)
    $sql_check = "SELECT id_detraccion FROM detraccion 
                  WHERE nombre_detraccion = '$nombre_escaped' 
                  AND id_detraccion != $id_detraccion
                  AND est_detraccion = 1";
    $result = mysqli_query($con, $sql_check);
    
    if (mysqli_num_rows($result) > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Actualizar
    $sql = "UPDATE detraccion 
            SET nombre_detraccion = '$nombre_escaped',
                porcentaje = $porcentaje,
                est_detraccion = $estado,
                id_detraccion_tipo = $id_detraccion_tipo
            WHERE id_detraccion = $id_detraccion";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

function CambiarEstadoDetraccion($id_detraccion, $nuevo_estado) {
    include("../_conexion/conexion.php");
    
    $id_detraccion = intval($id_detraccion);
    $nuevo_estado = intval($nuevo_estado);
    
    $sql = "UPDATE detraccion 
            SET est_detraccion = $nuevo_estado 
            WHERE id_detraccion = $id_detraccion";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}
?>