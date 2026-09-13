<!DOCTYPE html>
<!--
Copyright 2012 Mozilla Foundation

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

Adobe CMap resources are covered by their own copyright but the same license:

    Copyright 1990-2015 Adobe Systems Incorporated.

See https://github.com/adobe-type-tools/cmap-resources
-->
<?php
	
    $path_theme = sfConfig::get('theme_simad');
    $base_path = sfConfig::get('base_simad');
    $web_path = sfConfig::get('sf_web_dir');
    $publicUrl = sfConfig::get('publicUrl');

    /*$pdfjs_wdir = sfConfig::get('theme_simad').'assets/js/pdfjs/';
    $pdfjs_thumbs = 1;*/

    $fname = sprintf("%s.%s",md5(uniqid().time()),"pdf");
    $source_file = $unidad_documental->getEidxAbspath().DIRECTORY_SEPARATOR.$unidad_documental->getEidxRelpath();
    //**************************************************************************************************************
    $target_base = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.'tmp';
    $folder_tmp = md5($unidad_documental->getPrimaryKey().time());
    $target_path = $target_base.DIRECTORY_SEPARATOR.$folder_tmp;
    //**************************************************************************************************************
    simad_util::createPath($target_path);
    if(file_exists($source_file)){
        copy($source_file,$target_path.DIRECTORY_SEPARATOR.$fname);
        $pdf_file = '/tmp/'.$folder_tmp.'/'.$fname;
        sfContext::getInstance()->getUser()->setAttribute('document_idx', $pdf_file, 'subscriber');
    }else{
		exit();
	}
?>

<iframe style="background: white; display: block; visibility: visible;" frameborder="0" width="100%" height="100%" scrolling="auto" src="<?php echo $publicUrl; ?>/view?attachId=139422591134230010&redirectError=true"></iframe>

