<?php
/*
Clase de ejecucion.

usa:

$disk_unit : unidad donde esta la carpeta /Imagenes
$alterdir  : si esta a 1 suprime /xampp/ de la cadena de instalacion

*/

class command_exec {

function run_command($sbcommand , $work_dir="/apache/xampp/htdocs/simad/exec")
//function run_command($sbcommand , ///$work_dir="/apache/htdocs/simad/exec")
{
$nudebug=1;    
$alterdir=0;

if( $alterdir){
    $work_dir="/apache/htdocs/simad/exec";
}

$batch_file= sprintf( "exe%s.bat", md5( rand( 100000, 99999 ). date("YmdGis") ) );

if($nudebug) echo $batch_file . "<p>";

//$sbtmp=$work_dir ."/" .$sbcommand;
//$sbcommand=$sbtmp;
//$sbcommand .="\necho OK >> run.log";

file_put_contents($work_dir ."/" . $batch_file, $sbcommand);
//file_put_contents($work_dir ."/" . //$batch_file, "pause");

if($nudebug) echo $sbcommand . "<p>";

$mycmd=$work_dir . "/" . $batch_file .">>run.log";

echo '<PRE>';
$last_line = system($mycmd, $retval);
echo '</PRE>';
if($nudebug) echo "<pre>{$last_line}</pre>";

//erase tracks:
if(! $nudebug){
    unlink($work_dir ."/". $batch_file);
}


}



function create_command($cmd, $input_file, $outputfile="tmp.pdf", $position=1, $input_dir="c:/tmp", $output_dir="c:/tmp", $options="-a"){
/*
$position contiene la posicion en el archivo de 0 a N-1
si $position >0 es adicionar
si $position <0 es eliminar
si $position == 65536 es contar.

*/

$alterdir=0;   /*usa el directorio sin xampp */
$nudebug=1;
$disk_unit="";
$disk_unit="c:";  /*fija la unidad donde esta instalada la carpeta de imagenes */

if( $alterdir){
$work_dir="/apache/htdocs/simad/exec";
}else{
$work_dir="/apache/xampp/htdocs/simad/exec";
}

//get rid of double slashes //

$input_file=str_replace("//","/",$input_file);
$output_file=str_replace("//","/",$output_file);
$input_file=str_replace("//","/",$input_file);
$output_dir=str_replace("//","/",$output_dir);
//position must be decreased in 1.

$tmp_file=  sprintf( "tmp%s.tif",md5( rand(100000,99999)  . date("YmdGis") ) );
$tmp_file0= sprintf("tmp0%s.tif",md5( rand(100000,99999)  . date("YmdGis") ) );
$tmp_file1= sprintf("tmp1%s.tif",md5( rand(100000,99999)  . date("YmdGis") ) );


if($position>0){
    //Adicionar la imagen antes de $position al archivo. 
    $position=$position-1;
    if($position==0){
    $sb_commandline=sprintf("%s %s \"%s\" $tmp_file \r\n
                             %s %s \"%s\" $tmp_file \r\n
                             %s %s $tmp_file \"%s\" \r\n
                             echo OK >> run.log",     
							 $work_dir ."/".$cmd,"",       $disk_unit . $input_file,
							 $work_dir ."/".$cmd,$options, $disk_unit . $outputfile,
							 $work_dir ."/".$cmd,"",       $disk_unit . $outputfile
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
                             $work_dir ."/".$cmd,$options,$input_file,
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
        if($position== -65536 ){
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
//if($nudebug) echo "<p>" . $sb_commandline ."</p>";
$this->run_command($sb_commandline);
}

};

?>
