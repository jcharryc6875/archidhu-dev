/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    // Define changes to default configuration here. For example:
	config.language = 'es';
	// config.uiColor = '#AADC6E';
    config.height = '300px';
    config.tabIndex = 1;
    config.enterMode = CKEDITOR.ENTER_BR;
    config.scayt_sLang ="es_ES";//idioma del corrector ortografico
    config.scayt_autoStartup = true;//iniciar el corrector ortografico
    config.tabIndex = 5;
    //config.pasteFromWordCleanupFile = false;
    config.pasteFromWordPromptCleanup = false;
    config.pasteFromWordRemoveFontStyles = true;
    config.pasteFromWordRemoveStyles = false;
    config.skin = 'office2003';
    //config.startupOutlineBlocks = true;
    //config.shiftEnterMode = CKEDITOR.ENTER_P;
    // This is actually the default value.
    config.toolbar_Full =
    [
        //['Source','-','Save','NewPage','Preview','-','Templates'],
        ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
        ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
        //['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
        //'/',
        ['Bold','Italic','Underline','Strike'/*,'-','Subscript','Superscript'*/,'NumberedList','BulletedList','-','Outdent','Indent','Blockquote'/*,'CreateDiv'*/],        
        //'/',
        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', '-','BidiLtr', 'BidiRtl','Table'],        
        //['Link','Unlink','Anchor'],
        //['Table'/*,'Image','Flash','HorizontalRule','Smiley','SpecialChar','PageBreak'*/],
        '/',
        //['Styles','Format','Font','FontSize'],
        //['TextColor','BGColor'],
        ['Maximize', 'ShowBlocks','-','About']
    ];
};
