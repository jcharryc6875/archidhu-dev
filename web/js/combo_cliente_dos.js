function cargaContenido(subserie)
{
	var valor = subserie;
	if(valor==0)
	{
		combo = document.getElementById('tipo_documental');
		combo.length = 0;
		var nuevaOpcion = document.createElement('option'); 
		nuevaOpcion.value=0; 
		nuevaOpcion.innerHTML='Selecciona Unidad Documental...';
		combo.appendChild(nuevaOpcion);	
		combo.disabled=true;
	}
	else
	{
		ajax = nuevoAjax();
		ajax.open('GET', '/simad/clientes.php/transferencias_clientes/cargar?subserie_id='+valor, true);
		
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==1)
			{
				// Mientras carga elimino la opcion 'Elige pais' y pongo una que dice 'Cargando'
				combo=document.getElementById('tipo_documental');
				combo.length=0;
				var nuevaOpcion = document.createElement('option'); 
				nuevaOpcion.value=0; 
				nuevaOpcion.innerHTML = 'Cargando...';
				combo.appendChild(nuevaOpcion); 
				combo.disabled=true;	
			}
			if (ajax.readyState==4)
			{ 
				document.getElementById('contenedor_tipos').innerHTML = ajax.responseText;
				
			} 
		}
		ajax.send(null);
	}
}