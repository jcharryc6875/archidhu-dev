<?php
	
    $path_theme = sfConfig::get('theme_simad');
    $base_path = sfConfig::get('base_simad');
    $web_path = sfConfig::get('sf_web_dir');
    $publicUrl = sfConfig::get('publicUrl');


    //var_dump($com_interna); exit; 

    /*$pdfjs_wdir = sfConfig::get('theme_simad').'assets/js/pdfjs/';
    $pdfjs_thumbs = 1;*/

    //$fname = sprintf("%s.%s",md5(uniqid().time()),"pdf");
    //$source_file = $com_interna->getPathImageDigitByCom();

    //exit; 

    //**************************************************************************************************************
    //$target_base = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
    //$folder_tmp = md5($com_interna->getPrimaryKey().time());
    //$target_path = $target_base.DIRECTORY_SEPARATOR.$folder_tmp;
    //**************************************************************************************************************
    //simad_util::createPath($target_path);
    //if(file_exists($source_file))
    //{
        //copy($source_file,$target_path.DIRECTORY_SEPARATOR.$fname);
        //$pdf_file = '/tmp/'.$folder_tmp.'/'.$fname;
        //sfContext::getInstance()->getUser()->setAttribute('document_idx', $pdf_file, 'subscriber');
        ////$this->getUser()->setAttribute('com_interna_advance_create',$com_interna_advance_create,  'subscriber');
        ////$_SESSION['document_idx'] = $pdf_file;
    //}
    //else
    //{
		    //exit(); 
        //return;
	  //}
?>

<iframe style="background: white; display: block; visibility: visible;" frameborder="0" width="100%" height="100%" scrolling="auto" src="<?php echo $publicUrl; ?>/view?attachId=139422591134230010&redirectError=true"></iframe>
