<?php

function generarColor()
{
  return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Dashboard</title>

  <?php require_once("../_vista/v_estilo.php"); ?>
</head>

<body class="nav-md">
  <div class="container body">
    <div class="main_container">
      <?php
      require_once("../_vista/v_menu.php");
      require_once("../_vista/v_menu_user.php");

      require_once("../_modelo/m_dashboard.php");
     

      require_once("../_vista/v_dashboard.php");

      require_once("../_vista/v_footer.php");
      ?>

    </div>
  </div>

  <script>

  </script>

  <?php
  require_once("../_vista/v_script.php");
  require_once("../_vista/v_alertas.php");
  ?>

  <script src="../_complemento/vendors/raphael/raphael.min.js"></script>
  <script src="../_complemento/vendors/morris.js/morris.min.js"></script>
</body>

</html>