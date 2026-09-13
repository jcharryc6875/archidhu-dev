<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');
use_helper('jQuery');
?>
<!-- Imported styles on this page -->
<link rel="stylesheet" href="<?php echo $path_theme; ?>/assets/js/aci-tree/css/aciTree.css"/>
<script src="<?php echo $path_theme; ?>/assets/js/aci-tree/js/jquery.aciPlugin.min.js"></script>
<script src="<?php echo $path_theme; ?>/assets/js/aci-tree/js/jquery.aciTree.min.js"></script>
<script src="<?php echo $path_theme; ?>/assets/js/toastr.js"></script>

<div class="mail-env">
    <div class="panel panel-gradient" data-collapsed="0">
        <div class="panel-heading">
    		<div class="panel-title">
    			Clasificar Documento
    		</div>    		
    		<div class="panel-options">
    			<a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
    			<a href="#" data-rel="reload"><i class="entypo-arrows-ccw"></i></a>
    		</div>
    	</div>
        <!-- Mail Body -->
		<?php 
			echo form_tag('transferencia/listExpAsync', array('name'=>'form1','method'=>'POST', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
			echo input_hidden_tag('localizacionunidaddocumental_id', $sf_params->get('localizacionunidaddocumental_id'));
			echo input_hidden_tag($field_name, $field_value);
        ?>
			<div id="bodyselect" class="mail-body" style="padding-left: 10px; background-color: white;">
				<div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
			</div>
		</form>

        <!-- Sidebar -->
        <div class="mail-sidebar" style="background-color: white;">
            <div class="panel-body no-padding" style="padding: 0px 0;">		
        		<div id="tree1" class="aciTree aciTreeFullRow" style="height: 350px;overflow-y: auto;"></div>		
        	</div>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
 	var $ = jQuery;
    var divInner = jQuery('.main-content').height();
    jQuery('#tree1').css('height', divInner+'px');
    //toastr.info(divInner+'px');
	jQuery('#tree1').aciTree({
		ajax:{
		    type: 'POST',
            url: '<?php echo url_for('transferencia/aciTree').'?branch='; ?>',            
            data: jQuery.param({ origen_transferencia:"<?php echo $origen_transferencia; ?>", <?php echo $field_name; ?>:"<?php echo $field_value; ?>"}) ,
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            cache:false,
            processData: false,
		},
		fullRow: true,
		radio: true,
        selectable: true
	}).on('acitree', function(event, api, item, eventName, options) {
		// tell what tree it is
		//var index = (jQuery(this).attr('id') == 'tree1') ? 0 : 1;
		switch (eventName) {
			case 'init': 
				break;
			case 'selected':			
				// Log select event
				var itemData = api.itemData(item);
				var itemId = api.getId(item);
                if(!api.isInode(item)){
					//console.log(api.isInode(item));
                    <?php 
                    	echo jq_remote_function(
							array(
								//'update'    => 'bodyselect',
								'update'    => null,
								'url'     => 'transferencia/searchExpediente',
								'with' => "'origen_transferencia=' + ".$origen_transferencia." + '&".$field_name."=".$field_value."' + '&itemId=' + api.getId(item) + '&inode=' + itemData.inode + '&itemType='+itemData.type",
								'failure' => "toastr.info('Ocurrio un error, Por favor intente de nuevo!')",
								'script' => 1,
								'success' => 'jQuery("#bodyselect").html(data);javascript:callFuncLoadMeta();'
						));
                    ?>
                }
				//console.log(itemData);
				//$events_log.val( "Selected Item: " + api.getId(item) + " [Props size: "+itemData.size+"; type: "+itemData.type+" ]\n" + $events_log.val());
				break;
		}
	});
});
jQuery('form').on('submit', function(event) {
	event.preventDefault();
	if(jQuery('form').valid()){
		<?php
			echo jq_remote_function(array(
					'update'   => 'bodyselect',
					'url'      => 'transferencia/listExpAsync',
					'loading'  => "javascript:jQuery.LoadingStructData();",
					'complete' => "javascript:jQuery.CloseLoadingStructData();",
					'form'     => true,
					'script'   => 1,
				));				
		?>
	}	
	//alert( "Valid: " + jQuery('form').valid() );
});
</script>