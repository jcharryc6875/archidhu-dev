<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery'); 
echo input_hidden_tag('tipodescarte', '0');
?>
<?php
foreach($tipodescarte as  $descarte):
    $nombre_campo = $descarte->getPrimaryKey().'_'.mb_strtolower(str_replace(" ","_",$descarte->getDescripcion()));
?>
  <div class="col-sm-2">    
    <div id="tdcheckboxes">
      <label>        
        <?php 
            echo checkbox_tag($nombre_campo, $descarte->getPrimaryKey(), false, array('onchange'=>'chequedChanged(this);'));
            print "&nbsp;".$descarte->getDescripcion();
        ?>
      </label>
    </div>
  </div>
<?php
endforeach;
?>
<script type="text/javascript">
	function chequedChanged(){
        var selected = [];
        jQuery('div#tdcheckboxes input[type=checkbox]').each(function() {
           if (jQuery(this).is(":checked")) {
               selected.push(jQuery(this).attr('name'));               
           }
        });
        
        if (selected.length > 0){
            jQuery('#tipodescarte').val("1");
        }else{
            jQuery('#tipodescarte').val("0");
        }        
    }
</script>