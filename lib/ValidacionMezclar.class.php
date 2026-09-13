<?php

/**
 * @author 
 * @copyright 2009
 */
require_once('exec_class.php');
require_once('merge_pdf_class.php');

class UtilValidacion{


  public function cuentaPaginas($formatoDigit,$fileName)
  {
  	 $dirRaiz = ParametroPeer::retrieveByPk(17);
  	 $work_dir = $dirRaiz->getValortexto();
	 $numero_paginas = "";   
	  	 if($formatoDigit == 'pdf'){
			$merge_all = new merge_pdf();
			$merge_all->getNumeroPaginas($fileName,$work_dir."temp.txt",$work_dir);
	     	$vlineas = file($work_dir."temp.txt");
	     	/* Podemos mostrar / trabajar con todas las líneas:*/
         	foreach ($vlineas as $sLinea){
            	$tempLinea = explode(":",$sLinea);
             	if($tempLinea[0] == "NumberOfPages")
                	$numero_paginas = $tempLinea[1];
        	}     	
	     	/*****************************************************/
	     	unlink($work_dir."temp.txt");
		 }elseif($formatoDigit == 'tif' || $formatoDigit == 'tiff'){		   	
		   	$objExec3 = new command_exec();
    		$outputfile3= basename($fileName);    		    
    		$batch = $objExec3->create_command("tiffcount",  $outputfile3, $outputfile3 ,-65536);
    		$numero_paginas=file_get_contents($dirRaiz->getValortexto() .$outputfile3 .".count");
    
    		unlink($dirRaiz->getValortexto() .$outputfile3 .".count");
    		//unlink($dirRaiz->getValortexto().$batch);
		 }
	 return trim($numero_paginas);	 
  }
  	
  public function validarFolios($matriz_paginas,$numero_paginas="", $cod_barras="")
  {			 
	sort($matriz_paginas);			
	$rango_falta_cons = "";
	$rango_falta = "";
	$inicia_cont  = 0;
	$inicia_temp = "";			
	for($j = 0; $j < count($matriz_paginas); $j++){
		if($matriz_paginas[$j][0]==$inicia_cont+1){
			$inicia_cont=$matriz_paginas[$j][1];
		}
		else{
			$inicia_cont++;
			$rango_falta .= "Falta El Rango Paginas ".$inicia_cont."-".($matriz_paginas[$j][0]-1)." Para La Unidad Documental Con Codigo Barras ".$cod_barras."\n";
			for($k = $inicia_cont;$k < $matriz_paginas[$j][0];$k++ ){
				$rango_falta_cons.=$k.",";
			}
		}
	}
	//echo trim($matriz_paginas[$j-1][1]);
	//echo trim($numero_paginas);			
	//if(trim($matriz_paginas[$j-1][1]) < trim($numero_paginas)){							    		
		if(trim($matriz_paginas[$j-1][1]) < trim($numero_paginas)){
			$rango_falta .= "Falta El Rango Paginas ".($matriz_paginas[$j-1][1]+1)."-".$numero_paginas." Para La Unidad Documental Con Codigo Barras ".$cod_barras."\n";
			for($k = $matriz_paginas[$j-1][1]+1;$k <= $numero_paginas;$k++ ){
				$rango_falta_cons .= $k.",";
			}
		}else{
			$rango_falta.="Error Hay Mas Folios Relacionados Que Los Digitalizados"."\n";
		}								
	//}		
    return $rango_falta;		 		   		        	        		                  	
  }
  
  public function validarPaginas($matriz,$rango,$numero_paginas="")
  {    		
  	if($rango[0] > $rango[1]){
		return false;
	}
	
	
	if(trim($rango[1]) > trim($numero_paginas)){
		return false;
	}
	
	if(!is_numeric($rango[0])){
		return false;
	}
	
	if(!is_numeric($rango[1])){
		return false;
	}
	
  	$b = 0;
  	$length = count($matriz);  	
  	$resp = true;  	  		
	
  	if($length != 0){
  	  sort($matriz);
  	  for($i = 0; $i < $length; $i++){
  	  	$col1 = 0;
  	  	$col2 = 1;		
		if( $rango[0] < $matriz[$i][$col1] || $rango[0] > $matriz[$i][$col2]){				
			if($rango[1] < $matriz[$i][$col1] || $rango[1] > $matriz[$i][$col2]){			   
			   $resp = true;
			}else{			  
			   $resp = false;
			}			    	
		}else{		    
			$resp = false;
		}								
	  }	  	
	}	
	return $resp;			
  }
  
  
  function time_start() {
	global $starttime;
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$starttime = $mtime;
  }
 
  function time_end() {
	global $starttime;
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	return ($mtime - $starttime);
  }
}

/*
sort($matriz_existe);
			print_r($matriz_existe);
			$rango_falta = "";
			$inicia_sig  = 1;
			$inicia_temp = "";
			$contfolios=0;
			$foliosFaltantes="";
			for($j = 0; $j < count($matriz_existe); $j++){
					if($matriz_existe[$j][0]==$contfolios+1){
						$contfolios=$matriz_existe[$j][1];
					}
					else{
						 $contfolios++;
						//$foliosFaltantes.=$contfolios.",";
						for($k = $contfolios;$k < $matriz_existe[$j][0];$k++ ){
					    $foliosFaltantes.=$k.",";
				        }
					}
				}
			if($matriz_existe[$j-1][1]!=241){	
			   if($matriz_existe[$j-1][1]<241){
				   for($k = $matriz_existe[$j-1][1]+1;$k <=241;$k++ ){
					    $foliosFaltantes.=$k.",";
				   }
				}else{
					$foliosFaltantes.="HAY MAS FOLIOS RELACIONADOS QUE LOS DIGITALIZADOS";
				}								
			}
			echo $foliosFaltantes;
*/
?>