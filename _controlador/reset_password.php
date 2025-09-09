<?php
// reset_password.php - Vista completa para cambio de contraseña
require_once("../_conexion/sesion.php");
require_once("../_conexion/conexion.php");
require_once("../_modelo/m_auditoria.php");

// Obtener ID del usuario a cambiar contraseña
$id_cambio = isset($_GET['id']) ? (int)$_GET['id'] : $id;
$e = isset($_GET['e']) ? $_GET['e'] : '';

// Validar que solo el propio usuario pueda cambiar su contraseña
// (o usuarios con permisos especiales)
if ($id_cambio != $id && !verificarPermisoEspecifico('editar_usuarios')) {
    header("location: bienvenido.php?permisos=true");
    exit;
}

// Obtener datos del usuario
$sql_usuario = "SELECT 
                    u.usu_usuario,
                    CONCAT(p.nom_personal, ' ', p.ape_personal) as nombre_completo
                FROM usuario u 
                INNER JOIN personal p ON u.id_personal = p.id_personal 
                WHERE u.id_usuario = ?";
$stmt = mysqli_prepare($con, $sql_usuario);
mysqli_stmt_bind_param($stmt, "i", $id_cambio);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario_data = mysqli_fetch_array($resultado);

if (!$usuario_data) {
    header("location: bienvenido.php");
    exit;
}

$nombre = $usuario_data['nombre_completo'];
$usuario = $usuario_data['usu_usuario'];

// Variables para mensajes
$mensaje_error = '';
$mensaje_exito = '';

// Procesar formulario
if (isset($_POST['registrar'])) {
    $pass = trim($_POST['pass']);
    $pass2 = trim($_POST['pass2']);
    
    // Validaciones
    if (empty($pass) || empty($pass2)) {
        $mensaje_error = "Todos los campos son obligatorios";
    } elseif (strlen($pass) < 6) {
        $mensaje_error = "La contraseña debe tener al menos 6 caracteres";
    } elseif ($pass !== $pass2) {
        $mensaje_error = "Las contraseñas no coinciden";
    } else {
        // Actualizar contraseña
        $sql_update = "UPDATE usuario SET con_usuario = ? WHERE id_usuario = ?";
        $stmt_update = mysqli_prepare($con, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "si", $pass, $id_cambio);
        
        if (mysqli_stmt_execute($stmt_update)) {
            // Registrar en auditoría
            $accion = ($id_cambio == $id) ? "CAMBIO DE CONTRASEÑA PROPIA" : "CAMBIO DE CONTRASEÑA DE: $usuario";
            GrabarAuditoria($id, $usuario_sesion, $accion, 'USUARIO', $usuario);
            
            $mensaje_exito = "Contraseña actualizada correctamente";
            
            // Si cambió su propia contraseña, cerrar sesión después de 3 segundos
            if ($id_cambio == $id) {
                echo "<script>
                    setTimeout(function() {
                        alert('Su contraseña ha sido cambiada. Por seguridad, debe iniciar sesión nuevamente.');
                        window.location.href = '../_conexion/cerrarsesion.php';
                    }, 3000);
                </script>";
            }
        } else {
            $mensaje_error = "Error al actualizar la contraseña";
            GrabarAuditoria($id, $usuario_sesion, 'ERROR AL CAMBIAR CONTRASEÑA', 'USUARIO', $usuario);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cambiar Contraseña | ARCE PERÚ</title>
    
    <!-- Bootstrap -->
    <link href="../_complemento/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../_complemento/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="../_complemento/build/css/custom.min.css" rel="stylesheet">
    
    <style>
    .alert {
        margin-bottom: 20px;
    }
    .form-control:focus {
        border-color: #26B99A;
        box-shadow: 0 0 0 0.2rem rgba(38, 185, 154, 0.25);
    }
    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
    }
    .password-requirements {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }
    .x_panel {
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    </style>
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        
        <!-- Sidebar -->
        <?php include_once("../_vista/v_menu.php"); ?>

        <!-- Top Navigation -->
        <?php include_once("../_vista/v_menu_user.php"); ?>

        <!-- Page Content -->
        <div class="right_col" role="main">
          <div class="">
            
            <!-- Page Title -->
            <div class="page-title">
              <div class="title_left" style="width: 100%;">
                <h3>
                  <i class="fa fa-key"></i> 
                  Cambiar Contraseña - <?php echo htmlspecialchars($nombre); ?>
                  <small>Seguridad de la cuenta</small>
                </h3>
              </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-8 col-sm-12 offset-md-2">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>
                      <i class="fa fa-shield"></i> 
                      Actualizar Contraseña
                      <?php if ($id_cambio != $id): ?>
                        <small class="text-muted">- Usuario: <?php echo htmlspecialchars($usuario); ?></small>
                      <?php endif; ?>
                    </h2>
                    <div class="clearfix"></div>
                  </div>

                  <div class="x_content">
                    
                    <!-- Mensajes de estado -->
                    <?php if (!empty($mensaje_error)): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                      <i class="fa fa-exclamation-triangle"></i> <?php echo htmlspecialchars($mensaje_error); ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($mensaje_exito)): ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                      <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($mensaje_exito); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Formulario -->
                    <form class="form-horizontal form-label-left" action="" method="post" id="formPassword">
                      
                      <!-- Nueva contraseña -->
                      <div class="form-group row">
                        <label class="control-label col-md-3 col-sm-3">
                          <i class="fa fa-lock"></i> Nueva Contraseña 
                          <span class="text-danger">*</span>
                        </label>
                        <div class="col-md-9 col-sm-9">
                          <input type="password" 
                                 class="form-control" 
                                 name="pass" 
                                 id="pass"
                                 placeholder="Ingrese la nueva contraseña"
                                 minlength="6"
                                 required>
                          <div class="password-requirements">
                            <i class="fa fa-info-circle"></i> 
                            Mínimo 6 caracteres. Se recomienda usar letras, números y símbolos.
                          </div>
                        </div>
                      </div>

                      <!-- Confirmar contraseña -->
                      <div class="form-group row">
                        <label class="control-label col-md-3 col-sm-3">
                          <i class="fa fa-lock"></i> Confirmar Contraseña 
                          <span class="text-danger">*</span>
                        </label>
                        <div class="col-md-9 col-sm-9">
                          <input type="password" 
                                 class="form-control" 
                                 name="pass2" 
                                 id="pass2"
                                 placeholder="Repita la nueva contraseña"
                                 minlength="6"
                                 required>
                          <div id="password-match" class="password-requirements"></div>
                        </div>
                      </div>

                      <div class="ln_solid"></div>

                      <!-- Botones de acción -->
                      <div class="form-group row">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                          <div class="row">
                            <div class="col-md-6 col-sm-6">
                              <button type="reset" class="btn btn-outline-secondary btn-block">
                                <i class="fa fa-eraser"></i> Limpiar
                              </button>
                            </div>
                            <div class="col-md-6 col-sm-6">
                              <button class="btn btn-warning btn-block" type="submit" name="registrar" id="btnSubmit">
                                <i class="fa fa-save"></i> Actualizar Contraseña
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Información adicional -->
                      <div class="form-group row">
                        <div class="col-md-9 col-sm-9 offset-md-3">
                          <p class="text-muted">
                            <small>
                              <span class="text-danger">*</span> Los campos marcados son obligatorios.
                            </small>
                          </p>
                        </div>
                      </div>

                      <!-- Campos ocultos -->
                      <input type="hidden" name="e" value="<?php echo htmlspecialchars($e); ?>">
                      <input type="hidden" name="id" value="<?php echo $id_cambio; ?>">
                      
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->

      </div>
    </div>

    <!-- Scripts -->
    <script src="../_complemento/vendors/jquery/dist/jquery.min.js"></script>
    <script src="../_complemento/vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../_complemento/build/js/custom.min.js"></script>

    <script>
    $(document).ready(function() {
        // Validar coincidencia de contraseñas en tiempo real
        $('#pass2').on('keyup', function() {
            var pass1 = $('#pass').val();
            var pass2 = $(this).val();
            var matchDiv = $('#password-match');
            
            if (pass2.length > 0) {
                if (pass1 === pass2) {
                    matchDiv.html('<i class="fa fa-check text-success"></i> Las contraseñas coinciden')
                           .removeClass('text-danger')
                           .addClass('text-success');
                    $('#btnSubmit').prop('disabled', false);
                } else {
                    matchDiv.html('<i class="fa fa-times text-danger"></i> Las contraseñas no coinciden')
                           .removeClass('text-success')
                           .addClass('text-danger');
                    $('#btnSubmit').prop('disabled', true);
                }
            } else {
                matchDiv.html('').removeClass('text-success text-danger');
                $('#btnSubmit').prop('disabled', false);
            }
        });

        // Validar longitud mínima
        $('#pass').on('keyup', function() {
            var pass = $(this).val();
            if (pass.length > 0 && pass.length < 6) {
                $(this).removeClass('is-valid').addClass('is-invalid');
            } else if (pass.length >= 6) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).removeClass('is-valid is-invalid');
            }
        });

        // Confirmar antes de enviar
        $('#formPassword').on('submit', function(e) {
            var pass1 = $('#pass').val();
            var pass2 = $('#pass2').val();
            
            if (pass1 !== pass2) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
            
            if (pass1.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres');
                return false;
            }
            
            <?php if ($id_cambio == $id): ?>
            if (!confirm('¿Está seguro de cambiar su contraseña? Deberá iniciar sesión nuevamente.')) {
                e.preventDefault();
                return false;
            }
            <?php else: ?>
            if (!confirm('¿Está seguro de cambiar la contraseña del usuario <?php echo addslashes($usuario); ?>?')) {
                e.preventDefault();
                return false;
            }
            <?php endif; ?>
        });

        // Auto-hide alerts después de 5 segundos
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
    </script>

  </body>
</html>