<?php
        require_once('../lib/nusoap-1.124/lib/nusoap.php');		
        //$cliente = new nusoap_client('http://<ipServidor>/<ruta>/servicio.php');        
		//$cliente = new nusoap_client('http://201.245.77.198/simad/ws/servicio/service_search_img.php');
		$cliente = new nusoap_client('http://192.9.200.173/simad/ws/servicio/service_search_img.php');
		$resultado = $cliente->call('ConsultaPublicacionesWeb', array('no_radicado' => '412775'));
		//$array_restored_from_db = unserialize(base64_decode($resultado));
		//var_dump($resultado);exit;
		//*********************************************************************************************        
		$html = "<html><head>
		<!--meta charset='utf-8'-->
		<meta charset='iso-8859-1'>
		<meta http-equiv='X-UA-Compatible' content='IE=edge'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<meta name='description' content='SIMAD - Sistema Integrado de Administracion Documental'>
		<meta name='author' content='Aurea SAS'>
		<title>SIMAD.::.Notificaciones por aviso</title><table border='1'>
		<head/>
		<body>
		<thead>
			<tr>
			  <th>Imagen</th>
			  <th>Radicado</th>
			  <th>Destinatario</th>
			  <th>Ciudad</th>
			  <th>Fecha Pqr</th>		  
			  <th>Nro Pqr</th>
			  <th>Fecha Publicaci&oacute;n</th>		  
			  <th>Fecha Vencimiento</th>
			</tr>
		</thead>
		<tbody>";
		//var_dump($resultado);exit;
		if(count($resultado)){
			foreach ($resultado as $clave => $subitem){		
				$detailslist = explode(";", $subitem);
				$html .= "<tr>";
				foreach ($detailslist as $subclave => $subvalor){
					if($subclave == 0){
						if($subvalor != "none"){
							$html .= '<td style="text-align: center;"><a target="_new" href="'.$subvalor.'">
								<img alt="Digitalizado" border="0" width="32" height="32" align="middle" src="/simad/images/ico_view_publish.png"></a></td>';
						}else{
							$html .= "<td>".$subvalor."</td>";
						}
					}else{
						$html .= "<td>".$subvalor."</td>";
					}
				}
				$html .= "</tr>";
			}
		}
        $html .= "</tbody></table></body></html>";
        echo $html;
        //*********************************************************************************************
?>