<!DOCTYPE html>

<head>
  <meta charset="UTF-8">
  <title>Login | TJ H2B</title>
  <link rel="stylesheet" href="../_complemento/css/estilo_login.css">
  <link rel="shortcut icon" href="../_complemento/images/icon.png" type="image/x-icon">
</head>

<body>
  <!-- partial:index.partial.html -->
  <?php
  //------------------------------------------------------------------------------
  //Llamar a MODELO
  //------------------------------------------------------------------------------
  ?>
  <hgroup>
    <!-- <h1>TJ H2B Latina</h1> -->
    <img src="../_complemento/images/logo.png" />
    <h2>Iniciar Sesión</h2>
  </hgroup>
  <form  method="post">
    <div class="group">
      <input type="text" name="usu" required="required"><span class="highlight"></span><span class="bar"></span>
      <label>Usuario</label>
    </div>
    <div class="group">
      <input type="password" name="pass" required="required"><span class="highlight"></span><span class="bar"></span>
      <label>Contraseña</label>
    </div>

    <span class="bar"></span>
    <span class="highlight"></span>
    </div>



    <button class="button buttonBlue" href="../_controlador/dashboard.php">Ingresar

      <div class="ripples buttonRipples"><span class="ripplesCircle"></span></div>
    </button>
  </form>
  <!--
<footer><img src="../_complemento/images/logo.png"></a>
  <p>Powered by <a href="https://www.alicorp.com.pe/pe/es/" target="_blank">Alicorp</a>
-->
  </footer>
  <!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
  <script src="../_complemento/js/script_login.js"></script>
  <?php require_once("../_vista/v_alertas.php"); ?>
</body>

</html>

<?php
require_once("mapa2.php");
?>