<?php
// Cargar el modelo de compras para las alertas
require_once("../_modelo/m_compras.php");
require_once("../_modelo/m_producto.php");

// Obtener compras próximas a vencer (3 días antes)
$compras_por_vencer = ObtenerComprasProximasVencer(3);
$total_alertas_compras = count($compras_por_vencer);

// Obtener productos próximos a vencer calibrado/operatividad (15 días antes)
$productos_por_vencer = ObtenerProductosProximosVencer(15);
$total_alertas_productos = count($productos_por_vencer);
?>

<!-- top navigation -->
<div class="top_nav">
    <div class="nav_menu">
        <div class="nav toggle">
            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
        </div>
        <nav class="nav navbar-nav">
            <ul class="navbar-right">
                
                <!-- PERFIL DE USUARIO -->
                <li class="nav-item dropdown open" style="padding-left: 15px;">
                    <a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown" data-toggle="dropdown" aria-expanded="false">
                        <img src="../_complemento/images/img.jpg" alt="">
                    </a>
                    <div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="reset_password.php?id=<?php echo $id; ?>">
                            <i class="fa fa-key pull-right"></i> Cambiar Contraseña
                        </a>
                        <a class="dropdown-item" href="../_conexion/cerrarsesion.php">
                            <i class="fa fa-sign-out pull-right"></i> Cerrar Sesión
                        </a>
                    </div>
                </li>
                
                <!-- ALERTAS DE PRODUCTOS POR VENCER (CALIBRADO/OPERATIVIDAD) -->
                <?php if ($total_alertas_productos > 0): ?>
                <li role="presentation" class="nav-item dropdown open" style="margin-right: 20px;">
                    <a href="javascript:;" 
                       class="dropdown-toggle alerta-productos-btn" 
                       id="navbarDropdownProductos" 
                       data-toggle="dropdown" 
                       aria-expanded="false" 
                       title="Productos con calibrado u operatividad por vencer">
                        <div class="alerta-box alerta-box-productos">
                            <i class="fa fa-wrench"></i>
                            <span class="alerta-text">
                                <strong><?php echo $total_alertas_productos; ?></strong> 
                                <?php echo $total_alertas_productos == 1 ? 'Producto' : 'Productos'; ?> por vencer
                            </span>
                        </div>
                    </a>
                    <ul class="dropdown-menu list-unstyled msg_list alerta-dropdown" 
                        role="menu" 
                        aria-labelledby="navbarDropdownProductos">
                        
                        <!-- HEADER DEL DROPDOWN -->
                        <li class="nav-item alerta-header">
                            <div class="alerta-header-content">
                                <i class="fa fa-bell"></i> 
                                <strong>Alertas de Calibrado y Operatividad</strong>
                            </div>
                        </li>
                        
                        <!-- LISTA DE PRODUCTOS -->
                        <?php foreach ($productos_por_vencer as $producto): 
                            // Determinar qué está por vencer
                            $alertas = [];
                            
                            if ($producto['dias_calibrado'] !== null && $producto['dias_calibrado'] >= 0 && $producto['dias_calibrado'] <= 30) {
                                $dias = intval($producto['dias_calibrado']);
                                if ($dias == 0) {
                                    $alertas[] = ['tipo' => 'calibrado', 'texto' => 'Calibrado VENCE HOY', 'clase' => 'alerta-critica', 'icono' => 'fa-times-circle'];
                                } elseif ($dias <= 7) {
                                    $alertas[] = ['tipo' => 'calibrado', 'texto' => "Calibrado en $dias día" . ($dias > 1 ? 's' : ''), 'clase' => 'alerta-alta', 'icono' => 'fa-exclamation-circle'];
                                } else {
                                    $alertas[] = ['tipo' => 'calibrado', 'texto' => "Calibrado en $dias días", 'clase' => 'alerta-media', 'icono' => 'fa-clock-o'];
                                }
                            }
                            
                            if ($producto['dias_operatividad'] !== null && $producto['dias_operatividad'] >= 0 && $producto['dias_operatividad'] <= 30) {
                                $dias = intval($producto['dias_operatividad']);
                                if ($dias == 0) {
                                    $alertas[] = ['tipo' => 'operatividad', 'texto' => 'Operatividad VENCE HOY', 'clase' => 'alerta-critica', 'icono' => 'fa-times-circle'];
                                } elseif ($dias <= 7) {
                                    $alertas[] = ['tipo' => 'operatividad', 'texto' => "Operatividad en $dias día" . ($dias > 1 ? 's' : ''), 'clase' => 'alerta-alta', 'icono' => 'fa-exclamation-circle'];
                                } else {
                                    $alertas[] = ['tipo' => 'operatividad', 'texto' => "Operatividad en $dias días", 'clase' => 'alerta-media', 'icono' => 'fa-clock-o'];
                                }
                            }
                            
                            // Mostrar cada alerta
                            foreach ($alertas as $alerta):
                        ?>
                        <li class="nav-item alerta-item">
                            <a class="dropdown-item" href="producto_detalle.php?id=<?php echo $producto['id_producto']; ?>">
                                <div class="alerta-card <?php echo $alerta['clase']; ?>">
                                    <div class="alerta-badge">
                                        <i class="fa <?php echo $alerta['icono']; ?>"></i>
                                        <?php echo $alerta['texto']; ?>
                                    </div>
                                    <div class="alerta-info">
                                        <div class="alerta-title">
                                            <strong><?php echo htmlspecialchars($producto['nom_producto']); ?></strong>
                                        </div>
                                        <?php if (!empty($producto['cod_material'])): ?>
                                        <div class="alerta-codigo">
                                            <i class="fa fa-barcode"></i> 
                                            Código: <?php echo htmlspecialchars($producto['cod_material']); ?>
                                        </div>
                                        <?php endif; ?>
                                        <div class="alerta-tipo">
                                            <i class="fa fa-tag"></i> 
                                            <?php echo htmlspecialchars($producto['nom_producto_tipo']); ?>
                                        </div>
                                        <?php if ($alerta['tipo'] == 'calibrado' && $producto['fecha_prox_calibrado']): ?>
                                        <div class="alerta-fecha">
                                            <i class="fa fa-calendar"></i> 
                                            Fecha límite: <strong><?php echo date('d/m/Y', strtotime($producto['fecha_prox_calibrado'])); ?></strong>
                                        </div>
                                        <?php elseif ($alerta['tipo'] == 'operatividad' && $producto['fecha_prox_operatividad']): ?>
                                        <div class="alerta-fecha">
                                            <i class="fa fa-calendar"></i> 
                                            Fecha límite: <strong><?php echo date('d/m/Y', strtotime($producto['fecha_prox_operatividad'])); ?></strong>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <?php 
                            endforeach;
                        endforeach; 
                        ?>
                        
                        <!-- FOOTER DEL DROPDOWN -->
                        <li class="nav-item alerta-footer">
                            <div class="text-center">
                                <a href="producto_mostrar.php" class="btn-ver-todas">
                                    Ver todos los productos
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- ALERTAS DE ÓRDENES POR VENCER -->
                <?php if ($total_alertas_compras > 0): ?>
                <li role="presentation" class="nav-item dropdown open" style="margin-right: 20px;">
                    <a href="javascript:;" 
                       class="dropdown-toggle alerta-compras-btn" 
                       id="navbarDropdownAlertas" 
                       data-toggle="dropdown" 
                       aria-expanded="false" 
                       title="Órdenes de compra por vencer">
                        <div class="alerta-box">
                            <i class="fa fa-exclamation-triangle"></i>
                            <span class="alerta-text">
                                <strong><?php echo $total_alertas_compras; ?></strong> 
                                <?php echo $total_alertas_compras == 1 ? 'Orden' : 'Órdenes'; ?> por vencer
                            </span>
                        </div>
                    </a>
                    <ul class="dropdown-menu list-unstyled msg_list alerta-dropdown" 
                        role="menu" 
                        aria-labelledby="navbarDropdownAlertas">
                        
                        <!-- HEADER DEL DROPDOWN -->
                        <li class="nav-item alerta-header">
                            <div class="alerta-header-content">
                                <i class="fa fa-bell"></i> 
                                <strong>Alertas de Vencimiento</strong>
                            </div>
                        </li>
                        
                        <!-- LISTA DE ÓRDENES -->
                        <?php foreach ($compras_por_vencer as $compra): 
                            $dias_restantes = intval($compra['dias_restantes']);
                            
                            // Determinar clase de urgencia
                            if ($dias_restantes == 0) {
                                $clase_urgencia = 'alerta-critica';
                                $texto_urgencia = 'VENCE HOY';
                                $icono = 'fa-times-circle';
                            } elseif ($dias_restantes == 1) {
                                $clase_urgencia = 'alerta-alta';
                                $texto_urgencia = 'Vence mañana';
                                $icono = 'fa-exclamation-circle';
                            } else {
                                $clase_urgencia = 'alerta-media';
                                $texto_urgencia = "Vence en {$dias_restantes} días";
                                $icono = 'fa-clock-o';
                            }
                            
                            $fecha_venc_formato = date('d/m/Y', strtotime($compra['fecha_vencimiento']));
                        ?>
                        <li class="nav-item alerta-item">
                            <a class="dropdown-item" href="compras_mostrar.php?abrir_modal=<?php echo $compra['id_compra']; ?>">
                                <div class="alerta-card <?php echo $clase_urgencia; ?>">
                                    <div class="alerta-badge">
                                        <i class="fa <?php echo $icono; ?>"></i>
                                        <?php echo $texto_urgencia; ?>
                                    </div>
                                    <div class="alerta-info">
                                        <div class="alerta-title">
                                            <strong>Orden #<?php echo $compra['id_compra']; ?></strong> 
                                            <span class="text-muted">- <?php echo $compra['cod_pedido']; ?></span>
                                        </div>
                                        <div class="alerta-proveedor">
                                            <i class="fa fa-building-o"></i> 
                                            <?php echo htmlspecialchars($compra['nom_proveedor']); ?>
                                        </div>
                                        <div class="alerta-fecha">
                                            <i class="fa fa-calendar"></i> 
                                            Fecha límite: <strong><?php echo $fecha_venc_formato; ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        
                        <!-- FOOTER DEL DROPDOWN -->
                        <li class="nav-item alerta-footer">
                            <div class="text-center">
                                <a href="compras_mostrar.php" class="btn-ver-todas">
                                    Ver todas las compras
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

            </ul>
        </nav>
    </div>
</div>
<!-- /top navigation -->

<style>
/* ============================================
   ESTILOS PARA EL BOTÓN DE ALERTAS DE COMPRAS
   ============================================ */
.alerta-compras-btn {
    padding: 0 !important;
    text-decoration: none;
}

.alerta-box {
    display: flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(244, 67, 54, 0.3);
    transition: all 0.3s ease;
    cursor: pointer;
    animation: pulse-shadow 2s infinite;
}

.alerta-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(244, 67, 54, 0.5);
}

.alerta-box i {
    font-size: 22px;
    animation: shake 2s infinite;
}

.alerta-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
    font-size: 13px;
}

.alerta-text strong {
    font-size: 18px;
    font-weight: bold;
}

/* ============================================
   ESTILOS PARA EL BOTÓN DE ALERTAS DE PRODUCTOS
   ============================================ */
.alerta-productos-btn {
    padding: 0 !important;
    text-decoration: none;
}

.alerta-box-productos {
    background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
    box-shadow: 0 2px 8px rgba(255, 152, 0, 0.3);
}

.alerta-box-productos:hover {
    box-shadow: 0 4px 12px rgba(255, 152, 0, 0.5);
}

/* Animaciones */
@keyframes pulse-shadow {
    0%, 100% { 
        box-shadow: 0 2px 8px rgba(244, 67, 54, 0.3);
    }
    50% { 
        box-shadow: 0 4px 16px rgba(244, 67, 54, 0.6);
    }
}

@keyframes shake {
    0%, 100% { transform: rotate(0deg); }
    10%, 30%, 50%, 70%, 90% { transform: rotate(-5deg); }
    20%, 40%, 60%, 80% { transform: rotate(5deg); }
}

/* ============================================
   ESTILOS PARA EL DROPDOWN
   ============================================ */
.alerta-dropdown {
    min-width: 420px !important;
    max-height: 500px;
    overflow-y: auto;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    padding: 0;
}

.alerta-header {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 15px 20px;
    border-bottom: 3px solid #e74c3c;
    position: sticky;
    top: 0;
    z-index: 10;
}

.alerta-header-content {
    color: black;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
}

.alerta-header-content i {
    font-size: 20px;
}

/* ============================================
   ESTILOS PARA CADA ITEM
   ============================================ */
.alerta-item {
    border-bottom: 1px solid #ecf0f1;
}

.alerta-item:last-of-type {
    border-bottom: none;
}

.alerta-item .dropdown-item {
    padding: 0;
}

.alerta-card {
    padding: 15px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.alerta-card:hover {
    background-color: #f8f9fa;
}

.alerta-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 10px;
}

/* Clases de urgencia */
.alerta-critica .alerta-badge {
    background-color: #e74c3c;
    color: white;
}

.alerta-alta .alerta-badge {
    background-color: #f39c12;
    color: white;
}

.alerta-media .alerta-badge {
    background-color: #3498db;
    color: white;
}

.alerta-info {
    color: #2c3e50;
}

.alerta-title {
    font-size: 14px;
    margin-bottom: 8px;
}

.alerta-title strong {
    color: #2c3e50;
}

.alerta-proveedor,
.alerta-codigo,
.alerta-tipo,
.alerta-fecha {
    font-size: 12px;
    color: #7f8c8d;
    margin: 5px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.alerta-fecha strong {
    color: #e74c3c;
}

/* ============================================
   FOOTER DEL DROPDOWN
   ============================================ */
.alerta-footer {
    background-color: #f8f9fa;
    padding: 12px;
    border-top: 2px solid #e0e0e0;
    position: sticky;
    bottom: 0;
}

.btn-ver-todas {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #3498db;
    font-weight: bold;
    font-size: 13px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-ver-todas:hover {
    color: #2980b9;
    gap: 12px;
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 768px) {
    .alerta-dropdown {
        min-width: 320px !important;
    }
    
    .alerta-box {
        padding: 6px 12px;
    }
    
    .alerta-text {
        font-size: 11px;
    }
    
    .alerta-text strong {
        font-size: 16px;
    }
}
</style>