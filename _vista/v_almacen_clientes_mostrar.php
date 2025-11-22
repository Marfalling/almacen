<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Almacen Clientes<small></small></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <!-- --------------------------------------- -->
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <div class="row">
                            <div class="col-sm-10">
                                <h2>Almacen Clientes<small></small></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-2">
                            </div>
                        </div>
                    </div>

                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">

                                    <div class="form-group row" style="display: flex; justify-content: flex-end; padding-left: 15px;">
                                        <form method="post" action="almacen_clientes_mostrar.php" class="form-inline">

                                            <div class="form-group mr-3">
                                                <label for="id_cliente" class="control-label mr-2">Cliente:</label>
                                                <select id="id_cliente" name="id_cliente" class="form-control" required>
                                                    <option value="" disabled selected>Seleccione Cliente</option>
                                                    <?php
                                                    foreach ($clientes as $cliente) {
                                                        $selected = (isset($id_cliente) && $id_cliente == $cliente['id_cliente']) ? 'selected' : '';
                                                        echo "<option value='{$cliente['id_cliente']}' $selected>{$cliente['nom_cliente']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <!-- Botón de submit -->
                                            <button type="submit" name="consultar" class="btn btn-primary">Consultar</button>
                                        </form>

                                    </div>
    


                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Producto</th>
                                                <th>Almacén</th>
                                                <th>Obra</th>
                                                <th>Ubicación</th>
                                                <!-- <th>Stock Físico</th> -->
                                                <!-- <th>Stock Reservado</th> -->
                                                <th>Stock Disponible</th>
                                                <th>Stock Devolucion</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                         foreach ($almacenes as $index => $almacen) {
                                            echo "<tr>";
                                            echo "<td>" . ($index + 1) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Producto']) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Almacen']) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Obra']) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Ubicacion']) . "</td>";
                                            //echo "<td>" . htmlspecialchars($almacen['Stock_Fisico']) . "</td>";
                                            //echo "<td>" . htmlspecialchars($almacen['Stock_Reservado']) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Stock_Disponible']) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Stock_Devolucion']) . "</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- --------------------------------------- -->
        </div>
    </div>
</div>
<!-- /page content -->