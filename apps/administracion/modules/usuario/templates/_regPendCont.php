<?php 
$path_theme = substr(sfConfig::get('theme_simad'), -1) == "/" ? sfConfig::get('theme_simad') : sfConfig::get('theme_simad').'/';
$base_path = substr(sfConfig::get('base_simad'), -1) == "/" ? sfConfig::get('base_simad') : sfConfig::get('base_simad').'/';
//use_helper('Object','jQuery');

?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-success">
			<div class="panel-heading">
				<div class="panel-title">Lista de Actividades Pendientes</div>
			</div>
			<div class="panel-body with-table">
				<!-- Opciones Listar -->
				<div class="row">
					<div class="col-sm-12 form-group"> 
						<div class="panel-group joined" id="accordion-test-2-<?php echo md5($periodo_id); ?>">
	
							<div class="panel panel-danger" style="width: 100%;">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion-test-2-<?php echo md5($periodo_id); ?>" href="#collapseOne-2-<?php echo md5($periodo_id); ?>">
											Comunicaciones Externas Recibidas
										</a>
									</h4>
								</div>
								<div id="collapseOne-2-<?php echo md5($periodo_id); ?>" class="panel-collapse collapse in">
									<div class="panel-body">
										<table class="table table-bordered table-hover table-striped responsive">
											<tbody>
												<tr>
													<td class="text-center">
														<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario/detallePendientes?usuario_id=<?php echo $usuario->getUsuarioId(); ?>&periodo_id=<?php echo $periodo_id; ?>&cant_regsts=<?php echo (isset($totalRegPend['recibidas_leer']) ?  $totalRegPend['recibidas_leer'] : 0); ?>&concepto=recibidas_leer');">     
															<div class="tile-stats tile-plum">            
																<div class="icon"><i class="entypo-mail"></i></div>
																<div class="num" data-start="0" data-end="" data-postfix="" data-duration="1500" data-delay="0"><?php echo (isset($totalRegPend['recibidas_leer']) ?  $totalRegPend['recibidas_leer'] : 0);?></div>
																<h3>Por Leer</h3>
															</div>
														</a>
													</td>  
													<td class="text-center">
														<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario/detallePendientes?usuario_id=<?php echo $usuario->getUsuarioId(); ?>&periodo_id=<?php echo $periodo_id; ?>&cant_regsts=<?php echo (isset($totalRegPend['recibida_copia']) ?  $totalRegPend['recibida_copia'] : 0); ?>&concepto=recibida_copia');">
															<div class="tile-stats tile-blue">            
																<div class="icon"><i class="entypo-shuffle"></i></div>
																<div class="num" data-start="0" data-end="" data-postfix="" data-duration="1500" data-delay="0"><?php echo (isset($totalRegPend['recibida_copia']) ?  $totalRegPend['recibida_copia'] : 0);?></div>
																<h3>Copias</h3>
															</div>
														</a>   
													</td>                      
													<td class="text-center">
														<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario/detallePendientes?usuario_id=<?php echo $usuario->getUsuarioId(); ?>&periodo_id=<?php echo $periodo_id; ?>&cant_regsts=<?php echo (isset($totalRegPend['vencidas']) ?  $totalRegPend['vencidas'] : 0); ?>&concepto=vencidas');">
															<div class="tile-stats tile-red">            
																<div class="icon"><i class="entypo-shuffle"></i></div>
																<div class="num" data-start="0" data-end="" data-postfix="" data-duration="1500" data-delay="0"><?php echo (isset($totalRegPend['vencidas']) ?  $totalRegPend['vencidas'] : 0);?></div>
																<h3>Vencidas</h3>
															</div> 
														</a>
													</td>
													<td class="text-center">
														<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario/detallePendientes?usuario_id=<?php echo $usuario->getUsuarioId(); ?>&periodo_id=<?php echo $periodo_id; ?>&cant_regsts=<?php echo (isset($totalRegPend['por_vencer']) ?  $totalRegPend['por_vencer'] : 0); ?>&concepto=por_vencer');">
															<div class="tile-stats tile-pink">            
																<div class="icon"><i class="entypo-shuffle"></i></div>
																<div class="num" data-start="0" data-end="" data-postfix="" data-duration="1500" data-delay="0"><?php echo (isset($totalRegPend['por_vencer']) ?  $totalRegPend['por_vencer'] : 0);?></div>
																<h3>Por Vencer</h3>
															</div>
														</a>
													</td>
													<td class="text-center">
														<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario/detallePendientes?usuario_id=<?php echo $usuario->getUsuarioId(); ?>&periodo_id=<?php echo $periodo_id; ?>&cant_regsts=<?php echo (isset($totalRegPend['por_distribuir']) ?  $totalRegPend['por_distribuir'] : 0); ?>&concepto=por_distribuir');">
															<div class="tile-stats tile-red">            
																<div class="icon"><i class="entypo-mail"></i></div>
																<div class="num" data-start="0" data-end="" data-postfix="" data-duration="1500" data-delay="0"><?php echo (isset($totalRegPend['por_distribuir']) ?  $totalRegPend['por_distribuir'] : 0);?></div>
																<h3>Por Distribuir</h3>
															</div>
														</a>
													</td>

													<td class="text-center">
														<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario/detallePendientes?usuario_id=<?php echo $usuario->getUsuarioId(); ?>&periodo_id=<?php echo $periodo_id; ?>&cant_regsts=<?php echo (isset($totalRegPend['por_gestionar']) ?  $totalRegPend['por_gestionar'] : 0); ?>&concepto=por_gestionar');">
															<div class="tile-stats tile-red">            
																<div class="icon"><i class="entypo-mail"></i></div>
																<div class="num" data-start="0" data-end="" data-postfix="" data-duration="1500" data-delay="0"><?php echo (isset($totalRegPend['por_gestionar']) ?  $totalRegPend['por_gestionar'] : 0);?></div>
																<h3>Por Gestionar</h3>
															</div>
														</a>
													</td>
													
													<td class="text-center">
														<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario/detallePendientes?usuario_id=<?php echo $usuario->getUsuarioId(); ?>&periodo_id=<?php echo $periodo_id; ?>&cant_regsts=<?php echo (isset($totalRegPend['comrecibida_responder']) ?  $totalRegPend['comrecibida_responder'] : 0); ?>&concepto=comrecibida_responder');">
															<div class="tile-stats tile-red">            
																<div class="icon"><i class="entypo-mail"></i></div>
																<div class="num" data-start="0" data-end="" data-postfix="" data-duration="1500" data-delay="0"><?php echo (isset($totalRegPend['comrecibida_responder']) ?  $totalRegPend['comrecibida_responder'] : 0);?></div>
																<h3>Por Responder</h3>
															</div>
														</a>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							
							<div class="panel panel-danger">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion-test-2-<?php echo md5($periodo_id); ?>" href="#collapseTwo-2-<?php echo md5($periodo_id); ?>" class="collapsed">
											Comunicaciones Internas
										</a>
									</h4>
								</div>
								<div id="collapseTwo-2-<?php echo md5($periodo_id); ?>" class="panel-collapse collapse">
									<div class="panel-body">
										<table class="table table-bordered table-hover table-striped responsive">
											<tbody>
												<tr>
													<td class="text-center">
														<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario/detallePendientes?usuario_id=<?php echo $usuario->getUsuarioId(); ?>&periodo_id=<?php echo $periodo_id; ?>&cant_regsts=<?php echo (isset($totalRegPend['por_leer']) ?  $totalRegPend['por_leer'] : 0); ?>&concepto=por_leer');">     
															<div class="tile-stats tile-plum">            
																<div class="icon"><i class="entypo-mail"></i></div>
																<div class="num" data-start="0" data-end="" data-postfix="" data-duration="1500" data-delay="0"><?php echo (isset($totalRegPend['por_leer']) ?  $totalRegPend['por_leer'] : 0);?></div>
																<h3>Por Leer</h3>
															</div>
														</a>
													</td>  
													<td class="text-center">
														<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario/detallePendientes?usuario_id=<?php echo $usuario->getUsuarioId(); ?>&periodo_id=<?php echo $periodo_id; ?>&cant_regsts=<?php echo (isset($totalRegPend['por_responder']) ?  $totalRegPend['por_responder'] : 0); ?>&concepto=por_responder');">
															<div class="tile-stats tile-orange">            
																<div class="icon"><i class="entypo-shuffle"></i></div>
																<div class="num" data-start="0" data-end="" data-postfix="" data-duration="1500" data-delay="0"><?php echo (isset($totalRegPend['por_responder']) ?  $totalRegPend['por_responder'] : 0);?></div>
																<h3>Por Responder</h3>
															</div>
														</a> 
													</td>                      
													<td class="text-center">
														<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario/detallePendientes?usuario_id=<?php echo $usuario->getUsuarioId(); ?>&periodo_id=<?php echo $periodo_id; ?>&cant_regsts=<?php echo (isset($totalRegPend['copia']) ?  $totalRegPend['copia'] : 0); ?>&concepto=copia');">
															<div class="tile-stats tile-blue">            
																<div class="icon"><i class="entypo-shuffle"></i></div>
																<div class="num" data-start="0" data-end="" data-postfix="" data-duration="1500" data-delay="0"><?php echo (isset($totalRegPend['copia']) ?  $totalRegPend['copia'] : 0);?></div>
																<h3>Copias</h3>
															</div>
														</a> 
													</td>
													<td class="text-center">
													</td> 
													<td class="text-center">
													</td> 
												</tr>
											</tbody>
										</table>                                    
									</div>
								</div>
							</div> 
							
							<div class="panel panel-danger">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion-test-2-<?php echo md5($periodo_id); ?>" href="#collapseThree-2-<?php echo md5($periodo_id); ?>" class="collapsed">
											Comunicaciones Externas Enviadas
										</a>
									</h4>
								</div>
								<div id="collapseThree-2-<?php echo md5($periodo_id); ?>" class="panel-collapse collapse">
									<div class="panel-body">
										<table class="table table-bordered table-hover table-striped responsive">
											<tbody>
												<tr>
													<td class="text-center">
														<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario/detallePendientes?usuario_id=<?php echo $usuario->getUsuarioId(); ?>&periodo_id=<?php echo $periodo_id; ?>&cant_regsts=<?php echo (isset($totalRegPend['enviadas']) ?  $totalRegPend['enviadas'] : 0); ?>&concepto=enviadas');">     
															<div class="tile-stats tile-orange">            
																<div class="icon"><i class="entypo-mail"></i></div>
																<div class="num" data-start="0" data-end="" data-postfix="" data-duration="1500" data-delay="0"><?php echo (isset($totalRegPend['enviadas']) ?  $totalRegPend['enviadas'] : 0);?></div>
																<h3>Enviadas</h3>
															</div>
														</a>
													</td>  
													<td class="text-center">
														<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario/detallePendientes?usuario_id=<?php echo $usuario->getUsuarioId(); ?>&periodo_id=<?php echo $periodo_id; ?>&cant_regsts=<?php echo (isset($totalRegPend['copia_informativa']) ?  $totalRegPend['copia_informativa'] : 0); ?>&concepto=copia_informativa');">     
															<div class="tile-stats tile-blue">            
																<div class="icon"><i class="entypo-mail"></i></div>
																<div class="num" data-start="0" data-end="" data-postfix="" data-duration="1500" data-delay="0"><?php echo (isset($totalRegPend['copia_informativa']) ?  $totalRegPend['copia_informativa'] : 0);?></div>
																<h3>Copias</h3>
															</div>
														</a>
													</td>                      
													<td class="text-center">
														<a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario/detallePendientes?usuario_id=<?php echo $usuario->getUsuarioId(); ?>&periodo_id=<?php echo $periodo_id; ?>&cant_regsts=<?php echo (isset($totalRegPend['enviadas_gsalida']) ?  $totalRegPend['enviadas_gsalida'] : 0); ?>&concepto=enviadas_gsalida');">     
															<div class="tile-stats tile-cyan">            
																<div class="icon"><i class="entypo-mail"></i></div>
																<div class="num" data-start="0" data-end="" data-postfix="" data-duration="1500" data-delay="0"><?php echo (isset($totalRegPend['enviadas_gsalida']) ?  $totalRegPend['enviadas_gsalida'] : 0);?></div>
																<h3>Gestion Salida</h3>
															</div>
														</a>
													</td>
													<td class="text-center">
													</td> 
													<td class="text-center">
													</td>
												</tr>
											</tbody>
										</table> 
									</div>
								</div>
							</div>


							<div class="panel panel-danger">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion-test-2-<?php echo md5($periodo_id); ?>" href="#collapseFour-2-<?php echo md5($periodo_id); ?>" class="collapsed">
											Prestamos Pendientes
										</a>
									</h4>
								</div>
								<div id="collapseFour-2-<?php echo md5($periodo_id); ?>" class="panel-collapse collapse">
									<div class="panel-body">
										<table class="table table-bordered table-hover table-striped responsive">
											<tbody>
												<tr>
													<td class="text-center">
														<div class="tile-stats tile-red">            
															<div class="icon"><i class="entypo-suitcase"></i></div>
															<div class="num" data-start="0" data-end="" data-postfix="" data-duration="1500" data-delay="0"><?php echo (isset($totalRegPend['prestamos_pendientes']) ?  $totalRegPend['prestamos_pendientes'] : 0);?></div>
															<h3>Prestados</h3>
														</div>
													</td>  
													<td class="text-center">

													</td>                      
													<td class="text-center">

													</td>
													<td class="text-center">

													</td>                      
													<td class="text-center">

													</td>
												</tr>
											</tbody>
										</table> 
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<hr />
			</div>
		</div>
	</div>
</div>