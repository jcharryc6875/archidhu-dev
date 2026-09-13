function cargaContenido(selectACargar, permiso)
{    
	dependencias = 'dependencias'
	series = 'series'
	subseries = 'subseries'
		
	if(selectACargar == 'series'){
		selectAnterior = dependencias;
	}
	
	if(selectACargar == 'subseries'){
		selectAnterior = series;
	}
	
    var valor = document.getElementById(selectAnterior).options[document.getElementById(selectAnterior).selectedIndex].value; 
    
	var elemento;
	
	if(valor != 0){
		ajax = nuevoAjax();
		ajax.open('GET', '../unidad_documental/cargar?id_seleccion='+valor+'&combo_box='+selectACargar+'&permiso='+permiso, true);
		
		ajax.onreadystatechange = function ()
		{
				if(ajax.readyState == 1){
				
					elemento = document.getElementById(selectACargar);
					elemento.length = 0;
					
					var opcionCargando = document.createElement('option');
					opcionCargando.value = 0;
					opcionCargando.innerHTML = 'Cargando...';
					
					elemento.appendChild(opcionCargando);
					elemento.disable = true;
					
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
		  
			comboActual = 'dependencias';		
		}
		if(x == 2){
			comboActual = 'series';
		}
		
		valor = document.getElementById(comboActual).options[document.getElementById(comboActual).selectedIndex].value;
		if(valor == 0)
		{
			while(x <= 2) 
			{
				y = x+1;
				if(y == 2){
					comboActual_2 = 'series';				
				}
				if(y == 3){
					comboActual_2 = 'subseries';
				}
				
				elemento = document.getElementById(comboActual_2);
				elemento.length = 0;
				var opcionSelecciona = document.createElement('option'); 
				opcionSelecciona.value = 0; 
				opcionSelecciona.innerHTML='Selecciona opcion...';
				elemento.appendChild(opcionSelecciona); 
				elemento.disabled = true;
				x++;
			}
		}
		x++;
	}
	
	
}