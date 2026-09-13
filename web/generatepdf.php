<?php   
	require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
    $configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', true);
    sfContext::createInstance($configuration);
 
    // Borra las dos líneas siguientes si no utilizas la base de datos
    $databaseManager = new sfDatabaseManager($configuration);
    $databaseManager->loadConfiguration();
    //**********************************************************************************************
    $dir_jar = sfConfig::get('sf_lib_dir').DIRECTORY_SEPARATOR.'pd4ml'.DIRECTORY_SEPARATOR.'pd4ml.jar';
    $dir_font = sfConfig::get('sf_lib_dir').DIRECTORY_SEPARATOR.'pd4ml'.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'static';
    $java = simad_util::getJavaJdkRoot();
	//$java = 'C:\Java\jdk-11.0.12.7-hotspot\bin\java.exe';
	//**********************************************************************************************
    $name_file = "";
    $format_page = "A4";
    $size_point_page = "840";
    $margins = "";
    $adjustwidth = "-adjustwidth";
    $font_use = "-ttf ".$dir_font;
    $watermark = "";
    $orientation = "";    
    $savefile = trim($_REQUEST['savefile']);
    $out = "";
    //****************************************************************************************
	$margin_top = "20";
    $margin_buttom = "19";
    $margin_left = "18";
    $margin_rigth = "12";
    //****************************************************************************************
    if($_REQUEST['top'] != 0 && trim($_REQUEST['top']) != ""){
        $margin_top = ceil($_REQUEST['top']);  
    }
    if($_REQUEST['buttom'] != 0 && trim($_REQUEST['buttom']) != ""){
        $margin_buttom = ceil($_REQUEST['buttom']) < 22 ? 22 : ceil($_REQUEST['buttom']);  
    }
    if($_REQUEST['left'] != 0 && trim($_REQUEST['left']) != ""){
        $margin_left = ceil($_REQUEST['left']);  
    }
    if($_REQUEST['rigth'] != 0 && trim($_REQUEST['rigth']) != ""){
        $margin_rigth = ceil($_REQUEST['rigth']);  
    }
    //****************************************************************************************
    if($_REQUEST['cominterna_id'] != ""){
       $com_interna = ComInternaPeer::retrieveByPK($_REQUEST['cominterna_id']);       
       //*************************************************************************************
       if($com_interna->getUseMembrete()){
            $image_membrete = $com_interna->getRegional()->getImageMembrete();
            $watermark = getImageMembretePath($image_membrete,1);
       }
       //*************************************************************************************
       $name_file =  $_REQUEST['cominterna_id'];
       $fullpath = "com_html/com_interna/".$name_file.".php";
       $out .= md5($name_file).".pdf";       
       $url = getUrlBase() . $fullpath;
       //$margins = '-insets 10,23,8,17,mm';//superior=3,izquierda=3,inferior=2,derecha=2
       $margins = "-insets $margin_top,$margin_left,$margin_buttom,$margin_rigth,mm";
    }elseif($_REQUEST['comenviada_id'] != ""){
       $com_enviada = ComEnviadaPeer::retrieveByPK($_REQUEST['comenviada_id']);
       //*************************************************************************************
       if($com_enviada->getUseMembrete()){
            $image_membrete = $com_enviada->getRegional()->getImageMembrete();
            $watermark = getImageMembretePath($image_membrete,2);
       }
       //*************************************************************************************
       $name_file =  $_REQUEST['comenviada_id'];
       $out .= md5($name_file).".pdf";
       $fullpath = "com_html/com_enviada/".$name_file.".php";
       $url = getUrlBase() . $fullpath;       
       //$margins = '-insets 1,23,8,17,mm';
       $margins = "-insets $margin_top,$margin_left,$margin_buttom,$margin_rigth,mm";
	}elseif($_REQUEST['comenviadafactura_id'] != ""){
       $name_file =  'factura_'.$_REQUEST['comenviadafactura_id'];
       $out .= md5($name_file).".pdf";
       $fullpath = "com_html/com_enviada/".$name_file.".php";
       $url = getUrlBase() . $fullpath;       
       //$margins = '-insets 1,23,8,17,mm';
       $margins = '-insets 1,23,8,17,mm';
       $watermark = "";
    }elseif($_REQUEST['servicio_id'] != ""){
       $name_file =  $_REQUEST['servicio_id'];
       $margins = '-insets 5,20,1,20,pt';        
       $orientation = '-orientation LANDSCAPE';
       $size_point_page = "800";
       $format_page = "LETTER";
       $out .= md5($name_file);
       $fullpath = "com_html/servicios/".$name_file;
       $url = getUrlBase() . $fullpath;
    }elseif($_REQUEST['planillaprestamo_id'] != ""){
       $name_file =  $_REQUEST['planillaprestamo_id'];       
       $out .= md5($name_file).".pdf";
       $fullpath = "com_html/archivo/".$name_file;
       $url = getUrlBase() . $fullpath;
       $orientation = '-orientation LANDSCAPE';
       $margins = '-insets 5,1,1,1,pt';
       $format_page = "LEGAL";
    }elseif($_REQUEST['transferrpt'] != ""){
       $name_file =  $_REQUEST['transferrpt'];       
       $out .= md5($name_file).".pdf";
       $fullpath = "com_html/archivo/".$name_file;
       $url = getUrlBase() . $fullpath;
       $orientation = '-orientation LANDSCAPE';
       $margins = '-insets 5,1,1,1,pt';
       $format_page = "LEGAL";
    }elseif($_REQUEST['docplanillaprestamo_id'] != ""){
       $name_file =  $_REQUEST['docplanillaprestamo_id'];       
       $out .= md5($name_file).".pdf";
       $fullpath = "com_html/documentacion_tecnica/".$name_file.".php";
       $url = getUrlBase() . $fullpath; 
    }elseif($_REQUEST['planillarecibida_id'] != ""){
       $name_file =  $_REQUEST['planillarecibida_id'];
       $fullpath = $name_file;
       $out .= md5($name_file).".pdf";
       $fullpath = "com_html/com_recibida/".$name_file;
       $url = getUrlBase() . $fullpath;
       $margins = '-insets 5,5,5,5,pt';        
       $orientation = '-orientation LANDSCAPE';
    }elseif($_REQUEST['stikercaja_id'] != ""){
       $name_file =  $_REQUEST['stikercaja_id'];
       $fullpath = $name_file;
       $out .= md5($name_file).".pdf";
       $fullpath = "com_html/archivo/".$name_file;
       $url = getUrlBase() . $fullpath;
       $margins = '-insets 5,5,5,5,pt';        
       //$orientation = '-orientation LANDSCAPE';
    }elseif($_REQUEST['formato_inventario_id'] != ""){
       $name_file =  "com_html/archivo/".$_REQUEST['formato_inventario_id'];
       $fullpath = $name_file;
       $url = getUrlBase() . $name_file;
       $margins = "-insets 5,5,5,5,pt";
       $orientation = '-orientation LANDSCAPE';
	   $watermark = "";
       $adjustwidth = "";
    }elseif($_REQUEST['rotulocarpeta_id'] != ""){
        $name_file =  "com_html/archivo/".$_REQUEST['rotulocarpeta_id'];
        $fullpath = $name_file;
        $out .= md5($name_file).".pdf";
        $url = "http://".$_SERVER["HTTP_HOST"]."/".$name_file;
        //$url = getUrlBase() . $name_file;
        $margins = "-insets 1,1,1,1,mm";
        //$orientation = '-orientation LANDSCAPE';
        $watermark = "";
        $adjustwidth = "";
	}elseif($_REQUEST['rotulocaja_id'] != ""){
        $name_file =  "com_html/archivo/".$_REQUEST['rotulocaja_id'];
        $fullpath = $name_file;
        $out .= md5($name_file).".pdf";
        $url = "http://".$_SERVER["HTTP_HOST"]."/".$name_file;
        //$url = getUrlBase() . $name_file;
        $margins = "-insets 1,1,1,1,mm";
        //$orientation = '-orientation LANDSCAPE';
        $watermark = "";
        $adjustwidth = "";
    }elseif($_REQUEST['hojacontrol_id'] != ""){
        $name_file =  "com_html/archivo/".$_REQUEST['hojacontrol_id'];
        $out .= md5($name_file).".pdf";
        $url = "http://".$_SERVER["HTTP_HOST"]."/".$name_file;
        //$url = getUrlBase() . $name_file;
        $margins = "-insets 5,5,5,5,mm";
        $orientation = '-orientation LANDSCAPE';
        $watermark = "";
        $adjustwidth = "";
    }elseif($_REQUEST['constanciarecibido_id'] != ""){
       $name_file =  $_REQUEST['constanciarecibido_id'];
       $out .= md5($name_file).".pdf";
       $fullpath = "com_html/com_recibida/".$name_file.".php";
       $url = getUrlBase() . $fullpath;
       //$orientation = '-orientation LANDSCAPE';
    }elseif($_REQUEST['recordservicio_id'] != ""){
       $name_file = $_REQUEST['recordservicio_id'];
       $out .= md5($name_file).".pdf";
       $fullpath = "com_html/servicios/".$name_file.".php";
       $url = getUrlBase() . $fullpath;
       $margins = '-insets 5,1,5,1,pt';
       //$orientation = '-orientation LANDSCAPE';
    }elseif($_REQUEST['exportfact'] != ""){
       $name_file =  $_REQUEST['exportfact'];
       $out .= md5($name_file).".pdf";
       $fullpath = "com_html/facturas/".$name_file;
       $url = getUrlBase() . $fullpath;
       $margins = '-insets 5,10,5,5,mm';        
       $orientation = '-orientation LANDSCAPE';
    }elseif($_REQUEST['exportworkflow'] != ""){
       $name_file =  $_REQUEST['exportworkflow'];
       $out .= md5($name_file).".pdf";
       $fullpath = "com_html/workflow/".$name_file;
       $url = getUrlBase() . $fullpath;
       $margins = '-insets 5,10,5,5,mm';        
       $orientation = '-orientation LANDSCAPE';
    }
    //**********************************************************************************************
	$pdfname = md5(date("YmdGis")).'.pdf';
    //****************************************************************************************
    if($savefile)
        $out = "-out ".sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."tmp".DIRECTORY_SEPARATOR.$out;
    else
        $out = "";
	//**********************************************************************************************
    if(trim($url) != "")
    {
        if(!$savefile){
            header("Pragma: cache");
            header("Expires: 0");
            header("Cache-control: private");
            header('Content-type: application/pdf');        
            header('Content-Disposition: attachment; filename="'.$pdfname.'"');
            //header('Content-disposition: inline');
        }
        //******************************************************************************************
        if ( strpos(php_uname(), 'Windows' ) !== FALSE) { 
            // server platform: Windows
            $dir_jar = preg_replace('/\//', "\\", $dir_jar);
            $cmdline = "$java -Xmx512m -cp $dir_jar Pd4Cmd \"$url\" $size_point_page $format_page $orientation $margins $adjustwidth $watermark $font_use $out";
        } else {
            $cmdline = "$java -XX:MaxHeapSize=8m -XX:CompressedClassSpaceSize=64m -XX:+UseSerialGC -Djava.awt.headless=true -cp $dir_jar Pd4Cmd \"$url\" $size_point_page $format_page $orientation $margins $adjustwidth $watermark $font_use $out";
			//$cmdline = "$java -Xms1024m -Xmx4096m -Djava.awt.headless=true -cp $dir_jar Pd4Cmd \"$url\" $size_point_page $format_page $orientation $margins $adjustwidth $watermark $font_use";	    
        }
        //******************************************************************************************
		//echo $cmdline;exit;
		passthru($cmdline);
		//******************************************************************************************
        //para guardarlo en el servidor     
        /*passthru('java -Xmx512m -cp '.$dir_jar.'pd4ml_demo.jar Pd4Cmd http://localhost/simad/com_html/'.$com_interna->getPrimaryKey().'.html 800 A4 -out C:\Datos_Terpel\\'.$com_interna->getPrimaryKey().'.pdf');*/         
        /*} else {  
            echo 'invalid usage';  
        }*/
   }
   
   //para eliminarlo del disco        
   if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.$fullpath))
   {
    unlink(dirname(__FILE__).DIRECTORY_SEPARATOR.$fullpath);
   }
   
   function getUrlBase() {
		$http = 'http';        
		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'on') {
			$http .= "";
		}		
		$url = $http . "://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$filename = explode("/", $url);		
		$base = "";
		for( $i = 0; $i < (count($filename) - 1); ++$i ) {
			$base .= $filename[$i].'/';
		}
		return $base;
	}

   /**
   * Retrieves a url to image membrete for generate pdf.
   *
   * @param string $image_membrete nombre de la imagen que se usara como membrete
   * @param int    $type tipo de comunicacion que se esta generando  1 = com_interna, 2 = com_enviada, 0 retorna null
   *
   * @return full url to membrete image, otherwise null
   */
    function getImageMembretePath($image_membrete,$type=0)//0=default;1=com_interna;2=com_enviada
    {
        $str = "";
		switch($type){
		  case 1://com_interna
                $filepath = "images%sencabezado_carta%s".$image_membrete;
                $web_path = sprintf($filepath,"/","/");            
                $fullpath = sprintf($filepath,DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR);
                $realpath = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$fullpath;
                if(file_exists($realpath)){
                    $str = "-bgimage " . getUrlBase() . $web_path;
                }
            break;
          case 2://com_enviada
                if(trim($_REQUEST['dataheader'])){
                    $image_membrete = trim($_REQUEST['dataheader']);
                }
                //*********************************************************************
                $filepath = "images%sencabezado_carta%s".$image_membrete;
                $web_path = sprintf($filepath,"/","/");            
                $fullpath = sprintf($filepath,DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR);
                $realpath = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$fullpath;
                if(file_exists($realpath)){
                    $str = "-bgimage " . getUrlBase() . $web_path;
                }
            break;
          default:
                $str = null;
            break;
		}
        return $str;
	}
    
?>