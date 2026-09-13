<?php echo input_hidden_tag('fileuploadtmp',$inputFileName); ?>
<?php
	$print_header = 0;
	$count_header = 0;
?>
<div class="row">
	<div class="col-md-12">
		<?php
		$cantidad_registros = count($sheetData);
		?>
		<div id="tablelist" >
			<?php	    
			if($cantidad_registros == 0):
			?>
			<div class="panel panel-green">
				<div class="panel-heading">
					<div class="panel-title">Lista Datos</div>
				</div>
	
				<div class="panel-body">
					<div class="alert alert-default"><strong>No existen Registros</strong>, Intente cargar el archivo nuevamente.</div>
				</div>
			</div>
			<?php else: ?>
				<div class="panel panel-green">                
				<div class="panel-heading">
					<div class="panel-title">Lista Datos</div>                
				</div>
				<div class="panel-body with-table">
					<table class="table table-bordered table-hover table-striped responsive" id="table-1">
					<thead>
						<tr>
							<th data-hide="phone">Item</th>
							<?php 
								foreach($headerList[0] as $headerRow){
									if(trim($headerRow))
										echo '<th class="text-center">'.utf8_decode(strtoupper($headerRow)).'</th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
						<?php $print_header = false; $item_count = 0; ?>
						<?php 
							foreach($sheetData as $data){
								echo '<tr><td>'.++$item_count.'</td>';
									foreach($data as $row){
										if(trim($row)){										
											echo '<td>'.utf8_decode($row).'</td>';
										}
									}
								echo '</tr>';
							}
						?>
					</tbody>
					</table>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>