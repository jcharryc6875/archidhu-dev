<?php

//path to  the CreateDocx class within your PHPDocX installation
include_once("lib/phpdocx/classes/CreateDocx.inc");
//include_once dirname(__FILE__). '/classes/CreateDocx.inc' or die('error');
//$docx = new CreateDocxFromTemplate('files/carta.docx');
echo $archivo=$_REQUEST['archivo'];

if( $archivo == "PLANTILLA_WORD")
  $archivo= sfConfig::get('sf_lib_dir'). "/templates/Comunicacion_Interna.docx";

$ruta_local="tmp/".basename($archivo);
//copy($_REQUEST['archivo'],$ruta_local);

if(!@copy($archivo,$ruta_local))
{
    $errors= error_get_last();
    echo "COPY ERROR: ".$errors['type'];
    echo "<br />\n".$errors['message'];
} else {
    echo "File copied from remote!";
}

$docx = new CreateDocxFromTemplate($ruta_local);
var_dimp($docx);exit;

require_once(sfConfig::get('sf_lib_dir')."/barcode/applib_barcode.php");
$sticker_dir="./";
$cadena=$_REQUEST['Radicado'];
if($cadena!=""){
    $nombre_imagen=getBarcode( $cadena ,"");
}
            
//echo "<img src=\"tmp/$nombre_imagen\"/>";

//You may include manually the list of variables that should be preprocessed or use
//the getTemplateVariables method for an automatic listing
$variables = $docx->getTemplateVariables();
$docx->processTemplate($variables);

//**************************
//we create a few Word fragments to insert rich content in a table


//$image = new WordFragment($docx);
/*$options = array(
    'src' => "tmp/".$nombre_imagen
);*/

//$image->addImage($options);

$data = array(
	        array(
	            'ITEM' => 'Remitente:',
	            'REFERENCE' => $_REQUEST['Remitente'],
	        ),
	        array(
	            'ITEM' => 'Destinatario:',
	            'REFERENCE' => $_REQUEST['Destinatario'],
	        ),
	        array(
	            'ITEM' => 'Asunto',
	            'REFERENCE' => $_REQUEST['Asunto'],
	        ),
            array(
	            'ITEM' => 'Fecha:',
	            'REFERENCE' => $_REQUEST['Fecha'],
	        ),
	        array(
	            'ITEM' => 'Folios',
	            'REFERENCE' => $_REQUEST['Folios'],
	        )
            ,
	        array(
	            'ITEM' => 'Anexos',
	            'REFERENCE' => $_REQUEST['Anexos'],
	        )
        );
        
        //Para dar respuesta citar este codigo:

$docx->replaceTableVariable($data, array('parseLineBreaks' => true));
$docx->replacePlaceholderImage('LOGO',"tmp/".$nombre_imagen);

//**************

$docx->createDocx('Comunicacion_Interna');

header('Location: Comunicacion_Interna.docx');
