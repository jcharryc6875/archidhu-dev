<?php
$form_post = 'form'.md5(time());
?>
<div class="col-md-4">
    <!-- Contenedor Pagina -->
    <div class="panel panel-info" data-collapsed="0">  
    <div class="panel-heading">
        <div class="panel-title">
			Busqueda C&oacute;digo de Verificaci&oacute;n(CSV):
        </div>    
    </div>
        <!-- Contenedor Contenido Formulario-->
        <div class="panel-body">
            <form id="formHash" name="formHash" method="post" enctype="multipart/form-data" action="" class="form-wizard validate">
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
                            <label class="control-label">Para comprobar el c&oacute;digo seguro verificaci&oacute;n y ver el documento asociado, ingrese el CSV recibido y presione el botón "Validar"</label>
                            <textarea name="strhash" id="strhash" rows="4" cols="50" class='form-control input-sm required' placeholder= 'Ingresar o pegar aqui los 64 digitos del certificado del documento'></textarea>
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
                            <!-- <input type="submit" name="submit01" value="Validar HASH" id="" class="btn btn-primary"> -->
                            <button type="button" id="searching02" class="btn btn-success btn-icon">Validar<i class="entypo-search"></i></button>
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
        jQuery('#searching02').on("click", function(event)
        {
            event.preventDefault();
            const form = jQuery('#formHash');
            if(form.valid())
            {
				var data03 = new FormData(jQuery('form#formHash')[0]);
                javascript:jQuery.LoadingStructData(); 
                jQuery.ajax(
                {
					headers: createHeaderData(),
                    type: 'POST',           
                    url: 'resultadoHash.php',            
                    data: data03, //datos serializados.
                    cache: false, 
					contentType: false, 
					dataType: "json", 
					processData: false,
                    success: function(response)
                    {
                        console.log(response);
                        jQuery('#<?php echo md5('strhash');?>').html(response);
                        javascript:jQuery.CloseLoadingStructData(); 
                        toastr.success("consulta exitosa!!!"); 
                        IconCaptcha.reset();   
                    },
                    error: function(jqXHR, textStatus, errorThrown) 
                    {
						console.log('RESULTADO ARROJADO:   -->>' + jqXHR.status); // da... OK 
                        if (jqXHR.status == 404) 
                        { 
                            jQuery('#<?php echo md5('strhash');?>').html('');
                            javascript:jQuery.CloseLoadingStructData();  
                            toastr.error("Debe resolver primero el CAPTCHA!!");
                        }
                        else if(jqXHR.status == 400)
                        {
                            jQuery('#<?php echo md5('strhash');?>').html(jqXHR.responseText);
                            javascript:jQuery.CloseLoadingStructData();  
                            toastr.error("ERROR EN EL INGRESO DE LOS DATOS!!!");
                        }  
                        else if(jqXHR.status == 200)
                        {
                            jQuery('#<?php echo md5('strhash');?>').html(jqXHR.responseText);
                            javascript:jQuery.CloseLoadingStructData();  
                            toastr.success("consulta exitosa!!!"); 
                        }   
                        else 
                        {
                            jQuery('#<?php echo md5('strhash');?>').html('');
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