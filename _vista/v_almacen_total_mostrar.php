<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Almacen Total<small></small></h3>
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
                                <h2>Almacen Total<small></small></h2>
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
                                    <table id="datatable-buttons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Producto</th>
                                                <th>Almacén</th>
                                                <th>Cliente</th>
                                                <th>Obra</th>
                                                <th>Ubicación</th>
                                                <th>Stock Físico</th>
                                                <th>Stock Reservado</th>
                                                <th>Stock Disponible</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                         foreach ($almacenes as $index => $almacen) {
                                            echo "<tr>";
                                            echo "<td>" . ($index + 1) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Producto']) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Almacen']) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Cliente']) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Obra']) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Ubicacion']) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Stock_Fisico']) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Stock_Reservado']) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Stock_Disponible']) . "</td>";
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