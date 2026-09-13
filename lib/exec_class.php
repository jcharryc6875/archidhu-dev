<?php
/*
Clase de ejecucion.

usa:

$disk_unit : unidad donde esta la carpeta /Imagenes
$alterdir  : si esta a 1 suprime /xampp/ de la cadena de instalacion

*/

class command_exec {

function run_command($sbcommand , $work_dir="C:/Imagenes")
//function run_command($sbcommand , ///$work_dir="/apache/htdocs/simad/exec")
{
$nudebug=0;    
$alterdir=0;

if( $alterdir){
    $work_dir="C:/Imagenes";
}

$batch_file= sprintf( "exe%s.bat", md5( rand( 100000, 99999 ). date("YmdGis") ) );

if($nudebug) echo $batch_file . "<p>";
//echo $work_dir ."/" . $batch_file, $sbcommand;
//echo $sbcommand;
file_put_contents($work_dir ."/" . $batch_file, $sbcommand);
//file_put_contents($work_dir ."/" . //$batch_file, "pause");

if($nudebug){
 echo $sbcommand . "<p>";
}
$mycmd = $work_dir . "/" . $batch_file ;
$last_line = exec($mycmd);//system($mycmd, $retval);
if($nudebug) echo "<pre>{$last_line}</pre>";

//erase tracks:
if(! $nudebug){
    unlink($work_dir ."/". $batch_file);
}
 return $batch_file;
}



function create_command($cmd, $input_file, $outputfile="tmp.pdf", $position=1, $dir_raiz="",$input_dir="c:/tmp", $output_dir="c:/tmp",$options="-a"){
/*
$position contiene la posicion en el archivo de 0 a N-1
si $position >0 es adicionar
si $position <0 es eliminar
si $position == 65536 es contar.

*/
$alterdir=0;   /*usa el directorio sin xampp */
$nudebug=0;

$work_dir=sfConfig::get('sf_lib_dir')."/exec";

$disk_unit="";
$disk_unit = $dir_raiz;  /*fija la unidad donde esta instalada la carpeta de imagenes */
//get rid of double slashes //
//$image_count = file_get_contents($disk_unit .$outputfile .".count");

$input_file=str_replace("//","/",$input_file);
$output_file=str_replace("//","/",$outputfile);
$input_dir=str_replace("//","/",$input_dir);
$output_dir=str_replace("//","/",$output_dir);
//position must be decreased in 1.

$tmp_file=  $disk_unit."".sprintf( "tmp%s.tif",md5( rand(100000,99999)  . date("YmdGis") ) );
$tmp_file0= $disk_unit."".sprintf("tmp0%s.tif",md5( rand(100000,99999)  . date("YmdGis") ) );
$tmp_file1= $disk_unit."".sprintf("tmp1%s.tif",md5( rand(100000,99999)  . date("YmdGis") ) );


if($position>0){
    //Adicionar la imagen antes de $position al archivo. 
    $position=$position-1;
    if($position==0){
    $sb_commandline=sprintf("%s %s \"%s\" $tmp_file \r\n
                             %s %s \"%s\" $tmp_file \r\n
                             %s %s $tmp_file \"%s\" \r\n",     
							 $work_dir ."/".$cmd,"",$disk_unit . $input_file,
							 $work_dir ."/".$cmd,$options, $disk_unit . $outputfile,
							 $work_dir ."/".$cmd,"",$disk_unit . $outputfile
							 );
    }else{
    //dependiendo del numero crea el nuevo archivo.
    $arreglo_imagenes="";
    for($i=0;$i<$position;$i++){
        $arreglo_imagenes[$i]=$i;
    }    
    //lista de imagenes anteriores a $position
    if($arreglo_imagenes){
       $lista_imagenes1=implode(",", $arreglo_imagenes);
    }else {
       $lista_imagenes1="0";
    }
    //lista de imagenes desde position
    $lista_imagenes2=$position .",";
    $sb_commandline=sprintf("%s %s \"%s\",$lista_imagenes1 $tmp_file \r\n
                             %s %s \"%s\" $tmp_file \r\n
                             %s %s \"%s\",$lista_imagenes2 $tmp_file \r\n
                             %s %s $tmp_file \"%s\" \r\n
                             echo OK >> run.log",     
							 $work_dir ."/".$cmd,"",      $disk_unit . $outputfile,
                             $work_dir ."/".$cmd,$options,$disk_unit .$input_file,
                             $work_dir ."/".$cmd,$options,$disk_unit . $outputfile,
                             $work_dir ."/".$cmd,"",      $disk_unit . $outputfile
                            );    
    }	  
}else{
     //Elimina una imagen 
    if($position==0){
    $sb_commandline=sprintf("%s %s \"%s\"     $tmp_file0 \r\n
                             %s %s \"%s\"     $tmp_file1 \r\n
                             %s %s $tmp_file1 $tmp_file0 \r\n
                             %s %s $tmp_file0 \"%s\"     \r\n
                             echo OK >> run.log",     
							 $work_dir ."/".$cmd, "",      $input_file,
                             $work_dir ."/".$cmd, "",      $disk_unit . $outputfile,
                             $work_dir ."/".$cmd, $options, 
                             $work_dir ."/".$cmd, "",      $disk_unit . $outputfile );
    }else{
        if($position == -65536 ){
            $sb_commandline=sprintf("%s \"%s\" > \"%s.count\"\r\n", $work_dir ."/".$cmd, 
            $disk_unit . $outputfile, 
            $disk_unit . $outputfile);              
        } else{
           //erase an image
           if($debug) echo "Erase an image";
           
           $arreglo_imagenes="";
           $position=$position * (-1);
           $position=$position-1;
           
           if($position==0){
                 $sb_commandline=sprintf(
				            "%s %s   \"%s\",1, $tmp_file0 \r\n
                             %s %s $tmp_file0  \"%s\"     \r\n
                             echo OK >> run.log",     
							 $work_dir ."/".$cmd,"", $disk_unit . $outputfile,
                             $work_dir ."/".$cmd,"", $disk_unit . $outputfile
                            );
          }else{      
           for($i=0;$i<($position);$i++){
               $arreglo_imagenes[$i]=$i;
           }    
           if($arreglo_imagenes){
               $lista_imagenes1=implode(",", $arreglo_imagenes);
           }else{
               $lista_imagenes1="0";
           }
           $jumped=$position+1;
           $lista_imagenes2=$jumped .",";
           $sb_commandline=sprintf(
		                    "%s %s \"%s\",$lista_imagenes1 $tmp_file0 \r\n
                             %s %s \"%s\",$lista_imagenes2 $tmp_file0 \r\n
                             %s %s $tmp_file0              \"%s\"     \r\n
                             echo OK >> run.log",     
							 $work_dir ."/".$cmd,"",      $disk_unit . $outputfile,
                             $work_dir ."/".$cmd,$options,$disk_unit . $outputfile,
                             $work_dir ."/".$cmd,"",      $disk_unit . $outputfile
                             );
           //erase an image           
          }
        }    
    }
}

$sb_commandline=str_replace("//","/",$sb_commandline);
$sb_commandline=str_replace("//","/",$sb_commandline);
$sb_commandline=str_replace("//","/",$sb_commandline);
//echo $sb_commandline;
//echo $dir_raiz;
//exit;
//if($nudebug) echo "<p>" . $sb_commandline ."</p>";
 $batch_file = $this->run_command($sb_commandline,$dir_raiz);
 if(file_exists($tmp_file)) unlink($tmp_file);
 if(file_exists($tmp_file0)) unlink($tmp_file0);
 if(file_exists($tmp_file1)) unlink($tmp_file1);
 return $batch_file;
}

function deletePage($cmd,$outputfile,$page_delete,$disk_unit,$total_paginas,$options="-a")
 {
 	$work_dir="/projects/simad/lib/exec"; 
	$tmp_file0= sprintf("tmp0%s.tif",md5( rand(100000,99999)  . date("YmdGis") ) );
	//dependiendo del numero crea el nuevo archivo.
    $arreglo_imagenes="";
    for($i=0;$i<$position;$i++){
        $arreglo_imagenes[$i]=$i;
    }     	
           //erase an image
           if($debug) echo "Erase an image";
           
           $arreglo_imagenes="";
           //$position=$position;
           $position=$position-1;
           
           if($position==0){
                 $sb_commandline=sprintf(
				            "%s %s   \"%s\",1, $tmp_file0 \r\n
                             %s %s $tmp_file0  \"%s\"     \r\n
                             echo OK >> run.log",     
							 $work_dir ."/".$cmd,"", $disk_unit . $outputfile,
                             $work_dir ."/".$cmd,"", $disk_unit . $outputfile
                            );
          }else{      
           for($i=0;$i<($position);$i++){
               $arreglo_imagenes[$i]=$i;
           }    
           if($arreglo_imagenes){
               $lista_imagenes1=implode(",", $arreglo_imagenes);
           }else{
               $lista_imagenes1="0";
           }
           $jumped=$position+1;
           $lista_imagenes2=$jumped .",";
           $sb_commandline=sprintf(
		                    "%s %s \"%s\",$lista_imagenes1 $tmp_file0 \r\n
                             %s %s \"%s\",$lista_imagenes2 $tmp_file0 \r\n
                             %s %s $tmp_file0              \"%s\"     \r\n
                             echo OK >> run.log",     
							 $work_dir ."/".$cmd,"",      $disk_unit . $outputfile,
                             $work_dir ."/".$cmd,$options,$disk_unit . $outputfile,
                             $work_dir ."/".$cmd,"",      $disk_unit . $outputfile
                             );
           //erase an image
          }            
    return $tmp_file;
 }
 
function is_tiff($filename){
     $arfilename=explode(".", $filename);
     // var_dump($arfilename);
      if($arfilename[1]=="tif" ||
         $arfilename[1]=="TIF" ||
         $arfilename[1]=="tiff" ||
         $arfilename[1]=="TIFF" ){

          return 1;

      }
      return 0;
}


	public function mergePage($input_file, $outputfile="tmp.pdf", $position=1,$disk_unit='c:\Imagenes/', $options="-a")
	{
		$cmd="/projects/simad/lib/exec/tiffcp";
		$rango_paginas = split("-",$position);	
		if($rango_paginas[1] == ''){
			$rango_paginas[1] = $rango_paginas[0];	
		}
		//dependiendo del numero crea el nuevo archivo.
	    $arreglo_imagenes="";
	    $cont_inicial = ($rango_paginas[0] - 1);
	    //echo $rango_paginas[0];
	    for($i=$cont_inicial;$i<$rango_paginas[1];$i++){
	        $arreglo_imagenes .= ",".$i;
	    }    	       
	    $sb_commandline=sprintf("%s \"%s\"$arreglo_imagenes %s \r\n",     
	                             $cmd." ".$options,$input_file,$disk_unit.$outputfile.'.tif');				
		return $sb_commandline;			
	}

}

?>
