<?php 
function AutentificarUsuario($user,$pass)
{
    require_once("../_conexion/conexion.php");

    $sqlc = "SELECT 
                u.id_usuario,
                u.usu_usuario,
                u.id_personal,
                p.nom_personal
            FROM 
                usuario u
            INNER JOIN personal p ON u.id_personal = p.id_personal
            WHERE 
                BINARY u.usu_usuario = '$user' AND
                BINARY u.con_usuario = '$pass' AND
                u.est_usuario = 1 AND
                p.act_personal = 1
    ";
    $resc=mysqli_query($con,$sqlc);

    $resultado = array();
    
    while($rowc=mysqli_fetch_array($resc, MYSQLI_ASSOC))
    {
        $resultado[] = $rowc;        
    }
        
    mysqli_close($con);

    return $resultado;
}
?>