/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	// Define changes to default configuration here. For example:
	config.language = 'es';
	config.enterMode = CKEDITOR.ENTER_BR;
	config.height = '450px';
	// config.uiColor = '#AADC6E';
	
	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript';
	
	// Se the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';
	
	// Make dialogs simpler.
	config.removeDialogTabs = 'image:advanced;link:advanced';
	
	//config spellcheker
	config.scayt_autoStartup = true;
	config.scayt_sLang = 'es_ES';
	config.pasteFromWordRemoveFontStyles = true;
    config.pasteFromWordRemoveStyles = false;	
    config.forcePasteAsPlainText = false;
    config.pasteFromWord_inlineImages = true;
    
	//config toolbar	
	config.toolbar = [
		{ name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord' ] },
		{ name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
		{ name: 'links', items: [ 'Link', 'Unlink' ] },
		{ name: 'insert', items: [ 'Image', /*'Flash',*/ 'Table' /*, 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe'*/ ] },
		'/',
		/*{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },*/
        { name: 'styles', items: [ 'Styles', 'FontSize' ] },
		{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
		{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
		{ name: 'about', items: [ 'About' ] }
	];
	
};