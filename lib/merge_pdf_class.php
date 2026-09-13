<?php
/*
Clase de ejecucion.

usa:

$disk_unit : unidad donde esta la carpeta /Imagenes
$alterdir  : si esta a 1 suprime /xampp/ de la cadena de instalacion

*/

class merge_pdf {

function run_command($sbcommand , $work_dir)
{
$nudebug=0;    
$alterdir=0;
$work_dir .= "/";
$work_dir=str_replace("\/","\ ",$work_dir);
$work_dir=str_replace("//","/",$work_dir);
//echo trim($work_dir);
//se crea un identificador(nombre) para el archivo .bat
$batch_file= sprintf( "exe%s.bat", md5( rand( 100000, 99999 ). date("YmdGis") ) );

if($nudebug) echo $batch_file . "<p>";

//se escribe en un archivo q sera el .bat para ejecutar
file_put_contents(trim($work_dir) . $batch_file, $sbcommand);

if($nudebug){
 echo $sbcommand . "<p>";
}
//se asigana el directorio donde se encuentra el archivo .bat
$mycmd=trim($work_dir).$batch_file ;
//se ejecuta el archivo .bat
$last_line = exec($mycmd);//system($mycmd, $retval);

if($nudebug) echo "<pre>{$last_line}</pre>";

//erase tracks:
if(! $nudebug){
	//se elimona el archivo .bat para no dejar archivos q puedan llenar nuestro disco duro
    unlink(trim($work_dir).$batch_file);
}

 return $last_line;
}



function create_command($cmd, $input_file,$outputfile="tmp.pdf", $position=1, $numPag=0, $input_dir="c:/tmp", $output_dir="c:/tmp"){
/*
$position contiene la posicion en el archivo de 0 a N-1
si $position >0 es adicionar
si $position <0 es eliminar
si $position == 65536 es contar.
*/
$alterdir=0;
$nudebug=0;

//fijamos el directorio donde se encuentra el pdftk.exe
if( $alterdir){
$work_dir="/projects/simad/lib/exec";
}else{
$work_dir="/projects/simad/lib/exec";
}
$disk_unit='C:\Imagenes';  /*fija la unidad donde esta instalada la carpeta de imagenes */

//eliminamos los slahs dobles
$input_file=str_replace("//","/",$input_file);
$output_file=str_replace("//","/",$outputfile);
$input_file=str_replace("//","/",$input_file);
$output_dir=str_replace("//","/",$output_dir);

//se crea un nombre para el archivo pdf temporal donde se encuentra el nuevo documento
$tmp_file =  md5( rand(100000,99999)  . date("YmdGis") );


if($position>0){//verificamos q hallan paginas en el documento
    //Adicionar la imagen antes de $position al archivo.     
    if($position==1){//verificamos si se quiere insertar al principio del documento
    //para insertar al Principio del Pdf		
    $sb_commandline=sprintf("%s %s %s %s %s \r\n",     
						    $cmd,' A='.$input_file,
							' B='.$outputfile, " cat A B output ",
							$disk_unit.'\temp'.$tmp_file.'.pdf'/*, ' DEL "'.$outputfile*/);
	//exit();
    }elseif(trim($position) == trim($numPag)){//verificamos q se quiera insertar al final del documento		 	
			$sb_commandline=sprintf("%s %s %s %s %s \r\n",     
						    $cmd,' A='.$input_file,
							' B='.$outputfile, " cat B A output ",
							$disk_unit.'\temp'.$tmp_file.'.pdf'/*, ' DEL "'.$outputfile*/);							
		 }else{//else si se ha digitado una pagina especifica para insertar el pdf
		   $position_end = trim($position);//eliminamos los espacios del posicion digitada
		   $page_insert  = trim($position)-1;//restamos un digito para insertar en la posicion digitada			       
           $sb_commandline=sprintf("%s %s %s %s %s \r\n",     
						    $cmd,' A='.$input_file,
							' B='.$outputfile, " cat B1-".$page_insert." A B".$position_end."-end output ",
							$disk_unit.'\temp'.$tmp_file.'.pdf'/*, ' DEL "'.$outputfile*/);
		   //exit();                                
        }	            
}

$sb_commandline=str_replace("//","/",$sb_commandline);
$sb_commandline=str_replace("//","/",$sb_commandline);
$sb_commandline=str_replace("//","/",$sb_commandline);
//if($nudebug) echo "<p>" . $sb_commandline ."</p>";
 /*return $sb_commandline;*/
$this->run_command($sb_commandline,$disk_unit); 
 
  return $tmp_file;
 }

 function getNumeroPaginas($filename,$outFile,$work_dir)
 {
    $sb_commandline=sprintf("pdftk ".$filename." dump_data output ".$outFile);
    $this->run_command($sb_commandline,$work_dir);
 }

 function deletePage($outputfile,$page_delete,$disk_dir,$total_paginas)
 {
 	$cmd="/projects/simad/lib/exec/pdftk"; 	
 	$position_end = trim($page_delete)+1;//eliminamos los espacios de la posicion digitada
	$page_insert  = trim($page_delete)-1;//restamos un digito para insertar en la posicion anterior	
	$tmp_file =  md5( rand(100000,99999)  . date("YmdGis") );
	if($page_insert == 0){
		$sb_commandline = sprintf("%s %s %s",$cmd,' A='.$outputfile, " cat A".$position_end."-end");
	}else{
		$sb_commandline = sprintf("%s %s %s",$cmd,' A='.$outputfile, " cat A1-".$page_insert);			           
		if($total_paginas != $page_delete){
			if($position_end == $total_paginas){
				$sb_commandline .= sprintf("%s"," A-end  ");
			}else{		
				$sb_commandline .= sprintf("%s"," A".$position_end."-end  ");
			}			
		}
	}
    $sb_commandline .= sprintf("%s",' output '.$disk_dir.'temp'.$tmp_file.'.pdf');
	//exit();    						    
    $this->run_command($sb_commandline,$disk_dir);
    
    return $tmp_file;
 }
 
 function extracPag($fileOriginal,$outputfile,$rango_pages,$disk_dir)
 {
 	$cmd="/projects/simad/lib/exec/pdftk"; 	 		
	//$tmp_file =  md5( rand(100000,99999)  . date("YmdGis") );	
	$sb_commandline = sprintf("%s %s %s",$cmd,' A='.$fileOriginal, " cat A".$rango_pages);
		
    $sb_commandline .= sprintf("%s",' output '.$disk_dir.$outputfile.'.pdf');
	//exit();    						    
    //$this->run_command($sb_commandline,$disk_dir);
	return $sb_commandline;        
 }
 
 function mergePage($fileOriginal,$outputfile,$rango_pages,$disk_dir)
 {
 	$cmd="/projects/simad/lib/exec/pdftk"; 	 		
	//$tmp_file =  md5( rand(100000,99999)  . date("YmdGis") );	
	$sb_commandline = sprintf("%s %s %s",$cmd,' A='.$fileOriginal, " cat A".$rango_pages);
		
    $sb_commandline .= sprintf("%s",' output '.$disk_dir.$outputfile.'.pdf');
	//exit();
	return $sb_commandline;    						    
    //$this->run_command($sb_commandline,$disk_dir);        
 }
 
}

?>
