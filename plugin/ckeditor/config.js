/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:

		config.uiColor = '#F7F2E5';
		config.Defaultlanguage = 'ru';
		// config.language = 'ru';
		config.enterMode = CKEDITOR.ENTER_BR;
		config.shiftenterMode = CKEDITOR.ENTER_P;
		
		config.toolbarStartupExpanded = true;
		
		// config.stylesSet = 'my_styles:/styles.js';
		
        //config.filebrowserBrowseUrl = '/browser/browse.php';
        //config.filebrowserUploadUrl = '/uploader/upload.php';
        // config.filebrowserImageWindowWidth = '640';
        // config.filebrowserImageWindowHeight = '480';

		config.contentsCss = '/inc/style.css?v=3';
		config.bodyId = 'content';

		config.coreStyles_bold = { element : 'b', overrides : 'strong' };
		
		config.height = '250px';
		
		config.resize_dir = 'vertical';
		
		config.enableTabKeyTools = true;

		//config.startupMode = 'source';
		//config.toolbarLocation = 'bottom';
		
		config.toolbar = 'News';
		
		config.toolbar_Full =
		[
			['Source','-','Save','NewPage','Preview','-','Templates'],
			['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
			['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
			['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
			'/',
			['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
			['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
			['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			['BidiLtr', 'BidiRtl'],
			['Link','Unlink','Anchor'],
			['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe'],
			'/',
			['Styles','Format','Font','FontSize'],
			['TextColor','BGColor'],
			['Maximize', 'ShowBlocks','-','About']
		];
		
		config.toolbar_RooCMS =
		[
			['Source','-','Preview','-','Templates'],
			['Undo','Redo'],
			['SelectAll', '-', 'Cut','Copy','Paste','PasteText','PasteFromWord'],
			['Find','Replace'],
			['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
			['SpellChecker', 'Scayt'],
			['RemoveFormat'],
			'/',
			['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
			['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
			['BidiLtr', 'BidiRtl'],
			['Link','Unlink','Anchor'],
			['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','Iframe'],
			'/',
			['Styles','Format','Font','FontSize'],
			['TextColor','BGColor'],
			['Maximize', 'ShowBlocks','-','About']
		];
		
		config.toolbar_News =
		[
			['Source','-','Preview','-','Templates'],
			['Undo','Redo'],
			['SelectAll', '-', 'Cut','Copy','Paste','PasteText','PasteFromWord'],
			['Find','Replace'],
			['Image','Flash','Table','HorizontalRule','SpecialChar','Iframe'],
			['RemoveFormat'],
			'/',
			['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
			['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
			['BidiLtr', 'BidiRtl'],
			['Link','Unlink','Anchor'],
			'/',
			['Styles','Format','Font','FontSize'],
			['TextColor','BGColor'],
			['Maximize', 'ShowBlocks','-','About']
		];
		 
		config.toolbar_Basic =
		[
			['Bold', 'Italic', 'Underline','Strike', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-','About']
		];
};
