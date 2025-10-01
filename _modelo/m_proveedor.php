<?php
//-----------------------------------------------------------------------
// Insertar nuevo proveedor
//-----------------------------------------------------------------------
function GrabarProveedor($nom, $ruc, $dir, $tel, $cont, $est, $email) {
    include("../_conexion/conexion.php");

    $sql_verificar = "SELECT COUNT(*) as total FROM proveedor WHERE nom_proveedor = '$nom' OR ruc_proveedor = '$ruc'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);

    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO";
    }

    $sql = "INSERT INTO proveedor (nom_proveedor, ruc_proveedor, dir_proveedor, tel_proveedor, cont_proveedor, est_proveedor, mail_proveedor) 
            VALUES ('$nom', '$ruc', '$dir', '$tel', '$cont', $est, '$email')";

    if (mysqli_query($con, $sql)) {
        $id_proveedor = mysqli_insert_id($con);
        mysqli_close($con);
        return $id_proveedor;
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
function MostrarProveedores() {
    include("../_conexion/conexion.php");
    $sqlc = "SELECT * FROM proveedor ORDER BY nom_proveedor ASC";
    $resc = mysqli_query($con, $sqlc);

    $resultado = [];
    while ($rowc = mysqli_fetch_array($resc, MYSQLI_ASSOC)) {
        $resultado[] = $rowc;
    }
    mysqli_close($con);
    return $resultado;
}

//-----------------------------------------------------------------------
function ObtenerProveedor($id) {
    include("../_conexion/conexion.php");
    $sql = "SELECT * FROM proveedor WHERE id_proveedor = $id";
    $resultado = mysqli_query($con, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);
        mysqli_close($con);
        return $fila;
    } else {
        mysqli_close($con);
        return false;
    }
}

//-----------------------------------------------------------------------
function ActualizarProveedor($id, $nom, $ruc, $dir, $tel, $cont, $est, $email) {
    include("../_conexion/conexion.php");

    $sql_verificar = "SELECT COUNT(*) as total FROM proveedor 
                      WHERE (nom_proveedor = '$nom' OR ruc_proveedor = '$ruc') AND id_proveedor != $id";
    $resultado_verificar = mysqli_query($con, $sql_verificar);
    $fila = mysqli_fetch_assoc($resultado_verificar);

    if ($fila['total'] > 0) {
        mysqli_close($con);
        return "NO";
    }

    $sql = "UPDATE proveedor SET 
            nom_proveedor = '$nom',
            ruc_proveedor = '$ruc',
            dir_proveedor = '$dir',
            tel_proveedor = '$tel',
            cont_proveedor = '$cont',
            mail_proveedor = '$email',
            est_proveedor = $est
            WHERE id_proveedor = $id";

    if (mysqli_query($con, $sql)) {
        mysqli_close($con);
        return "SI";
    } else {
        mysqli_close($con);
        return "ERROR";
    }
}

//-----------------------------------------------------------------------
// Cuentas bancarias
//-----------------------------------------------------------------------
function ObtenerCuentasProveedor($id_proveedor) {
    include("../_conexion/conexion.php");
    $sql = "SELECT pc.*, m.nom_moneda 
            FROM proveedor_cuenta pc
            LEFT JOIN moneda m ON pc.id_moneda = m.id_moneda
            WHERE pc.id_proveedor = $id_proveedor";
    $resultado = mysqli_query($con, $sql);

    $cuentas = [];
    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
        $cuentas[] = $row;
    }
    mysqli_close($con);
    return $cuentas;
}

function EliminarCuentasProveedor($id_proveedor) {
    include("../_conexion/conexion.php");
    $sql = "DELETE FROM proveedor_cuenta WHERE id_proveedor = $id_proveedor";
    mysqli_query($con, $sql);
    mysqli_close($con);
}

function GrabarCuentaProveedor($id_proveedor, $banco, $id_moneda, $cta_corriente, $cta_interbancaria) {
    include("../_conexion/conexion.php");
    $sql = "INSERT INTO proveedor_cuenta (id_proveedor, banco_proveedor, id_moneda, nro_cuenta_corriente, nro_cuenta_interbancaria) 
            VALUES ($id_proveedor, '$banco', $id_moneda, '$cta_corriente', '$cta_interbancaria')";
    mysqli_query($con, $sql);
    mysqli_close($con);
}
?>
