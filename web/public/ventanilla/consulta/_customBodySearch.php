<?php
$form_post = 'form'.md5(time());
?>

<div class="col-md-4">
    <!-- Contenedor Pagina -->
    <div class="panel panel-info" data-collapsed="0">  
    <div class="panel-heading">
        <div class="panel-title">
        Busqueda por Numero de Radicado
        </div>    
    </div>

        <!-- Contenedor Contenido Formulario-->
        <div class="panel-body">      
            
            <form action="action.php" method="post" name='formBodySearch' id='formBodySearch' role='form' class='form-groups-bordered validate'>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <!-- Id Expediente -->
                            <label class="control-label">N&uacute;mero Radicado:</label>
                            <input type="text" name="radicado" class='form-control input-sm' placeholder= 'Buscar por el radicado de la comunicaci&oacute;n'>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <!-- The IconCaptcha will be rendered in this element - REQUIRED -->
                            <div class="iconcaptcha-widget" data-theme="light"></div>
                            <!-- Additional security feature to prevent CSRF. -->
                            <?php echo \IconCaptcha\Token\IconCaptchaToken::render(); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <!-- Botonera -->
                        <div class="col-sm-offset-1 col-sm-10">
                            <button type="button" id="searching" class="btn btn-success btn-icon">Consultar Radicado<i class="entypo-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>    
            </form>
        </div>

    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function()
{
    jQuery('#searching').on( "click", function(event){
        event.preventDefault();
        const form = jQuery('#formBodySearch');
        javascript:jQuery.LoadingStructData();   
        jQuery.ajax({
            type: 'POST',
            url: 'resultado.php',            
            data: form.serialize(), //serializar.
            //contentType: 'application/x-www-form-urlencoded; charset=UTF-8', 
            //cache:false,
            //processData: false,
            success: function(response)
            {
                //console.log('<?php echo md5('strlistinfo');?>');
                //jQuery('#<?php // echo md5('strlistinfo');?>').html(response);
                jQuery('#<?php echo md5('strlistinfo');?>').html(response);
                javascript:jQuery.CloseLoadingStructData();
                toastr.success("consulta exitosa!!!"); 
                IconCaptcha.reset();   
            },
            error: function(jqXHR, textStatus, errorThrown) 
            {
                if (jqXHR.status == 404) 
                {
                    //alert(jqXHR.responseText);
                    javascript:jQuery.CloseLoadingStructData();  
                    toastr.error("Debe resolver primero el CAPTCHA!!");
                }
                else if(jqXHR.status == 400)
                {
                    javascript:jQuery.CloseLoadingStructData();  
                    toastr.error("Debe ingresar el numero de Radicado!!");
                } 
                else 
                {
                    javascript:jQuery.CloseLoadingStructData();  
                    toastr.error("Error Interno del Servidor!");
                }
            }
        });
	});
});
</script>