<?php
//=======================================================================
// MODELO: m_detraccion.php
//=======================================================================

function GrabarDetraccion($nombre, $cod_detraccion, $porcentaje, $id_detraccion_tipo) {
    include("../_conexion/conexion.php");
    
    // Escapar los datos
    $nombre_escaped = mysqli_real_escape_string($con, $nombre);
    $cod_detraccion_escaped = mysqli_real_escape_string($con, $cod_detraccion);

    // Validar que no exista un duplicado por nombre o código
    $sql_check = "SELECT id_detraccion FROM detraccion 
                  WHERE (nombre_detraccion = '$nombre_escaped' OR cod_detraccion = '$cod_detraccion_escaped')
                  AND est_detraccion = 1";
    $result = mysqli_query($con, $sql_check);
    
    if (mysqli_num_rows($result) > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar
    $id_detraccion_tipo = intval($id_detraccion_tipo);
    $porcentaje = floatval($porcentaje);
    
    $sql = "INSERT INTO detraccion ( nombre_detraccion, cod_detraccion, porcentaje, id_detraccion_tipo, est_detraccion) 
            VALUES ( '$nombre_escaped','$cod_detraccion_escaped', $porcentaje, $id_detraccion_tipo, 1)";
    
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

function EditarDetraccion($id_detraccion, $nombre, $cod_detraccion, $porcentaje, $estado, $id_detraccion_tipo) {
    include("../_conexion/conexion.php");
    
    $id_detraccion = intval($id_detraccion);
    $nombre_esc = mysqli_real_escape_string($con, trim($nombre));
    $cod_detraccion_esc = mysqli_real_escape_string($con, trim($cod_detraccion));
    $porcentaje = floatval($porcentaje);
    $estado = intval($estado);
    $id_detraccion_tipo = intval($id_detraccion_tipo);

    // Validar duplicados por nombre o código, excluyendo el registro actual
    $sql_check = "SELECT id_detraccion 
                  FROM detraccion
                  WHERE (UPPER(nombre_detraccion) = UPPER('$nombre_esc') 
                         OR UPPER(cod_detraccion) = UPPER('$cod_detraccion_esc'))
                    AND id_detraccion != $id_detraccion
                    AND est_detraccion = 1
                  LIMIT 1";

    $result = mysqli_query($con, $sql_check);
    if ($result && mysqli_num_rows($result) > 0) {
        mysqli_close($con);
        return "NO"; // duplicado
    }

    // Actualizar
    $sql = "UPDATE detraccion 
            SET nombre_detraccion = '$nombre_esc',
                cod_detraccion = '$cod_detraccion_esc',
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
