function cargaContenido(selectACargar)
{
	usuarios = 'usuario_id'
	actividades = 'actividades'	
	
	if(selectACargar == 'usuario_id'){
		selectAnterior = actividades;
	}			
			
	var valor = document.getElementById(selectAnterior).options[document.getElementById(selectAnterior).selectedIndex].value;
	var elemento;
	
	if(valor != ''){
		ajax = nuevoAjax();
		ajax.open('GET', '/simad/administracion.php/wf_instancia_bitacora/usuarios?id_seleccion='+valor+'&combo_box='+selectACargar, true);
		
		ajax.onreadystatechange = function ()
		{
				if(ajax.readyState == 1){
				
					elemento = document.getElementById(selectACargar);
                    //elementtr = document.getElementById('tr_usuarios');
					elemento.length = 0;
					
					var opcionCargando = document.createElement('option');
					opcionCargando.value = 0;
					opcionCargando.innerHTML = 'Cargando...';
					
					elemento.appendChild(opcionCargando);
					elemento.disable = true;
                    //elementtr.style.display = "none";
                    elementodiv=document.getElementById('contenedor_'+selectACargar);
                    elementodiv.style.display = "inline";
				}
				
				if(ajax.readyState == 4){
					
					document.getElementById('contenedor_'+selectACargar).innerHTML = ajax.responseText;
				
				}
		}
		ajax.send(null);
	}
	
	var x = 1;
	var y = null;
	
	while(x <= 2)
	{
		if(x == 1){
			comboActual = 'actividades';					
		}
		if(x == 2){
			comboActual = 'actividades';			
		}
		
		valor = document.getElementById(comboActual).options[document.getElementById(comboActual).selectedIndex].value;
		if(valor == '')
		{
			while(x <= 2) 
			{
				y = x+1;
				if(y == 2 ){
					comboActual_2 = 'usuario_id';
				}												
								
				elemento = document.getElementById(comboActual_2);
				elemento.length = 0;
				var opcionSelecciona = document.createElement('option'); 
				opcionSelecciona.value = 0; 
				opcionSelecciona.innerHTML='Seleccione Opcion...';
				elemento.appendChild(opcionSelecciona); 
				elemento.disabled = true;
                elementodiv=document.getElementById('contenedor_'+selectACargar);
                elementodiv.style.display = "none";                
				x++;
			}
		}
		x++;
	}
	
	
}