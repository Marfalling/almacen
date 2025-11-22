<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Almacen Arce<small></small></h3>
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
                                <h2>Almacen Arce<small></small></h2>
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
                                                <th>Tipo de Producto</th>
                                                <th>Tipo de Material</th>
                                                <th>Unidad de Medida</th>
                                                <!-- <th>Stock Físico</th> -->
                                                <!-- <th>Stock Reservado</th> -->
                                                <th>Stock Disponible</th>
                                                <th>Stock Devolución</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                         foreach ($almacenes as $index => $almacen) {
                                            echo "<tr>";
                                            echo "<td>" . ($index + 1) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Producto']) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Tipo_Producto']) . "</td>";
                                            echo "<td>" . htmlspecialchars($almacen['Tipo_Material']) . "</td>";   
                                            echo "<td>" . htmlspecialchars($almacen['Unidad_Medida']) . "</td>";
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