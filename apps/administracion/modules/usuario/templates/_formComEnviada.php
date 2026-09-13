<!-- INICIO PARTIAL -->
<div class="panel-body">
        <?php 
            echo form_tag('usuario/reasignarGestor', array('name'=>'form1', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')); 
            echo object_input_hidden_tag($com_enviada, 'getComenviadaId');
            echo input_hidden_tag('idUser');
            echo input_hidden_tag('cargousuarioId');
        ?>
        <!-- Informacion Detalle -->
        <div class="col-sm-12 col-md-12">
          <div class="row">					
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Radicado:</strong></p></div>
              <div class="col-sm-8">
                              <p><?php echo $com_enviada->getRadicado(); ?></p>
                          </div>
            </div>
                      <div class="col-sm-6">
              <div class="col-sm-5"><p><strong>Asunto:</strong></p></div>
              <div class="col-sm-7"><p><?php echo $com_enviada->getAsunto(); ?></p></div>
            </div>
          </div>
                  
          <div class="row">					
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Fecha Radicaci&oacute;n:</strong></p></div>
              <div class="col-sm-8">
                              <p><?php echo $com_enviada->getFechaCreacion(); ?></p>
              </div>
            </div>
            <div class="col-sm-6">
                <div class="col-sm-5"><p><strong>Entidad de Origen:</strong></p></div>
                <div class="col-sm-7"><p>
                  <?php 
                      echo '&nbsp;';
                  ?>
                </p></div>
              </div>
            </div>
                  
          <div class="row">					
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Dependencia Destino:</strong></p></div>
              <div class="col-sm-8">
                              <p><?php echo $com_enviada->getDependencia()->getNombre(); ?></p>
                          </div>
            </div>
                      <div class="col-sm-6">
              <div class="col-sm-5"><p><strong>Puntos de radicaci&oacute;n:</strong></p></div>
              <div class="col-sm-7"><p><?php echo $com_enviada->getRegional();?></p></div>
            </div>
          </div>

          <div class="row">					
            <div class="col-sm-6">
              <div class="col-sm-4"><p><strong>Usuario Asignado:</strong></p></div>
              <div class="col-sm-8">
                              <p>
                                <?php 
                                    // echo $com_enviada->getPrimaryKey(); 
                                    /**/
                                    $comenviadausuario = EnviadaUsuarioPeer::getCurrentUserAsig($com_enviada->getPrimaryKey());
                                    $usuario_id = $comenviadausuario->getUsuarioId();
                                    $usuario = UsuarioPeer::retrieveByPK($usuario_id);
                                    $nombre_usuario = $usuario->getNombre();

                                    echo $nombre_usuario; 
                                    
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
          echo component_user_multiple("paraUser","idUser","cargousuarioId",array('url'=>'usuario_firma/selectUserSearching', 'byfilterdep'=>$dependencia_asig, 'bytipoprocesocom'=>$tipoprocesocom_id, 'caption'=>'Usuario Destino(<span class="ctrlreq">*</span>)', 'buttontitle'=>'Buscar Destinatario','class'=>'form-control input-sm required','values'=>null,'values_text'=>null,'values_cuid'=>null,'option'=>0,'maximumSelectionSize'=>1));
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
