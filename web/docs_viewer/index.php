
<?php
    require_once(dirname(__FILE__).'/../../config/ProjectConfiguration.class.php');

    $configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
    sfContext::createInstance($configuration);
    const MAX_FILE_SIZE_BYTES = 99999999;
	
    $path_theme = sfConfig::get('theme_simad');
    $base_path = sfConfig::get('base_simad');
    $web_path = sfConfig::get('sf_web_dir');
    $publicUrl = sfConfig::get('publicUrl');
	
	$pdfjs_wdir = sfConfig::get('theme_simad').'assets/js/pdfjs/';
	//**************************************************************************************************************
	$pdfjs_thumbs = 1;
    $pdf_file = $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('document_idx', '', 'subscriber');
	//**************************************************************************************************************
    if(empty($pdf_file)){
        header("Location: /no_file_exists.html");
        exit();
    }
	//**************************************************************************************************************
	$filesize = filesize($pdf_file);
    if($filesize > MAX_FILE_SIZE_BYTES)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($pdf_file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $filesize);
        ob_clean();
        flush();
        readfile($pdf_file);
        exit;
    }
	//**************************************************************************************************************
	include_once('viewDocLite.php');
?>