<?php 
    $path_theme = sfConfig::get('theme_simad');
    $base_path = sfConfig::get('base_simad');

	$usuariologuiado = $sf_user->getAttribute('usuario_id','', 'subscriber');
	$usuario = UsuarioPeer::retrieveByPk($usuariologuiado);
	$receptor_dep = $usuario->getReceptorDep();
?>

<div class="panel panel-primary" id="charts_env">
    <div class="panel-heading">
        <div class="panel-title">Grafico Estadistico</div>
        <div class="panel-options" >
            <ul class="nav nav-tabs">
				<?php if($receptor_dep == 1){ ?>
					<li class="active"><a href="#linea-chart-tab-<?php echo $periodo_id; ?>" data-toggle="tab">Pendientes por Usuario</a></li>
					<li class=""><a href="#area-chart-tab-<?php echo $periodo_id; ?>" data-toggle="tab">Pendientes por Leer</a></li>
				<?php }else { ?>
					<li class="active"><a href="#area-chart-tab-<?php echo $periodo_id; ?>" data-toggle="tab">Pendientes por Leer</a></li>					
				<?php } ?>

                <li class=""><a href="#line-chart-tab-<?php echo $periodo_id; ?>" data-toggle="tab">Recibidas con respuesta</a></li>
                <li class=""><a href="#pie-chart-tab-<?php echo $periodo_id; ?>" data-toggle="tab">Recibidas por tramites</a></li>
            </ul>
        </div>
    </div> 

    <div class="panel-body">
        <div class="tab-content">
			<?php if($receptor_dep == 1){ ?>
				<div class="tab-pane active" id="linea-chart-tab-<?php echo $periodo_id; ?>">
					<canvas id="line-chart2-<?php echo $periodo_id; ?>" style="min-height: 500px !important;"></canvas>
				</div>
				<div class="tab-pane" id="area-chart-tab-<?php echo $periodo_id; ?>">
					<div id="area-chart-demo-<?php echo $periodo_id; ?>" class="morrischart" style="height: 300px"></div>
				</div>
			<?php }else{ ?>
				<div class="tab-pane active" id="area-chart-tab-<?php echo $periodo_id; ?>">
					<div id="area-chart-demo-<?php echo $periodo_id; ?>" class="morrischart" style="height: 300px"></div>
				</div>
			<?php } ?>

            <div class="tab-pane" id="line-chart-tab-<?php echo $periodo_id; ?>">
				<canvas id="barstacked-chart-demo-<?php echo $periodo_id; ?>" style="max-height: 500px !important;"></canvas>
            </div>
            
			<div class="tab-pane" id="pie-chart-tab-<?php echo $periodo_id; ?>">
				<div style="display: flex; align-items: center;">
					<canvas id="pie-chart-demo-<?php echo $periodo_id; ?>" style="max-height: 290px !important;min-width: 80% !important; max-width: 900px !important;"></canvas>
				</div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function() {
		obtenerComunicacionesAreaChart();
		obtenerComRecibidasConRespuestaBarStacked();
		obtenerRecibidasPorTramitesPieChart();
		<?php if($receptor_dep == 1){ ?>
			obtenerComRecibidasSinRespuestaAllUsuariosLineChart();
		<?php } ?>
	});
	
	function obtenerComunicacionesAreaChart() 
	{
		jQuery.ajax(
		{
			url: 'resumen/areaChart',
			data: jQuery.param({ periodo_id: <?php echo $periodo_id; ?> }) ,
			type: 'POST',
			success: function(response) 
			{
				const lasComs = response;
				let numItems = Object.keys(lasComs).length;
				let miFinalResultado = [];

				for (let i = 0; i < numItems; i++) 
				{ 
					miFinalResultado[i] = { y: lasComs[i]['FECHA_REGISTRO'], a: lasComs[i]['TOTAL']} 
				}

				var area_chart_demo = jQuery("#area-chart-demo-<?php echo $periodo_id; ?>");
				area_chart_demo.parent().show();
				var area_chart = Morris.Area({
					element: 'area-chart-demo-<?php echo $periodo_id; ?>',
					data : miFinalResultado.length ? miFinalResultado : [{ y:"0000-00-00", a:0 }],
					xkey: 'y',
					ykeys: ['a'],
					labels: ['Enviadas'],
					lineColors: ['#303641', '#576277', '#f00']
				});

				area_chart_demo.parent().attr('style', '');
			}
		});
	}

	function obtenerComRecibidasSinRespuestaAllUsuariosLineChart() 
	{
		jQuery.ajax(
		{
			url: 'resumen/lineChart2',
			data: jQuery.param({ periodo_id: <?php echo $periodo_id; ?> }) ,
			type: 'POST',
			success: function(response) 
			{
				javascript:jQuery.MiMegaFuncion(response);
			}
		});
	}

	function obtenerComRecibidasConRespuestaBarStacked() 
	{
		jQuery.ajax(
		{
			url: 'resumen/barStacked',
			data: jQuery.param({ periodo_id: <?php echo $periodo_id; ?> }) ,
			type: 'POST',
			success: function(response) 
			{
				javascript:jQuery.MiMegaFuncion(response);
			}
		});
	}

	function obtenerComunicacionesLineChart() 
	{
		jQuery.ajax(
		{
			url: 'resumen/areaChart',
			data: jQuery.param({ periodo_id: <?php echo $periodo_id; ?> }) ,
			type: 'POST',
			success: function(response) 
			{
				const lasComs = response;
				let numItems = Object.keys(lasComs).length;

				let miFinalResultado = [];
				for (let i = 0; i < numItems; i++) { miFinalResultado[i] = { y: lasComs[i]['FECHA_REGISTRO'], a: lasComs[i]['TOTAL']} }

				var line_chart_demo = jQuery("#line-chart-demo-<?php echo $periodo_id; ?>");
				var line_chart = Morris.Line({
					element: 'line-chart-demo-<?php echo $periodo_id; ?>',
					data : miFinalResultado.length ? miFinalResultado : [{ y:"0000-00-00", a:0 }],
					xkey: 'y',
					ykeys: ['a'],
					labels: ['Enviadas'],
					redraw: true
				});
				line_chart_demo.parent().attr('style', '');
			}
		});
	}

	function obtenerRecibidasPorTramitesPieChart() 
	{
		jQuery.ajax(
		{
			url: 'resumen/donutChart2',
			data: jQuery.param({ periodo_id: <?php echo $periodo_id; ?> }) ,
			type: 'POST',
			success: function(response) 
			{
				javascript:jQuery.MiMegaFuncion(response);
			}
		});
	}

	function ColorCode() 
	{
		var makingColorCode = '0123456789ABCDEF';
		var finalCode = '#';
		for (var counter = 0; counter < 6; counter++) 
		{
			finalCode = finalCode + makingColorCode[Math.floor(Math.random() * 16)];
		}
		return finalCode;
	}

	function obtenerComunicacionesDonutChart() 
	{
		jQuery.ajax(
		{
			url: 'resumen/donutChart',
			data: jQuery.param({ periodo_id: <?php echo $periodo_id; ?> }) ,
			type: 'POST',
			success: function(response) 
			{
				const lasComs = response;
				let numItems = Object.keys(lasComs).length;

				let miFinalResultado = [];
				let miColorFinal = [];
				for (let i = 0; i < numItems; i++) 
				{
					miFinalResultado[i] = {label: lasComs[i]['DESCRIPCION'], value: lasComs[i]['total']}
					miColorFinal[i] = ColorCode();
				}


				var donut_chart_demo = jQuery("#donut-chart-demo-<?php echo $periodo_id; ?>");
				donut_chart_demo.parent().show();
				var donut_chart = Morris.Donut({
					element: 'donut-chart-demo-<?php echo $periodo_id; ?>',
					data: miFinalResultado.length ? miFinalResultado : [ { label:"Sin Información", value:100 } ],
					colors: miColorFinal
				});
				donut_chart_demo.parent().attr('style', '');
			}
		});
	}
	
	function getRandomInt(min, max)
	{
		return Math.floor(Math.random() * (max - min + 1)) + min;
	}
</script>
<!-- fin bloque js graficos -->