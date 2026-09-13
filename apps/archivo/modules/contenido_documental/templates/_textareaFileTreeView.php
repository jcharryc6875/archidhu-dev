<div class="panel-body border-top">
    <textarea class="form-control" id="events_log" rows="5" placeholder="Logged Events"></textarea>
</div>

<!-- HABILTAR EL BLOQUE RESALTADO AQUI ABAJO EN EL JQUERY DE listFolderTreeSuccess.php PARA QUE FUNCIONE EL TEXTAREA --> 
<!-- 
<script type="text/javascript">
    jQuery(document).ready(function() {
        var $ = jQuery,
            $events_log = $("#events_log");

        $('#tree1').aciTree({
            ajax: {
                url: '<?php // echo url_for('/archivo.php/contenido_documental/dataFolderTree'); ?>?branch=',
                data: {unidaddocumental_id: '<?php // echo $unidaddocumental_id; ?>'}
            },
            fullRow: true,
            checkbox: false,
            // === COLUMNAS ===
            itemHook: function(parent, item, itemData, level) {
                if (!itemData) return;
                // Etiqueta especial para archivos
                // Agregamos evento click a los nodos tipo file
                if (itemData.type === 'file' && itemData.data) 
                {
                    this.setLabel(item, {label: itemData.data.tipo_doc});

                    $(item).find('.aciTreeText').off('click').on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Mostrar un loader temporal
                        //$('#detalleContenido').html('<em>Cargando detalle...</em>');

                        // Hacemos la petición AJAX al nuevo action Symfony
                        $.ajax({
                            url: '<?php // echo url_for('/archivo.php/contenido_documental/showFileTree'); ?>',
                            // url: '<?php //echo url_for("contenidoUnidadDocumental/showContenidoDoc"); ?>',
                            method: 'POST',
                            data: { 
                                contenidounidaddocumental_id: parseInt(itemData.id.match(/\d+/)[0], 10),
                                unidaddocumental_id: '<?php // echo $unidaddocumental_id; ?>', 
                                modulo: '<?php // echo $modulo; ?>' 
                            },
                            success: function(response) 
                            {
                                $('#<?php // echo md5('contudocftreeinfo');?>').html(response);
                            },
                            error: function() 
                            {
                                $('#<?php // echo md5('contudocftreeinfo');?>').html('<span style="color:red;">Error al cargar el detalle.</span>');
                            }
                        });
                    });


                }

                if (itemData.type === 'folder') 
                {
                    $(item).find('.aciTreeText').off('click').on('click', function(e) {


                        // ⚠️ Importante: primero, permitimos el toggle normal
                        const api = $('#tree1').aciTree('api');
                        const currentItem = api.itemFrom(e.target);

                        if (currentItem && api.isInode(currentItem)) 
                        {
                            if (api.isOpen(currentItem)) 
                            {
                                api.close(currentItem);
                            } 
                            else 
                            {
                                api.open(currentItem);
                            }
                        }

                        $('#<?php // echo md5('contudocftreeinfo');?>').html(`<div class="lazyview"></div>`);

                    });
                }

                this.itemData(item, itemData);
            },
            //console.log('EL ORIGEN ES: ' + props.origen);
        })
        .on('acitree', function(event, api, item, eventName, options) {
            switch (eventName) {
                case 'init':
                    api.open(api.first(), {
                        success: function(item) {
                            this.select(this.first(item));
                        }
                    });
                    $('#tree1').focus();
                    break;

                case 'selected':
                    
                    // ESTE ES EL TEXTAREA!!
                    var itemData = api.itemData(item);
                    if (itemData.data) {
                        let info = "📄 " + (itemData.label || 'Documento') + "\n";
                        $.each(itemData.data, function(key, value) {
                            info += key + ": " + value + "\n";
                        });
                        $events_log.val(info + "\n" + $events_log.val());
                    } else {
                        $events_log.val("Selected Item: " + api.getId(item) + " [Props type: "+itemData.type+"]\n" + $events_log.val());
                    }
                    // ESTE ES EL TEXTAREA!!    
                    
                    
                    break;
            }
        });
    });
</script> 
-->