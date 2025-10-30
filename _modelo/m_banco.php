<?php
//=======================================================================
// FUNCIONES PARA BANCO
//=======================================================================

//-----------------------------------------------------------------------
// Mostrar todos los bancos
function MostrarBanco() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM banco ORDER BY nom_banco ASC";
    $resc = mysqli_query($con, $sqlc);
    $resultado = array();
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
// Registrar un nuevo banco
function GrabarBanco($cod, $nom, $est) {
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe el código o el nombre
    $sqlv = "SELECT * FROM banco WHERE cod_banco = '$cod' OR nom_banco = '$nom'";
    $resv = mysqli_query($con, $sqlv);
    
    if (mysqli_num_rows($resv) > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe
    }
    
    // Insertar nuevo registro
    $sqli = "INSERT INTO banco (cod_banco, nom_banco, est_banco) VALUES ('$cod', '$nom', $est)";
    $resi = mysqli_query($con, $sqli);
    
    mysqli_close($con);
    
    if ($resi) {
        return "SI";
    } else {
        return "NO";
    }
}

//-----------------------------------------------------------------------
// Obtener un banco específico por ID
function ObtenerBanco($id_banco)
{
    include("../_conexion/conexion.php");

    $sql = "SELECT * FROM banco WHERE id_banco = '$id_banco'";
    $resultado = mysqli_query($con, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $banco = mysqli_fetch_assoc($resultado);
        mysqli_close($con);
        return $banco;
    } else {
        mysqli_close($con);
        return false;
    }
}

//-----------------------------------------------------------------------
// Editar un banco existente
function EditarBanco($id_banco, $cod, $nom, $est) 
{
    include("../_conexion/conexion.php");
    
    // Verificar si ya existe otro banco con el mismo código o nombre (excluyendo el actual)
    $sql_verificar = "SELECT COUNT(*) as total FROM banco 
                      WHERE (cod_banco = '$cod' OR nom_banco = '$nom') 
                      AND id_banco != '$id_banco'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);
    
    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO"; // Ya existe otro banco con ese código o nombre
    }
    
    // Actualizar registro
    $sql = "UPDATE banco 
            SET cod_banco = '$cod', nom_banco = '$nom', est_banco = $est 
            WHERE id_banco = '$id_banco'";
    
    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
// Obtener o crear un banco de forma normalizada (evita duplicados)
function ObtenerOCrearBancoNormalizado($nom)
{
    include("../_conexion/conexion.php");

    // Normalizar el nombre
    $nombre = strtoupper(trim($nom));

    // Normalización de nombres comunes
    if (strpos($nombre, 'BCP') !== false || strpos($nombre, 'CREDITO') !== false) {
        $nom_normalizado = 'BANCO DE CREDITO DEL PERU';
        $cod = 'BCP';
    } elseif (strpos($nombre, 'NACION') !== false || strpos($nombre, 'BANCO NACION') !== false) {
        $nom_normalizado = 'BANCO DE LA NACION';
        $cod = 'BANCO DE LA NACION';
    } elseif (strpos($nombre, 'BBVA') !== false || strpos($nombre, 'CONTINENTAL') !== false) {
        $nom_normalizado = 'BANCO CONTINENTAL';
        $cod = 'BBVA';
    } elseif (strpos($nombre, 'INTERBANK') !== false || strpos($nombre, 'INTER') !== false) {
        $nom_normalizado = 'INTERBANK';
        $cod = 'INTERBANK';
    } elseif (strpos($nombre, 'SCOTIA') !== false) {
        $nom_normalizado = 'SCOTIABANK';
        $cod = 'SCOTIABANK';
    } else {
        // Si no coincide con ningún patrón conocido
        $nom_normalizado = $nombre;
        $cod = $nombre;
    }

    // Buscar si el banco ya existe
    $sql_buscar = "SELECT id_banco FROM banco 
                   WHERE UPPER(TRIM(cod_banco)) = '$cod' 
                      OR UPPER(TRIM(nom_banco)) = '$nom_normalizado'
                   LIMIT 1";
    $res_buscar = mysqli_query($con, $sql_buscar);

    if ($res_buscar && mysqli_num_rows($res_buscar) > 0) {
        // Ya existe → devolver ID existente
        $row = mysqli_fetch_assoc($res_buscar);
        mysqli_close($con);
        return $row['id_banco'];
    } else {
        // Insertar nuevo registro si no existe
        $sql_insertar = "INSERT INTO banco (cod_banco, nom_banco, est_banco)
                         VALUES ('$cod', '$nom_normalizado', 1)";
        $resultado = mysqli_query($con, $sql_insertar);

        if ($resultado) {
            $nuevoID = mysqli_insert_id($con);
            mysqli_close($con);
            return $nuevoID;
        } else {
            mysqli_close($con);
            return null;
        }
    }
}


?>