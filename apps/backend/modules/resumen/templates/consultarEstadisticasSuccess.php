<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentForm="estadisticas/generate";

$currentUser= $sf_user->getAttribute('username', '', 'subscriber') ;
$currentUserId= $sf_user->getAttribute('usuario_id','', 'subscriber');

use_helper('Object','jQuery','UserComponent');
?> 
<style>svg { overflow: visible !important; }</style>

<div class="row">
	<div class="col-md-12">
	    <?php
	    $cantidad_registros = count($data_views[0]['resultado']);
	    if($cantidad_registros == 0)
        {
            ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Resultados</div>
                </div>

                <div class="panel-body">
                    <div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
                </div>
                
            </div>		

            <?php
        }
	    else
        {
            ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="panel-title">Resultados</div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <?php
                            if($data_views[0]['tgraph'] == 'BarCustom')
                            {
                                ?>
                                    <canvas id="myChart7" style="height:400px; width:100%;"></canvas>  
                                    <button id="exportBtn" class="btn btn-blue">Exportar como Imagen</button>
                                <?php
                            }
                            else if($data_views[0]['tgraph'] == 'BarStacked')
                            {
                                ?>
                                <canvas id="myChart19" style="width:100%;"></canvas>
                                <button id="exportBtn04" class="btn btn-blue">Exportar como Imagen</button>
                                <?php
                            }
                            else if($data_views[0]['tgraph'] == 'LineChart')
                            {
                                ?>
                                <canvas id="myChart21" style="width:100%;"></canvas>
                                <button id="exportBtn03" class="btn btn-blue">Exportar como Imagen</button>
                                <?php
                            }
                            else
                            {
                                ?> 
                                    <div>
                                        <canvas id="myChart8" style="max-height: 400px !important; min-width: 80% !important; max-width: 900px !important;"></canvas>  
                                        <button id="exportBtn02" class="btn btn-blue">Exportar como Imagen</button>
                                    </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                    <div style="width:100%; clear:both; height:80px;"></div>

                    <div class="row">
                        <div class="col-sm-12 text-center"> 
                            <?php
                            if($data_views[0]['tgraph'] == 'BarStacked') 
                            {
                                ?>
                                <!-- <h2>SELECCIONE ESTADISTICA: OPCION 4</h2> -->
                                <table class="table table-bordered table-hover table-striped responsive">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 8%">VIGENCIA</th>
                                            <th class="text-center" style="width: 8%">MES</th>
                                            <th class="text-center" style="width: 8%">CON RESPUESTA</th>
                                            <th class="text-center" style="width: 8%">SIN RESPUESTA</th>
                                            <th class="text-center" style="width: 8%">TOTAL RADICADOS</th> 
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php 
                                        for ($i = 0; $i <= count($data_views[0]['resultado']); $i++)
                                        {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php        		              	
                                                    echo $data_views[0]['resultado'][$i]["VIGENCIA"];
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php        		              	
                                                    echo $data_views[0]['resultado'][$i]["MES_TEXT"];
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php        		              	
                                                    echo $data_views[0]['dataviews']['totales']['total_con_resp'][$i];
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php        		              	
                                                    echo $data_views[0]['dataviews']['totales']['total_sin_resp'][$i];
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php        		              	
                                                    echo $data_views[0]['dataviews']['totales']['total_radicados'][$i];
                                                    ?>
                                                </td> 
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <a class="btn btn-info" data-toggle="tooltip" data-original-title="Generar informe" target="_blank" href="<?php echo $base_path; ?>/backend.php/resumen/excel?f_inicial=<?php echo $las_fechas['inicial'];?>&f_final=<?php echo $las_fechas['final'];?>&opcion=4" > 
                                    Exportar a Excel
                                </a>
                                <?php
                            }
                            if($data_views[0]['ttable'] == 'opcion5')
                            {
                                ?>
                                <!-- <h2>SELECCIONE ESTADISTICA: OPCION 5</h2> -->
                                <table class="table table-bordered table-hover table-striped responsive">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 8%">VIGENCIA</th>
                                            <th class="text-center" style="width: 8%">MES</th>
                                            <th class="text-center" style="width: 8%">TOTAL</th> 
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php 
                                        for ($i = 0; $i < count($data_views[0]['resultado']); $i++)
                                        {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php        		              	
                                                    echo $data_views[0]['resultado'][$i]["VIGENCIA"];
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php        		              	
                                                    echo $data_views[0]['resultado'][$i]["MES_TEXT"];
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php        		              	
                                                    echo $data_views[0]['resultado'][$i]["TOTAL"];
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <a class="btn btn-info" data-toggle="tooltip" data-original-title="Generar informe" target="_blank" href="<?php echo $base_path; ?>/backend.php/resumen/excel?f_inicial=<?php echo $las_fechas['inicial'];?>&f_final=<?php echo $las_fechas['final'];?>&opcion=5" > 
                                    Exportar a Excel
                                </a>
                                <?php
                            }
                            else if($data_views[0]['ttable'] == 'opcion6')
                            {
                                ?>
                                <!-- <h2>SELECCIONE ESTADISTICA: OPCION 6</h2> -->
                                <table class="table table-bordered table-hover table-striped responsive">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 8%">VIGENCIA</th>
                                            <th class="text-center" style="width: 8%">MES</th>
                                            <th class="text-center" style="width: 8%">TIPO SERVICIO</th>
                                            <th class="text-center" style="width: 8%">TOTAL</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        foreach ($data_views[0]['resultado'] as $row):
                                        ?>
                                        <tr>
                                            <td>
                                                <?php        		              	 
                                                echo $row['VIGENCIA'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php        		              	
                                                echo $row['MES_TEXT'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php        		              	
                                                echo $row['NOMBRE_SERVICIO'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php        		              	
                                                echo $row['TOTAL'];
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                        endforeach;
                                        ?>
                                    </tbody>
                                </table>
                                <a class="btn btn-info" data-toggle="tooltip" data-original-title="Generar informe" target="_blank" href="<?php echo $base_path; ?>/backend.php/resumen/excel?f_inicial=<?php echo $las_fechas['inicial'];?>&f_final=<?php echo $las_fechas['final'];?>&opcion=6" > 
                                    Exportar a Excel
                                </a>

                                <?php
                            }
                            else if($data_views[0]['ttable'] == 'opcion7')
                            {
                                ?>
                                <!-- <h2>SELECCIONE ESTADISTICA: OPCION 7</h2> -->
                                <table class="table table-bordered table-hover table-striped responsive">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 8%">VIGENCIA</th>
                                            <th class="text-center" style="width: 8%">MES</th>
                                            <th class="text-center" style="width: 8%">ESTADO SERVICIO</th>
                                            <th class="text-center" style="width: 8%">TOTAL</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        foreach ($data_views[0]['resultado'] as $row):
                                        ?>
                                        <tr>
                                            <td>
                                                <?php        		              	
                                                echo $row['VIGENCIA'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php        		              	
                                                echo $row['MES_TEXT'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php        		              	
                                                echo $row['ESTADO_SERVICIO'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php        		              	 
                                                echo $row['TOTAL'];
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                        endforeach;
                                        ?>
                                    </tbody>
                                </table>
                                <a class="btn btn-info" data-toggle="tooltip" data-original-title="Generar informe" target="_blank" href="<?php echo $base_path; ?>/backend.php/resumen/excel?f_inicial=<?php echo $las_fechas['inicial'];?>&f_final=<?php echo $las_fechas['final'];?>&opcion=7" > 
                                    Exportar a Excel
                                </a>

                                <?php
                            }
                            else if($data_views[0]['tgraph'] == 'BarCustom' || $data_views[0]['tgraph'] == 'DonutCustom')
                            {
                                if($data_views[0]['ttable'] == 'opcion1')
                                {
                                    ?>
                                        <!-- <h2>SELECCIONE ESTADISTICA: OPCION 1</h2> -->
                                    <table class="table table-bordered table-hover table-striped responsive">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 8%">DESCRIPCION</th>
                                                <th class="text-center" style="width: 8%">NOMBRE</th>
                                                <th class="text-center" style="width: 8%">TOTAL</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            foreach ($data_views[0]['resultado'] as $row):
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php        		              	
                                                    echo $row['DESCRIPCION'];
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php        		              	
                                                    echo !empty($row['NOMBRE']) ? $row['NOMBRE'] : '';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php        		              	
                                                    echo $row['TOTAL'];
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                            endforeach;
                                            ?>
                                        </tbody>
                                    </table>
                                    <a class="btn btn-info" data-toggle="tooltip" data-original-title="Generar informe" target="_blank" href="<?php echo $base_path; ?>/backend.php/resumen/excel?f_inicial=<?php echo $las_fechas['inicial'];?>&f_final=<?php echo $las_fechas['final'];?>&opcion=1" > 
                                        Exportar a Excel
                                    </a>
                                    <?php
                                }
                                else if($data_views[0]['ttable'] == 'opcion2')
                                {
                                    ?>
                                        <!-- <h2>SELECCIONE ESTADISTICA: OPCION 2</h2> -->
                                    <table class="table table-bordered table-hover table-striped responsive">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 8%">DESCRIPCION</th>
                                                <th class="text-center" style="width: 8%">TOTAL</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            foreach ($data_views[0]['resultado'] as $row):
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php        		              	
                                                    echo $row['DESCRIPCION'];
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php        		              	
                                                    echo $row['TOTAL'];
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                            endforeach;
                                            ?>
                                        </tbody>
                                    </table>
                                    <a class="btn btn-info" data-toggle="tooltip" data-original-title="Generar informe" target="_blank" href="<?php echo $base_path; ?>/backend.php/resumen/excel?f_inicial=<?php echo $las_fechas['inicial'];?>&f_final=<?php echo $las_fechas['final'];?>&opcion=2" > 
                                        Exportar a Excel
                                    </a>
                                    <?php
                                }
                                else if($data_views[0]['ttable'] == 'opcion3')
                                {
                                    ?>
                                        <!-- <h2>SELECCIONE ESTADISTICA: OPCION 3</h2> -->
                                    <table class="table table-bordered table-hover table-striped responsive">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 8%">DESCRIPCION</th>
                                                <th class="text-center" style="width: 8%">TOTAL</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            foreach ($data_views[0]['resultado'] as $row):
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php        		              	
                                                    echo $row['DESCRIPCION'];
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php        		              	
                                                    echo $row['TOTAL'];
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                            endforeach;
                                            ?>
                                        </tbody>
                                    </table>
                                    <a class="btn btn-info" data-toggle="tooltip" data-original-title="Generar informe" target="_blank" href="<?php echo $base_path; ?>/backend.php/resumen/excel?f_inicial=<?php echo $las_fechas['inicial'];?>&f_final=<?php echo $las_fechas['final'];?>&opcion=3" > 
                                        Exportar a Excel
                                    </a>
                                    <?php
                                }
                                /**/
                                ?>
                                <?php 
                            }
                            ?>
                        </div>
                    </div>

                    <div style="width:100%; clear:both; height:80px;"></div>
                </div>
            <?php
      	}
      	?>
  	</div>
</div>

<script type="text/javascript">
    javascript:jQuery.MiMegaFuncion(<?php echo json_encode($data_views); ?>); 
</script>