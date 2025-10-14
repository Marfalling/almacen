<?php
function GrabarModulo($nom_modulo, $acciones, $est) 
{
    include("../_conexion/conexion.php");
    
    // Sanitizar el nombre del módulo
    $nom_modulo = mysqli_real_escape_string($con, $nom_modulo);
    $est = (int)$est;
    
    // Verificar si ya existe un módulo con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM modulo WHERE nom_modulo = '$nom_modulo'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    
    if (!$resultado_verificar) {
        mysqli_close($con);
        return "ERROR";
    }
    
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Verificar que se hayan seleccionado acciones
    if (empty($acciones) || !is_array($acciones)) {
        mysqli_close($con);
        return "SIN_ACCIONES";
    }
    
    // Insertar nuevo módulo
    $sql = "INSERT INTO modulo (nom_modulo, est_modulo) VALUES ('$nom_modulo', $est)";
    
    if (mysqli_query($con, $sql)) {
        $id_modulo = mysqli_insert_id($con);
        
        // Crear relaciones modulo_accion para las acciones seleccionadas
        foreach ($acciones as $id_accion) {
            $id_accion = (int)$id_accion; // Sanitizar
            $sql_modulo_accion = "INSERT INTO modulo_accion (id_modulo, id_accion, est_modulo_accion) 
                                 VALUES ($id_modulo, $id_accion, 1)";
            $resultado_ma = mysqli_query($con, $sql_modulo_accion);
            
            if (!$resultado_ma) {
                error_log("Error creando relación módulo-acción: " . mysqli_error($con));
            }
        }
        
        mysqli_close($con);
        return "SI";
    } else {
        error_log("Error insertando módulo: " . mysqli_error($con));
        mysqli_close($con);
        return "ERROR";
    }
}

function MostrarAcciones()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM accion WHERE est_accion = 1 ORDER BY nom_accion";
    $result = mysqli_query($con, $sql);

    $resultado = array();

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarModulos()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT m.*, 
                   COUNT(ma.id_modulo_accion) as total_acciones
            FROM modulo m 
            LEFT JOIN modulo_accion ma ON m.id_modulo = ma.id_modulo AND ma.est_modulo_accion = 1
            GROUP BY m.id_modulo
            ORDER BY m.nom_modulo ASC";
    
    $result = mysqli_query($con, $sql);

    if (!$result) {
        error_log("Error en MostrarModulos(): " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }

    $resultado = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function MostrarModulosActivos()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM modulo WHERE est_modulo = 1 ORDER BY nom_modulo ASC";
    $result = mysqli_query($con, $sql);

    if (!$result) {
        error_log("Error en MostrarModulosActivos(): " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }

    $resultado = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }

    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function ObtenerModulo($id_modulo)
{
    include("../_conexion/conexion.php");

    $id_modulo = (int)$id_modulo;
    $sql = "SELECT * FROM modulo WHERE id_modulo = $id_modulo";
    $result = mysqli_query($con, $sql);
    
    if (!$result) {
        error_log("Error en ObtenerModulo(): " . mysqli_error($con));
        mysqli_close($con);
        return null;
    }
    
    $modulo = mysqli_fetch_assoc($result);
    
    mysqli_close($con);
    return $modulo;
}

//-----------------------------------------------------------------------
function EditarModulo($id_modulo, $nom_modulo, $est)
{
    include("../_conexion/conexion.php");
    
    // Sanitizar datos
    $id_modulo = (int)$id_modulo;
    $nom_modulo = mysqli_real_escape_string($con, $nom_modulo);
    $est = (int)$est;
    
    // Verificar si ya existe otro módulo con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM modulo WHERE nom_modulo = '$nom_modulo' AND id_modulo != $id_modulo";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    
    if (!$resultado_verificar) {
        error_log("Error verificando módulo existente: " . mysqli_error($con));
        mysqli_close($con);
        return "ERROR";
    }
    
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Actualizar módulo
    $sql = "UPDATE modulo SET nom_modulo = '$nom_modulo', est_modulo = $est WHERE id_modulo = $id_modulo";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        error_log("Error actualizando módulo: " . mysqli_error($con));
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function EliminarModulo($id_modulo)
{
    include("../_conexion/conexion.php");
    
    $id_modulo = (int)$id_modulo;
    
    // Verificar si el módulo tiene acciones asignadas
    $sql_verificar = "SELECT COUNT(*) as total FROM modulo_accion WHERE id_modulo = $id_modulo AND est_modulo_accion = 1";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "ACCIONES_ASIGNADAS";
    }
    
    // Cambiar estado a inactivo (eliminación lógica)
    $sql = "UPDATE modulo SET est_modulo = 0 WHERE id_modulo = $id_modulo";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        error_log("Error eliminando módulo: " . mysqli_error($con));
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function ContarModulosActivos()
{
    include("../_conexion/conexion.php");

    $sql = "SELECT COUNT(*) as total FROM modulo WHERE est_modulo = 1";
    $result = mysqli_query($con, $sql);
    
    if (!$result) {
        mysqli_close($con);
        return 0;
    }
    
    $row = mysqli_fetch_assoc($result);
    mysqli_close($con);
    
    return $row['total'];
}

//-----------------------------------------------------------------------
function ObtenerAccionesModulo($id_modulo)
{
    include("../_conexion/conexion.php");
    
    $id_modulo = (int)$id_modulo;
    $sql = "SELECT ma.*, a.nom_accion 
            FROM modulo_accion ma 
            INNER JOIN accion a ON ma.id_accion = a.id_accion 
            WHERE ma.id_modulo = $id_modulo AND ma.est_modulo_accion = 1";
    
    $result = mysqli_query($con, $sql);
    
    if (!$result) {
        error_log("Error en ObtenerAccionesModulo(): " . mysqli_error($con));
        mysqli_close($con);
        return array();
    }
    
    $resultado = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $resultado[] = $row;
    }
    
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function ActualizarModuloCompleto($id_modulo, $nom_modulo, $acciones, $est)
{
    include("../_conexion/conexion.php");
    
    // Sanitizar datos
    $id_modulo = (int)$id_modulo;
    $nom_modulo = mysqli_real_escape_string($con, $nom_modulo);
    $est = (int)$est;
    
    // Verificar si ya existe otro módulo con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM modulo WHERE nom_modulo = '$nom_modulo' AND id_modulo != $id_modulo";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    
    if (!$resultado_verificar) {
        error_log("Error verificando módulo existente: " . mysqli_error($con));
        mysqli_close($con);
        return "ERROR";
    }
    
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Verificar que se hayan seleccionado acciones
    if (empty($acciones) || !is_array($acciones)) {
        mysqli_close($con);
        return "SIN_ACCIONES";
    }
    
    // Iniciar transacción
    mysqli_autocommit($con, FALSE);
    
    try {
        // 1. Actualizar datos básicos del módulo
        $sql_modulo = "UPDATE modulo SET nom_modulo = '$nom_modulo', est_modulo = $est WHERE id_modulo = $id_modulo";
        
        if (!mysqli_query($con, $sql_modulo)) {
            throw new Exception("Error actualizando módulo: " . mysqli_error($con));
        }
        
        // 2. Desactivar todas las relaciones modulo_accion existentes
        $sql_desactivar = "UPDATE modulo_accion SET est_modulo_accion = 0 WHERE id_modulo = $id_modulo";
        
        if (!mysqli_query($con, $sql_desactivar)) {
            throw new Exception("Error desactivando acciones: " . mysqli_error($con));
        }
        
        // 3. Activar o crear las nuevas relaciones modulo_accion
        foreach ($acciones as $id_accion) {
            $id_accion = (int)$id_accion;
            
            // Verificar si ya existe la relación
            $sql_existe = "SELECT id_modulo_accion FROM modulo_accion WHERE id_modulo = $id_modulo AND id_accion = $id_accion";
            $resultado_existe = mysqli_query($con, $sql_existe);
            
            if (mysqli_num_rows($resultado_existe) > 0) {
                // Si existe, solo activarla
                $sql_activar = "UPDATE modulo_accion SET est_modulo_accion = 1 WHERE id_modulo = $id_modulo AND id_accion = $id_accion";
                if (!mysqli_query($con, $sql_activar)) {
                    throw new Exception("Error activando relación módulo-acción: " . mysqli_error($con));
                }
            } else {
                // Si no existe, crearla
                $sql_crear = "INSERT INTO modulo_accion (id_modulo, id_accion, est_modulo_accion) VALUES ($id_modulo, $id_accion, 1)";
                if (!mysqli_query($con, $sql_crear)) {
                    throw new Exception("Error creando relación módulo-acción: " . mysqli_error($con));
                }
            }
        }
        
        // Confirmar transacción
        mysqli_commit($con);
        mysqli_close($con);
        return "SI";
        
    } catch (Exception $e) {
        // Revertir transacción
        mysqli_rollback($con);
        error_log("Error en ActualizarModuloCompleto(): " . $e->getMessage());
        mysqli_close($con);
        return "ERROR";
    }
}