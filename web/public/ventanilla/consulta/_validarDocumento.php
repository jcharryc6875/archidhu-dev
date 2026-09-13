<?php
$form_post = 'form'.md5(time());
?>
<div class="col-md-4">
	<!-- Contenedor Pagina -->
	<div class="panel panel-info" data-collapsed="0">   
		<div class="panel-heading">
			<div class="panel-title">
				Valicaci&oacute;n de Documentos
			</div>    
		</div> 
        <!-- Contenedor Contenido Formulario-->  
        <div class="panel-body">      
            <form id="formDoc" name="formDoc" method="post" enctype="multipart/form-data" action="" class="form-wizard validate">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <!-- Id Expediente -->
                            <label class="control-label">N&uacute;mero Radicado:</label>
                            <input type="text" name="radicado" id="radicado" class='form-control input-sm required' placeholder= 'Buscar por el radicado de la comunicaci&oacute;n'>
                        </div>
                    </div>
                </div>
                <div class="row"> 
                    <div class="col-sm-12">
                        <div class="form-group">
                            <!-- Id Expediente -->
                            <label class="control-label">Para comprobar la validez del documento seleccione el archivo que desee validar y presione el botón "Validar"</label>
                            <input name="adjunto" id="adjunto" placeholder= 'Seleccione el archivo que quiere validar' class="realinputvalue form-control input-sm required" multiple type="file">
                            <!-- <input name="file" id="file" class="realinputvalue form-control input-file" multiple type="file"/> -->
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
                            <!-- <input type="submit" name="aveneva" value="Validar documento" id="MainContent_CmdValidarDOC" class="btn btn-primary"> -->
                            <button type="button" id="searching03" class="btn btn-success btn-icon">Validar Documento<i class="entypo-search"></i></button>
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
        jQuery('#searching03').on("click", function(event)
        {
            event.preventDefault();
            const form = jQuery('#formDoc');
            if(form.valid())
            {
				var data02 = new FormData(jQuery('form#formDoc')[0]);
                javascript:jQuery.LoadingStructData();
                jQuery.ajax(
                {
					headers: createHeaderData(),
                    type: 'POST',           
                    url: 'resultadoDocumento.php',            
                    data: data02, //datos serializados.
                    cache: false, 
					contentType: false, 
					dataType: "json", 
					processData: false,
                    success: function(response)
                    {
                        console.log(response);
                        jQuery('#<?php echo md5('strlistinfodoc2');?>').html(response);
                        javascript:jQuery.CloseLoadingStructData(); 
                        toastr.success("consulta exitosa!!! ENTRO A SUCCESS!!!"); 
                        IconCaptcha.reset();   
                    },
                    error: function(jqXHR, textStatus, errorThrown) 
                    {
                        if (jqXHR.status == 404) 
                        {
                            javascript:jQuery.CloseLoadingStructData();  
                            toastr.error("Debe resolver primero el CAPTCHA!!");
                        }
                        else if(jqXHR.status == 400)
                        {
                            javascript:jQuery.CloseLoadingStructData();  
                            toastr.error("Debe ingresar el numero de Radicado!!");
                        } 
                        else if(jqXHR.status == 200)
                        {
                            jQuery('#<?php echo md5('strlistinfodoc2');?>').html(jqXHR.responseText);
                            javascript:jQuery.CloseLoadingStructData();  
                            toastr.success("consulta exitosa!!!"); 
                        }  
                        else 
                        {
                            javascript:jQuery.CloseLoadingStructData();  
                            toastr.error("Error Interno del Servidor!");
                        }
                    },
                    complete: function(data)
                    {
                        IconCaptcha.reset();
                    }, 
                });
            }
        });
    });
</script>

