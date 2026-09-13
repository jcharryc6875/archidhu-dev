<!-- INICIO PARTIAL -->
<div class="panel-body">
        <?php 
            echo form_tag('usuario/reasignarGestor', array('name'=>'form1', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')); 
            echo object_input_hidden_tag($com_interna, 'getCominternaId');
            echo input_hidden_tag('idUser');
            echo input_hidden_tag('cargousuarioId');
        ?>
        <!-- Informacion Detalle -->
        <div class="col-sm-12 col-md-12">
			<div class="row">					
				<div class="col-sm-6">
				  <div class="col-sm-4"><p><strong>Radicado:</strong></p></div>
				  <div class="col-sm-8">
					<p><?php echo $com_interna->getRadicado(); ?></p>
				  </div>
				</div>
				<div class="col-sm-6">
				  <div class="col-sm-5"><p><strong>Asunto:</strong></p></div>
				  <div class="col-sm-7"><p><?php echo $com_interna->getReferencia(); ?></p></div>
				</div>
			</div>
                  
			<div class="row">					
				<div class="col-sm-6">
				  <div class="col-sm-4"><p><strong>Fecha Radicaci&oacute;n:</strong></p></div>
				  <div class="col-sm-8">
					<p><?php echo $com_interna->getFechaCreacion(); ?></p>
				  </div>
				</div>
				<div class="col-sm-6">
					<div class="col-sm-5"><p><strong>Tipo:</strong></p></div>
					<div class="col-sm-7"><p>
					  <?php 
						  echo $com_interna->getTipoComInterna(); 
					  ?>
					</p></div>
				</div>
			</div>
                  
          <div class="row">					
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Dependencia Destino:</strong></p></div>
              <div class="col-sm-8">
                              <p><?php echo $com_interna->getDependencia()->getNombre(); ?></p>
                          </div>
            </div>
                      <div class="col-sm-6">
              <div class="col-sm-5"><p><strong>Puntos de radicaci&oacute;n:</strong></p></div>
              <div class="col-sm-7"><p><?php echo $com_interna->getRegional();?></p></div>
            </div>
          </div>

          <div class="row">					
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Usuario Asignado:</strong></p></div>
              <div class="col-sm-8">
				  <p>
					<?php 
						$cominternausuario = CominternaUsuarioPeer::getCurrentUserAsig($com_interna->getPrimaryKey());
						echo $cominternausuario != null ? $cominternausuario->getUsuario()->getFullNombre() : "";
					?>
				  </p>
			  </div>
            </div>
            <div class="col-sm-6">
              
            </div>
          </div>


        </div>
        <hr />

        <?php
          $dependencia_asig = isset($listcom_users['dependencia_asignado']) ? $listcom_users['dependencia_asignado'] : 0;
          echo component_user_multiple("paraUser","idUser","cargousuarioId",array('url'=>'usuario_firma/selectUserSearching', 'byfilterdep'=>$dependencia_asig, 'caption'=>'Usuario Destino(<span class="ctrlreq">*</span>)', 'buttontitle'=>'Buscar Destinatario','class'=>'form-control input-sm required','values'=>null,'values_text'=>null,'values_cuid'=>null,'option'=>0,'maximumSelectionSize'=>1));
        ?>        
        
        <div class="form-group">          
          <!-- Observaciones -->
          <label for="lbCreador" class="col-sm-1 control-label">Observaciones:</label>
          <div class="col-sm-8">                       
                <?php 
                    echo textarea_tag('detalles',$creador, array ('class'=>'form-control input-sm required', 'data-validate' => "maxlength[50]", 'placeholder' => 'Maximo 50 Caracteres')); 
                ?>
          </div>
        </div>

        <div class="form-group">
          <!-- Botonera --> 
          <div class="col-sm-offset-4 col-sm-5">
            <button type="button" id="asignar" class="btn btn-success">Asignar Comunicaci&oacute;n</button>          
          </div>
        </div>
        <div class="clear"></div>
      </form>
      <!-- FIN PARTIAL -->