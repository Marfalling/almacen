<?php
//-----------------------------------------------------------------------
// FUNCIÓN: Mostrar registros de auditoría por rango de fechas
//-----------------------------------------------------------------------
function MostrarAuditoria($fecha_inicio, $fecha_fin)
{
    include("../_conexion/conexion.php");

    // Preparar consulta con parámetros
    $sql = "SELECT * FROM auditoria 
            WHERE DATE(fecha) BETWEEN ? AND ? 
            ORDER BY fecha DESC";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $fecha_inicio, $fecha_fin);
    mysqli_stmt_execute($stmt);
    $resultado_query = mysqli_stmt_get_result($stmt);

    $resultado = array();
    while ($row = mysqli_fetch_array($resultado_query, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    return $resultado;
}

//-----------------------------------------------------------------------
// FUNCIÓN: Grabar evento en la auditoría
//-----------------------------------------------------------------------
function GrabarAuditoria($id_usuario, $nom_usuario, $accion, $modulo, $descripcion)
{
    include("../_conexion/conexion.php");

    date_default_timezone_set('America/Lima');
    $fecha_actual = date("Y-m-d H:i:s");

    // Preparar consulta con parámetros para evitar SQL injection
    $sql = "INSERT INTO auditoria (id_usuario, nom_usuario, accion, modulo, descripcion, fecha) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "isssss", $id_usuario, $nom_usuario, $accion, $modulo, $descripcion, $fecha_actual);
    
    if (mysqli_stmt_execute($stmt)) {
        $rpta = "SI";
    } else {
        $rpta = "NO";
        // Log del error (opcional)
        error_log("Error en auditoría: " . mysqli_error($con));
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    
    return $rpta;
}
?>